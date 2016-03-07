<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 * 
 * 
 * List of Functions, which are not object oriented
 */


/**
 * Set Capabitilities for roles
 * @global \WP_Roles $wp_roles
 * @param array $roles
 * @param array $caps
 */
function my_aia_set_mass_capabilities( $roles, $caps ){
	global $wp_roles;
	foreach( $roles as $user_role ){
		foreach($caps as $cap){
			$wp_roles->add_cap($user_role, $cap);
		}
	}
}

/*
 * Set Roles 
 * @param array $roles roles to declare
 * @param array $caps caps to initiate (default: empty array)
 */
function my_aia_set_mass_roles( $roles, $capabilities = array()) {
	foreach ($roles as $role) {
		remove_role( $role, __($role), $capabilities );
		add_role( $role, __($role), $capabilities );
	}
}

//Register the Upload field
add_action('init', 'my_custom_field_register');
function my_custom_field_register(){
	$args = array(
		'name' => 'Mijn Custom Field',
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'my_text',
				'label' => 'My Text Label',
				'class' => 'widefat',
			),
			array(
				'type' => 'select',
				'name' => 'my_select',
				'label' => 'My Select Label',
			),
		),
		'display_function' => 'ninja_forms_field_upload_display',
		'sub_edit_function' => 'ninja_forms_field_upload_sub_edit',
		'group' => '',
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_upload_pre_process',
		'process' => 'ninja_forms_field_upload_process',
		'req_validation' => 'ninja_forms_field_upload_req_validation',
	);

	if (function_exists('ninja_forms_register_field')) return ninja_forms_register_field('_upload', $args);
	return false;
}

function my_aia_test_notification ($nf_notification_types) {
	$nf_notification_types['custom_post'] = require_once (MY_AIA_PLUGIN_DIR . 'include/ninja-forms/custom-post-notification.php');
	return $nf_notification_types;
}

/**
 * Returns the lower_case_and_underscore style of $name. 
 *  - to _
 *  spaces to _
 * @param string $name
 * @return string
 */
function lowercase_underscore ($name) {
	return str_replace(array('-',' '), array('_','_'), strtolower($name));
}

/**
 * Returns the wp-style-style of $name. 
 *  underscores to -
 *  spaces to -
 * @param string $name
 * @return string
 */
function lowercase_wordpressize ($name) {
	return str_replace(array('_',' '), array('-','-'), strtolower($name));
}

