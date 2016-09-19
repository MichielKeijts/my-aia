<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_product_categorie() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_PRODUCT_CATEGORIE,array(MY_AIA_POST_TYPE_PRODUCT),array( 
        'hierarchical' => true, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => MY_AIA_TAXONOMY_PRODUCT_CATEGORIE_SLUG,'with_front'=>true),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Product Categories','my-aia'),
        'singular_label' => __('Product Categorie','my-aia'),
        'labels' => array(
            'name'=>__('Product Categorie','my-aia'),
            'singular_name'=>__('Product Categorie','my-aia'),
            'search_items'=>__('Search Product Categorie','my-aia'),
            'popular_items'=>__('Popular Product Categorie','my-aia'),
            'all_items'=>__('All Product Categorie','my-aia'),
            'parent_items'=>__('Parent Product Categorie','my-aia'),
            'parent_item_colon'=>__('Parent Product Categorie:','my-aia'),
            'edit_item'=>__('Edit Product Categorie','my-aia'),
            'update_item'=>__('Update Product Categorie','my-aia'),
            'add_new_item'=>__('Add New Product Categorie','my-aia'),
            'new_item_name'=>__('New Product Categorie','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Product Categorie with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Product Categorie','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Product Categorie','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_PRODUCT_CATEGORIE,"taxonomy")
    ));
}