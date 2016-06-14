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

/**
 * Adds the Attribute widget to the custom post type page
 */
function my_aia_post_type_partner_add_form_widget() {
	$partner = new MY_AIA_PARTNER();

	add_meta_box('my-aia-partner-attribute-box', __('Attributes','my-aia'), array($partner,'get_attributes_form'), MY_AIA_POST_TYPE_PARTNER, 'normal', 'high');
}

/**
 * Adds the Attribute widget to the custom post type page
 */
function my_aia_post_type_partner_add_metaboxes() {
	add_meta_box('my-aia-partner-buddy-press-box', __('Groep','my-aia'), 'my_aia_post_type_partner_display_buddy_press_groups_metabox', MY_AIA_POST_TYPE_PARTNER, 'side', 'high');
	add_meta_box('my-aia-partner-assigned-user-box', __('Contactpersoon','my-aia'), 'my_aia_post_type_partner_display_assigned_user_id_metabox', MY_AIA_POST_TYPE_PARTNER, 'side', 'high');
}

function my_aia_post_type_partner_display_buddy_press_groups_metabox() {
	global $post;
	
	$partner = new MY_AIA_PARTNER($post);
	
	$groups = BP_Groups_Group::get(array('order_by'=>'name'));
	
	if ($groups['total'] > 0) {
		?><select name="bp_group_id"><option value="0"><i>geen group</i></option><?php
		foreach ($groups['groups'] as $group) {
			echo sprintf('<option value="%d" %s>%s</option>', $group->id, ($group->id == $partner->bp_group_id)?'selected':'', $group->name);
		}
		?></select><p><?= __('Selecteer de group waar de partner bij hoort of maak een nieuwe aan'); ?></p><?php
	}
	//@todo: add link to group
}

/**
 * Display a list of users
 */
function my_aia_post_type_partner_display_assigned_user_id_metabox() {
	global $post;
	
	$partner = new MY_AIA_PARTNER($post);
	
	wp_dropdown_users(array(
		'option_none_value' => 0,
		'show_option_none'	=> '<i> - leeg - </li>',
		'name'				=> 'assigned_user_id',
		'selected'			=> $partner->assigned_user_id
	));
	?><p>Selecteer de contactpersoon (gebruiker) voor deze partner</p><?php
	//@todo: add link to user
}


/**
 * Function which calls custom post types to save in case a custom post class
 * exists.
 */
function my_aia_post_save_action($id, $post) {
	switch ($post->post_type) {
		case MY_AIA_POST_TYPE_PARTNER:
			// update partner meta
			$partner = new MY_AIA_PARTNER($post);
			$partner->update_post_meta();
			break;
		default:
			return true;
	}
}