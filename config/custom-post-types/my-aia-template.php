<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type TEMPLATE	
function my_aia_register_post_type_template(){
	$template_post_type = array(	
		'public' => false,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => 'my-aia-admin',
		'show_in_nav_menus'=>true,
		'can_export' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'rewrite' => false,//array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => false,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('title','editor'),
		'capability_type' => MY_AIA_POST_TYPE_TEMPLATE,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_TEMPLATE),
		'label' => __('Templates','my-aia'),
		'description' => __('Templates are templates for email or PDF creation for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Templates','my-aia'),
			'singular_name' => __('Template','my-aia'),
			'menu_name' => __('Templates','my-aia'),
			'add_new' => __('Add Template','my-aia'),
			'add_new_item' => __('Add New Template','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Template','my-aia'),
			'new_item' => __('New Template','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Template','my-aia'),
			'search_items' => __('Search Templates','my-aia'),
			'not_found' => __('No Templates Found','my-aia'),
			'not_found_in_trash' => __('No Templates Found in Trash','my-aia'),
			'parent' => __('Parent Template','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_TEMPLATE, $template_post_type);
}