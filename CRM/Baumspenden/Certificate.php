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
                'id' => $this->contribution['contact_id'],
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
        $msg_tpl_id = Civi::settings()->get('baumspenden_msg_tpl_id');

        // Load message template.
        $msg_tpl = civicrm_api3(
            'MessageTemplate',
            'getsingle',
            ['id' => $msg_tpl_id]
        );
        $this->html = $msg_tpl['msg_html'];

        // Prepare message template.
        \CRM_Contact_Form_Task_PDFLetterCommon::formatMessage($this->html);

        // Replace tokens.
        $this->replaceTokens();
    }

    /**
     * Converts the rendered message template to PDF and downloads it.
     */
    public function convertAndDownload() {
        \CRM_Utils_PDF_Utils::html2pdf(
            [$this->html],
            'baumspenden_certificate_' . $this->contribution['id'] . '.pdf'
        );
    }

    /**
     * Replaces contact and contribution tokens in the HTML contents.
     */
    protected function replaceTokens()
    {
        // Extract tokens from the HTML.
        $contact_id = $this->contribution['contact_id'];
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
        $this->html = \CRM_Utils_Token::replaceHookTokens(
            $this->html,
            $contact[$contact_id],
            $tokenCategories,
            true
        );
        $this->html = CRM_Utils_Token::replaceContributionTokens(
            $this->html,
            $this->contribution,
            true,
            $messageToken
        );

        // Render with Smarty, if enabled.
        if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY) {
            $smarty = \CRM_Core_Smarty::singleton();
            // also add the contact tokens to the template
            $smarty->assign_by_ref('contact', $contact);
            $this->html = $smarty->fetch("string:$this->html");
        }
    }
}
