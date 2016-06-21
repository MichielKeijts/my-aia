<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type INVOICE
function my_aia_register_post_type_invoice(){
	$invoice_post_type = array(	
		'public' => false,
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
		'capability_type' => MY_AIA_POST_TYPE_INVOICE,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_INVOICE),
		'label' => __('Invoices','my-aia'),
		'description' => __('Invoices are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Invoices','my-aia'),
			'singular_name' => __('Invoice','my-aia'),
			'menu_name' => __('Invoices','my-aia'),
			'add_new' => __('Add Invoice','my-aia'),
			'add_new_item' => __('Add New Invoice','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Invoice','my-aia'),
			'new_item' => __('New Invoice','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Invoice','my-aia'),
			'search_items' => __('Search Invoices','my-aia'),
			'not_found' => __('No Invoices Found','my-aia'),
			'not_found_in_trash' => __('No Invoices Found in Trash','my-aia'),
			'parent' => __('Parent Invoice','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_INVOICE, $invoice_post_type);
}