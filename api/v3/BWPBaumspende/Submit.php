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
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_b_w_p_baumspende_submit_spec(&$spec) {
  $spec['first_name'] = array(
    'name' => 'first_name',
    'title' => 'First name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The first name of the donation initiator.',
  );
  $spec['last_name'] = array(
    'name' => 'last_name',
    'title' => 'Last name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The Last name of the donation initiator.',
  );
  $spec['street_address'] = array(
    'name' => 'street_address',
    'title' => 'Street address',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The street address of the donation initiator\'s address.',
  );
  $spec['supplemental_address_1'] = array(
    'name' => 'supplemental_address_1',
    'title' => 'Supplemental address 1',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The supplemental address 1 of the donation initiator\'s address.',
  );
  $spec['postal_code'] = array(
    'name' => 'postal_code',
    'title' => 'Postal code',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The postal code of the donation initiator\'s address.',
  );
  $spec['city'] = array(
    'name' => 'city',
    'title' => 'City',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The city of the donation initiator\'s address.',
  );
  $spec['email'] = array(
    'name' => 'email',
    'title' => 'E-mail address',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The e-mail address of the donation initiator.',
  );
  $spec['source'] = array(
    'name' => 'source',
    'title' => 'Contact source',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The contact source of the donation initiator.',
  );
  $spec['payment_method'] = array(
    'name' => 'payment_method',
    'title' => 'Payment method',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The payment method used to make the donation. One of "sepa_direct_debit", "payment_request", "paypal" and "credit_card".',
  );
  $spec['iban'] = array(
    'name' => 'iban',
    'title' => 'IBAN',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The IBAN of the donation initiator\'s bank account.',
  );
  $spec['bic'] = array(
    'name' => 'bic',
    'title' => 'BIC',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The SWIFT-Code (BIC) of the donation initiator\'s bank account.',
  );
  $spec['unit_price'] = array(
    'name' => 'unit_price',
    'title' => 'Unit price',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
    'description' => 'The unit price of the donation (in minor currency unit).',
  );
  $spec['amount'] = array(
    'name' => 'amount',
    'title' => 'Amount',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
    'description' => 'The amount of donations being made.',
  );
  $spec['plant_region'] = array(
    'name' => 'plant_region',
    'title' => 'Planting region',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The region which the trees are to be planted in.',
  );
  $spec['plant_period'] = array(
    'name' => 'plant_period',
    'title' => 'Planting period',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The period of time which the trees are to be planted in.',
  );
  $spec['plant_tree'] = array(
    'name' => 'plant_tree',
    'title' => 'Tree species',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The species of which the trees are to be planted.',
  );
  $spec['shipping_mode'] = array(
    'name' => 'shipping_mode',
    'title' => 'Shipping mode',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The mode of shipping of the donation certificate ("email" or "postal").',
  );
  $spec['as_present'] = array(
    'name' => 'as_present',
    'title' => 'As present',
    'type' => CRM_Utils_Type::T_BOOLEAN,
    'api.required' => 1,
    'api.default' => 0,
    'description' => 'Whether the donation is being purchased for a presentee.',
  );
  $spec['presentee_shipping'] = array(
    'name' => 'presentee_shipping',
    'title' => 'Ship to presentee',
    'type' => CRM_Utils_Type::T_BOOLEAN,
    'api.required' => 0,
    'api.default' => 0,
    'description' => 'Whether the donation certificate is to be shipped to the presentee.',
  );
  $spec['presentee_first_name'] = array(
    'name' => 'presentee_first_name',
    'title' => 'Presentee First name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The first name of the presentee.',
  );
  $spec['presentee_last_name'] = array(
    'name' => 'presentee_last_name',
    'title' => 'Presentee Last name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The Last name of the presentee.',
  );
  $spec['presentee_street_address'] = array(
    'name' => 'presentee_street_address',
    'title' => 'Presentee Street address',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The street address of the presentee\'s address.',
  );
  $spec['presentee_supplemental_address_1'] = array(
    'name' => 'presentee_supplemental_address_1',
    'title' => 'PresenteeSupplemental address 1',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The supplemental address 1 of the donation presentee\'s address.',
  );
  $spec['presentee_postal_code'] = array(
    'name' => 'presentee_postal_code',
    'title' => 'Presentee Postal code',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The postal code of the presentee\'s address.',
  );
  $spec['presentee_city'] = array(
    'name' => 'presentee_city',
    'title' => 'Presentee City',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The city of the presentee\'s address.',
  );
  $spec['presentee_email'] = array(
    'name' => 'presentee_email',
    'title' => 'Presentee E-mail address',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The e-mail address of the presentee.',
  );
  $spec['presentee_message'] = array(
    'name' => 'presentee_message',
    'title' => 'Presentee message',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The message being sent to the presentee.',
  );
  $spec['newsletter'] = array(
    'name' => 'newsletter',
    'title' => 'Subscribe to noewsletter',
    'type' => CRM_Utils_Type::T_BOOLEAN,
    'api.required' => 0,
    'api.default' => 0,
    'description' => 'Whether the initiator wants to subscribe to the newsletter.',
  );
}

