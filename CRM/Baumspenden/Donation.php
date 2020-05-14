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

class CRM_Baumspenden_Donation
{

    /**
     * @var array $contribution
     *   The CiviCRM contribution.
     */
    protected $contribution;

    /**
     * CRM_Baumspenden_Donation constructor.
     *
     * @param int $contribution_id
     *
     * @throws \CiviCRM_API3_Exception
     */
    public function __construct($contribution_id = NULL)
    {
        if (!empty($contribution_id)) {
            $financial_type = civicrm_api3(
                'FinancialType',
                'getsingle',
                ['name' => CRM_Baumspenden_Submission::FINANCIAL_TYPE_NAME]
            );
            $contribution = civicrm_api3(
                'Contribution',
                'getsingle',
                [
                    'id' => $contribution_id,
                    'financial_type_id' => $financial_type['id'],
                ]
            );
            $this->contribution = $contribution;
        }
    }

    /**
     * Returns the value of the contribution's property with the given name.
     * This may be any property natively available on contributions or custom
     * field names in the custom group "baumspende", which will be resolved to
     * the "custom_X" notation,
     *
     * @param string $property
     *   The property name.
     *
     * @return mixed
     *   The property value.
     *
     * @throws \Exception
     *   When the property could not be retrieved from the contribution.
     */
    public function get($property)
    {
        if (array_key_exists($property, $this->contribution)) {
            $value = $this->contribution[$property];
        }
        elseif (array_key_exists(
            $custom_field_key = CRM_Baumspenden_CustomData::getCustomFieldKey('baumspende', 'baumspende.baumspende_' . $property),
            $this->contribution
        )) {
            $value = $this->contribution[$custom_field_key];
        }
        else {
            throw new Exception('Property ' . $property . ' not found.');
        }

        return $value;
    }

    public static function create($params)
    {
        $donation = new self();
        // TODO: Move code from the BWPBaumspenden.Submit API here, set
        //   $donation->contribution and return $donation.
    }

}
