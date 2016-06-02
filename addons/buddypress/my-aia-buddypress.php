<?php

/* 
 * The file containing all BuddyPress addons for the admin interface
 * part of MY-AIA
 * 
 * (C) Michiel Keijts, 2016
 */

include_once 'classes/my-aia-xprofile-change-moderate.php';

// add hooks
add_action( 'xprofile_updated_profile',		'my_aia_xprofile_sync_wp_profile'	, 99, 1);			// update user_date with xprofile data
add_action( 'profile_update',				'my_aia_wp_profile_sync_xprofile'	, 99, 2 );			// update xprofile with user_data
add_action( 'profile_update',				MY_AIA_XPROFILE_CHANGE_MODERATE::xprofile_before_save()	, 10, 2 );			// update xprofile with user_data
//add_action( 'bp_xprofile_settings_before_save', MY_AIA_XPROFILE_CHANGE_MODERATE::xprofile_before_save, '',	1, 2);						// save profile edits to an review table

/**
 * INIT function, called in all child functions
 */
function my_aia_xprofile_edit_init() {
	// include all the files
	include_once 'classes/my-aia-taxonomy-field.php' ;
}


/**
 * Update the custom boxes for the BP Admin
 * 	$fields = array(
		'checkbox'       => 'BP_XProfile_Field_Type_Checkbox',
		'datebox'        => 'BP_XProfile_Field_Type_Datebox',
		.. etc
	);
 * @param type $fields
 */
function my_aia_bp_xprofile_get_field_types($fields) {
	my_aia_xprofile_edit_init();
	
	$fields['taxonomy'] = 'MY_AIA_BUDDYPRESS_TAXONOMY_FIELD';
	
	return $fields;
}

/**
 * Save modifications to the xprofile field
 * @param type $field
 */
function my_aia_xprofile_fields_saved_field($field) {
	if ( ! empty( $_POST['taxonomy_name'] ) ) {
		bp_xprofile_update_field_meta( $field->id, 'taxonomy_name', $_POST['taxonomy_name'] );
	} else {
		bp_xprofile_delete_meta( $field->id, 'field', 'taxonomy_name' );
	}
	
	if ( ! empty( $_POST['taxonomy_display_select'] ) ) {
		bp_xprofile_update_field_meta( $field->id, 'taxonomy_display_select', (int)$_POST['taxonomy_display_select'] );
	} else {
		bp_xprofile_delete_meta( $field->id, 'field', 'taxonomy_display_select' );
	}
		
	return true;
}

/**
 * Sync some user values (hardcoded) from BuddyPress to the userprofile
 * @global \wpdb $wpdb
 * @param int $user_id	user id (empty--> use current logged in user)
 * @return boolean
 */
function my_aia_xprofile_sync_wp_profile( $user_id = 0) {
	global $wpdb;
	
	//$field_names = array('first_name','middle_name', 'last_name', 'display_name');
	
	if ($user_id == 0) return FALSE;
	
	if ( empty( $user_id ) ) {
		$user_id = bp_loggedin_user_id();
	}
	
	if ( empty( $user_id ) ) {
		return false;
	}
	
	// get fullname
	$fullname = xprofile_get_field_data( bp_xprofile_fullname_field_id(), $user_id );
	$first_name = xprofile_get_field_data('first_name' , $user_id);
	$last_name = xprofile_get_field_data('last_name' , $user_id );
	$middle_name = xprofile_get_field_data('middle_name', $user_id);
	

	// update user_meta (wrapper from BP)
	bp_update_user_meta( $user_id, 'nickname',   $fullname  );
	bp_update_user_meta( $user_id, 'first_name', $first_name );
	bp_update_user_meta( $user_id, 'middle_name', $middle_name );
	bp_update_user_meta( $user_id, 'last_name',  $last_name  );

	// update wp_user data
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->users} SET display_name = %s WHERE ID = %d", $fullname, $user_id ) );
}


/**
 * Update XProfile (BuddyPress) With userdata
 * @param int $user_id
 * @param array $old_user_data
 * @return boolean
 */
function my_aia_wp_profile_sync_xprofile( $user_id =0 , $old_user_data = NULL) {
	// fields to update
	$field_names = array('first_name','middle_name', 'last_name', 'display_name');
	
	if ($user_id == 0) return FALSE;
	
	if ( empty( $user_id ) ) {
		$user_id = bp_loggedin_user_id();
	}
	
	if ( empty( $user_id ) ) {
		return false;
	}
	
	foreach ($field_names as $field_name) {
		$value = get_user_meta($user_id, $field_name, TRUE);
		xprofile_set_field_data( $field_name, $user_id, $value );
	}
}