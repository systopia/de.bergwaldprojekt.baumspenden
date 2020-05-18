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
class CRM_Baumspenden_Form_Task_Generate extends CRM_Contact_Form_Task
{
    public function buildQuickForm()
    {
    }


    /**
     * get the last iteration's values
     */
    public function setDefaultValues()
    {
    }


    /**
     * PostProcess:
     *  - store submitted settings as new defaults
     *  - generate CSV
     *
     * @throws CiviCRM_API3_Exception
     */
    public function postProcess()
    {
        parent::postProcess();
    }
}
