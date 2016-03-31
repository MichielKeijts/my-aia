<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_sportweek_eigenschap() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_SPORTWEEK_EIGENSCHAP,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Sportweek Eigenschap Tags','my-aia'),
        'singular_label' => __('Sportweek Eigenschap Tag','my-aia'),
        'labels' => array(
            'name'=>__('Sportweek Eigenschap','my-aia'),
            'singular_name'=>__('Sportweek Eigenschap','my-aia'),
            'search_items'=>__('Search Sportweek Eigenschap','my-aia'),
            'popular_items'=>__('Popular Sportweek Eigenschap','my-aia'),
            'all_items'=>__('All Sportweek Eigenschap','my-aia'),
            'parent_items'=>__('Parent Sportweek Eigenschap','my-aia'),
            'parent_item_colon'=>__('Parent Sportweek Eigenschap:','my-aia'),
            'edit_item'=>__('Edit Sportweek Eigenschap','my-aia'),
            'update_item'=>__('Update Sportweek Eigenschap','my-aia'),
            'add_new_item'=>__('Add New Sportweek Eigenschap','my-aia'),
            'new_item_name'=>__('New Sportweek Eigenschap Name','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Sportweek Eigenschap with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Sportweek Eigenschap','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Sportweek Eigenschap','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_SPORTWEEK_EIGENSCHAP,"taxonomy")
    ));
}