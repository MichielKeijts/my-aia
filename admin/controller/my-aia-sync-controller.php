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

include_once(MY_AIA_PLUGIN_DIR . 'classes/crmsync/class_soap_sugar.php');
include_once(MY_AIA_PLUGIN_DIR . 'classes/crmsync/class_conversion_helper.php');

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
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'includes/definitions/my-aia-sugacrm-event-fields.txt');
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
		
		
		// get all booking meta fields --> NinjaForms Fields (admin label)
		// get all fields, with an admin label not empty. Such fields can be
		// used for the sync to CRM
		$nf_fields = ninja_forms_get_all_fields();
		
		$internal_fields = array();
		// create form: <TYPE>::<ID>::<READABLE NAME>
		foreach (em_get_booking()->fields as $field=>$val) $internal_fields[] = 'EM::BOOKING::' . $field;	// EM_Booking fields
		foreach ($nf_fields as $field) {
			if (!empty($field['data']['admin_label'])) {
				$internal_fields[] = 'EM::BOOKING_META::' . $field['data']['admin_label'];	// EM_Event fields
			}
		}
	
		// get EXTERNAL (sugarcrm) fields
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'includes/definitions/my-aia-sugacrm-registration-fields.txt');
		$sugar_fields = explode("\r\n",$sugar_fields_string);
	
		$this->set('internal_fields', $internal_fields);
		$this->set('external_fields', $sugar_fields);
		
		
		$this->set('data',	get_option('my_aia_registration_sync', array()));
				
		$this->set('sync_type', 'registration');
		$this->render('edit_sync_rules');
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
	 * Actual function to sync data. 
	 * This function should be called to start the sync process
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
		
		// update Wordpress with Sugar Registration Data
		$this->sync_registrations_sugar_to_wordpress();
		
		//$this->set_meta('booking-sugar', 10, "bladiebla");
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

			// step through Sugar Events
			foreach ($items as $sugar_event) {
				if (empty($sugar_event['projectcode']) || empty($sugar_event['titel'])) continue;

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
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$offset += count($items); // increase by number of restults
		}
	
		return true;
	}
	
	/**
	 * Function to update or insert EM_Bookings with the SugarCRM aia_ministry_deelnames
	 * data. This uses the sync columns
	 * @param type $fromdate
	 * @param type $create
	 * @return boolean
	 */
	private function sync_registrations_sugar_to_wordpress($fromdate='2015-01-01', $create=TRUE) {
/*
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
		$offset = 0;
		while ($items_found && $offset<=100 ) { //TEMP !! FOR DEBUG!!
			// retrieve list of contacts from date (incremental)
			// manyware_aiarelatie = 1 (!!)
			$items = $this->sugar->searchCommon(
					"AIA_ministry_deelnames.contact_id_c <>'' AND UNIX_TIMESTAMP(AIA_ministry_deelnames.date_modified) > ".  strtotime($fromdate),
					"AIA_ministry_deelnames",
					$num_items_per_query,
					$offset
				);
			
			// step through Sugar Events
			foreach ($items as $sugar_registration) {
				if (empty($sugar_registration['aia_ministry_project_id_c']) || empty($sugar_registration['contact_id_c'])) continue;

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
				if (!$events) continue;	 //STOP!

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
								$booking = new EM_Booking();
								$booking->event_id = $eventID;
								$booking->person_id = $userID;
								$booking->save();
							} else {
								$booking = new EM_Booking($bookingID);
							}
							// set $event and $user data
							$booking->event_id = $eventID;
							$booking->person_id = $userID;
							
							// other metadata (and sugarID normally..)
							// saving is also done in update function !
							$this->update_wordpress_registration_data($booking, $sugar_registration,$ticketID);	
						}
					}
				} else {
					//@TODO error: no event found.. or could not create new one..
				}
			}
			
			$items_found = !empty($items) && count($items[0])>1; // not empty! returns empty array if empty
			$offset += count($items); // increase by number of restults
		}		
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
					$from_field = $from_def[2];
					$to_field = $to_def[2];

					// check for existence
					if (array_key_exists($from_field, $from_data)) {
						// form:	$to_data['BP'][id] = val
						// or:		$to_data['WP'][name] = val
						
						// if a CHILD class
						if ($to_def[1] != $to_def[2]) {
							if (!is_array($to_data[$to_def[1]])) $to_data[$to_def[1]] = array();
							$to_data[ $to_def[1] ][ $to_def[2] ] = ConversionHelper::from_sugar($from_field, $from_data);
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
					
					// check for existence
					if (array_key_exists($from_field, $from_data)) {
						// form:	$to_data['BP'][id] = val
						// or:		$to_data['WP'][name] = val
						
						if (!is_array($to_data[$to_def[0]])) $to_data[$to_def[0]] = array();
						$to_data[ $to_def[0] ][ $to_def[1] ] = ConversionHelper::from_wordpress($from_field, $from_data);
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
						if (strlen($dataset['EM']['location_country'])>2) 
							$dataset['EM']['location_country'] = ISO3166::from_3to2_characters($dataset['EM']['location_country']);
						
						$dataset['EM']['location_country'] = empty($dataset['EM']['location_country']) ? "NL":$dataset['EM']['location_country'];
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
								$location->location_country = $results['country'];
								$location->location_latitude = $results['latitude'];
								$location->location_longitude = $results['longitude'];
								$location->location_address = $results['route'];
								$location->location_country = $results['country'];
								$location->location_town = $results['locality'];
								$location->location_postcode = $results['postal_code'];
								$location->location_status = 1; //found
								
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
		
		// save booking
		return $booking->save(FALSE); //NO EMAIL!
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
	
}

