<?php
/**
 * MY_AIA addon to BuddyPress
 * 
 * BuddyPress XProfile change moderation.
 *
 * @package MY_AIA
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// include



/**
 * Moderation functions for the XProfile changes a user can do.
 */
class MY_AIA_XPROFILE_CHANGE_MODERATE {
	
	static $is_admin = NULL;
	static $is_employee = NULL;
	
	/**
	 * including BuddyPress classes if the admin interface is loaded. Normally,
	 * the admin interface lazy loads BuddyPress
	 */
	static function include_bp_classes() {
		include_once MY_AIA_PLUGIN_DIR . '../buddypress/bp-xprofile/bp-xprofile-functions.php';
		include_once MY_AIA_PLUGIN_DIR . '../buddypress/bp-xprofile/classes/class-bp-xprofile-profiledata.php';
	}
	
	/**
	 * Save Changes before XProfile is saved.
	 */
	static function xprofile_before_save() {
		
		//if ($self::is_admin() || self::is_employee()) return TRUE; // DONE
		
		//self::include_bp_classes();
		// save modifications
		// Only save if there are field ID's being posted.
		if ( ! empty( $_POST['field_ids'] ) ) {

			// Get the POST'ed field ID's.
			$posted_field_ids = explode( ',', $_POST['field_ids'] );

			// Backward compatibility: a bug in BP 2.0 caused only a single
			// group's field IDs to be submitted. Look for values submitted
			// in the POST request that may not appear in 'field_ids', and
			// add them to the list of IDs to save.
			$data = Array();
			foreach ( $_POST as $posted_key => $posted_value ) {
				preg_match( '/^field_([0-9]+)$/', $posted_key, $matches );
				if ( ! empty( $matches[1] ) && in_array( $matches[1], $posted_field_ids ) ) {
					$key =  $matches[1];
					$value  = filter_input(INPUT_POST, 'field_' . $key);
					
					$data[$key] = $value;
				}
			}
	
			//self::save_xprofile_changes($data, bp_displayed_user_id());
			self::save_xprofile_changes($data, 1);
		}
	}
	
	/**
	 * 
	 * @global WP_User $current_user
	 * @return type
	 */
	static function is_admin() {
		if (is_bool(self::$is_admin)) return self::$is_admin;
		
		
		global $current_user;
		
		// get user info
		self::$is_admin = (bool) $current_user->has_cap("") == TRUE;
		
		return self::$is_admin;
	}
	
	/**
	 * Update the profile changes of the current user.
	 * @param array array($key => $value) for field_id and its new value
	 * @param int $user_id ID of the xprofile user
	 */
	static function save_xprofile_changes($data, $user_id) {
		$data_to_moderate	= array('BuddyPress'=>array());
		$old_data			= array('BuddyPress'=>array());
		foreach ($data as $key=>$value) {
			// first get old data
			$old_value = self::xprofile_get_field_data($key, $user_id);
			
			// check if changed
			if ($old_value != $value) {
				// save into update field
				$data_to_moderate	['BuddyPress'][$key] = $value;
				$old_data			['BuddyPress'][$key] = $old_value;
			}
		}
		
		// check if something is to be moderated..
		if (!empty($data_to_moderate)) {
			self::insert_row($user_id, $old_data, $data_to_moderate);
		}
		
		
		return TRUE;
	}
	
	/**
	 * 
	 * @global \wpdb $wpdb
	 * @param int $wp_id
	 * @param array $old_values
	 * @param array $new_values
	 * @param array $options override defaults
	 * $default = array(
			`id`			=>	NULL, 
			`crm_id`		=>	NULL,
			`approved`		=>	FALSE, 
			`approved_by`	=>	NULL, 
			`done`			=>	FALSE, 
			`from_object`	=>	'BuddyPress',
			`to_object`		=>	'Contacts',
			`fields`		=>	array()
			 `modifed`		=>	date('Y-m-d H:i:s')
			 `created`		=>	date('Y-m-d H:i:s')
		);
	 * @return boolean
	 */
	static function insert_row($wp_id, $old_values, $new_values, $options = Array()) {
		global $wpdb;
		
		$default = array(
			'id'			=>	NULL, 
			'crm_id'		=>	NULL,
			'wp_id'			=>	$wp_id,
			'approved'		=>	FALSE, 
			'approved_by'	=>	NULL, 
			'done'			=>	FALSE, 
			'from_object'	=>	'BuddyPress',
			'to_object'		=>	'Contacts',
			'fields'		=>	array(),
			'modifed'		=>	date('Y-m-d H:i:s'),
			'created'		=>	date('Y-m-d H:i:s')
		);
		$values = array_merge($options, $default);	// set default options
		
		// check if necessary parameters are set
		if (!	(is_numeric($wp_id) && self::get_user_by("ID", $wp_id))	) {
			// NO ID set or found in database
			return FALSE;
		}
		
		// get current CRM_ID
		if ($values['from_object'] == 'BuddyPress') {
			$id = get_user_meta($wp_id, 'sugar_id', TRUE);
			if (!$id) {
				// NO sugar ID found, we do not create either.
				return FALSE;
			}
			$values['crm_id'] = $id;
		}
		
		// set value (serialized)
		$values['old_values']	= maybe_serialize($old_values);
		$values['new_values']	= maybe_serialize($new_values);
		$values['fields']		= maybe_serialize(array_keys($new_values));
		
		$wpdb->insert(
				$wpdb->prefix . 'my_aia_crm_sync',
				$values
		);
	}
	
	/**
	 * Copy From pluggable.php
	 * @param type $field
	 * @param type $value
	 * @return boolean|\WP_User
	 */
	static function get_user_by( $field, $value ) {
		$userdata = WP_User::get_data_by( $field, $value );

		if ( !$userdata )
			return false;

		$user = new WP_User;
		$user->init( $userdata );

		return $user;
	}
	
	/**
	 * Static function to get the Field Data without Loading BuddyPress class
	 * @global \wpdb $wpdb
	 * @param type $key
	 * @param type $user_id
	 */
	static function xprofile_get_field_data($key, $user_id) {
		global $wpdb;
		$queried_data = $wpdb->get_results( $wpdb->prepare( "SELECT id, user_id, field_id, value, last_updated FROM ".$wpdb->prefix.BUDDYPRESS_TABLE_NAME_DATA." WHERE field_id = %d AND user_id IN ({$user_id})", $key ) );
		
		if ($queried_data && is_array($queried_data)) 
			return $queried_data[0]->value;
		
		return FALSE;
	}
}