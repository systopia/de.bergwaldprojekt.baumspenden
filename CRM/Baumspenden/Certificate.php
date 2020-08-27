<?php
/*-------------------------------------------------------+
| Bergwaldprojekt Baumspenden                            |
| Copyright (C) 2020 SYSTOPIA                            |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Baumspenden_ExtensionUtil as E;

class CRM_Baumspenden_Certificate
{

    /**
     * @var array $contribution
     *   The CiviCRM contribution this certificate is issued for.
     */
    protected $contribution;

    /**
     * @var string $mode
     *   Either "email" or "postal".
     */
    protected $mode;

    /**
     * @var string $html
     *   The HTML contents of the certificate.
     */
    protected $html;

    /**
     * @var int $pdf_format_id
     *   The PDF format ID to use with the message template.
     */
    protected $pdf_format_id;

    /**
     * @var string $cover_letter_html
     *   The HTML contents of the cover letter.
     */
    protected $cover_letter_html;

    /**
     * @var int $cover_letter_pdf_format_id
     *   The PDF format ID to use with the cover letter message template.
     */
    protected $cover_letter_pdf_format_id;

    /**
     * @var array $pdf_file
     *   The CiviCRM File entity representing the PDF file.
     */
    protected $pdf_file;

    /**
     * CRM_Baumspenden_Certificate constructor.
     *
     * @param int $contribution_id
     * @param string $mode
     *
     * @throws Exception
     */
    public function __construct($contribution_id, $mode = "email")
    {
        $this->contribution = new CRM_Baumspenden_Donation($contribution_id);

        // Load PDF file ID from custom field.
        $this->pdf_file = $this->getPDFFile();

        // Check if the contact exists. The API will throw an exception, if it
        // doesn't.
        civicrm_api3(
            'Contact',
            'getsingle',
            [
                'id' => $this->contribution->get('contact_id'),
                'return' => ['display_name'],
            ]
        );

        // Validate and set mode.
        if (!in_array($mode, ['email', 'postal'])) {
            throw new Exception(
                E::ts('Invalid certificate mode %1'),
                [1 => $mode]
            );
        }
        $this->mode = $mode;
    }

    /**
     * Retrieves the certificate PDF file entity from the custom field on the
     * contribution.
     *
     * @return array
     * @throws \CiviCRM_API3_Exception
     */
    protected function getPDFFile()
    {
        $file = null;
        $file_id = $this->contribution->get(
            CRM_Baumspenden_CustomData::getCustomFieldKey(
                'baumspende',
                'baumspende_certificate_file'
            )
        );
        if (!empty($file_id)) {
            $file = civicrm_api3(
                'Attachment',
                'getsingle',
                ['id' => $file_id]
            );
        }
        return $file;
    }

    /**
     * Renders the certificate.
     *
     * @throws \CiviCRM_API3_Exception
     *   When the configured message template could not be retrieved.
     */
    public function render(
        $msg_tpl_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_CERTIFICATE
    ) {
        // Load message template.
        $msg_tpl = civicrm_api3(
            'MessageTemplate',
            'getsingle',
            ['id' => $msg_tpl_id]
        );
        $this->html = $msg_tpl['msg_html'];
        $this->pdf_format_id = $msg_tpl['pdf_format_id'];

        // Prepare message template.
        CRM_Contact_Form_Task_PDFLetterCommon::formatMessage($this->html);

        // Replace tokens.
        $this->replaceTokens($this->html);
    }

    /**
     * Renders the cover letter for the certificate.
     *
     * @throws \CiviCRM_API3_Exception
     */
    public function renderCoverLetter()
    {
        if ($this->contribution->get('presentee')) {
            $contact_id = $this->contribution->get('presentee');
            $template_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_COVER_LETTER_PRESENTEE;
        } else {
            $contact_id = $this->contribution->get('contact_id');
            $template_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_COVER_LETTER;
        }
        $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contact_id]);

        $msg_tpl = civicrm_api3(
            'MessageTemplate',
            'getsingle',
            ['id' => $template_id]
        );
        $this->cover_letter_html = $msg_tpl['msg_html'];
        $this->cover_letter_pdf_format_id = $msg_tpl['pdf_format_id'];

        // Prepare message template.
        CRM_Contact_Form_Task_PDFLetterCommon::formatMessage(
            $this->cover_letter_html
        );

        // Replace tokens.
        $this->replaceTokens($this->cover_letter_html);
    }

    /**
     * Converts the rendered message template to PDF and either downloads it or
     * creates a File entity and attaches it to the contribution in the custom
     * file field.
     *
     * @param bool $download
     *   Whether to download the generated file and exit. Defaults to FALSE.
     *
     * @return array
     *   The CiviCRM File entity attached to the contribution.
     *
     * @throws \Exception
     */
    public function convertToPDF($download = false)
    {
        $contribution_id = $this->contribution->get('id');
        $filename = 'baumspenden_zertifikat_' . $contribution_id . '.pdf';
        $pdf = CRM_Utils_PDF_Utils::html2pdf(
            [$this->html],
            $filename,
            !$download,
            $this->pdf_format_id
        );
        if ($download) {
            CRM_Utils_System::civiExit();
            return false;
        } else {
            // Create file attachment entity.
            $file = civicrm_api3(
                'Attachment',
                'create',
                [
                    'entity_table' => 'civicrm_contribution',
                    'entity_id' => $contribution_id,
                    'name' => $filename,
                    'mime_type' => 'application/pdf',
                    'content' => $pdf,
                ]
            );
            if ($file['is_error']) {
                throw new Exception($file['error_message']);
            }

            // Remove previous certificate attachment.
            $custom_field_file = CRM_Baumspenden_CustomData::getCustomFieldKey(
                'baumspende',
                'baumspende_certificate_file'
            );
            $old_file = civicrm_api3(
                'Contribution',
                'getsingle',
                [
                    'id' => $contribution_id,
                    'return' => [$custom_field_file],
                ]
            );
            if (!empty($old_file[$custom_field_file])) {
                civicrm_api3(
                    'Attachment',
                    'delete',
                    ['id' => $old_file[$custom_field_file]]
                );
            }

            // Add as value for the custom field.
            civicrm_api3(
                'Contribution',
                'create',
                [
                    'id' => $this->contribution->get('id'),
                    CRM_Baumspenden_CustomData::getCustomFieldKey(
                        'baumspende',
                        'baumspende_certificate_file'
                    ) => $file['id'],
                ]
            );

            $this->pdf_file = $file['values'][$file['id']];
            return $this->pdf_file;
        }
    }

    /**
     * Sends the PDF certificate file to either the donor or the presentee, if
     * shipping mode is "email", or to the office e-mail address, if shipping
     * mode is "postal".
     *
     * @throws \CiviCRM_API3_Exception
     */
    public function send()
    {
        $donor_contact_id = $this->contribution->get('contact_id');
        if ($this->contribution->get('presentee')) {
            $contact_id = $this->contribution->get('presentee');
            $cover_letter_template_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_COVER_LETTER_PRESENTEE;
        } else {
            $contact_id = $donor_contact_id;
            $cover_letter_template_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_COVER_LETTER;
        }
        $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contact_id]);

        // Render cover letter PDF from message template.
        $this->renderCoverLetter();
        $cover_letter_pdf = CRM_Utils_PDF_Utils::html2pdf(
            [$this->cover_letter_html],
            'baumspenden_anschreiben.pdf',
            true,
            $this->cover_letter_pdf_format_id
        );
        $cover_letter_file = civicrm_api3(
            'Attachment',
            'create',
            [
                'entity_table' => 'civicrm_contribution',
                'entity_id' => $this->contribution->get('id'),
                'name' => 'baumspenden_zertifikat_' . $this->contribution->get(
                        'id'
                    ) . '_anschreiben.pdf',
                'mime_type' => 'application/pdf',
                'content' => $cover_letter_pdf,
            ]
        );
        if ($cover_letter_file['is_error']) {
            throw new Exception($file['error_message']);
        }
        $cover_letter_file = $cover_letter_file['values'][$cover_letter_file['id']];

        if ($this->mode == 'postal') {
            $to_email = CRM_Baumspenden_Configuration::EMAIL_ADDRESS_OFFICE;
            $email_template_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_EMAIL_OFFICE;
        } else {
            $to_email = $contact['email'];
            $email_template_id = CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_EMAIL;
        }

        $from_email = CRM_Core_BAO_Domain::getNameAndEmail(false, true);

        $message_template = civicrm_api3(
            'MessageTemplate',
            'getsingle',
            [
                'id' => $email_template_id
            ]
        );

        // Replace tokens in html and text body.
        if (!empty($message_template['msg_html'])) {
            // Prepare message template.
            CRM_Contact_Form_Task_PDFLetterCommon::formatMessage(
                $message_template['msg_html']
            );
            $this->replaceTokens($message_template['msg_html']);
        }
        if (!empty($message_template['msg_text'])) {
            $this->replaceTokens($message_template['msg_text']);
        }

        $mail_params = [
            'from' => reset($from_email),
            'toName' => $contact['display_name'],
            'toEmail' => $to_email,
            'subject' => $message_template['msg_subject'],
            'text' => !empty($message_template['msg_text']) ? $message_template['msg_text'] : null,
            'html' => !empty($message_template['msg_html']) ? $message_template['msg_html'] : null,
            'attachments' => [
                $cover_letter_file['id'] => [
                    'fullPath' => $cover_letter_file['path'],
                    'mime_type' => $cover_letter_file['mime_type'],
                    'cleanName' => $cover_letter_file['name'],
                ],
                $this->pdf_file['id'] => [
                    'fullPath' => $this->pdf_file['path'],
                    'mime_type' => $this->pdf_file['mime_type'],
                    'cleanName' => $this->pdf_file['name'],
                ],
            ],
        ];
        $sent = CRM_Utils_Mail::send($mail_params);

        // Remove cover letter file attachment.
        civicrm_api3(
            'Attachment',
            'delete',
            [
                'id' => $cover_letter_file['id'],
            ]
        );

        // Handle sending failures: create activity.
        if (!$sent) {
            // Create activity of type "fehlgeschlagene_baumspende".
            $sending_failed_activity_type_id = CRM_Core_PseudoConstant::getKey(
                'CRM_Activity_BAO_Activity',
                'activity_type_id',
                'fehlgeschlagener_baumspende_versand'
            );
            civicrm_api3(
                'Activity',
                'create',
                [
                    'source_contact_id' => CRM_Core_Session::singleton()
                        ->getLoggedInContactID(),
                    'target_id' => $contact_id,
                    'activity_type_id' => $sending_failed_activity_type_id,
                    'status_id' => 'Scheduled',
                    'subject' => CRM_Baumspenden_Configuration::ACTIVITY_SUBJECT_SENDING_FAILED,
                    'details' => E::ts(
                        'Sending a certificate for the <a href="%1">contribution %2</a> failed.',
                        [
                            1 => CRM_Utils_System::url(
                                "civicrm/contact/view/contribution",
                                'reset=1'
                                . '&action=view'
                                . '&cid=' . $donor_contact_id
                                . '&id=' . $this->contribution->get('id')
                            ),
                            2 => $this->contribution->get('id'),
                        ]
                    ),
                ]
            );
        }
    }

    /**
     * Replaces contact and contribution tokens in the HTML contents.
     *
     * @throws Exception
     */
    protected function replaceTokens(&$html)
    {
        // Extract tokens from the HTML.
        $contribution = $this->contribution->getContribution();
        $donor_contact_id = $this->contribution->get('contact_id');
        if ($this->contribution->get('presentee')) {
            $contact_id = $this->contribution->get('presentee');
        } else {
            $contact_id = $donor_contact_id;
        }
        $tokenCategories = self::getTokenCategories();
        $messageToken = CRM_Utils_Token::getTokens($html);
        $returnProperties = [];
        if (isset($messageToken['contact'])) {
            foreach ($messageToken['contact'] as $key => $value) {
                $returnProperties[$value] = 1;
            }
        }
        if (isset($messageToken['contribution'])) {
            foreach ($messageToken['contribution'] as $key => $value) {
                $returnProperties[$value] = 1;
            }
        }
        [$contact] = CRM_Utils_Token::getTokenDetails(
            [$contact_id],
            $returnProperties,
            false,
            false,
            null,
            $messageToken,
            null
        );
        $html = CRM_Utils_Token::replaceContactTokens(
            $html,
            $contact[$contact_id],
            true,
            $messageToken
        );

        // Decide whether amount is 1 or more.
        $custom_field_key_amount = CRM_Baumspenden_CustomData::getCustomFieldKey(
            'baumspende',
            'baumspende_amount'
        );
        $contribution['baumspende_amount'] = $contribution[$custom_field_key_amount];
        $is_plural = (int)($contribution[$custom_field_key_amount] > 1);

        // Replace custom field values that are option values with the
        // corresponding option value name.
        foreach ([
            'plant_period',
            'plant_region',
            'plant_tree',
                 ] as $custom_field_name) {
            $custom_field_key = CRM_Baumspenden_CustomData::getCustomFieldKey(
                'baumspende',
                'baumspende_' . $custom_field_name
            );
            $contribution['baumspende_' . $custom_field_name . '_key'] = $contribution[$custom_field_key];
            $contribution['baumspende_' . $custom_field_name] = CRM_Core_PseudoConstant::getName(
                'CRM_Contribute_BAO_Contribution',
                $custom_field_key,
                $contribution[$custom_field_key]
            );

            // Handle plural format for tree species.
            if ($custom_field_name == 'plant_tree') {
                $plural_variants = explode(
                    '|',
                    $contribution['baumspende_' . $custom_field_name]
                );
                $contribution['baumspende_' . $custom_field_name] = $plural_variants[$is_plural];
                // Replace singular amount with article if set.
                if (!$is_plural && !empty($plural_variants[2])) {
                    $contribution['baumspende_amount'] = $plural_variants[2];
                }
            }
        }

        // Add donor tokens to contribution, since they are not available when sending
        // to peresentee.
        $contribution['baumspende_donor_contact_id'] = $donor_contact_id;
        if ($donor_contact_id != $contact_id) {
            $donor_contact = civicrm_api3(
                'Contact',
                'getsingle',
                ['id' => $donor_contact_id]
            );
        }
        else {
            $donor_contact = $contact;
        }
        $contribution['baumspende_donor_first_name'] = $donor_contact['first_name'];
        $contribution['baumspende_donor_last_name'] = $donor_contact['last_name'];

        $html = CRM_Utils_Token::replaceContributionTokens(
            $html,
            $contribution,
            true,
            $messageToken
        );
        $html = CRM_Utils_Token::replaceHookTokens(
            $html,
            $contact[$contact_id],
            $tokenCategories,
            true
        );

        // Render with Smarty, if enabled.
        if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY) {
            /* @var CRM_Core_Smarty $smarty */
            $smarty = CRM_Core_Smarty::singleton();
            // also add the contact tokens to the template
            $smarty->assign_by_ref('contact', $contact);
            $html = $smarty->fetch("string:$html");
        }
    }

    /**
     * Get the categories required for rendering tokens.
     *
     * @return array
     */
    protected static function getTokenCategories()
    {
        if (!isset(Civi::$statics[__CLASS__]['token_categories'])) {
            $tokens = [];
            CRM_Utils_Hook::tokens($tokens);
            Civi::$statics[__CLASS__]['token_categories'] = array_keys(
                $tokens
            );
        }
        return Civi::$statics[__CLASS__]['token_categories'];
    }
}
