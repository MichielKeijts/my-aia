<?php if ( ! defined( 'ABSPATH' ) ) exit;
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
/*add_action('init', 'my_custom_field_register');
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
}*/

function my_aia_nf_add_notifications ($nf_notification_types) {
	$nf_notification_types['custom_post'] = require_once (MY_AIA_PLUGIN_DIR . 'addons/ninja-forms/custom-notifications/custom-post-notification.php');
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


/*
 * Saves and autocreates options
 * @param array $fields 
 * @param int	$method (default INPUT_POST)
 * @return bool 
 */
function my_aia_save_options ($fields, $method=INPUT_POST) {
	foreach ($fields as $var=>$params) {
		if (!$param['autoload']) $autoload = FALSE;
		else $autoload = TRUE;
		
		if ($value = filter_input($method, $var)) {
			update_option('my_aia_'.$var, $value, $autoload);
		}
	}
	return true;
}


/*
 * Saves and autocreates options
 * @param array $fields 
 * @param int	$method (default INPUT_POST)
 * @return array $data 
 */
function my_aia_get_options_data ($fields) {
	$data = array();
	foreach ($fields as $var=>$params) {
		if (!isset($param['default'])) {
			$data[$var] = get_option('my_aia_'.$var);
		} else {
			$data[$var] = get_option('my_aia_'.$var, $param['default']);
		}
	}
	return $data;
}


/**
 * Get the default (init) set of the input field.
 * 
 * @param string $field Fieldname
 * @return array (type: .. ,name:..
 */
function my_aia_get_default_field_type($field) {
	return array('type'=>'%s','id'=>$field,'name'=>$field,'label'=>$field);
}


/**
 * Add Attributes form to the post edit page. This is a same idea as custom post types,
 * but now in the MY_AIA setup
 * @global array $wp_meta_boxes
 * @param string $name Name of the (replaced) form
 * @param string $post_type
 * @param array $data array('field' => 'value') pairs
 * @return boolean
 */
function my_aia_add_attributes_form($name='em-event-attributes', $post_type = EM_POST_TYPE_EVENT, $data) {
	global $wp_meta_boxes;

	// remove the old box, as we are called before!
	// remove_meta_box('em-event-attributes', EM_POST_TYPE_EVENT, 'normal');
	// above not working, is in sorted part, not being removed by remove_meta_box(!)
	if (is_array($wp_meta_boxes[$post_type]['normal']['sorted']) && array_key_exists($name, $wp_meta_boxes[$post_type]['normal']['sorted'])) {
		unset($wp_meta_boxes[$post_type]['normal']['sorted']['em-event-attributes']);
	}
	?>
		<table class="form-data">
			<thead>
				<tr>
					<td valign="top"><?= __('Attribute Name','my-aia'); ?></td>
					<td valign="top"><?= __('Attribute Value','my-aia'); ?></td>
				</tr>
			</thead>
			<tbody>
	<?php
		foreach ($data as $field):
			switch ($field['type']) {
				case "%b":
					?>
					<tr>
						<td><label for="<?= $field['name']; ?>"><?= $field['label']; ?>:</label></td>
						<td><input type='checkbox' id="<?= $field['name']; ?>" name="<?= $field['name']; ?>" <?= $field['value']>0?'checked':''; ?>  /></td>
					</tr>
					<?php
					break;
				case "%s":
				case "%d":
				default:
					?>
					<tr>
						<td><label for="<?= $field['name']; ?>"><?= $field['label']; ?>:</label></td>
						<td><input type='text' name="<?= $field['name']; ?>" value="<?= $field['value']; ?>" /></td>
					</tr>
					<?php
			}
		endforeach; // loop over $fields
	?>
			</tbody>
		</table>

	<?php 
	return true;
}


/**
 * Works much like <a href="http://codex.wordpress.org/Function_Reference/locate_template" target="_blank">locate_template</a>, except it takes a string instead of an array of templates, we only need to load one.
 * Copied and modified from Events-Manager
 * @param string $template_name
 * @param boolean $load
 * @uses locate_template()
 * @return string
 */
function my_aia_locate_template( $template_name, $load=false, $args = array() ) {
	//First we check if there are overriding tempates in the child or parent theme
	$located = locate_template(array(MY_AIA_PLUGIN_DIR.$template_name, $template_name));
	if( !$located ){
		if ( file_exists(MY_AIA_PLUGIN_DIR.'/themes/default/'.$template_name) ) {
			$located = MY_AIA_PLUGIN_DIR.'/themes/default/'.$template_name;
		}
	}
	$located = apply_filters('my_aia_locate_template', $located, $template_name, $load, $args);
	if( $located && $load ){
		if( is_array($args) ) extract($args);
		include($located);
	}
	return $located;
}


/**
 * Shorthand to return the instance of MY_AIA_ORDER declared in 
 * MY_AIA::$post_types[MY_AIA_ORDER]
 * @return \MY_AIA_ORDER Order Instace
 */
function my_aia_order() {
	return MY_AIA::$post_types[MY_AIA_POST_TYPE_ORDER];
}

/**
 * Shorthand to return the instance of MY_AIA_INVOICE declared in 
 * MY_AIA::$post_types[MY_AIA_INVOICE]
 * @return \MY_AIA_INVOICE Order Instace
 */
function my_aia_invoice() {
	return MY_AIA::$post_types[MY_AIA_POST_TYPE_INVOICE];
}