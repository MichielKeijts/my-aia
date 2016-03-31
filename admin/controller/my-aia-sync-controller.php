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


/**
 * Description of my-aia-sync-controller
 * Sync controller handles the incoming and outgoing request for the CRM-MY_AIA
 * Synchronisation. This only deals 
 * @author Michiel
 */
class MY_AIA_SYNC_CONTROLLER extends MY_AIA_APP_CONTROLLER {

	public $classname = 'sync';
	
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
		foreach ($fields as $field) $internal_fields[] = 'WP::'.$field.'::'.$field;
		foreach ($buddypress_fields as $id=>$field) $internal_fields[] = 'BuddyPress::' . $id . '::' . $field;
		
		
		// get EXTERNAL (sugarcrm) fields
		$sugar_fields_string = file_get_contents(MY_AIA_PLUGIN_DIR . 'includes/definitions/my-aia-sugacrm-contact-fields.txt');
		$sugar_fields = explode("\n",$sugar_fields_string);
	
		$this->set('internal_fields', $internal_fields);
		$this->set('external_fields', $sugar_fields);
		$this->set('data',	get_option('my_aia_profile_sync', array()));
	}
	
	/**
	 * function use to update the profile fields sync with sugar
	 */
	public function edit_events() {
		$this->set('data',	get_option('my_aia_event_sync', array()));
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
			switch ($_POST['type']) {
				case 'profile_sync':
					update_option('my_aia_profile_sync', $_POST['data']);
					break;
				case 'event_sync':
					update_option('my_aia_event_sync', $_POST['data']);
					break;
				case 'registration_sync':
					update_option('my_aia_registration_sync', $_POST['data']);
					break;
			}
		}
	}
}
