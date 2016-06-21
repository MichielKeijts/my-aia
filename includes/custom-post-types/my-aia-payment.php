<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type PAYMENT
function my_aia_register_post_type_payment(){
	$payment_post_type = array(	
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
		'supports' => array('editor','author'),
		'capability_type' => MY_AIA_POST_TYPE_PAYMENT,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_PAYMENT),
		'label' => __('Payments','my-aia'),
		'description' => __('Payments are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Payments','my-aia'),
			'singular_name' => __('Payment','my-aia'),
			'menu_name' => __('Payments','my-aia'),
			'add_new' => __('Add Payment','my-aia'),
			'add_new_item' => __('Add New Payment','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Payment','my-aia'),
			'new_item' => __('New Payment','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Payment','my-aia'),
			'search_items' => __('Search Payments','my-aia'),
			'not_found' => __('No Payments Found','my-aia'),
			'not_found_in_trash' => __('No Payments Found in Trash','my-aia'),
			'parent' => __('Parent Payment','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_PAYMENT, $payment_post_type);
}