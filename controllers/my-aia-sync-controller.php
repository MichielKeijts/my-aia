<?php

/*
 * Copyright (C) 2016 Michiel Keijts
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

include_once(MY_AIA_PLUGIN_DIR . 'core/crmsync/class_soap_sugar.php');
include_once(MY_AIA_PLUGIN_DIR . 'core/crmsync/class_conversion_helper.php');

/**
 * Description of my-aia-sync-controller
 * Sync controller handles the incoming and outgoing request for the CRM-MY_AIA
 * Synchronisation. 
 * @author Michiel
 */
class MY_AIA_SYNC_CONTROLLER extends MY_AIA_CONTROLLER {
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
	 * Sync dates ($this->get_sync_dates)
	 * @var array
	 */
	private $sync_dates;
	
	
	/**
	 * Start time in seconds
	 * @var int (seconds)
	 */
	private $start_time;
	
	/*
	 * Ids of some members
	 * array
	 */
	private $ids = array(
			/*"5ebdca15-a5a7-7b71-628c-5604faa86791", ///kath
			"d2b794c3-be82-4f25-3753-4f0719f19583", // agien
			"a6ddce37-0ff0-c229-66fe-4f070f503fac", // jouke*/
			"3d1ec996-64a2-b605-539d-4f070e094638", // michiel
			/*"86d7815a-2abf-e51b-14e3-4f070e7a73ca", // marcel
			"a4910c38-455c-a5dc-63bb-4f07190c038a", // christiaan
			"d0039759-0e53-c0c8-b70b-5278f15f0604", // jonathan
			"17d8443c-6ed8-929a-f493-53329b669f51", // wytze*/
			);
	
	
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * - Add the AJAX save over here
	 */
	public function before_filter(){
		add_action( 'wp_ajax_my_aia_admin_sync_save', array($this, 'sync_save'), 1);	
		add_action( 'wp_ajax_my_aia_admin_sync_start', array($this, 'do_sync'), 1);	
	}
	
	public function before_render() {
		// setting the menu bar for this controller
		$menu_bar = array(
			'edit_profile' => __('Sync Instellingen Profiel', 'my-aia'),
			'edit_events' => __('Sync Instellingen Events', 'my-aia'),
			'edit_registrations' => __('Sync Instellingen Registraties', 'my-aia'),
			'sync' => __('Handmatige Synchronisatie','my-aia'),
		);
		
		$this->set('menu_bar', $menu_bar);
		
		// scripts
		wp_enqueue_script( 'my-aia-admin-sync', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-sync.js', '', MY_AIA_VERSION );
		
		parent::before_render();
	}
	
	/**
	 * Index function, just a function with leads to the various settings one can do 
	 */
	public function index() {}
	
	/**
	 * Main non-cron Sync Function (auto rendererd)
	 */
	public function sync() {}// auto rendered
	
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
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'config/definitions/my-aia-sugacrm-contact-fields.txt');
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
		
		$ticket = new EM_Ticket;
		
