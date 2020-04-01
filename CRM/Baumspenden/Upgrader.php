<?php

use CRM_Baumspenden_ExtensionUtil as E;

/**
 * Class CRM_Baumspenden_Upgrader
 */
class CRM_Baumspenden_Upgrader extends CRM_Baumspenden_Upgrader_Base
{
    public function install()
    {
        // Update custom data structures.
        $customData = new CRM_Baumspenden_CustomData(E::LONG_NAME);
        $customData->syncOptionGroup(
            __DIR__ . '/resources/option_group_plant_period.json'
        );
        $customData->syncOptionGroup(
            __DIR__ . '/resources/option_group_plant_region.json'
        );
        $customData->syncOptionGroup(
            __DIR__ . '/resources/option_group_plant_tree.json'
        );
        $customData->syncEntities(
            __DIR__ . '/resources/financial_type_baumspende.json'
        );
        $customData->syncCustomGroup(
            __DIR__ . '/resources/custom_group_baumspende.json'
        );
        $customData->syncOptionGroup(
            __DIR__ . '/resources/option_group_activity_type.json'
        );
    }
}
