<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

//Custom Post Type and Taxonomy Names

//if( !defined('MY_AIA_POST_TYPE_PARTNER') ) define('EM_POST_TYPE_LOCATION','location');
//if( !defined('EM_TAXONOMY_CATEGORY') ) define('EM_TAXONOMY_CATEGORY','event-categories');
//if( !defined('EM_TAXONOMY_TAG') ) define('EM_TAXONOMY_TAG','event-tags');

//Slugs
//define('MY_AIA_POST_TYPE_PARTNER_SLUG','partner');//get_option('my-aia_cp_events_slug', 'events'));
//define('MY_AIA_POST_TYPE_EVENT_SLUG','partner');//get_option('my-aia_cp_locations_slug', 'locations'));


// Registration of Custom Post Type
//add_action('init','my_aia_plugin_init',1);
function my_aia_plugin_init(){
	//define('EM_ADMIN_URL',admin_url().'edit.php?post_type='.MY_AIA_POST_TYPE_PARTNER_SLUG); //we assume the admin url is absolute with at least one querystring
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