<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_kerkstroming() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_KERKSTROMING,array(EM_POST_TYPE_EVENT,'event-recurring', MY_AIA_POST_TYPE_PARTNER),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Kerkstromingen','my-aia'),
        'singular_label' => __('Kerkstroming','my-aia'),
        'labels' => array(
            'name'=>__('Kerkstroming','my-aia'),
            'singular_name'=>__('Kerkstroming','my-aia'),
            'search_items'=>__('Search Kerkstroming','my-aia'),
            'popular_items'=>__('Popular Kerkstroming','my-aia'),
            'all_items'=>__('All Kerkstroming','my-aia'),
            'parent_items'=>__('Parent Kerkstroming','my-aia'),
            'parent_item_colon'=>__('Parent Kerkstroming:','my-aia'),
            'edit_item'=>__('Edit Kerkstroming','my-aia'),
            'update_item'=>__('Update Kerkstroming','my-aia'),
            'add_new_item'=>__('Add New Kerkstroming','my-aia'),
            'new_item_name'=>__('New Kerkstroming','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Kerkstroming with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Kerkstroming','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Kerkstroming','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_KERKSTROMING,"taxonomy")
    ));
}