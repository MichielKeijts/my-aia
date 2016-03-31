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