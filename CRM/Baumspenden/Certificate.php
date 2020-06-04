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
     * Renders the certificate.
     *
     * @throws \CiviCRM_API3_Exception
     *   When the configured message template could not be retrieved.
     */
    public function render()
    {
        // Load message template.
        $msg_tpl = civicrm_api3(
            'MessageTemplate',
            'getsingle',
            ['id' => CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_CERTIFICATE]
        );
        $this->html = $msg_tpl['msg_html'];
        $this->pdf_format_id = $msg_tpl['pdf_format_id'];

        // Prepare message template.
        CRM_Contact_Form_Task_PDFLetterCommon::formatMessage($this->html);

        // Replace tokens.
        $this->replaceTokens();
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
        $filename = 'baumspenden_certificate_' . $contribution_id . '.pdf';
        $pdf = CRM_Utils_PDF_Utils::html2pdf(
            [$this->html],
            $filename,
            !$download
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
        if ($this->contribution->get('presentee')) {
            $contact_id = $this->contribution->get('presentee');
            // TODO: Select cover letter template for presentees.
        } else {
            $contact_id = $this->contribution->get('contact_id');
            // TODO: Select cover letter template for donors.
        }
        $contact = civicrm_api3('Contact', 'getsingle', ['id' => $contact_id]);

        // TODO: Render cover letter PDF from message template.

        if ($this->mode == 'postal') {
            $to_email = CRM_Baumspenden_Configuration::EMAIL_ADDRESS_OFFICE;
            // TODO: Select different e-mail template?
        } else {
            $to_email = $contact['email'];
            // TODO: Select different e-mail template?
        }

        $from_email = CRM_Core_BAO_Domain::getNameAndEmail(false, true);
        $result = civicrm_api3(
            'MessageTemplate',
            'send',
            [
                'id' => CRM_Baumspenden_Configuration::MESSAGE_TEMPLATE_ID_EMAIL,
                'contact_id' => $contact_id,
                'template_params' => "",
                'from' => reset($from_email),
                'to_name' => $contact['display_name'],
                'to_email' => $to_email,
                'attachments' => [
                    $this->pdf_file['id'] => [
                        'fullPath' => $this->pdf_file['path'],
                        'mime_type' => $this->pdf_file['mime_type'],
                        'cleanName' => $this->pdf_file['name'],
                    ],
                    // TODO: Add cover letter PDF file as attachment.
                ],
            ]
        );
        // TODO: Handle sending failures? Create activity?
    }

    /**
     * Replaces contact and contribution tokens in the HTML contents.
     *
     * @throws Exception
     */
    protected function replaceTokens()
    {
        // Extract tokens from the HTML.
        $contribution = $this->contribution->getContribution();
        if ($this->contribution->get('presentee')) {
            $contact_id = $this->contribution->get('presentee');
        } else {
            $contact_id = $this->contribution->get('contact_id');
        }
        $tokenCategories = self::getTokenCategories();
        $messageToken = CRM_Utils_Token::getTokens($this->html);
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
        $this->html = CRM_Utils_Token::replaceContactTokens(
            $this->html,
            $contact[$contact_id],
            true,
            $messageToken
        );
        $this->html = CRM_Utils_Token::replaceContributionTokens(
            $this->html,
            $contribution,
            true,
            $messageToken
        );
        $this->html = CRM_Utils_Token::replaceHookTokens(
            $this->html,
            $contact[$contact_id],
            $tokenCategories,
            true
        );

        // Render with Smarty, if enabled.
        if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY) {
            $smarty = CRM_Core_Smarty::singleton();
            // also add the contact tokens to the template
            $smarty->assign_by_ref('contact', $contact);
            $this->html = $smarty->fetch("string:$this->html");
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
