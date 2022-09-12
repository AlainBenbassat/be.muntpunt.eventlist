<?php

require_once 'eventlist.civix.php';
// phpcs:disable
use CRM_Eventlist_ExtensionUtil as E;
// phpcs:enable

function eventlist_civicrm_postProcess($formName, &$form) {
  if (eventlist_userHasSavedEvent($formName) || eventlist_userHasDeletedEvent($formName)) {
    eventlist_redirectUserToEventList();
  }
}

function eventlist_userHasSavedEvent($formName) {
  if ($formName == 'CRM_Event_Form_ManageEvent_EventInfo') {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

function eventlist_userHasDeletedEvent($formName) {
  if ($formName == 'CRM_Event_Form_ManageEvent_Delete') {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

function eventlist_redirectUserToEventList() {
  $session = CRM_Core_Session::singleton();
  $url = CRM_Utils_System::url('civicrm/eventlist');
  $session->replaceUserContext($url);
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function eventlist_civicrm_config(&$config) {
  _eventlist_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function eventlist_civicrm_xmlMenu(&$files) {
  _eventlist_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function eventlist_civicrm_install() {
  _eventlist_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function eventlist_civicrm_postInstall() {
  _eventlist_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function eventlist_civicrm_uninstall() {
  _eventlist_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function eventlist_civicrm_enable() {
  _eventlist_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function eventlist_civicrm_disable() {
  _eventlist_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function eventlist_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventlist_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function eventlist_civicrm_managed(&$entities) {
  _eventlist_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function eventlist_civicrm_caseTypes(&$caseTypes) {
  _eventlist_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function eventlist_civicrm_angularModules(&$angularModules) {
  _eventlist_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function eventlist_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventlist_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function eventlist_civicrm_entityTypes(&$entityTypes) {
  _eventlist_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function eventlist_civicrm_themes(&$themes) {
  _eventlist_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function eventlist_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function eventlist_civicrm_navigationMenu(&$menu) {
  _eventlist_civix_insert_navigation_menu($menu, 'Events', [
    'label' => 'Lijstweergave',
    'name' => 'event_lijstweergave',
    'url' => 'civicrm/eventlist',
    'permission' => 'access CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
    'weight' => 1,
  ]);
  _eventlist_civix_navigationMenu($menu);
}
