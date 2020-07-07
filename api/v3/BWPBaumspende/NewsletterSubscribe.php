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
 * BWPBaumspende.newsletter_subscribe API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_b_w_p_baumspende_newsletter_subscribe_spec(&$spec)
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
}

/**
 * BWPBaumspende.newsletter_subscribe API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @throws API_Exception
 * @see civicrm_api3_create_success
 *
 */
function civicrm_api3_b_w_p_baumspende_newsletter_subscribe($params)
{
    $contact_id = CRM_Baumspenden_Donation::retrieveContact($params);
    $contact = civicrm_api3(
        'Contact',
        'getsingle',
        ['id' => $contact_id]
    );
    civicrm_api3(
        'MailingEventSubscribe',
        'create',
        [
            'contact_id' => $contact_id,
            'email' => $contact['email'],
            'group_id' => CRM_Baumspenden_Configuration::GROUP_ID_NEWSLETTER,
        ]
    );
}
