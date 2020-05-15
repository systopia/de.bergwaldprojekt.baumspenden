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
     *   Either "digital" or "postal".
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
     * CRM_Baumspenden_Certificate constructor.
     *
     * @param int $contribution_id
     * @param string $mode
     * @param string $name
     */
    public function __construct($contribution_id, $mode = "digital")
    {
        $this->contribution = new CRM_Baumspenden_Donation($contribution_id);

        // Check if contact exists.
        $contact = civicrm_api3(
            'Contact',
            'getsingle',
            [
                'id' => $this->contribution->get('contact_id'),
                'return' => ['display_name'],
            ]
        );

        // Validate and set mode.
        if (!in_array($mode, ['digital', 'postal'])) {
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
        // Retrieve message template from configuration.
        $msg_tpl_id = CRM_Baumspenden_Configuration::CERTIFICATE_MESSAGE_TEMPLATE_ID;

        // Load message template.
        $msg_tpl = civicrm_api3(
            'MessageTemplate',
            'getsingle',
            ['id' => $msg_tpl_id]
        );
        $this->html = $msg_tpl['msg_html'];
        $this->pdf_format_id = $msg_tpl['pdf_format_id'];

        // Prepare message template.
        \CRM_Contact_Form_Task_PDFLetterCommon::formatMessage($this->html);

        // Replace tokens.
        $this->replaceTokens();
    }

    /**
     * Converts the rendered message template to PDF and downloads it.
     *
     * @param string $filename
     *   (optional) The file name to use when downloading.
     *
     * @param bool $store
     *   Whether to store the generated file as a file entity. Defaults to TRUE.
     *
     * @param bool $download
     *   Whether to download the generated file and exit. Defaults to FALSE.
     *
     * @throws \Exception
     */
    public function convertToPDF($store = true, $download = false)
    {
        $contribution_id = $this->contribution->get('id');
        $filename = 'baumspenden_certificate_'. $contribution_id . '_'. '.pdf';
        $pdf = \CRM_Utils_PDF_Utils::html2pdf(
            [$this->html],
            $filename,
            !$download
        );
        if ($download) {
            CRM_Utils_System::civiExit();
        } else {
            $path = Civi::paths()->getPath(
                '[civicrm.private]/baumspenden/' . $filename
            );
            if (!file_put_contents($path, $pdf)) {
                throw new Exception('Could not create file.');
            }

            // Create file entity.
            $file_types = CRM_Core_OptionGroup::values(
                'safe_file_extension',
                true
            );
            // TODO: The uri gets cut off and the file entity is stored with
            //   only the file name, causing the file not being downloadable.
            $file = civicrm_api3(
                'File',
                'create',
                [
                    'file_type_id' => $file_types['pdf'],
                    'uri' => $path,
                    'mime_type' => 'application/pdf',
                ]
            );

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

            // TODO: Create activity and attach certificate file.
        }
    }

    /**
     * Replaces contact and contribution tokens in the HTML contents.
     */
    protected function replaceTokens()
    {
        // Extract tokens from the HTML.
        $contact_id = $this->contribution->get('contact_id');
        $tokenCategories = self::getTokenCategories();
        $messageToken = \CRM_Utils_Token::getTokens($this->html);
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
        [$contact] = \CRM_Utils_Token::getTokenDetails(
            [$contact_id],
            $returnProperties,
            false,
            false,
            null,
            $messageToken,
            null
        );
        $this->html = \CRM_Utils_Token::replaceContactTokens(
            $this->html,
            $contact[$contact_id],
            true,
            $messageToken
        );
        $this->html = CRM_Utils_Token::replaceContributionTokens(
            $this->html,
            $this->contribution->getContribution(),
            true,
            $messageToken
        );
        $this->html = \CRM_Utils_Token::replaceHookTokens(
            $this->html,
            $contact[$contact_id],
            $tokenCategories,
            true
        );

        // Render with Smarty, if enabled.
        if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY) {
            $smarty = \CRM_Core_Smarty::singleton();
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
        if (!isset(\Civi::$statics[__CLASS__]['token_categories'])) {
            $tokens = [];
            \CRM_Utils_Hook::tokens($tokens);
            \Civi::$statics[__CLASS__]['token_categories'] = array_keys(
                $tokens
            );
        }
        return \Civi::$statics[__CLASS__]['token_categories'];
    }
}
