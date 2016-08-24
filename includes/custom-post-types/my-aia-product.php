<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type PRODUCT	
function my_aia_register_post_type_product(){
	$product_post_type = array(	
		'public' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => 'my-aia-admin',
		'show_in_nav_menus'=>true,
		'can_export' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'rewrite' => array('slug' => MY_AIA_POST_TYPE_PRODUCT_SLUG, 'with_front'=>false),
		'has_archive' => true,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('title','editor','thumbnail','author'),
		'capability_type' => MY_AIA_POST_TYPE_PRODUCT,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_PRODUCT),
		'label' => __('Products','my-aia'),
		'description' => __('Products are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Products','my-aia'),
			'singular_name' => __('Product','my-aia'),
			'menu_name' => __('Products','my-aia'),
			'add_new' => __('Add Product','my-aia'),
			'add_new_item' => __('Add New Product','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Product','my-aia'),
			'new_item' => __('New Product','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Product','my-aia'),
			'search_items' => __('Search Products','my-aia'),
			'not_found' => __('No Products Found','my-aia'),
			'not_found_in_trash' => __('No Products Found in Trash','my-aia'),
			'parent' => __('Parent Product','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_PRODUCT, $product_post_type);
}