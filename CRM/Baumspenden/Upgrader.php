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
 * Class CRM_Baumspenden_Upgrader
 */
class CRM_Baumspenden_Upgrader extends CRM_Baumspenden_Upgrader_Base
{
    public function install()
    {
        // TODO: Create XCM profile.

        // TODO: Create financial type.

        // Update custom data structures.
        $customData = new CRM_Baumspenden_CustomData(E::LONG_NAME);
        $customData->syncOptionGroup(
            E::path('/resources/option_group_plant_period.json')
        );
        $customData->syncOptionGroup(
            E::path('/resources/option_group_plant_region.json')
        );
        $customData->syncOptionGroup(
            E::path('/resources/option_group_plant_tree.json')
        );
        $customData->syncEntities(
            E::path('/resources/financial_type_baumspende.json')
        );
        $customData->syncCustomGroup(
            E::path('/resources/custom_group_baumspende.json')
        );
        $customData->syncOptionGroup(
            E::path('/resources/option_group_activity_type.json')
        );
    }
}
