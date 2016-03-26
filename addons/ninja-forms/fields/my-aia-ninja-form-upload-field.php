<?php if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * @copyright (c) 2016, Michiel Keijts
 */

function my_aia_ninja_form_upload_field_register(){
	$args = array(
		'name' => 'File Upload',
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'my_aia_upload_field',
				'label' => 'Upload Field',
				'class' => 'widefat',
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

	ninja_forms_register_field('upload', $args);
}