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

require_once 'baumspenden.civix.php';
use CRM_Baumspenden_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function baumspenden_civicrm_config(&$config) {
  _baumspenden_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function baumspenden_civicrm_xmlMenu(&$files) {
  _baumspenden_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function baumspenden_civicrm_install() {
  _baumspenden_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function baumspenden_civicrm_postInstall() {
  _baumspenden_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function baumspenden_civicrm_uninstall() {
  _baumspenden_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function baumspenden_civicrm_enable() {
  _baumspenden_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function baumspenden_civicrm_disable() {
  _baumspenden_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function baumspenden_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _baumspenden_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function baumspenden_civicrm_managed(&$entities) {
  _baumspenden_civix_civicrm_managed($entities);
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
function baumspenden_civicrm_caseTypes(&$caseTypes) {
  _baumspenden_civix_civicrm_caseTypes($caseTypes);
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
function baumspenden_civicrm_angularModules(&$angularModules) {
  _baumspenden_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function baumspenden_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _baumspenden_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function baumspenden_civicrm_entityTypes(&$entityTypes) {
  _baumspenden_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_permission().
 */
function baumspenden_civicrm_permission(&$permissions) {
  $permissions['access BWP API BWPBaumspende.submit'] = 'BWP API: Access BWPBaumspende.Submit API';
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 */
function baumspenden_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['b_w_p_baumspende']['submit'] = ['access BWP API BWPBaumspende.submit'];
}

/**
 * Implements hook_civicrm_searchTasks().
 *
 * @param $objectType
 *   the object for this search - activity, campaign, case, contact,
 *   contribution, event, grant, membership, and pledge are supported.
 * @param $tasks
 *   the current set of tasks for that custom field. You can add/remove existing
 *   tasks. Each task is an array with a title
 *   (eg 'title' => ts( 'Add Contacts to Group')) and a class
 *   (eg 'class' => 'CRM_Contact_Form_Task_AddToGroup').
 *   Optional result (boolean) may also be provided. Class can be an array of
 *   classes (not sure what that does :( ). The key for new Task(s) should not
 *   conflict with the keys for core tasks of that $objectType, which can be
 *   found in CRM/$objectType/Task.php.
 */
function baumspenden_civicrm_searchTasks($objectType, &$tasks) {
  if ($objectType == 'contribution') {
    $tasks['bwp_baumspenden_generate_certificates'] = array(
      'title'  => E::ts('Generate Baumspenden Certificates'),
      'class'  => 'CRM_Baumspenden_Form_Task_GenerateCertificates',
      'result' => FALSE
    );
  }
}
