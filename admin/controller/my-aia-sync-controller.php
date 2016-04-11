<?php

/*
 * Copyright (C) 2016 Michiel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

include_once(MY_AIA_PLUGIN_DIR . 'classes/crmsync/class_soap_sugar.php');

/**
 * Description of my-aia-sync-controller
 * Sync controller handles the incoming and outgoing request for the CRM-MY_AIA
 * Synchronisation. 
 * @author Michiel
 */
class MY_AIA_SYNC_CONTROLLER extends MY_AIA_APP_CONTROLLER {
	public $classname = 'sync';
	
	/**
	 * Sugar Soap connection
	 * @var \SoapSugar 
	 */
	private $sugar;
	/**
	 * Manyware Soap connection
	 * @var \SoapManyware 
	 */
	private $manyware;
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * - Add the AJAX save over here
	 */
	public function before_filter(){
		add_action( 'wp_ajax_my_aia_admin_sync_save', array($this, 'sync_save'), 1);	
	}
	
	public function before_render() {
		// setting the menu bar for this controller
		$menu_bar = array(
			'index' => __('Overzicht'),
			'settings' => __('Instellingen','my-aia'),			
		);
		
		$this->set('menu_bar', $menu_bar);
		
		// scripts
		wp_enqueue_script( 'my-aia-admin-sync', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-sync.js', '', MY_AIA_VERSION );
		
		parent::before_render();
	}
	
	public function index() {
		
	}
	
	/**
	 * function use to update the profile fields sync with sugar
	 */
	public function edit_profile() {
		global $current_user;
		// get INTERNAL (wordpress profile & BuddyPress) fields
		$fields = array_keys(get_user_meta($current_user->ID));
		$buddypress_fields=my_aia_get_buddy_press_xprofile_fields(FALSE);
		
		$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
		$internal_fields[] = 'WP::user_email::user_email';
		$internal_fields[] = 'UserMeta::sugar_id::sugar_id';
		foreach ($buddypress_fields as $id=>$field) $internal_fields[] = 'BuddyPress::' . $id . '::' . $field;
		
		
		// get EXTERNAL (sugarcrm) fields
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'includes/definitions/my-aia-sugacrm-contact-fields.txt');
		$sugar_fields = explode("\r\n",$sugar_fields_string);
	
		$this->set('internal_fields', $internal_fields);
		$this->set('external_fields', $sugar_fields);
		$this->set('sync_type', 'profile');
		$this->set('data',	get_option('my_aia_profile_sync', array()));
		
		$this->render('edit_sync_rules');
	}
	
	/**
	 * function use to update the profile fields sync with sugar
	 */
	public function edit_events() {
		global $current_user;
		// get INTERNAL (wordpress profile & Events-Manager) fields
		$fields = array_keys(get_user_meta($current_user->ID));
		//$event_fields = my_aia_get_event_fields();
		
		$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
		//$internal_fields[] = 'WP::user_email::user_email';
		//$internal_fields[] = 'UserMeta::sugar_id::sugar_id';
		//foreach ($buddypress_fields as $id=>$field) $internal_fields[] = 'BuddyPress::' . $id . '::' . $field;
		
		
		// get EXTERNAL (sugarcrm) fields
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'includes/definitions/my-aia-sugacrm-event-fields.txt');
		$sugar_fields = explode("\r\n",$sugar_fields_string);
	
		$this->set('internal_fields', $internal_fields);
		$this->set('external_fields', $sugar_fields);
		$this->set('data',	get_option('my_aia_profile_sync', array()));
		
		
		$this->set('data',	get_option('my_aia_event_sync', array()));
		
