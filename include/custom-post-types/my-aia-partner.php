<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// Registration of Custom Post Type
function my_aia_register_posttype_partner(){
	$partner_post_type = array(	
		'public' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus'=>true,
		'can_export' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'rewrite' => false,//array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => true,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('custom-fields','title','editor','excerpt','comments','thumbnail','author'),
		'capability_type' => 'partner',
		'capabilities' => MY_AIA::get_capabilities('partner'),
		'label' => __('Partners','my-aia'),
		'description' => __('Display events on your blog.','my-aia'),
		'labels' => array (
			'name' => __('Partners','my-aia'),
			'singular_name' => __('Partner','my-aia'),
			'menu_name' => __('Partners','my-aia'),
			'add_new' => __('Add Partner','my-aia'),
			'add_new_item' => __('Add New Partner','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Partner','my-aia'),
			'new_item' => __('New Partner','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Partner','my-aia'),
			'search_items' => __('Search Partners','my-aia'),
			'not_found' => __('No Partners Found','my-aia'),
			'not_found_in_trash' => __('No Partners Found in Trash','my-aia'),
			'parent' => __('Parent Partner','my-aia'),
		),
		'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type('partner', $partner_post_type);
}