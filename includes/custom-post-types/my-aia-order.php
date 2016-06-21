<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type
function my_aia_register_post_type_order(){
	$order_post_type = array(	
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
		'supports' => array('title','editor','author'),
		'capability_type' => MY_AIA_POST_TYPE_ORDER,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_ORDER),
		'label' => __('Orders','my-aia'),
		'description' => __('Orders are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Orders','my-aia'),
			'singular_name' => __('Order','my-aia'),
			'menu_name' => __('Orders','my-aia'),
			'add_new' => __('Add Order','my-aia'),
			'add_new_item' => __('Add New Order','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Order','my-aia'),
			'new_item' => __('New Order','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Order','my-aia'),
			'search_items' => __('Search Orders','my-aia'),
			'not_found' => __('No Orders Found','my-aia'),
			'not_found_in_trash' => __('No Orders Found in Trash','my-aia'),
			'parent' => __('Parent Order','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_ORDER, $order_post_type);
}