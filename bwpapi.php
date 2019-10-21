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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function bwpapi_civicrm_xmlMenu(&$files) {
  _bwpapi_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function bwpapi_civicrm_postInstall() {
  _bwpapi_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function bwpapi_civicrm_uninstall() {
  _bwpapi_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function bwpapi_civicrm_disable() {
  _bwpapi_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function bwpapi_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _bwpapi_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function bwpapi_civicrm_managed(&$entities) {
  _bwpapi_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function bwpapi_civicrm_caseTypes(&$caseTypes) {
  _bwpapi_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function bwpapi_civicrm_angularModules(&$angularModules) {
  _bwpapi_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function bwpapi_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _bwpapi_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function bwpapi_civicrm_entityTypes(&$entityTypes) {
  _bwpapi_civix_civicrm_entityTypes($entityTypes);
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
function bwpapi_civicrm_preProcess($formName, &$form) {

} // */

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
