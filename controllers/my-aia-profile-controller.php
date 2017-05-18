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
 * Description of my-aia-page-controller
 *
 * @author Michiel
 */
class MY_AIA_PROFILE_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'profile';
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_hook_save', array($this, 'hook_save'),1);	
		add_action( 'wp_ajax_my_aia_admin_profile_order_save', array($this, 'profile_order_save'),1);	
		
		add_action( 'wp_ajax_my_aia_admin_static_condition_load', array($this, 'static_condition_load'),1);	
	}
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {		
		parent::before_render();
		
		// extra script
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
		
		// setting the menu bar for this controller
		$menu_bar = array(
			'index' => __('Profiel Wijzigingen'),
			'reserveringen' => __('Reserveringen'),
		);
		
		$this->set('menu_bar', $menu_bar);
	}
	
	/** 
	 * Main index. Overview of the Processflow admin
	 */
	public function add() {
		$this->set('title', __('Hook Toevoegen','my-aia'));
		
		// make subset of filters to be defined as hook
		$hooks=array();
		foreach ($GLOBALS['wp_filter'] as $filter=>$val) {
			if (strpos($filter, 'save')===FALSE)
				continue;
			$hooks[$filter]=$filter;
		}	
		
		$this->view->set_flash('Dit is een belangrijk bericht!');
		$this->set('hooks', $hooks);
		$this->set('data', array('id'=>  uniqid(),'hook_name'=>'save_post'));
		// only a placeholder..
		//$this->template='index';
		$this->template = 'edit';
	}
	
	/**
	 * Overview of the current 
	 * @global \wpdb $wpdb
	 */
	public function index(){
		global $wpdb;
		$this->set('title', __('Overzicht van alle wijzigingen m.b.t profielen','my-aia'));
		
		$this->parse_approved_by_post_data();
		
		// get the modification things
		$query = sprintf("SELECT * FROM %smy_aia_crm_sync WHERE 1=1 %s", $wpdb->prefix, $this->get_filter());
		$results = $wpdb->get_results($query, OBJECT);
		
		
		

		
		// fill hooks with description data
		if (!empty($results)) {
			foreach ($results as &$result) {
				$result->approved_by_name = get_user_by('ID',$result->approved_by)->display_name;
				$result->readable_from = $this->get_readable_fields($result->old_values);
				$result->readable_to = $this->get_readable_fields($result->new_values);
			}
		}
		$this->view->set('data', array('results'=>$results));
	}
	
	/**
	 * Handles the dealing with post data, approved ID's for the aia_crm_sync
	 * This means: approve_id_<id> isset and value is 1
	 * otherwise 0 is returned
	 */
	private function parse_approved_by_post_data () {
		foreach ($_POST as $key=>$value) {
			if (preg_match("/^approve_id_([0-9]+)$/", $key, $matches) == 1 && count($matches) >1) {
				// set ID for approval
				$id = $matches[1];
				$approve = filter_input(INPUT_POST, 'approve_id_' . $id);
				if ($approve == 1) {
					$this->approve_id($id);
				} else {
					$this->disapprove_id($id);
				}
			}			
		}
	}
	
	/**
	 * Disprove ID $id and set data back to the database
	 * @param int $id
	 * @global \wpdb $wpdb
	 */
	private function disapprove_id($id=0) {
		global $wpdb;
		// get the modification things
		$query = sprintf("SELECT * FROM %smy_aia_crm_sync WHERE 1=1 AND (id = %d) %s", $wpdb->prefix, (int) $id, $this->get_filter());
		$results = $wpdb->get_results($query, OBJECT);
		
		// results
		if (count($results) != 1) return FALSE; // should only be one row with ID
		
		// result is array (0)
		$result = $results[0];
		
		// parse the result and update data
		$mixed = maybe_unserialize($result->old_values);
			
		// Only have implementation for BuddyPress yet
		foreach ($mixed['BuddyPress'] as $key=>$value) {
			// update data. Make sure not again a entity in my_aia_crm_sync is placed ... !!
			xprofile_set_field_data($key, $result->wp_id, $value);
		}
		
		
		
		// update the my_aia_crm_sync d
		$query = sprintf("UPDATE %smy_aia_crm_sync SET approved = 0, done = 1, approved_by = %d, modified = NOW() WHERE id=%d LIMIT 1", $wpdb->prefix, get_current_user_id(), (int)$id);
		return $wpdb->query($query);
	}
	
	/**
	 * Approve the ID by updating database value
	 * @param int $id
	 * @return int|FALSE 
	 * @global \wpdb $wpdb
	 */
	private function approve_id($id=0) {
		global $wpdb;
		$query = sprintf("UPDATE %smy_aia_crm_sync SET approved = 1, approved_by = %d, modified = NOW() WHERE id=%d LIMIT 1", $wpdb->prefix, get_current_user_id(), (int)$id);
		return $wpdb->query($query);
	}
	
	/**
	 * Get readable fields. 
	 * $mixed = array ('BuddyPres'=>array(<field_id> => <field_value)))
	 * @param SERIALIZED String | OBJECT $mixed
	 * @param bool $table_layout parse as <table>..</table>
	 */
	private function get_readable_fields($mixed, $table_layout = true) {
		$mixed = maybe_unserialize($mixed);
			
		$output = $table_layout ? "<table><thead><tr><th>Key</th><td>Value</td></tr></thead>" : "";
		// Only have implementation for BuddyPress yet
		foreach ($mixed['BuddyPress'] as $key=>$value) {
			$field_name = xprofile_get_field($key)->name; // assume field exists!!
						
			if ($table_layout) 
				$output = sprintf('%s<tr><th>%s</th><td>%s</td></tr>',$output, $field_name, $value);
			else 
				$output = sprintf('%s, %s:%s',$output,$field_name, $value);
			
		}
		
		$output = $table_layout ? sprintf('%s</table>', $output) : $output;
		return $output;
	}
	
	
	/**
	 * Parses the filter params if set
	 * @return string WHERE clause, starting with AND 
	 */
	private function get_filter() {
		return "AND (approved + done < 1)";
	}


	/**
	 * Overview of the actual registered hooks in the plugin. This means that the
	 * hooks are editable using the plugin and are therefore flexible. 
	 * This function returns a list of the possible hooks
	 */
	public function edit() {
		if (isset($_REQUEST['id'])) {
			$this->set('title', __('Hook Aanpassen','my-aia'));

			// make subset of filters to be defined as hook
			$hooks=array();
			foreach ($GLOBALS['wp_filter'] as $filter=>$val) {
				if (strpos($filter, 'save')===FALSE)
					continue;
				$hooks[$filter]=$filter;
			}	
			
			$this->view->set('data', array('hooks'=>get_option('my-aia-registered-hooks',array())));

			// get data
			$data = get_option('my-aia-registered-hook-'.$_REQUEST['id'], false);
			if ($data) {	
				$this->view->set('data',$data);
				
				$this->view->set_flash('Dit is een belangrijk bericht!');
				$this->set('hooks', $hooks);
			} else {
				$this->view->set_flash('Kan ID niet vinden in de database!');
			}
		} else {
			// cannot find the hook
			$this->view->set_flash('Geen ID meegegeven!');
			return $this->add();
		}
		$this->template = 'edit';
	}
	
	/**
	 * Load the static condition
	 */
	public function static_condition_load() {
		if (isset($_REQUEST['id'])) {
			$json_data = MY_AIA_PPROFILE::get_static_condition($_REQUEST['id']);
			
			if (!empty($json_data)) 
				wp_send_json_success($data);
			else 
				wp_send_json_error($data);
		}
	}
	
	/**
	 * Condition save by ajax call
	 * We save the id in option_name my-aia-hook-id
	 */
	public function hook_save() {
		if (isset($_REQUEST['id'])) {
			// first add hook to the hook list in wordpress
			$hooks = get_option('my-aia-registered-hooks', array());
			
			if (!isset($_REQUEST['hook_name'])) $hooks[$_REQUEST['hook_name']]=array();
			
			$hooks[$_REQUEST['hook_name']][]=$_REQUEST['id'];
			// see if the order is set as well
			/*if (isset($_REQUEST['hook_order'])) {
				// first remove old hook entry
				foreach ($hooks[$_REQUEST['hook_name']] as $key=>$hook) {
					if ($hook==$_REQUEST['hook_id']) 
						unset($hooks[$_REQUEST['hook_name']][$key]);
				}
				
				//update_hooks to right order
				$hooks[$_REQUEST['hook_name']][$_REQUEST['hook_order']]=TRUE;
				
			} else {
				// add hooks
				$hooks[$_REQUEST['hook_name']][]=TRUE;
			}*/
			
			// save option. Option init is done in plugin installation
			update_option('my-aia-registered-hooks', $hooks);
			
			
			if (MY_AIA_PPROFILE::save_condition($_REQUEST['id'], $_REQUEST['hook_name'], $_REQUEST['description'], $_REQUEST['data'])) {
				wp_send_json_success();		
			}
		}
		wp_send_json_error();
	}
	
	/**
	 * Save the order of the profiles. They can be reordered by drag and drop
	 * on the admin interface.
	 * Save by Ajax Call
	 * 
	 * @retun string json
	 */
	public function profile_order_save() {
		if (isset($_POST['hook_name'])) {
			// get the option data
			
			$hook_info = get_option(MY_AIA_REGISTERED_HOOKS, false);
			
			if (!$hook_info || isset($hook_info[$_POST['hook_name']]) || empty($hook_info[$_POST['hook_name']])) {
				// cannot happen, but send json_error
				$msg=__('Cannot find hooks under the supplied condition');
				wp_send_json_error($msg);
			}
			
			$hook_info[$_POST['hook_name']] = array();
			
			// update hooks
			foreach ($_POST['data'] as $id) {
				$hook_info[$hook_name][] = $id; // set ID				
			}
			
			update_option(MY_AIA_REGISTERED_HOOKS, $hook_info);
			
			wp_send_json_success();		
		}
		wp_send_json_error();
	}
}
