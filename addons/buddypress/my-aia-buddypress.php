<?php

/* 
 * The file containing all BuddyPress addons for the admin interface
 * part of MY-AIA
 * 
 * (C) Michiel Keijts, 2016
 */

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