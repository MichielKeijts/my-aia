<?php

/* 
 * The file containing all BuddyPress modifications and addons for the admin interface
 * part of MY-AIA
 * 
 * (C) Michiel Keijts, 2016
 */




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
	// include all the files
	include_once 'classes/my-aia-taxonomy-field.php' ;
	
	$fields['taxonomy'] = 'MY_AIA_BUDDYPRESS_TAXONOMY_FIELD';
	
	return $fields;
}