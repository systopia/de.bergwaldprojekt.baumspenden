<?php

require_once 'bwpapi.civix.php';
use CRM_Bwpapi_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function bwpapi_civicrm_config(&$config) {
  _bwpapi_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function bwpapi_civicrm_install() {
  _bwpapi_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function bwpapi_civicrm_enable() {
  _bwpapi_civix_civicrm_enable();

  require_once 'CRM/Bwpapi/CustomData.php';
  $customData = new CRM_Bwpapi_CustomData(E::LONG_NAME);
  $customData->syncOptionGroup(__DIR__ . '/resources/option_group_plant_period.json');
  $customData->syncOptionGroup(__DIR__ . '/resources/option_group_plant_region.json');
  $customData->syncOptionGroup(__DIR__ . '/resources/option_group_plant_tree.json');
  $customData->syncEntities(__DIR__ . '/resources/financial_type_baumspende.json');
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_baumspende.json');
  $customData->syncOptionGroup(__DIR__ . '/resources/option_group_activity_type.json');
}

/**
 * Implements hook_civicrm_permission().
 */
function bwpapi_civicrm_permission(&$permissions) {
  $permissions['access BWP API BWPBaumspende.Submit'] = 'BWP API: Access BWPBaumspende.Submit API';
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 */
function bwpapi_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['b_w_p_baumspende']['submit'] = ['access BWP API BWPBaumspende.Submit'];
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function bwpapi_civicrm_navigationMenu(&$menu) {
  _bwpapi_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _bwpapi_civix_navigationMenu($menu);
} // */
