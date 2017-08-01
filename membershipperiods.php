<?php

require_once 'membershipperiods.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipperiods_civicrm_config(&$config) {
  _membershipperiods_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipperiods_civicrm_xmlMenu(&$files) {
  _membershipperiods_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipperiods_civicrm_install() {
  _membershipperiods_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function membershipperiods_civicrm_postInstall() {
  _membershipperiods_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipperiods_civicrm_uninstall() {
  _membershipperiods_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipperiods_civicrm_enable() {
  _membershipperiods_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipperiods_civicrm_disable() {
  _membershipperiods_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipperiods_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipperiods_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipperiods_civicrm_managed(&$entities) {
  _membershipperiods_civix_civicrm_managed($entities);
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
function membershipperiods_civicrm_caseTypes(&$caseTypes) {
  _membershipperiods_civix_civicrm_caseTypes($caseTypes);
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
function membershipperiods_civicrm_angularModules(&$angularModules) {
  _membershipperiods_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipperiods_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipperiods_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_tabset().
 *
 *adds a contact tab "Membership Periods" and fetches the content from database.
 */
function membershipperiods_civicrm_tabset($tabsetName, &$tabs, $context) {
	if ($tabsetName == 'civicrm/contact/view') {
	//for contact view page	
		$contactID = $context['contact_id'];
		$url = CRM_Utils_System::url('civicrm/membership-page?cid='.$contactID);
		$period_count = "SELECT p.start_date, p.end_date, p.renewed_date, p.contribution_id 
				  FROM civicrm_membership_period p
				  INNER JOIN civicrm_membership m ON m.id = p.membership_id
				  WHERE m.contact_id = $contactID
				 ";
		$results = CRM_Core_DAO::executeQuery($period_count);	  
		$pcount = 0;
		while ($results->fetch()) {
			$pcount++;
		}		
		$tabs[] = array( 'id'    => 'membershipPeriodsTab',
		'url'   => $url,
		'title' => 'Membership Periods',
		'weight' => 300,
		'count' => $pcount,
		);

	}
}

/**
 * Implements hook_civicrm_post().
 */
function membershipperiods_civicrm_post($op, $objectName, $objectId, &$objectRef) {
	
	$f= dirname(__FILE__).'/hello.txt';
	$t =  json_encode(func_get_args());
	file_put_contents($f, $t, FILE_APPEND);
	
	if ($objectName == "Membership" && ($op == "edit" || $op == "create")) {
		$member_type_sql	="SELECT * FROM civicrm_membership_type WHERE id = $objectRef->membership_type_id";
		$result = CRM_Core_DAO::executeQuery($member_type_sql);
		$result->fetch();
		$term  = '+'.$result->duration_interval.' '.$result->duration_unit;
	// inserts a record into database when membership is created/renewed.
		$check = "SELECT * FROM civicrm_membership_period WHERE end_date=$objectRef->end_date AND membership_id = $objectRef->id";
		$result = CRM_Core_DAO::executeQuery($check);
		if (!($result->fetch())) {
			$queryc = "SELECT MAX(DATE_ADD(end_date, INTERVAL 1 DAY)) AS new_date FROM civicrm_membership_period WHERE membership_id = $objectRef->id";
			$resultc = CRM_Core_DAO::executeQuery($queryc);
			if($resultc->fetch()) {
				if(!empty($resultc->new_date))
					$query = "INSERT INTO civicrm_membership_period (start_date, end_date,membership_id, contribution_id, renewed_date) VALUES ('$resultc->new_date', '$objectRef->end_date', $objectRef->id, 0, NOW())";
				else
					$query = "INSERT INTO civicrm_membership_period (start_date, end_date, term_id, membership_id, contribution_id, renewed_date) VALUES ('$objectRef->start_date', '$objectRef->end_date', '$term', $objectRef->id, 0, NOW())";	

					CRM_Core_DAO::executeQuery($query);
			}
		}
	}
	// updates membership period when membership payment is made.
	else if ($objectName == "MembershipPayment" && $op == "create") {
		$check = "SELECT * FROM civicrm_membership_period WHERE contribution_id = $objectRef->contribution_id";
		$result = CRM_Core_DAO::executeQuery($check);
		if (!($result->fetch())) {
			$query = "UPDATE civicrm_membership_period SET contribution_id = $objectRef->contribution_id WHERE membership_id = $objectRef->membership_id AND contribution_id=0 ORDER BY id DESC LIMIT 1";
			CRM_Core_DAO::executeQuery($query);
		}
	}
}