		$this->set('sync_type', 'event');
		$this->render('edit_sync_rules');
	}
	
	/**
	 * function use to update the registration fields sync with sugar
	 */
	public function edit_registrations() {
		$this->set('data',	get_option('my_aia_registration_sync', array()));
	}
	
	public function sync() {
		include_once(MY_AIA_PLUGIN_DIR . 'classes/crmsync/class_soap_sugar.php');
		
		// create a sugar Client
		$sugar = new SoapSugar(
			get_option('my_aia_sugar_url','').'/soap.php?wsdl',
			array(
				'user' => get_option('my_aia_sugar_user',''),
				'pass' => get_option('my_aia_sugar_user_password','')
			)
		);
		
		$item = $sugar->searchCommon("UNIX_TIMESTAMP(contacts.date_modified) > ".mktime(0,0,0,12,31,2015));
		//$item = $sugar->findContactByRelatienummer('244677');
		var_export($item);
	}
	
	/**
	 * Run the Settings VIEW
	 */
	public function settings() {
		// safeguard: set the array of fields to be saved
		$fields = array('sugar_user','sugar_user_password','sugar_url',
						'manyware_c_client_login','manyware_c_client_password','manyware_user','manyware_user_password','manyware_url');
		if ($_POST['sugar_user']) {
			my_aia_save_options ($fields, INPUT_POST);
		}
		
		$this->set('data', my_aia_get_options_data($fields));
	}
	
	/**
	 * AJAX save the sync object
	 * $_POST: 'data':array('internal_field','external_field')
	 * 
	 */
	public function sync_save() {
		if (isset($_POST['type'])) {
			$data=array();
			// remove \r\n from dataset
			foreach ($_POST['data'] as $key1=>$val1) {
				foreach ($val1 as $key=>$val) {
					$data[$key1][$key] = str_replace(array("\r","\n"),array("",""),$val);
				}
			}
			switch ($_POST['type']) {
				case 'profile_sync':
					update_option('my_aia_profile_sync', $data);
					break;
				case 'event_sync':
					update_option('my_aia_event_sync', $data);
					break;
				case 'registration_sync':
					update_option('my_aia_registration_sync', $data);
					break;
			}
		}
	}
	
	
	/**
	 * Actual function to sync data
	 */
	public function do_sync() {
		// create a sugar Client
		$this->sugar = new SoapSugar(
			get_option('my_aia_sugar_url','').'/soap.php?wsdl',
			array(
				'user' => get_option('my_aia_sugar_user',''),
				'pass' => get_option('my_aia_sugar_user_password','')
			)
		);
		
		// update Wordpress with Sugar profile Data
		//$this->sync_profiles_sugar_to_wordpress();
		
		// update Wordpress with Sugar Event Data
		$this->sync_events_sugar_to_wordpress();
	}
	
	/**
	 * Synchronise a
	 * @param string $fromdate Sync from this date
	 * @parem boolean $create if TRUE create, if not exists
	 * @return boolean
	 */
	private function sync_profiles_sugar_to_wordpress($fromdate='2012-01-01', $create=TRUE) {
		global $wpdb;
		
		
		// loop over all contacts
		$items_found = TRUE;
		$num_items_per_query = 50;
		$offset = 0;
		while ($items_found && $offset<=100 ) { //TEMP !! FOR DEBUG!!
			// retrieve list of contacts from date (incremental)
			// manyware_aiarelatie = 1 (!!)
			$items = $this->sugar->searchCommon(
					"contacts_cstm.manyware_aiarelatie_c = 1 && contacts.last_name<>'' AND UNIX_TIMESTAMP(contacts.date_modified) > ".  strtotime($fromdate),
					"Contacts",
					$num_items_per_query,
					$offset
				);

			foreach ($items as $contact) {
				if (empty($contact['email1']) || empty($contact['first_name']) || empty($contact['last_name'])) continue;

				// try and find user by sugar_id
				$user = get_user_by_meta_data('sugar_id', $contact['id']);

				if (!$user) {
					// try and find user by email
					$user = get_user_by('email', $contact['email1']);
					if (!$user) {
						// no user found, create into wordpress
						$id = $this->wordpress_create_user($contact['email1'],$contact['first_name'],$contact['middle_name'],$contact['last_name']);
						if (!$id) 
							continue; // FAIL @Todo add log

						$user = get_user_by('id', $id);				
					}
				}

				// update meta data
				
				// for safety ALWAYS set sugar_id!
				update_user_meta($user->ID, 'sugar_id', $contact['id']);
				// other metadata (and sugarID normally..)
				$this->update_wordpress_user_data($user->ID, $contact);
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$offset += count($items); // increase by number of restults
		}
	
		return true;
	}
	
	/**
	 * Synchronise a Event from Sugar to Wordpress
	 * @param string $fromdate Sync from this date
	 * @parem boolean $create if TRUE create, if not exists
	 * @return boolean
	 */
	private function sync_events_sugar_to_wordpress($fromdate='2015-01-01', $create=TRUE) {
		global $wpdb;		
		
		/* DEBUG 
[aia_ministry_projecten_project_name]	string	"JN03 Outreaches Brazilië 2016"	
[aia_teamleden]	string	"4"	
[aia_teamleiders]	string	"1"	
[assigned_user_id]	string	"d60fe9fc-a16c-81c5-aeca-4f298134d79d"	
[assigned_user_name]	string	"Els Bogema"	
[begindatum]	string	"2015-07-13"	
[contact_id_c]	string	""	
[created_by]	string	"d60fe9fc-a16c-81c5-aeca-4f298134d79d"	
[created_by_name]	string	"Els Bogema"	
[currency_id]	string	"-99"	
[date_entered]	string	"2014-10-23 12:38:52"	
[date_modified]	string	"2015-05-26 14:53:17"	
[deleted]	string	"0"	
[description]	string	""	
[einddatum]	string	"2015-08-02"	
[gewenst_teamgrootte]	string	"8"	
[icb]	string	"0,00"	
[id]	string	"12192160-dc8a-6ca6-f9b4-5448f608d58b"	
[land]	string	"BRA"	
[manyware_projectid]	string	""	
[modified_by_name]	string	"Michiel Keijts"	
[modified_user_id]	string	"7822233a-90ae-2a54-d7bf-4f06f52ee8e2"	
[name]	string	"2015_BR15-05_Brazilië, Rio de Janeiro 1"	
[partner_teamleden]	string	"3"	
[partner_teamleiders]	string	"1"	
[penningmeester]	string	""	
[projectcode]	string	"BR15-05"	
[projectprijs]	string	"1.995,00"	
[projecttype]	string	"Project"	
[stad]	string	"Rio de Janeiro"	
[status_accomodatie]	string	"0_niet_bekend"	
[status_icb]	string	"0"	
[status_kerk_tl_koppeling]	string	"0_niet_bekend"	
[status_kerkinfo]	string	"0_niet_bekend"	
[status_programma]	string	"0_niet_bekend"	
[status_teaminfo]	string	"0_niet_bekend"	
[status_transport]	string	"0_niet_bekend"	
[status_vertaling]	string	"0_niet_bekend"	
[systeemnaam]	string	""	
[termijn_1_datum]	string	"2015-05-08"	
[termijn_1_prijs]	string	"395,00"	
[termijn_2_datum]	string	"2015-05-26"	
[termijn_2_prijs]	string	"750,00"	
[termijn_3_datum]	string	"2015-06-24"	
[termijn_3_prijs]	string	"850,00"	
[titel]	string	"Brazilië, Rio de Janeiro 1"	
[visible_website]	string	"0"	
		 */
		
		// loop over all contacts
		$items_found = TRUE;
		$num_items_per_query = 50;
		$offset = 0;
		while ($items_found && $offset<=100 ) { //TEMP !! FOR DEBUG!!
			// retrieve list of contacts from date (incremental)
			// manyware_aiarelatie = 1 (!!)
			$items = $this->sugar->searchCommon(
					"AIA_ministry_projecten.titel <>'' AND UNIX_TIMESTAMP(AIA_ministry_projecten.date_modified) > ".  strtotime($fromdate),
					"AIA_ministry_projecten",
					$num_items_per_query,
					$offset
				);

			/*foreach ($items as $contact) {
				if (empty($contact['email1']) || empty($contact['first_name']) || empty($contact['last_name'])) continue;

				// try and find user by sugar_id
				$user = get_user_by_meta_data('sugar_id', $contact['id']);

				if (!$user) {
					// try and find user by email
					$user = get_user_by('email', $contact['email1']);
					if (!$user) {
						// no user found, create into wordpress
						$id = $this->wordpress_create_user($contact['email1'],$contact['first_name'],$contact['middle_name'],$contact['last_name']);
						if (!$id) 
							continue; // FAIL @Todo add log

						$user = get_user_by('id', $id);				
					}
				}

				// update meta data
				
				// for safety ALWAYS set sugar_id!
				update_user_meta($user->ID, 'sugar_id', $contact['id']);
				// other metadata (and sugarID normally..)
				$this->update_wordpress_user_data($user->ID, $contact);
			}*/
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$offset += count($items); // increase by number of restults
		}
	
		return true;
	}
	
	private function sync_registrations_sugar_to_wordpress() {
		return true;
	}
	
	/**
	 * Function called to create new user
	 * @return int ID|False if failed
	 */
	private function wordpress_create_user($email, $first_name, $middle_name, $last_name) {
		$user = new stdClass;
		
		$user->user_login = sanitize_user($first_name.$middle_name.$last_name, true);
		$user->user_nickname = sanitize_user(trim($first_name) . ltrim($middle_name.' ') . $last_name, true);
		$user->user_password = substr(md5($first_name.uniqid().mt_rand(0, 1000).mt_rand(0, 1000)),0,12);
		$user->user_email = $email;
		$user->use_ssl = 0;
		$user->role = MY_AIA_DEFAULT_ROLE_NEW_USER; // default role!
		$user->comment_shortcuts = '';
		
		
		// check user_login_exist
		$i=0;
		while (username_exists($user->user_login)) {
			$user->user_login = $user->user_login.($i++);
		}
		
		// finally, add user!
		return wp_insert_user($user);
	}
	
	/**
	 * Function which reads the Wordpress SYNC columns and parses the desired
	 * data.
	 * 
	 * The sync_key definition is saved in the wp option my_aia_<type>_sync 
	 * according to the definition:
	 * array(
	 * ..
	 *	array(
	 *		'internal_field' => <type>::<id>::<name> (<id> is either a key or budypress field ID)
	 *		'external_field' => <type>::<module>::<name>
	 * )
	 *
	 * returns an array of the form:
	 * 
	 * array(
	 *	<type> => array(
	 *		<id|name> => array(<VALUE>,<VALUE>, <VALUE>,.. ) 
	 *	)
	 * )
	 * 
	 * e.g. array('sugarcrm'=>array('contacts'=>array('value',..)))
	 *  
	 * @param array $wpData
	 * @param array $crmData
	 * @param string $type "profile"|"event"|"registration"
	 * @param int   $direction (@default: FROM_WORDPRESS_TO_CRM)
	 * @return array $to_data (FROM_.._TO_..) data
	 */
	private function parse_crm_and_wordpress_data($wpData, $crmData, $type="profile", $direction = FROM_WORDPRESS_TO_CRM) {
		$sync_keys = get_option( "my_aia_" . $type . "_sync", FALSE);
		if (!$sync_keys)
			return false; //TODO add error!!
		
		// set from data
		$from_data = ($direction == FROM_WORDPRESS_TO_CRM)?$wpData:$crmData;
		$to_data = array();
		
		foreach ($sync_keys as $rule) {
			switch ($direction) {
				case FROM_CRM_TO_WORDPRESS:
					$from_def	= explode("::", $rule['external_field']);
					$to_def		= explode("::", $rule['internal_field']);
					$from_field = $from_def[2];
					$to_field = $to_def[2];

					// check for existence
					if (array_key_exists($from_field, $from_data)) {
						// form:	$to_data['BP'][id] = val
						// or:		$to_data['WP'][name] = val
						
						if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
						$to_data[ $to_def[0] ][ $to_def[1] ] = $from_data[$from_field];
					}
					
					
					break;
				default:
					$from_def	= explode("::", $rule['internal_field']);
					$to_def		= explode("::", $rule['external_field']);
					
					// check for existence
					if (array_key_exists($from_field, $from_data)) {
						// form:	$to_data['BP'][id] = val
						// or:		$to_data['WP'][name] = val
						
						if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
						$to_data[ $to_def[0] ][ $to_def[1] ] = $from_data[ $from_field ];
					}
			}
			
			
		}
	
		return $to_data;
	}
	
	/**
	 * Private function to update the usermeta with CRM DATA
	 * @param int $userID
	 * @param array $crmData
	 * @return boolean
	 */
	private function update_wordpress_user_data($userID, $crmData) {
		$dataset = $this->parse_crm_and_wordpress_data(array(), $crmData, "profile", FROM_CRM_TO_WORDPRESS);
		
		// update standard wordpress profile data (WP)
		if (isset($dataset['WP'])) { // UserMeta
			$user = get_user_by('ID', $userID);
			
			foreach ($dataset['WP'] as $key=>$val) {
				$user->$key = $val;
			}
			
			// update user command
			$error = wp_update_user($user);
		}
		
		// update wordpress userdata (UserMeta)
		if (isset($dataset['UserMeta'])) { // UserMeta
			foreach ($dataset['UserMeta'] as $key=>$val) {
				$error = $error + update_user_meta($userID, $key, $val);
			}
		}
		
		// update buddypress meta (BP)
		if (isset($dataset['BuddyPress'])) { // UserMeta
			foreach ($dataset['BuddyPress'] as $key=>$val) {
				$error = $error + xprofile_set_field_data($key, $userID, $val);
			}
		}
		
		// TODO: add error handling!
		return true;
	}
	
	/**
	 * Private function to update the usermeta with CRM DATA
	 * @param int $eventID
	 * @param array $crmData
	 * @return boolean
	 */
	private function update_wordpress_event_data($eventID, $crmData) {
		return true;
	}
	
	/**
	 * Private function to update the usermeta with CRM DATA
	 * @param int $userID
	 * @param int $eventID
	 * @param array $crmData
	 * @return boolean
	 */
	private function update_wordpress_registration_data($userID, $eventID, $crmData) {
		return true;
	}
	
	/**
	 * Private function to update the usermeta with CRM DATA
	 * @param int $userID
	 * @param int $eventID
	 * @param array $crmData
	 * @return boolean
	 */
	private function update_crm_user_data($userID, $eventID, $crmData) {
		return true;
	}
	
}
