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
class MY_AIA_ROLE_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'role';
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_get_roles', array($this, 'get_roles'),1);	
		add_action( 'wp_ajax_my_aia_admin_create_role', array($this, 'create_role'),1);	
		add_action( 'wp_ajax_my_aia_admin_delete_role', array($this, 'delete_role'),1);	
		//add_action( 'wp_ajax_my_aia_admin_profile_order_save', array($this, 'profile_order_save'),1);	
		
		//add_action( 'wp_ajax_my_aia_admin_static_condition_load', array($this, 'static_condition_load'),1);	
	}
	
		/**
	 * Save the order of the processflows. They can be reordered by drag and drop
	 * on the admin interface.
	 * Save by Ajax Call
	 * 
	 * @retun string json
	 */
	public function get_roles() {
		global $wpdb;
		if ($term = filter_input(INPUT_GET, 'term')) {
				// check if we need to add 
				$results = array();
				
				// find groups
				$bp_group = new BP_Groups_Group();
				$groups = $bp_group->get(array('name'=>  filter_input(INPUT_GET, 'term')));
				if (count($groups['groups']) > 0) {
					foreach ($groups['groups'] as $group) {
						$result        = new stdClass();
						$result->id    = $group->id;
						$result->value  = $group->id;
						$result->label  = $group->name;
						$result->type  = 'group';
						array_push($results, $result);
					}
				}
				
				
				// find members
				$user_query = array(
					'type'            => 'alphabetical',
					'page'            => 1,
					'search_terms'    => filter_input(INPUT_GET, 'term')
				);
				
				$user_query = new BP_User_Query( $user_query );
				foreach ( $user_query->results as $user ) {
					$result        = new stdClass();
					$result->id    = $user->ID;
					$result->value  = $user->ID;
					$result->label  = bp_core_get_user_displayname( $user->ID );
					$result->type  = 'member';

					$results[] = $result;
				}
					
				
		
				echo json_encode($results);
				wp_die();
			
			
			wp_send_json_success();		
		}
		wp_send_json_error();
	}
	
	/**
	 * Create a role in the database
	 * @global \wpdb $wpdb
	 */
	public function create_role() {
		global $wpdb;
		
		if (!current_user_can('edit_user')) {
			wp_send_json_error();
			
		}
		$post_id = filter_input(INPUT_POST, 'post_id');
		$type = filter_input(INPUT_POST, 'type');
		$id = filter_input(INPUT_POST, 'id');
		
		if ($post_id && $type && $id) {
			$wpdb->query(sprintf('REPLACE INTO %s%s (post_id, `type`, id) VALUES (%d, "%s", %d)', $wpdb->prefix, MY_AIA_TABLE_ROLES, $post_id, $type, $id));
			wp_send_json_success();
		} 
		wp_send_json_error();
	}
	
	/**
	 * Create a role in the database
	 * @global \wpdb $wpdb
	 */
	public function delete_role() {
		global $wpdb;
		
		if (!current_user_can('edit_user')) {
			wp_send_json_error();
			
		}
		$post_id = filter_input(INPUT_POST, 'post_id');
		$type = filter_input(INPUT_POST, 'type');
		$id = filter_input(INPUT_POST, 'id');
		
		if ($post_id && $type && $id) {
			$wpdb->query(sprintf('DELETE FROM %s%s WHERE post_id = %d AND `type` = "%s" AND id = %d', $wpdb->prefix, MY_AIA_TABLE_ROLES, $post_id, $type, $id));
			wp_send_json_success();
		} 
		wp_send_json_error();
	}
	
	/**
	 * Get the roles corresponding to the postID
	 * @global wpdb $wpdb
	 * @param int $postID
	 * @return array roles
	 */
	public function get_post_roles ($postID) {
		global $wpdb;
		
		// get roles
		$roles = $wpdb->get_results(sprintf('SELECT * FROM %s%s WHERE post_id = %d', $wpdb->prefix, MY_AIA_TABLE_ROLES, $postID));
		
		if ($roles) return $roles;
		
		// always return array
		return array();
	}
	
	/**
	 * returns the name of the object described in Role
	 * @param object $role
	 * @return string
	 */
	public function get_role_name($role) {
		if ($role->type == 'member') 
			return bp_core_get_user_displayname($role->id);
		
		$bp_group = new BP_Groups_Group($role->id);
		return $bp_group->name;
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
	
}
