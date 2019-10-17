<?php
use CRM_Bwpapi_ExtensionUtil as E;

/**
 * BWPBaumspende.Submit API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_b_w_p_baumspende_Submit_spec(&$spec) {
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
  $spec['iban'] = array(
    'name' => 'iban',
    'title' => 'IBAN',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The IBAN of the donation initiator\'s bank account.',
  );
  $spec['bic'] = array(
    'name' => 'bic',
    'title' => 'BIC',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The SWIFT-Code (BIC) of the donation initiator\'s bank account.',
  );
  $spec['unit_price'] = array(
    'name' => 'unit_preice',
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
    'description' => 'Whether the donation is to be shipped to the presentee.',
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
}

/**
 * BWPBaumspende.Submit API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_b_w_p_baumspende_Submit($params) {

}
