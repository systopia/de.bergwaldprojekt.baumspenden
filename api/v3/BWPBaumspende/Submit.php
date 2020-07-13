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
 * BWPBaumspende.submit API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_b_w_p_baumspende_submit_spec(&$spec)
{
    $spec['first_name'] = [
        'name' => 'first_name',
        'title' => 'First name',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The first name of the donation initiator.',
    ];
    $spec['last_name'] = [
        'name' => 'last_name',
        'title' => 'Last name',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The Last name of the donation initiator.',
    ];
    $spec['street_address'] = [
        'name' => 'street_address',
        'title' => 'Street address',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The street address of the donation initiator\'s address.',
    ];
    $spec['supplemental_address_1'] = [
        'name' => 'supplemental_address_1',
        'title' => 'Supplemental address 1',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The supplemental address 1 of the donation initiator\'s address.',
    ];
    $spec['postal_code'] = [
        'name' => 'postal_code',
        'title' => 'Postal code',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The postal code of the donation initiator\'s address.',
    ];
    $spec['city'] = [
        'name' => 'city',
        'title' => 'City',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The city of the donation initiator\'s address.',
    ];
    $spec['email'] = [
        'name' => 'email',
        'title' => 'E-mail address',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The e-mail address of the donation initiator.',
    ];
    $spec['source'] = [
        'name' => 'source',
        'title' => 'Contact source',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The contact source of the donation initiator.',
    ];
    $spec['payment_method'] = [
        'name' => 'payment_method',
        'title' => 'Payment method',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The payment method used to make the donation. One of "sepa_direct_debit", "payment_request", "paypal" and "credit_card".',
    ];
    $spec['iban'] = [
        'name' => 'iban',
        'title' => 'IBAN',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The IBAN of the donation initiator\'s bank account.',
    ];
    $spec['bic'] = [
        'name' => 'bic',
        'title' => 'BIC',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The SWIFT-Code (BIC) of the donation initiator\'s bank account.',
    ];
    $spec['unit_price'] = [
        'name' => 'unit_price',
        'title' => 'Unit price',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
        'description' => 'The unit price of the donation (in minor currency unit).',
    ];
    $spec['amount'] = [
        'name' => 'amount',
        'title' => 'Amount',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
        'description' => 'The amount of donations being made.',
    ];
    $spec['plant_region'] = [
        'name' => 'plant_region',
        'title' => 'Planting region',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The region which the trees are to be planted in.',
    ];
    $spec['plant_period'] = [
        'name' => 'plant_period',
        'title' => 'Planting period',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The period of time which the trees are to be planted in.',
    ];
    $spec['plant_tree'] = [
        'name' => 'plant_tree',
        'title' => 'Tree species',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The species of which the trees are to be planted.',
    ];
    $spec['plant_region_label'] = [
        'name' => 'plant_region_label',
        'title' => 'Planting region label',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The label of the region which the trees are to be planted in.',
    ];
    $spec['plant_period_label'] = [
        'name' => 'plant_period_label',
        'title' => 'Planting period label',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The label of the period of time which the trees are to be planted in.',
    ];
    $spec['plant_tree_label'] = [
        'name' => 'plant_tree_label',
        'title' => 'Tree species label',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The label of the species of which the trees are to be planted.',
    ];
    $spec['shipping_mode'] = [
        'name' => 'shipping_mode',
        'title' => 'Shipping mode',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
        'description' => 'The mode of shipping of the donation certificate ("email" or "postal").',
    ];
    $spec['as_present'] = [
        'name' => 'as_present',
        'title' => 'As present',
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'api.required' => 1,
        'api.default' => 0,
        'description' => 'Whether the donation is being purchased for a presentee.',
    ];
    $spec['presentee_first_name'] = [
        'name' => 'presentee_first_name',
        'title' => 'Presentee First name',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The first name of the presentee.',
    ];
    $spec['presentee_last_name'] = [
        'name' => 'presentee_last_name',
        'title' => 'Presentee Last name',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The Last name of the presentee.',
    ];
    $spec['presentee_street_address'] = [
        'name' => 'presentee_street_address',
        'title' => 'Presentee Street address',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The street address of the presentee\'s address.',
    ];
    $spec['presentee_supplemental_address_1'] = [
        'name' => 'presentee_supplemental_address_1',
        'title' => 'PresenteeSupplemental address 1',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The supplemental address 1 of the donation presentee\'s address.',
    ];
    $spec['presentee_postal_code'] = [
        'name' => 'presentee_postal_code',
        'title' => 'Presentee Postal code',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The postal code of the presentee\'s address.',
    ];
    $spec['presentee_city'] = [
        'name' => 'presentee_city',
        'title' => 'Presentee City',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The city of the presentee\'s address.',
    ];
    $spec['presentee_email'] = [
        'name' => 'presentee_email',
        'title' => 'Presentee E-mail address',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The e-mail address of the presentee.',
    ];
    $spec['presentee_message'] = [
        'name' => 'presentee_message',
        'title' => 'Presentee message',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 0,
        'description' => 'The message being sent to the presentee.',
    ];
    $spec['newsletter'] = [
        'name' => 'newsletter',
        'title' => 'Subscribe to noewsletter',
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'api.required' => 0,
        'api.default' => 0,
        'description' => 'Whether the initiator wants to subscribe to the newsletter.',
    ];
}

/**
 * BWPBaumspende.submit API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 */
function civicrm_api3_b_w_p_baumspende_submit($params)
{
    try {
        $donation = CRM_Baumspenden_Donation::create($params);

        if ($params['shipping_mode'] != 'none') {
            $certificate = new CRM_Baumspenden_Certificate(
                $donation->get('id'),
                $params['shipping_mode']
            );
            $certificate->render();
            $certificate->convertToPDF();
            $certificate->send();
        }

        return civicrm_api3_create_success(
            ['contribution_id' => $donation->get('id')]
        );
    } catch (Exception $exception) {
        return civicrm_api3_create_error($exception->getMessage());
    }
}
