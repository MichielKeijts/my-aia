<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type INVOICE
function my_aia_register_post_type_coupon(){
	$coupon_post_type = array(	
		'public' => false,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => 'my-aia-admin',
		'show_in_nav_menus'=>false,
		'can_export' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'rewrite' => false,//array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => true,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('title','author'),
		'capability_type' => MY_AIA_POST_TYPE_COUPON,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_COUPON),
		'label' => __('Coupons','my-aia'),
		'description' => __('Coupons represent a value to be used in orders.','my-aia'),
		'labels' => array (
			'name' => __('Coupons','my-aia'),
			'singular_name' => __('Coupon','my-aia'),
			'menu_name' => __('Coupons','my-aia'),
			'add_new' => __('Add Coupon','my-aia'),
			'add_new_item' => __('Add New Coupon','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Coupon','my-aia'),
			'new_item' => __('New Coupon','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Coupon','my-aia'),
			'search_items' => __('Search Coupons','my-aia'),
			'not_found' => __('No Coupons Found','my-aia'),
			'not_found_in_trash' => __('No Coupons Found in Trash','my-aia'),
			'parent' => __('Parent Coupon','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_COUPON, $coupon_post_type);
}

