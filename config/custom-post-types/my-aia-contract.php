<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type
function my_aia_register_post_type_contract(){
	$contract_post_type = array(	
		'public' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => 'my-aia-admin',
		'show_in_nav_menus'=>true,
		'can_export' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'rewrite' => false,//array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => true,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('title','editor','thumbnail','author'),
		'capability_type' => MY_AIA_POST_TYPE_CONTRACT,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_CONTRACT),
		'label' => __('Contracts','my-aia'),
		'description' => __('Contracts are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Contracts','my-aia'),
			'singular_name' => __('Contract','my-aia'),
			'menu_name' => __('Contracts','my-aia'),
			'add_new' => __('Add Contract','my-aia'),
			'add_new_item' => __('Add New Contract','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Contract','my-aia'),
			'new_item' => __('New Contract','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Contract','my-aia'),
			'search_items' => __('Search Contracts','my-aia'),
			'not_found' => __('No Contracts Found','my-aia'),
			'not_found_in_trash' => __('No Contracts Found in Trash','my-aia'),
			'parent' => __('Parent Contract','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_CONTRACT, $contract_post_type);
}