/**
 * BWPBaumspende.submit API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_b_w_p_baumspende_submit($params) {
  try {
    $result = array();

    /**
     * Prepare parameters.
     */
    if (!empty($params['as_present']) && is_array($params['as_present'])) {
      $params['as_present'] = reset($params['as_present']);
    }
    if (!empty($params['newsletter']) && is_array($params['newsletter'])) {
      $params['newsletter'] = reset($params['newsletter']);
    }

    /**
     * Identify or create initiator contact.
     */
    $initiator_data = array_intersect_key($params, array_fill_keys(array(
      'first_name',
      'last_name',
      'street_address',
      'supplemental_address_1',
      'postal_code',
      'city',
      'email',
      'source',
    ), TRUE));
    $xcm_result = civicrm_api3(
      'Contact',
      'getorcreate',
      $initiator_data + array(
        'xcm_profile' => CRM_Baumspenden_Configuration::XCM_PROFILE,
      ));
    if ($xcm_result['is_error']) {
      throw new Exception($xcm_result['error_message']);
    }
    $initiator_contact_id = $xcm_result['id'];
    $result['initiator_contact_id'] = $initiator_contact_id;

    /**
     * Create SEPA mandate (with contribution).
     */
    $financial_type = civicrm_api3('FinancialType', 'getsingle', array(
      'name' => CRM_Baumspenden_Configuration::FINANCIAL_TYPE_NAME,
    ));
    $contribution_data = array(
      'contact_id' => $initiator_contact_id,
      'amount' => $params['unit_price'] * $params['amount'],
      'financial_type_id' => $financial_type['id'],
      'source' => CRM_Baumspenden_Configuration::CONTRIBUTION_SOURCE,
    );
    // Include custom field data.
    if (empty($params['certificate_name'])) {
      $params['certificate_name'] = "{$params['first_name']} {$params['last_name']}";
    }
    foreach (array(
               'unit_price' => FALSE,
               'amount' => FALSE,
               'plant_region' => TRUE,
               'plant_period' => TRUE,
               'plant_tree' => TRUE,
               'certificate_name' => FALSE,
             ) as $custom_field_name => $is_option_group) {
      $custom_field = CRM_Baumspenden_CustomData::getCustomField(
        'baumspende',
        'baumspende_' . $custom_field_name
      );

      // Resolve or add option values for the custom fields.
      if ($is_option_group) {
        try {
          $option_value = civicrm_api3('OptionValue', 'getsingle', array(
            'option_group_id' => 'baumspenden_' . $custom_field_name,
            'name' => $params[$custom_field_name],
          ));
        }
        catch (Exception $exception) {
          $option_value = civicrm_api3('OptionValue', 'create', array(
            'option_group_id' => 'baumspenden_' . $custom_field_name,
            'name' => $params[$custom_field_name],
          ));
          $option_value = reset($option_value['values']);
        }
        $value = $option_value['value'];
      }
      else {
        $value = (isset($params[$custom_field_name]) ? $params[$custom_field_name] : NULL);
      }

      $contribution_data['custom_' . $custom_field['id']] = $value;
    }

    // TODO: Accept different payment instruments, SEPA being one of them.
    switch ($params['payment_method']) {
      case 'sepa_direct_debit':
        $mandate_data = $contribution_data + array(
            'type' => 'OOFF',
            'iban' => $params['iban'],
            'bic' => $params['bic'],
          );
        $mandate = civicrm_api3('SepaMandate', 'createfull', $mandate_data);
        if ($mandate['is_error']) {
          throw new Exception($mandate['error_message']);
        }
        $result['mandate_id'] = $mandate['id'];
        $result['contribution_id'] = $mandate['values'][$mandate['id']]['entity_id'];
        break;
      case 'payment_request':
        $contribution_data['payment_instrument_id'] = CRM_Baumspenden_Configuration::PAYMENT_INSTRUMENT_ID_PAYMENT_REQUEST;
        // Donors have to issue the payment themselves, therefore it is pending.
        $contribution_data['contribution_status_id'] = 'Pending';
        break;
      case 'paypal':
        $contribution_data['payment_instrument_id'] = CRM_Baumspenden_Configuration::PAYMENT_INSTRUMENT_ID_PAYPAL;
        // The payment has been initialized by the payment processor, therefore
        // it is in progress already.
        $contribution_data['contribution_status_id'] = 'In Progress';
        break;
      case 'credit_card':
        $contribution_data['payment_instrument_id'] = CRM_Baumspenden_Configuration::PAYMENT_INSTRUMENT_ID_CREDIT_CARD;
        // The payment has been initialized by the payment processor, therefore
        // it is in progress already.
        $contribution_data['contribution_status_id'] = 'In Progress';
        break;
      default:
        throw new Exception(E::ts('Unsupported payment method'));
    }
    if (!isset($result['contribution_id'])) {
      $contribution = civicrm_api3('contribution', 'create', $contribution_data);
      if ($contribution['is_error']) {
        throw new Exception($contribution['error_message']);
      }
      $result['contribution_id'] = $contribution['id'];
    }

    /**
     * Create activity of type "Donation".
     */
    $contribution_params = array(
      'id' => $result['contribution_id'],
    );
    // Fetch the contribution BAO.
    $contribution = CRM_Contribute_BAO_Contribution::retrieve($contribution_params, $defaults = array(), $ids = array());
    // Fake the contribution status to force create an activity for this pending
    // contribution.
    $contribution->contribution_status_id = CRM_Core_PseudoConstant::getKey(
      'CRM_Contribute_BAO_Contribution',
      'contribution_status_id',
      'Completed'
    );
    // Add the activity using core's logic.
    CRM_Activity_BAO_Activity::addActivity($contribution, 'Contribution');

    /**
     * Create activity of type "Schenkung Baumspende", if requested.
     */
    if (!empty($params['as_present'])) {
//      // Identify or create presentee contact.
//      $presentee_data = array(
//        'first_name' => $params['presentee_first_name'],
//        'last_name' => $params['presentee_last_name'],
//        'street_address' => $params['presentee_street_address'],
//        'supplemental_address_1' => (!empty($params['presentee_supplemental_address_1']) ? $params['presentee_supplemental_address_1'] : NULL),
//        'postal_code' => $params['presentee_postal_code'],
//        'city' => $params['presentee_city'],
//        'email' => $params['presentee_email'],
//      );
//      $xcm_result = civicrm_api3(
//        'Contact',
//        'getorcreate',
//        $presentee_data + array(
//          'xcm_profile' => CRM_Baumspenden_Submission::XCM_PROFILE,
//        ));
//      if ($xcm_result['is_error']) {
//        throw new Exception($xcm_result['error_message']);
//      }
//      $presentee_contact_id = $xcm_result['id'];
//      $result['presentee_contact_id'] = $presentee_contact_id;

      $present_activity_type_id = CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'schenkung_baumspende');

      // Create the activity.
      $present_activity = civicrm_api3('Activity', 'create', array(
        'source_contact_id' => $initiator_contact_id,
        'activity_type_id' => $present_activity_type_id,
        'subject' => CRM_Baumspenden_Configuration::ACTIVITY_SUBJECT_PRESENT,
//        'target_id' => $presentee_contact_id,
      ));
      $result['present_activity_id'] = $present_activity['id'];
    }

    /**
     * Add newsletter subscription for group_id 19, if requested.
     */
    if (!empty($params['newsletter'])) {
      // Find the initiator contact's e-mail address to use for subscription.
      try {
        $initiator_email = civicrm_api3('Email', 'getsingle', array(
          'contact_id' => $initiator_contact_id,
          'is_bulkmail' => 1,
        ));
      }
      catch (Exception $exception) {
        $initiator_email = civicrm_api3('Email', 'getsingle', array(
          'contact_id' => $initiator_contact_id,
          'is_primary' => 1,
        ));
      }
      $mailing_event_subscribe = civicrm_api3('MailingEventSubscribe', 'create', array(
        'contact_id' => $initiator_contact_id,
        'email' => $initiator_email['email'],
        'group_id' => 19,
      ));
      $result['mailing_event_subscribe_id'] = $mailing_event_subscribe['id'];
    }

    return civicrm_api3_create_success($result);
  }
  catch (Exception $exception) {
    // Rollback current base transaction in order to not rollback the creation
    // of the "failed" activity.
    if (($frame = \Civi\Core\Transaction\Manager::singleton()->getFrame()) !== NULL) {
      $frame->forceRollback();
    }
    // Check whether the initiator contact exists or its creation has been
    // rolled back.
    try {
      civicrm_api3('Contact', 'getsingle', array('id' => $initiator_contact_id));
    }
    catch (CiviCRM_API3_Exception $excpetion) {
      unset($initiator_contact_id);
    }
    
    // Create activity of type "fehlgeschlagene_baumspende".
    $failed_activity_type_id = CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'fehlgeschlagene_baumspende');
    civicrm_api3('Activity', 'create', array(
      'source_contact_id' => CRM_Core_Session::singleton()->getLoggedInContactID(),
      'target_id' => (isset($initiator_contact_id) ? $initiator_contact_id : NULL),
      'activity_type_id' => $failed_activity_type_id,
      'status_id' => 'Scheduled',
      'subject' => CRM_Baumspenden_Configuration::ACTIVITY_SUBJECT_FAILED,
      'details' => '<p>' . $exception->getMessage() . '</p>'
        . '<pre>' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>',
    ));

    return civicrm_api3_create_error($exception->getMessage());
  }
}
