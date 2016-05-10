<?php
/* 
 * @copyright (c) 2016, Michiel Keijts
 * @licence			restricted
 * 
 * Contains a list of helper functions without class interface
 */



/**
 * Returns a list of all buddypress xprofile fields
 * Form:
 *	non-grouped =>	array(<id> => <name>);
 *  grouped		=>	array(<group_id> => array (<name> => <group_name>, <id>=><name>, <id2>=><name2>);
 * @param bool $return_grouped
 * @return array $grouped_list_of_fields|$list_of_fields
 */
function my_aia_get_buddy_press_xprofile_fields($return_grouped = FALSE) {
	$group_data = BP_XProfile_Group::get(array('fetch_fields'=>true));
	
	$grouped_list_of_fields = array();
	$list_of_fields = array();
	
	// loop over groups
	foreach ($group_data as $group=>$field_data) {
		$grouped_list_of_fields[$field_data->id] = array('name'=>$field_data->name);
		if (!empty($field_data->fields)){
			foreach ($field_data->fields as $field=>$data) {
				$list_of_fields[$data->id] = $data->name;
				$grouped_list_of_fields[$field_data->id][$data->id] = $data->name;
			}
		}
	}
	
	if ($return_grouped) return $grouped_list_of_fields;
	
	return $list_of_fields;	
}


/**
 * Get User by Meta Key / Value 
 * @param type $meta_key
 * @param type $meta_value
 * @return type
 */
function get_user_by_meta_data($meta_key, $meta_value, $return_all = FALSE) {
	wp_reset_query();	
	// get al the users by key/value	
	$user_query = new WP_User_Query(
		array(
			'meta_key'	  =>	$meta_key,
			'meta_value'	=>	$meta_value
		)
	);

	// Get the results from the query
	$users = $user_query->get_results();

	if (!is_array($users)) 
		return false; // no users found
	
	return $return_all ? $users:$users[0];
}


/**
 * Get a Google Geocode Result 
 * Maps API implementation See https://console.developers.google.com/apis/api/geocoding_backend/usage?project=mijn-athletesinaction&authuser=1&duration=PT1H
 * @param mixed $data
 * @return mixed Array(result) | FALSE
 */
function get_google_geocode_result($data) {
	include_once MY_AIA_PLUGIN_DIR . 'classes/crmsync/class_google_geocode.php';
	
	$geocoder = new class_google_geocode();
	return $geocoder->get_result($data);
}