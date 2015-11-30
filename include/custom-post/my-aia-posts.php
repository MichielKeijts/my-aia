<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 * 
 */

//Custom Post Type and Taxonomy Names
if( !defined('MY_AIA_POST_TYPE_PARTNER') ) define('MY_AIA_POST_TYPE_PARTNER','partner');
//if( !defined('MY_AIA_POST_TYPE_PARTNER') ) define('EM_POST_TYPE_LOCATION','location');
//if( !defined('EM_TAXONOMY_CATEGORY') ) define('EM_TAXONOMY_CATEGORY','event-categories');
//if( !defined('EM_TAXONOMY_TAG') ) define('EM_TAXONOMY_TAG','event-tags');

//Slugs
define('MY_AIA_POST_TYPE_PARTNER_SLUG','partner');//get_option('db-my-aia_cp_events_slug', 'events'));
//define('MY_AIA_POST_TYPE_EVENT_SLUG','partner');//get_option('db-my-aia_cp_locations_slug', 'locations'));


// Registration of Custom Post Type
//add_action('init','my_aia_plugin_init',1);
function my_aia_plugin_init(){
	define('EM_ADMIN_URL',admin_url().'edit.php?post_type='.MY_AIA_POST_TYPE_PARTNER_SLUG); //we assume the admin url is absolute with at least one querystring
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
		'has_archive' => true,//get_option('db-my-aia_cp_events_has_archive', false) == true,
		'supports' => apply_filters('em_cp_event_supports', array('custom-fields','title','editor','excerpt','comments','thumbnail','author')),
		'capability_type' => 'event',
		'capabilities' => array(
			'publish_posts' => 'publish_partners',
			'edit_posts' => 'edit_partners',
			'edit_others_posts' => 'edit_others_partners',
			'delete_posts' => 'delete_partners',
			'delete_others_posts' => 'delete_others_partners',
			'read_private_posts' => 'read_private_partners',
			'edit_post' => 'edit_partner',
			'delete_post' => 'delete_partner',
			'read_post' => 'read_partner',		
		),
		'label' => __('Partners','db-my-aia'),
		'description' => __('Display events on your blog.','db-my-aia'),
		'labels' => array (
			'name' => __('Partners','db-my-aia'),
			'singular_name' => __('Partner','db-my-aia'),
			'menu_name' => __('Partners','db-my-aia'),
			'add_new' => __('Add Partner','db-my-aia'),
			'add_new_item' => __('Add New Partner','db-my-aia'),
			'edit' => __('Edit','db-my-aia'),
			'edit_item' => __('Edit Partner','db-my-aia'),
			'new_item' => __('New Partner','db-my-aia'),
			'view' => __('View','db-my-aia'),
			'view_item' => __('View Partner','db-my-aia'),
			'search_items' => __('Search Partners','db-my-aia'),
			'not_found' => __('No Partners Found','db-my-aia'),
			'not_found_in_trash' => __('No Partners Found in Trash','db-my-aia'),
			'parent' => __('Parent Partner','db-my-aia'),
		),
		'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type('partner', $partner_post_type);
}