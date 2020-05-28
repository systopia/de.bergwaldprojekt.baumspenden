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

/**
 * Form controller class for search result task form for generating Baumspenden
 * certificates for contributions.
 */
class CRM_Baumspenden_Form_Task_GenerateCertificates extends
    CRM_Contribute_Form_Task
{
    public function buildQuickForm()
    {
        $this->addButtons(
            [
                [
                    'type' => 'submit',
                    'name' => E::ts('Generate Certificates'),
                    'isDefault' => true,
                ],
                [
                    'type' => 'cancel',
                    'name' => E::ts('Cancel'),
                    'isDefault' => false,
                ],
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function postProcess()
    {
        $certificates = [];

        $financial_type = civicrm_api3(
            'FinancialType',
            'getsingle',
            ['name' => CRM_Baumspenden_Configuration::FINANCIAL_TYPE_NAME]
        );
        $contributions = civicrm_api3(
            'Contribution',
            'get',
            [
                'id' => ['IN' => $this->_contributionIds],
                'financial_type_id' => $financial_type['id'],
                'return' => ['id'],
                'options' => ['limit' => 0],
            ]
        );
        foreach ($contributions['values'] as $contribution) {
            $certificate = civicrm_api3(
                'BWPBaumspende',
                'generate_certificate',
                [
                    'contribution_id' => $contribution['id'],
                ]
            );
            $certificates[$certificate['values']['id']] = $certificate['values'];
        }

        $zipfile = $this->createZipArchive($certificates);
        CRM_Utils_System::setHttpHeader('Content-Type', 'application/pdf');
        CRM_Utils_System::setHttpHeader(
            'Content-Disposition',
            'attachment; filename="baumspenden.zip"'
        );
        echo readfile($zipfile);
        CRM_Utils_System::civiExit();
    }

    /**
     * Creates a ZIP archive file containing all given certificate files.
     *
     * @param $certificates
     *   An array of File.getsingle API results
     *
     * @return string
     *   The ZIP archive file path.
     * @throws \Exception
     *   When generating the ZIP archive file failed.
     */
    public function createZipArchive($certificates)
    {
        $archiveFileName = tempnam(
                sys_get_temp_dir(),
                'baumspenden' . '-'
            ) . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($archiveFileName, ZIPARCHIVE::CREATE) === true) {
            foreach ($certificates as $certificate) {
                $zip->addFile(
                    $certificate['path'],
                    $certificate['name']
                );
            }
            if (!$zip->close()) {
                throw new Exception('Could not save archive file.');
            }
        } else {
            throw new Exception('Could not create archive file.');
        }

        return $archiveFileName;
    }
}