		$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
		foreach (em_get_event()->fields as $field) $internal_fields[] = 'EM::' . $field['name'] . '::' . $field['name'];	// EM_Event fields
		foreach ($ticket->fields as $field=>$val) $internal_fields[] = 'EM::TICKET::' . $field;	// EM_Event fields
		foreach (em_get_attributes()['names'] as $field) $internal_fields[] = 'EM::' . $field . '::' . $field;	// META fields
		$internal_fields[] = 'EM::location_town::location_town';
		$internal_fields[] = 'EM::location_country::location_country';
		$internal_fields[] = 'EM::post_content::post_content';
		
		
		// get EXTERNAL (sugarcrm) fields
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'config/definitions/my-aia-sugacrm-event-fields.txt');
		$sugar_fields = explode("\r\n",$sugar_fields_string);
	
		$this->set('internal_fields', $internal_fields);
		$this->set('external_fields', $sugar_fields);
		
		
		$this->set('data',	get_option('my_aia_event_sync', array()));
		
		$this->set('sync_type', 'event');
		$this->render('edit_sync_rules');
	}
	
	/**
	 * function use to update the registration fields sync with sugar
	 */
	public function edit_registrations() {
		global $current_user;
		// get INTERNAL (wordpress profile & Events-Manager) fields
		$fields = array_keys(get_user_meta($current_user->ID));
		
		
		$buddypress_fields=my_aia_get_buddy_press_xprofile_fields(FALSE);
		
		$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
		$internal_fields[] = 'WP::user_email::user_email';
		$internal_fields[] = 'UserMeta::sugar_id::sugar_id';
		foreach ($buddypress_fields as $id=>$field) $internal_fields[] = 'BuddyPress::' . $id . '::' . $field;
		
		// get all booking meta fields --> NinjaForms Fields (admin label)
		// get all fields, with an admin label not empty. Such fields can be
		// used for the sync to CRM
		$nf_fields = ninja_forms_get_all_fields();
		
		//$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
		foreach (em_get_booking()->fields as $field=>$val) $internal_fields[] = 'EM::BOOKING::' . $field;	// EM_Booking fields
		foreach ($nf_fields as $field) {
			if (!empty($field['data']['admin_label'])) {
				$internal_fields[] = 'EM::BOOKING_META::' . $field['data']['admin_label'];	// EM_Event fields
			}
		}
		
		// add date modified field
		$internal_fields[] = 'EM::BOOKING_META::sugar_date_modified';	// EM_Event fields
	
		// get EXTERNAL (sugarcrm) fields
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'config/definitions/my-aia-sugacrm-registration-fields.txt');
		$sugar_fields_string .= "\r\n".file_get_contents(MY_AIA_PLUGIN_DIR . 'config/definitions/my-aia-sugacrm-contact-fields.txt');
		$sugar_fields = explode("\r\n",$sugar_fields_string);
	
		$this->set('internal_fields', $internal_fields);
		$this->set('external_fields', $sugar_fields);
		
		
		$this->set('data',	get_option('my_aia_registration_sync', array()));
				
		$this->set('sync_type', 'registration');
		$this->render('edit_sync_rules');
	}
	
	public function test_sync() {
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
	 * Initiates the SugarCRM client
	 * saved in $this->sugar
	 */
	private function create_sugar_client() {
		// create a sugar Client
		$this->sugar = new SoapSugar(
			get_option('my_aia_sugar_url','').'/soap.php?wsdl',
			array(
				'user' => get_option('my_aia_sugar_user',''),
				'pass' => get_option('my_aia_sugar_user_password','')
			)
		);
	}
	
	/**
	 * Actual function to sync data. 
	 * This function should be called to start the sync process
	 */
	public function do_sync() {
		define('DOING_SYNC', true);	// set doing sync to true: doing this.
		
		$this->create_sugar_client();
		
		// set sync dates, the start date from which syncing is needed
		$this->sync_dates = $this->get_sync_dates();
		$items = 0; // number of updates
		
		
		//-- FROM SUGAR TO WORDPRESS
		
		// update Wordpress with Sugar profile Data
		//*
		if (filter_input(INPUT_POST, 'user_sync')>0 && $items < 100) 
			$items += $this->sync_profiles_sugar_to_wordpress($this->sync_dates['sync_profiles_sugar_to_wordpress']);
		
		// update Wordpress with Sugar Event Data
		if (filter_input(INPUT_POST, 'event_sync')>0 && $items < 100) 
			$items += $this->sync_events_sugar_to_wordpress($this->sync_dates['sync_events_sugar_to_wordpress']);
		
		// update Wordpress with Sugar Registration Data
		if (filter_input(INPUT_POST, 'registration_sync')>0 && $items < 100) /**/
			$items += $this->sync_registrations_sugar_to_wordpress($this->sync_dates['sync_registrations_sugar_to_wordpress']);
		/**/
		//-- END FROM SUGAR TO WORDPRESS
		//-- START FROM WORDPRESS TO SUGAR
		if ($items < 100) $items += $this->sync_profiles_wordpress_to_sugar($this->sync_dates['sync_profiles_wordpress_to_sugar']);	// based on modifications table
		//if ($items < 100) $items += $this->sync_events_wordpress_to_sugar();		// based on events->push
		//if ($items < 100) $items += $this->sync_registrations_wordpress_to_sugar();	// based on modifications table
		
		//-- END FROM WORDPRESS TO SUGAR
		
		// check if we need to send..
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ($items > 0)	$this->send_json_response($this->sync_dates + array('count' => $items));
			$this->send_json_response($this->sync_dates + array('count' => $items));
		}
	}
	
	/**
	 * Synchronise a Profile from Sugar to Wordpress
	 * @param string $date_offset['date'] Sync from this date
	 * @parem boolean $create if TRUE create, if not exists
	 * @return boolean
	 */
	private function sync_profiles_sugar_to_wordpress($date_offset, $create=TRUE) {
		global $wpdb;
		
		$this->write_log('--> STARTING WITH sync_profiles_sugar_to_wordpress', $date_offset);
		
		// seleect only a small number of ID, namely AIA employees
		
		$subset = "";//sprintf("contacts.id IN ('%s') AND ", implode("','", $this->ids));
		
		// loop over all contacts
		$items_found = TRUE;
		$num_items_per_query = 50;
		$offset = $date_offset['offset'];
		while ($items_found && $date_offset['offset']-$offset<=100 ) { // 100 per time for speed issues
			// retrieve list of contacts from date (incremental)
			// manyware_aiarelatie = 1 (!!)
			$items = $this->sugar->searchCommon(
					$subset ."contacts_cstm.manyware_aiarelatie_c = 1 && contacts.last_name<>'' AND UNIX_TIMESTAMP(contacts.date_modified) >= ".  (strtotime($date_offset['date'])),
					"Contacts",
					$num_items_per_query,
					$date_offset['offset'],
					'date_modified ASC'	// order by
				);

			foreach ($items as $contact) {
				if (empty($contact['email1']) || (empty($contact['first_name']) && empty($contact['last_name']))) {
					$this->write_log('No email/name present', $contact['id']);
					continue;
				}

				// try and find user by sugar_id
				$user = get_user_by_meta_data('sugar_id', $contact['id']);

				if (!$user) {
					// try and find user by email
					$user = get_user_by('email', $contact['email1']);
					if (!$user) {
						// no user found, create into wordpress
						$id = $this->wordpress_create_user($contact['email1'],$contact['first_name'],$contact['middle_name'],$contact['last_name']);
						if (!$id) {
							$this->write_log('Could not insert this user into wordpress', $contact);
							continue; // FAIL @Todo add log
						}

						$user = get_user_by('id', $id);				
					}
				}
				
				$this->write_log('Selected User ID', $user->ID);

				// -- update meta data
				
				// for safety ALWAYS set sugar_id && sugar_date_modified
				update_user_meta($user->ID, 'sugar_id', $contact['id']);
				update_user_meta($user->ID, 'sugar_date_modified', $contact['date_modified']);
				
				// other metadata (and sugarID normally..)
				if (!$this->update_wordpress_user_data($user->ID, $contact)) {
					$this->write_log("Updating USER {$user->ID} failed.", $contact);
				}
				
				// check for script execution time
				$this->get_elapsed_time_and_break(__FUNCTION__, $contact['date_modified']);
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$date_offset['offset'] += count($items); // increase by number of restults
			
			// update sync_date:
			if (!$items_found) {			
				$this->set_sync_dates(__FUNCTION__, $last_fromdate, 0 );
			} else {
				$last_fromdate =  $items[ count($items)-1 ]['date_modified'];
				$this->set_sync_dates(__FUNCTION__, $date_offset['date'], $date_offset['offset'] );
			}
		}
		
		$this->write_log('--> ENDING  WITH ' . __FUNCTION__, $date_offset['offset']-$offset);
	
		return $date_offset['offset']-$offset;
	}
	
	/**
	 * Synchronise a Event from Sugar to Wordpress
	 * @param string $date_offset['date'] Sync from this date
	 * @parem boolean $create if TRUE create, if not exists
	 * @return boolean
	 */
	private function sync_events_sugar_to_wordpress($date_offset = NULL, $create=TRUE) {
		global $wpdb;		
		
		$this->write_log('--> STARTING WITH sync_events_sugar_to_wordpress', $date_offset);
		
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
		$start_offset = $date_offset['offset'];
		while ($items_found && $date_offset['offset']-$start_offset<=100 ) { //TEMP !! FOR DEBUG!!
			// retrieve list of contacts from date (incremental)
			// manyware_aiarelatie = 1 (!!)
			$items = $this->sugar->searchCommon(
					"AIA_ministry_projecten.titel <>'' AND UNIX_TIMESTAMP(AIA_ministry_projecten.date_modified) >= ". strtotime($date_offset['date']),
					"AIA_ministry_projecten",
					$num_items_per_query,
					$date_offset['offset'],
					'date_modified ASC'	// order by
				);

			// step through Sugar Events
			foreach ($items as $sugar_event) {
				if (empty($sugar_event['titel'])) 
					continue;

				// TEMP!!
				if (empty($sugar_event['projectcode'])) {
					$sugar_event['projectcode'] = $this->get_projectcode($sugar_event);
				}
				
				// try and find event by sugar_id
				$args = array(
					'meta_key' => 'sugar_id',
					'meta_value' => $sugar_event['id'],
					'post_type' => EM_POST_TYPE_EVENT,
					'post_status' => 'any',
					'posts_per_page' => -1
				);
				$events = get_posts($args);

				// event exist in Wordpress?
				if (!$events) {
					// create new event
					$event = $this->wordpress_create_event($sugar_event, $sugar_event['id']);
					if ($event) 
						$eventID = $event;
				} else {
					// get event idsync_registrations_sugar_to_wordpress
					$event_meta = get_post_meta($events[0]->ID,'_event_id');
					$eventID = $event_meta[0];
				}

				// if we have an event...
 				if ($eventID) {
					$event = new EM_Event($eventID);
					
					// for safety ALWAYS set sugar_id! (cannot be removed by sync settings)
					$event->event_attributes['sugar_id'] = $sugar_event['id'];
				
					// other metadata (and sugarID normally..)
					$this->update_wordpress_event_data($event, $sugar_event);
				} else {
					//@TODO error: no event found.. or could not create new one..
				}
				
				// check for script execution time
				//$this->get_elapsed_time_and_break(__FUNCTION__, $sugar_event['date_modified']);
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$date_offset['offset'] += count($items); // increase by number of restults
			
			// update sync_date:
			if (!$items_found) {			
				$this->set_sync_dates(__FUNCTION__, $last_fromdate,0 );
			} else {
				$last_fromdate =  $items[ count($items)-1 ]['date_modified'];
				$this->set_sync_dates(__FUNCTION__, $date_offset['date'], $date_offset['offset'] );
			}
		}
	
		$this->write_log('--> ENDING  WITH ' . __FUNCTION__, $date_offset['offset']-$offset);
		
		return $date_offset['offset'] - $start_offset;
	}
	
	/**
	 * Function to update or insert EM_Bookings with the SugarCRM aia_ministry_deelnames
	 * data. This uses the sync columns
	 * @param type $date_offset['date']
	 * @param type $create
	 * @return boolean
	 */
	private function sync_registrations_sugar_to_wordpress($date_offset=NULL, $create=TRUE) {
		$this->write_log('--> STARTING WITH sync_registrations_sugar_to_wordpress', $date_offset);
		
/* -- EXAMPLE DATA
aia_ministry_project_id_c	7fc114e2-c080-9ff5-11d6-560e735be09f
aia_ministry_project_name	2016_SK016-T_Sportkampen 2016 Tieners
assigned_user_id	1
assigned_user_name	Sugar Daddy
bankrekeninghouder	MJ Roorda
bankrekeningnummer	NL90ABNA0438798422
betaling_compleet	0
contact_id_c	171c7194-f9a0-8788-3994-556ee4d39ffd
contact_name	Mark Roorda
created_by	1
created_by_name	Sugar Daddy
currency_id	-99
date_entered	11-03-16 12:47
date_modified	11-03-16 12:47
deelnemertype	Teamlid
deleted	0
description	
emergency_contact	Marjan Roorda
emergency_phonenumber	+316-33928171
extra_optie_1_c	Door mijn vrienden
extra_optie_2_c	
familie_korting	0
formulieren_ok	0
geen_conferentie	0
gratis	0
historie_sportkamp_team	Basketbal
id	e5e0b9d9-c609-bbea-046e-56e2be440289
kamervoorkeur1	
kamervoorkeur2	
kamervoorkeur3	
kamervoorkeur4	
manyware_actnr	
manyware_herkomstdatumtijd	11-03-16 00:00
manyware_projectid	
medisch_ok	0
minderjarig_ok	0
modified_by_name	Sugar Daddy
modified_user_id	1
motivatie_korting	
name	Outreachgegevens - Mark Roorda -2016_SK016-T_Sportkampen 2016 Tieners
opmerkingen	
passpoort_ok	0
projectprijs	285
termijn_1_ok	0
termijn_2_ok	30-12-99 00:00
termijn_3_ok	30-12-99 00:00
tiener_korting	0
uitschrijf_datum	
vrijwaring_ok	0
 
 */
		// loop over all registrations
		$items_found = TRUE;
		$num_items_per_query = 50;
		$start_offset = $date_offset['offset'];
		$subset = "";
		//$subset = sprintf("AIA_ministry_deelnames.contact_id_c IN ('%s') AND ", implode("','", $this->ids));
		while ($items_found && $date_offset['offset']-$start_offset<=100 ) { //TEMP !! FOR DEBUG!!
			// retrieve list of contacts from date (incremental)
			// manyware_aiarelatie = 1 (!!)
			$items = $this->sugar->searchCommon(
					
					$subset . "AIA_ministry_deelnames.contact_id_c <>'' AND UNIX_TIMESTAMP(AIA_ministry_deelnames.date_modified) >= ". strtotime($date_offset['date']),
					//$subset . "AIA_ministry_deelnames.contact_id_c = 'a4910c38-455c-a5dc-63bb-4f07190c038a' AND UNIX_TIMESTAMP(AIA_ministry_deelnames.date_modified) > ".  (strtotime($date_offset['date'])),
					"AIA_ministry_deelnames",
					$num_items_per_query,
					$date_offset['offset'],
					'date_modified ASC'	// order by
				);
			
			$counter = 0;
			
			// step through Sugar Events
			foreach ($items as $sugar_registration) {
				if (empty($sugar_registration['aia_ministry_project_id_c']) || empty($sugar_registration['contact_id_c'])) {
					$this->write_log('--> no project or contact set', $sugar_registration);
					
					continue;
				}
				
				//var_export($sugar_registration);

				// popuplate fields with user_data
				$contacts = $this->sugar->searchContact("contacts.id = '{$sugar_registration['contact_id_c']}'"); 
				if (count($contacts) > 0) {
					$contact = reset($contacts);
				} else {
					$this->write_log('--> could not find in SUGAR', $sugar_registration);
					//echo "could not find contact" . $sugar_registration['contact_id_c'];
					//debug($sugar_registration);
					continue; // no valid data found
				}
				
				// array add
				$sugar_registration = $sugar_registration + $contact;
				
				
				// try and find event by sugar_id
				$args = array(
					'meta_key' => 'sugar_id',
					'meta_value' => $sugar_registration['aia_ministry_project_id_c'],
					'post_type' => EM_POST_TYPE_EVENT,
					'post_status' => 'any',
					'posts_per_page' => -1
				);
				$events = get_posts($args);

				// event exist in Wordpress?
				if (!$events) {
					$this->write_log('Trying to get a event but no one found in Wordpress. Sugar Event ID below.', $sugar_registration['aia_ministry_project_id_c']);
					//echo "<span style='background-color: red'>no event found for Sugar ID >> {$sugar_registration['aia_ministry_project_id_c']} << </span>\r\n";
					continue;	 //STOP!
				}

				// get event idsync_registrations_sugar_to_wordpress
				$event_meta = get_post_meta($events[0]->ID,'_event_id');
				$eventID = $event_meta[0];

				// if we have an event...
 				if ($eventID) {
					// get Ticket
					$event = new EM_Event($eventID);
					$ticket = $event->get_tickets()->get_first();
					if ($ticket !== FALSE) {
						$ticketID = $ticket->ticket_id;
						// get personID, try and find user by sugar_id
						$user = get_user_by_meta_data('sugar_id', $sugar_registration['contact_id_c']);
						if (!$user) continue;	//STOP!

						$userID = $user->ID;

						// userID set, eventID set, .. go!
						if ($userID) {
							$bookingID = $this->get_meta_object_id('booking-crm', $sugar_registration['id']);

							if ($bookingID === FALSE) {
								$this->write_log("Booking found... updating {$sugar_registration['id']}");
								$booking = new EM_Booking();
								$booking->event_id = $eventID;
								$booking->person_id = $userID;
								$booking->save();
							} else {
								$this->write_log("No Booking found... {$sugar_registration['id']}");
								$booking = new EM_Booking($bookingID);
							}
							// set $event and $user data
							$booking->event_id = $eventID;
							$booking->person_id = $userID;
							
							// other metadata (and sugarID normally..)
							// saving is also done in update function !
							$e = $this->update_wordpress_registration_data($booking, $sugar_registration,$ticketID);	
							if (!$e) $this->write_log("Failed updating registration data for Booking {$booking->booking_id}", $sugar_registration);
							//echo "<span style='background-color: green'>SAVED {$e->booking_id} </span>\r\n";
						}
					} else {
						$this->write_log("Trying to get a TICKET for EVENT{$eventID} but no one found in Wordpress.", $event->event_name);
						//echo "<span style='background-color: red'>no ticket found for Event ID {$eventID} </span>\r\n";
					}
				} else {
					//@TODO error: no event found.. or could not create new one..
					$this->write_log('Trying to get a event but no one found in Wordpress. Sugar Event ID below.', $sugar_registration['aia_ministry_project_id_c']);
					//echo "<span style='background-color: red'>no event found for Sugar ID {$sugar_registration['aia_ministry_project_id_c']} </span>\r\n";
				}
				
				// check for script execution time
				//$this->get_elapsed_time_and_break(__FUNCTION__, $sugar_registration['date_modified']);
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$date_offset['offset'] += count($items); // increase by number of restults
			
			
			
			// update sync_date:
			if (!$items_found) {			
				$this->set_sync_dates(__FUNCTION__, $last_fromdate,0 );
			} else {
				$last_fromdate =  $items[ count($items)-1 ]['date_modified'];
				$this->set_sync_dates(__FUNCTION__, $date_offset['date'], $date_offset['offset'] );
			}
		}		
		
		$this->write_log('--> ENDING  WITH ' . __FUNCTION__, $date_offset['offset']-$offset);
		
		return $date_offset['offset']-$start_offset;
	}
	
	/**
	 * Sync EM_Events from WP to Sugar. 
	 * 
	 * This is based on comparisson from date_modified (sugar) to the modification date in WP
	 * @global \WPDB $wpdb
	 * @return int
	 */
	public function sync_events_wordpress_to_sugar($event = NULL) {
		global $wpdb;

		if (empty($event)) {
			// get list of modified events (post_type = EM_POST_TYPE_EVENT

			// try and find event by where modification is past last sync date
			$args = array(
				'post_type'			=> EM_POST_TYPE_EVENT,
				'post_status'		=> 'any',
				'posts_per_page'	=> -1,
				'orderby'			=> 'post_modified_gmt',
				'order'				=> 'DESC',
				'date_query'		=>  array(
					'after'			=>  $this->sync_dates['sync_events_wordpress_to_sugar']['date'],
					'offset'		=>	$this->sync_dates['sync_events_wordpress_to_sugar']['offset'],
					'column'		=>	'post_modified',
				)
			);
			$events = get_posts($args);

			/*$events = $wpdb->get_results( $wpdb->prepare( sprintf("SELECT %s_posts.* FROM aia_posts  WHERE 1=1  AND (%sposts.post_modified > '%s') AND %sposts.post_type = 'event' AND ((%sposts.post_status <> 'trash' AND %sposts.post_status <> 'auto-draft'))  ORDER BY %sposts.post_modified DESC",
					$wpdb->prefix,
					$this->sync_dates['sync_events_wordpress_to_sugar']['date'],
					$wpdb->prefix,
					$wpdb->prefix,
					$wpdb->prefix,
					$wpdb->prefix,
					$wpdb->prefix
			) ) );*/
		} else {
			$events = array($event);
		}
		
		// step over event
		foreach ($events as $_event) {
			$event = new EM_Event($_event);	// load EM Event
			if ($event->recurrence_id && $event->recurrence_id > 0) continue; // not the event with recurrence
			
			// check if sugar_id, otherwise create
			if (!array_key_exists('sugar_id', $event->event_attributes)) {
				// create new event!
				$create = TRUE;
			} elseif (array_key_exists('sugar_date_modified', $event->event_attributes) 
					&& strtotime($event->event_attributes['sugar_date_modified']) < strtotime($event->post_modified)	
					|| !array_key_exists('sugar_date_modified', $event->event_attributes)) {
				// date modified is bigger than sugar date, update
				$create = FALSE;
			} else {
				continue; // step over this event.
			}
			
			// init creation and update process
			$set_entry_data = Array();
			if ($create) {
				$set_entry_data[] = array(
					'name'	=> 'new_with_id',
					'value'	=> true
				);
			} else {
				// make sure to set the sugar id
				$set_entry_data[] = array(
						'name'	=>	'id',
						'value'	=>	$event->event_attributes['sugar_id']
				);
			}
			
			// format the dataset according to be read by sync rules..
			$dataset = $this->format_wordpress_data_for_syncing(['EM'=>$event]);											// format data
			$parsed_data = $this->parse_crm_and_wordpress_data($dataset, array(), "event", FROM_WORDPRESS_TO_CRM);	// parse the data
			$set_entry_data = $this->from_array_key_value_to_array_name_value_list($parsed_data['AIA_ministry_projecten'], $set_entry_data);
			
			// save the data..
			//continue; //STOP!
			if (!$this->sugar) $this->create_sugar_client ();
			// save.. 
			if ($sugar_id = $this->sugar->updateModule($set_entry_data, 'AIA_ministry_projecten')) {
				// Finally, save set Sugar Meta and Update Datemodified
				$event->event_attributes['sugar_id'] = $sugar_id;
				$event->event_attributes['sugar_date_modified'] = date('Y-m-d H:i:s'); // now. Should be bigger than sugar_date_modified (by default)
				$event->save_meta(); // save metadata to wp_post_meta
			}
		}
		
		$this->set_sync_dates('sync_events_wordpress_to_sugar', date('Y-m-d H:i:s'));
		
		
		// return number of events
		return (int) $offset;
	}
	
	/**
	 * Synchronise a Profile from Wordpress to Sugar
	 * The way this goes
	 * 1) updated profile data is got from the table with modification data.
	 *	Date for synchronisation is got from the options table
	 * 2) actions to create a user (also in Manyware) are done. 
	 * 3) New user is created in Sugar
	 * 4) 
	 * 
	 * @param string $date_offset['date'] Sync from this date
	 * @parem boolean $create if TRUE create, if not exists
	 * @return boolean
	 */
	private function sync_profiles_wordpress_to_sugar($date_offset, $create=TRUE) {
		global $wpdb;
		
		$this->write_log('--> STARTING WITH sync_profiles_wordpress_to_sugar', $date_offset);
		
		// Set Queyry and go on
		$items_found = TRUE;
		$num_items_per_query = 50;
		$offset = $date_offset['offset'];
		while ($items_found && $date_offset['offset']-$offset<=100 ) { // 100 per time for speed issues
			// get query (including offset)
			// list of approved contact modifications
			$query = sprintf("SELECT * FROM %smy_aia_crm_sync WHERE done = 0 && approved = 1 LIMIT %d, %d", $wpdb->prefix, $offset, $offset + 50);
			$results = $wpdb->get_results($query, ARRAY_A);
			
			
			foreach ($results as $contact) {
				// not existing or invalid data: step over
				if (empty($contact['crm_id']) && empty($contact['wp_id'])) continue;

				
				// try and find user by sugar_id
				if (empty($contact['crm_id'])) {
					$user = get_user_by('ID', $contact['wp_id']);
					if(!$user) {
						// cannot find user data
						continue;
					}
					// get new User ID, also saved to user_meta
					$id = $this->sugar_create_contact($user);
				} else {
					$id = $contact['crm_id'];
				}
				
				// now get the new values to be updates
				$values = maybe_unserialize($contact['new_values']);
				//$values['UserMeta']['sugar_id']['sugar_id'] = $id;
				
				// other metadata (and sugarID normally..)
				if ($this->update_sugar_user_data($id, $values)) {
					$query = sprintf("UPDATE %smy_aia_crm_sync SET done = 1, modified = NOW() WHERE id=%d LIMIT 1", $wpdb->prefix, $contact['id']);
					$wpdb->query($query);
				}
				
				// check for script execution time
				$this->get_elapsed_time_and_break(__FUNCTION__, $contact['date_modified']);
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$date_offset['offset'] += count($items); // increase by number of restults
			
			// update sync_date:
			if (!$items_found) {			
				$this->set_sync_dates(__FUNCTION__, $last_fromdate, 0 );
			} else {
				$last_fromdate =  $items[ count($items)-1 ]['date_modified'];
				$this->set_sync_dates(__FUNCTION__, $date_offset['date'], $date_offset['offset'] );
			}
		}
	
		return $date_offset['offset']-$offset;
	}
	
	/**
	 * Create a sugar contact by $user info
	 * @param WP_User $user
	 * @return string UUID sugar user
	 */
	private function sugar_create_contact($user, $create_manyware_contact = TRUE) {
		$id = $this->sugar->createContact($user->user_firstname, $user->user_lastname, $user->user_email);
		
		// if ID created, 
		if ($id && strlen($id) > 5) { // sugar id is UUID
			// TODO: create manyware contact
			
			
			return update_user_meta($user->ID, 'sugar_id', $id) > 0 ? $id : FALSE;
		}
		
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
	 * Create an event with SugarCRM data
	 * @param array $sugar_event sugar->data
	 * @param string $sugar_id UUID sugar
	 * @return int ID of the new Event
	 */
	private function wordpress_create_event($sugar_event, $sugar_id) {
		$event = new EM_Event();

		// set necessary data
		$event->event_name = ConversionHelper::from_sugar('titel', $sugar_event);
		$event->post_content = empty($sugar_event['description']) ? "Event " . $sugar_event['titel']:$sugar_event['description'];
		$event->post_excerpt = "";
		$event->event_attributes['sugar_id'] = $sugar_id;
		
		// save..
		$event->save();
	
		return $event->event_id;
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
					// Field Def either: 
					// -	CLASS::CHILD::field (child exists)
					// -	CLASS::field:field  (no child)
					
					
					$from_def	= explode("::", $rule['external_field']);
					$to_def		= explode("::", $rule['internal_field']);
					if (count($from_def) != 3 || count($to_def) != 3) continue;
					
					$from_field = $from_def[2];
					$to_field = $to_def[2];

					// check for existence
					if (array_key_exists($from_field, $from_data)) {
						// form:	$to_data['BP'][id] = val
						// or:		$to_data['WP'][name] = val
						
						// if a CHILD class
						if ($to_def[1] != $to_def[2]) {
							// for Buddypress: BP::ID::NAME (numeric)
							// otherwise: EM::CHILD::NAME (not numeric)
							if (is_numeric($to_def[1])) {
								if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
								$to_data[ $to_def[0] ][ $to_def[1] ] = ConversionHelper::from_sugar($from_field, $from_data);
							} else {
								if (!is_array($to_data[$to_def[1]])) $to_data[$to_def[1]] = array();
								$to_data[ $to_def[1] ][ $to_def[2] ] = ConversionHelper::from_sugar($from_field, $from_data);
							}
						} else {
							if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
							$to_data[ $to_def[0] ][ $to_def[2] ] = ConversionHelper::from_sugar($from_field, $from_data);
						}
					}
					
					
					break;
					
				case FROM_WORDPRESS_TO_CRM:
				default: // FROM WORDPRESS TO CRM
					$from_def	= explode("::", $rule['internal_field']);
					$to_def		= explode("::", $rule['external_field']);
					
					$from_field = $from_def[2]; 
					$to_field = $to_def[2];
					
					// get field data
					$_from_data = NULL;
					// Buddypress::first_name::first_name
					if (
							array_key_exists($from_def[0], $from_data) && 
							array_key_exists($from_def[1], $from_data[ $from_def[0] ])) {
						$_from_data = $from_data[ $from_def[0] ][ $from_def[1] ];
					}
					
					// old method, to overrule if also in array..
					$_from_data = array_key_exists($from_field, $from_data) ? $from_data[$from_field] : $_from_data;
					
					// check for existence
					if (!empty($_from_data)) {// || $from_def[2]=='sugar_name') { //@TODO unsafe from_def
						// form:	$to_data['BP'][id] = val
						// or:		$to_data['WP'][name] = val
						
						if ($to_def[1] != $to_def[2]) {
							if (!is_array($to_data[$to_def[1]])) $to_data[$to_def[1]] = array();
							$to_data[ $to_def[1] ][ $to_def[2] ] = ConversionHelper::from_wordpress($from_field, $_from_data, $_from_data);
						} else {
							if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
							$to_data[ $to_def[0] ][ $to_def[2] ] = ConversionHelper::from_wordpress($from_field, $_from_data, $from_data);
							
						}
					} else {
						
						// DEPRECATED WAY, still used:
						
						$from_def	= explode("::", $rule['internal_field']);
						$to_def		= explode("::", $rule['external_field']);

						$from_field = $rule['internal_field'];
						$to_field = $to_def[2];

						// check for existence
						if (array_key_exists($from_field, $from_data)) {// || $from_def[2]=='sugar_name') { //@TODO unsafe from_def
							// form:	$to_data['BP'][id] = val
							// or:		$to_data['WP'][name] = val

							if ($to_def[1] != $to_def[2]) {
								if (!is_array($to_data[$to_def[1]])) $to_data[$to_def[1]] = array();
								$to_data[ $to_def[1] ][ $to_def[2] ] = ConversionHelper::from_wordpress($from_field, $from_data[$from_field], $from_data);
							} else {
								if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
								$to_data[ $to_def[0] ][ $to_def[2] ] = ConversionHelper::from_wordpress($from_field, $from_data[$from_field], $from_data);
							}
						}
					}					
			}
			
			
		}
	
		return $to_data;
	}
	
	/**
	 * Preformatting WP post data (or EM_Event, EM_Booking, etc.) to the 
	 * corresponding format. the $data parameter can be of:
	 * array(
	 *	'EM'			=> EM_Event object (auto set TICKET)
	 *	'BOOKING'		=> EM_Booking object (auto parsing BOOKING_META)
	 *  'Buddypress'	=> Xprofile Fields
	 *  )
	 * @param array $data
	 * @return array formatted data
	 */
	private function format_wordpress_data_for_syncing($data) {
		if (!is_array($data)) return array(); // empty array
		
		$formatted_data = array();
		foreach ($data as $key=>$obj) {
			switch ($key) {
				case "EM":
					$internal_fields = array();
					// create form: <TYPE>::<ID>::<READABLE NAME>
					// set values
					foreach ($data['EM']->fields as $field)								$formatted_data[	'EM::' . $field['name'] . '::' . $field['name']]	= $data['EM']->{$field['name']};	// EM_Event fields
					if ($data['EM']->get_tickets()->get_first()) foreach ($data['EM']->get_tickets()->get_first()->fields as $field=>$key)	$formatted_data[	'EM::TICKET::' . $field	]					= $data['EM']->get_tickets()->get_first()->{$field};	
					foreach ($data['EM']->event_attributes as $field=>$val)				$formatted_data[	'EM::' . $field . '::' . $field]					= $val;	
					
					$formatted_data[	'EM::location_town::location_town']			= $data['EM']->get_location()->location_town;	
					$formatted_data[	'EM::location_country::location_country']	= $data['EM']->get_location()->location_country;	
					$formatted_data[	'EM::post_content::post_content']			= $data['EM']->post_content;	
					
					$category = current($data['EM']->get_categories()->categories);
					if (is_a($category, "EM_Category")) {
						$formatted_data[	'EM::projecttype::projecttype']		= str_replace(
								array('Sportweek'), 
								array('Project'), 
								$category->name
							);
					}
					break;
				case "BOOKING": 
					break;
				case "BuddyPress":
					break;
				default:
					//..
			}
		}
		
		return $formatted_data;
	}
	
	/**
	 * Convert the $data from array(key1=>value,key2=>value,..) to a list
	 * <br> where the format is SugarCRM soap entry style:
	 * <br> 
	 * array (
	 *	array(
	 *		'name' => key
	 *		'value'=> key
	 *	),
	 *	array(
	 *		'name' => key
	 *		'value'=> key
	 *	),
	 *  ..
	 * )
	 * @param type $data
	 * @param type $name_value_list
	 * @return type
	 */
	private function from_array_key_value_to_array_name_value_list($data, $name_value_list=array()) {
		foreach ($data as $key=>$val) {
			array_push($name_value_list,
					array(
						'name'	=>	$key,
						'value'	=>	$val
					)
				);
		}
		
		return $name_value_list;
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
				$user->{$key} = $val;
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
	 * @param \EM_Event $event
	 * @param array $crmData
	 * @return boolean
	 */
	private function update_wordpress_event_data($event, $crmData) {
		$dataset = $this->parse_crm_and_wordpress_data(array(), $crmData, "event", FROM_CRM_TO_WORDPRESS);
		
		// get event attributes
		$attributes = em_get_attributes();
		$meta_fields = $attributes['names']; // attribute (POST_META) fields
		
		// update standard wordpress Events Manager data (EM)
		if (isset($dataset['EM'])) { 
			foreach ($dataset['EM'] as $key=>$val) {
				switch ($key) {
					case "projecttype": // Save Category
						// replace Sugar Values with Wordpress
						$val = str_replace(
								array('Project', 'SportkampCoach', 'Trainingsdagen'), 
								array('Sportweek', 'Sportkamp',  'Event'), 
								$val
							);
						
						$category = get_term_by('name', $val, EM_TAXONOMY_CATEGORY);
						if ($category) {
							// apply by adding a new EM_Category 
							$event->get_categories()->categories[0]=new EM_Category($category);
						};
						
						break;
					case "location_town":
					case "location_country":	// Location
						// fix Sugar (3) to WP (2) country code abbreviation
						if (strlen($dataset['EM']['location_country'])>2) {
							if ($dataset['EM']['location_country'] == 'NED') {
								$dataset['EM']['location_country'] = 'NL'; // OVERRULE, correct 3166 is NLD
							} else 
								$dataset['EM']['location_country'] = ISO3166::from_3to2_characters($dataset['EM']['location_country']);
						}
						
						$dataset['EM']['location_country'] = empty($dataset['EM']['location_country']) || !$dataset['EM']['location_country'] ? "NL":$dataset['EM']['location_country'];
						$dataset['EM']['location_town'] = trim($dataset['EM']['location_town']);
						
						// try and find location in database					
						
						// set location default parameter (init)
 						$location_name = sprintf('%s (%s)', $dataset['EM']['location_town'], ISO3166::get_full_country_name($dataset['EM']['location_country']));
						
						// get location (or location_id is NULL if not able to find)
						if ($location_post = get_page_by_title($location_name, OBJECT, EM_POST_TYPE_LOCATION)) {
							// if post data is found, get META
							$location_id = get_post_meta($location_post->ID,'_location_id');
							if (is_array($location_id)) $location_id = reset($location_id);
						}
						$location = em_get_location($location_id);
						
						if (empty($location->location_id)) {	// no existing location found
							$location = new EM_Location();
							$location->location_name = $location_name;
							$location->location_address = 'Puntenburgerlaan'; // default
							$location->location_country = 'NL'; // default
							$location->location_town  = 'Amersfoort';
						
							$location->post_status = 'published';
							$location->post_name = $location->location_name;
							
							// check google maps api
							usleep(100);	// force not more than 10 requests per second to google.. (free plan)
							if ($results = get_google_geocode_result(sprintf('%s %s', $dataset['EM']['location_town'], ISO3166::get_full_country_name( $dataset['EM']['location_country']) ))) {
								// apply data
								$location->location_country		= $results['country'];
								$location->location_latitude	= $results['latitude'];
								$location->location_longitude	= $results['longitude'];
								$location->location_address		= $results['route'];
								$location->location_country		= $results['country'];
								$location->location_town		= $results['locality'];
								$location->location_postcode	= $results['postal_code'];
								$location->location_status		= 1; //found
								
							}
							
							// also overwrite dataset:
							$dataset['EM']['location_town'] = $location->location_town;
							$dataset['EM']['location_country'] = $location->location_country;
												
							// save to database
							$location->save();
						}
						
						// set Event Location ID
						$event->location = $location;
						$event->location_id = $location->location_id;
						
						
						break;
					case "start_time":
					case "end_time":
						$val = date('H:i:s', strtotime($val)); // set time to right format. Expect start/end time to be a datetime object
					default:

						// check if it is an meta_field (save as such)
						if (in_array($key, $meta_fields)) {
							// its a meta field--> save as event_attribute
							$event->event_attributes[$key] = $val;
						}
						
						// normal behavior
						$key = 'event_'.$key; // prefix
						$event->$key = $val;
				
				}
			}
			
			// update user command
			$error = $event->save();
		} 
		
		// EM TICKET UPDATE
		if ($error && isset($dataset['TICKET'])) { 
			/*
			 * Process: find a ticket for the event. 
			 * If exists: update the ticket value (price, date, places)
			 * If not exists: create ticket
			 */
			
			// returns first EM_Ticket object or false if no ticket
			$EM_Ticket = $event->get_tickets()->get_first();
			if ($EM_Ticket === FALSE) {
				// we got no ticket, create.
				$EM_Ticket = new EM_Ticket();
				$EM_Ticket->event_id = $event->event_id;
				$EM_Ticket->ticket_required = TRUE;
				$EM_Ticket->ticket_max = 1;
				$EM_Ticket->ticket_min = 1;
				$EM_Ticket->ticket_members = TRUE;
				$EM_Ticket->ticket_spaces = 2500; // random high number to allow massive groups @TODO: find good way of saveing this data
			}
			
			
			// parse all the data and set to EM_Ticket object
			foreach ($dataset['TICKET'] as $key=>$val) {
				$EM_Ticket->$key = $val;
			}
			
			// save the Ticket
			$ticket_error = $EM_Ticket->save();
		}
		
		if ($error === TRUE && $ticket_error === TRUE ) {
			return true;
		}
		
		// TODO: add error handling!
		
		return array($error, $ticket_error);
	}
	
	/**
	 * Private function to update the usermeta with CRM DATA
	 * @param \EM_Booking $booking
	 * @param array $crmData
	 * @param int	$ticketID id of the ticket for the event
	 * @return boolean
	 */
	private function update_wordpress_registration_data($booking, $crmData, $ticketID) {
		$dataset = $this->parse_crm_and_wordpress_data(array(), $crmData, "registration", FROM_CRM_TO_WORDPRESS);
		
		$booking->booking_status = 1;	// default!
		$booking->booking_spaces = 1;	// default!
		
		$ticket_booking = new EM_Ticket_Booking;
		$ticket_booking->booking_id = $booking->booking_id;
		$ticket_booking->ticket_booking_spaces = 1;
		$ticket_booking->ticket_booking_price = ConversionHelper::projectprijs($crmData);
		$ticket_booking->spaces = $ticket_booking->ticket_booking_spaces;
		$ticket_booking->price = $ticket_booking->ticket_booking_price;
		$ticket_booking->ticket_id = (int) $ticketID;

		// save EM_Ticket_Booking to part of booking (EM_Ticket_Bookings)
		$booking->tickets_bookings = new EM_Tickets_Bookings;
		$booking->tickets_bookings->tickets_bookings[$ticketID] = $ticket_booking;
		
		$booking->booking_price = $ticket_booking->price;

		// update meta
		$this->set_meta('booking-crm', $booking->booking_id, $crmData['id']);
		
		// update standard wordpress Events Manager data (EM)
		if (isset($dataset['BOOKING'])) { 
			foreach ($dataset['BOOKING'] as $key=>$val) {
				$booking->$key = $val;
								
			}
		}
		
		// update meta data (NinjaForms data pairs via admin label)
		if (isset($dataset['BOOKING_META'])) { 
			foreach ($dataset['BOOKING_META'] as $key=>$val) {
				$booking->booking_meta[$key] = $val;
			}
		}
		// set sugar ID
		$booking->booking_meta['sugar_id'] = $crmData['id'];
		
		// save booking
		return $booking->save(FALSE); //NO EMAIL!
	}

	/**
	 * Private function to update the userdata within SugarCRM
	 * @param string $userID (UUID)
	 * @param array $wordpressData
	 * @return boolean
	 */
	private function update_sugar_user_data($userID, $wordpressData) {
		$dataset = $this->parse_crm_and_wordpress_data($wordpressData, array(), "profile", FROM_WORDPRESS_TO_CRM);
		
		// update standard sugar profile data
		if (isset($dataset['Contacts'])) { // UserMeta
						
			$data = array();
			array_push($data, array(
					'name' => 'id',
					'value' => $userID
				));
			foreach ($dataset['Contacts'] as $key=>$val) {
				array_push($data, array(
					'name' => $key,
					'value' => $val
				));
			}
			
			// update user command
			$done = $this->sugar->updateContact($data);
			$error = $error + $done;
		}
		
		// TODO: add error handling!
		return true;
	}
	
	
	/**
	 * Create OR Update a AIA_Ministry_deelname entity in SugarCRM
	 * @param \EM_Booking $booking
	 * @param bool $create (default TRUE)
	 * @param bool $force (default FALSE) Force insert a contact into sugar if not existing
	 * @global wpdb $wpdb
	 * @return \EM_Booking Or False on Failure
	 */
	public function sugar_update_aia_ministry_deelname ($booking, $create = TRUE, $force = FALSE) {
		global $wpdb;
		
		if (!isset($booking->booking_meta['sugar_id']) || strlen($booking->booking_meta['sugar_id']) < 10 ) {
			// create..
			$create = TRUE;
		} else {
			$create = FALSE;
		}
		
		//$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
	
		// fill the dataset with current booking info
		$dataset = [];
		foreach ($booking->fields as $field) {
			if (!empty($booking->{$field['name']})) 
				$dataset ['EM::BOOKING::' . $field['name'] ] = $booking->{$field['name']};
		}
		foreach ($booking->booking_meta as $field=>$val) {
			if (!empty($val)) 
				$dataset ['EM::BOOKING_META::' . $field ] = $val;
		}
	
		// get sugar id for contact and event
		$event_id = $booking->event->event_attributes['sugar_id'];
		$user_id = get_user_meta($booking->person_id,'sugar_id', TRUE);	// single value
		
		$this->create_sugar_client();
		// Modification: if user_id not existing, but forced to insert (this method is default called when accepting a reservation
		if (!$user_id && $force) {
			// inserting user first
			$user = get_user_by('id', $booking->person_id);
			$user_id = $this->sugar_create_contact($user);
			
			// if succesful insert, update the crm_insert table
			if ($user_id) {
				$wpdb->query(sprintf('UPDATE %smy_aia_crm_sync SET crm_id = "%s" WHERE wp_id=%s', $wpdb->prefix, $user_id, $user->ID));
			}
		}
		
		if (!$event_id || !$user_id || strlen($event_id) < 10 || strlen($user_id) < 10) 
			return FALSE;
		
		$dataset['EM::BOOKING::event_id'] = $event_id;
		$dataset['UserMeta::sugar_id::sugar_id'] = $user_id;
		
		// format the insert data
		$sugar_data = $this->parse_crm_and_wordpress_data($dataset, $sugar_data, 'registration', FROM_WORDPRESS_TO_CRM);
		// user full name in buddypress
		$sugar_data['AIA_ministry_deelnames']['contact_name'] = xprofile_get_field_data(1, $booking->person_id);
		$sugar_data['AIA_ministry_deelnames']['aia_ministry_project_id_c'] = $event_id; // make sure event is set
		$sugar_data['AIA_ministry_deelnames']['aia_ministry_project_name'] = $booking->event->post_title;
		
		
		$formated_sugar_data = $this->from_array_key_value_to_array_name_value_list(
				$sugar_data['AIA_ministry_deelnames'],
				$create ? array() : array(array('name'=> 'id', 'value'=>$booking->booking_meta['sugar_id']))
		);
	
		// finally try and insert to sugar
		//$this->create_sugar_client();
		if ($id = $this->sugar->updateDeelname($formated_sugar_data)) {
			// update sugar id and save booking
			$booking->booking_meta['sugar_id'] = $id;
			$booking->save(FALSE);
			return TRUE;
		}
		
		return FALSE; // fail!
	}
	
	/**
	 * Delete a AIA_Ministry_deelname entity in SugarCRM
	 * @param \EM_Booking $booking
	 * @return boolean
	 */
	public function sugar_remove_aia_ministry_deelname ($booking) {
		if (!isset($booking->booking_meta['sugar_id']) || strlen($booking->booking_meta['sugar_id']) < 10 ) {
			return TRUE;	// nothing to remove, succes!
		} else {
			$create = FALSE;
		}
		
		// set data
		$formated_sugar_data = [
			['name'=> 'id', 'value'=>$booking->booking_meta['sugar_id']],
			['name'=> 'deleted', 'value'=>1]				
		];
		
		// finally try and insert to sugar
		$this->create_sugar_client();
		if ($id = $this->sugar->updateDeelname($formated_sugar_data)) {
			// update sugar id and save booking
			$booking->booking_meta['sugar_id'] = NULL;
			$booking->save(FALSE);
			return TRUE;
		}
		
		return FALSE; // fail!
	}
	
	
	
	/**
	 * Uses EM_Meta Table to add meta
	 * @param string	$key Meta Key name
	 * @param mixed		$object_id Meta Object
	 * @param boolean	$first  (default: TRUE) only return one element
	 * @global \WPDB	$wpdb
	 * @return corresponding ID / True
	 */
	function get_meta_value($key, $object_id, $first=TRUE){
		global $wpdb;

		
		// get meta keys
		$rows = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='{$key}' AND object_id ='{$id}'", ARRAY_A);
		
		if (empty($rows) || count($rows)<1) 
			return FALSE;
		
		$return = array();
	
		foreach($rows as $row) {
			if ($first) return maybe_unserialize($row['meta_value']);
			$return[] = maybe_unserialize($row['meta_value']);
		}
		
		return $return;
	}
	
	/**
	 * Uses EM_Meta Table to retrieve meta object_id by searching meta_value
	 * @param string $key Meta Key
	 * @param mixed $value Meta Value
	 * @param boolean $first  (default: TRUE) only return one element
	 * @global \WPDB $wpdb
	 * @return corresponding ID / True
	 */
	function get_meta_object_id($key, $value, $first=TRUE){
		global $wpdb;

		
		// get meta keys
		$rows = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='{$key}' AND meta_value ='{$value}'", ARRAY_A);
		
		if (empty($rows) || count($rows)<1) 
			return FALSE;
		
		$return = array();
	
		foreach($rows as $row) {
			if ($first) return maybe_unserialize($row['object_id']);
			$return[] = maybe_unserialize($row['object_id']);
		}
		
		return $return;
	}
	
	/**
	 * Set Meta value (Insert Only!)
	 * @param type $key
	 * @param type $object_id
	 * @param type $value
	 * @global \wpdb $wpdb
	 * @return type
	 */
	function set_meta($key, $object_id, $value) {
		global $wpdb;
		
		return $wpdb->insert(EM_META_TABLE, array('object_id'=>$object_id, 'meta_key'=>$key, 'meta_value'=> maybe_serialize($value)), array('%d','%s','%s'));
	}
	
	/**
	 * Get Syc dates from WP_option which corresponding functions should sync,
	 * or the default:
	 *		'sync_profiles_sugar_to_wordpress'		=> '2000-01-01',
			'sync_events_sugar_to_wordpress'		=> '2000-01-01',
			'sync_registrations_sugar_to_wordpress'	=> '2000-01-01',
	 * @return array
	 */
	private function get_sync_dates() {
		$default_sync_dates = array(
			'sync_profiles_sugar_to_wordpress'		=> array('date'=>'2000-01-01 00:00:00','offset'=>0),
			'sync_events_sugar_to_wordpress'		=> array('date'=>'2000-01-01 00:00:00','offset'=>0),
			'sync_registrations_sugar_to_wordpress'	=> array('date'=>'2000-01-01 00:00:00','offset'=>0),
			'sync_events_wordpress_to_sugar'		=> array('date'=>'2012-01-01 00:00:00','offset'=>0),
			'sync_registrations_wordpress_to_sugar'	=> array('date'=>'2012-01-01 00:00:00','offset'=>0),
			'sync_profiles_wordpress_to_sugar'		=> array('date'=>'2012-01-01 00:00:00','offset'=>0)
		);
		
		$dates = get_option(MY_AIA_SYNC_DATES, $default_sync_dates);
		
		foreach ($default_sync_dates as $key=>$date) {
			if (!isset($dates[$key]['date']) || !is_numeric(strtotime($dates[$key]['date']))) $dates[$key]['date'] = $default_sync_dates[$key];
		}
		
		return $dates;
	}
	
	/**
	 * Set WP_option for sync dates
	 * @param string $key
	 * @param string $value date-time readable by strtotime
	 */
	private function set_sync_dates($key, $date=NULL, $offset = 0) {
		if (empty($key) || empty($date)) return FALSE;
		
		// use variabele to set dates
		if (empty($this->sync_dates)) {
			$this->sync_dates = $this->get_sync_dates();
		}
		
		if (!array_key_exists($key, $this->sync_dates)) return FALSE; // Not a valid key to set
		
		// set value & update wp_option
		$this->sync_dates[$key]['date'] = $date;		
		$this->sync_dates[$key]['offset'] = $offset;		
		return update_option(MY_AIA_SYNC_DATES, $this->sync_dates, FALSE);
	}
	
	/**
	 * Check if script execution is longer than $max_length, saves and breaks;
	 * @param int $max_length (default 25) including error margin
	 */
	private function get_elapsed_time_and_break($param_name, $date, $max_length = 25) {
		if (!$this->start_time || $this->start_time<1) {
			$this->start_time = time();
			return true; // no break;
		}
		
		// time set, check elapsed time
		if ((time() - $this->start_time) > $max_length) {
			$this->set_sync_dates($param_name, $date);	// save current state
			$this->send_json_response();				// exit
		}		
	}
	
	/**
	 * Send JSON response. Called from do_sync and to quit script execution
	 * @param $data	(optional) send custom data
	 */
	private function send_json_response($data = NULL) {
		if ($data == NULL)	wp_send_json($this->sync_dates +  array('count' => 1));
		else				wp_send_json($data);
		wp_die();
	}
	
	
	/**
	 * Obtains a numeric project code: <country><year>-<number>
	 * for example NL16-01
	 * 
	 * @param array $sugar_event
	 * @return string
	 */
	private function get_projectcode($sugar_event) {
		// uses data: begindatum, land
		
		$_code = sprintf('%s%s-00', ISO3166::from_3to2_characters($sugar_event['land']), date('y', strtotime($sugar_event['begindatum'])));
		
				
		return $_code;
	}
	
	
	
	
	/**
	 * Dump Log 
	 * @param type $message
	 * @param type $var_to_export
	 */
	private function write_log($message, $var_to_export=NULL) {
		$fname = WP_CONTENT_DIR . '/synclog.txt';
		
		$log = fopen($fname,'a');
		fwrite($log,  sprintf("%s: %s\n", date('Y-m-d H:i:s'), $message)); 
		
		if (!empty($var_to_export)) {
			fwrite($log,  sprintf("---- DEBUG: --- \n")); 
			fwrite($log, var_export($var_to_export, true)); 
			fwrite($log,  sprintf("----        --- \n")); 
		}
		
		fclose($fname);
	}
	
}