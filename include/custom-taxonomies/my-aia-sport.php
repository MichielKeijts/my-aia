<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_sport() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_SPORT,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Sport Tags','my-aia'),
        'singular_label' => __('Sport Tag','my-aia'),
        'labels' => array(
            'name'=>__('Sport','my-aia'),
            'singular_name'=>__('Sport','my-aia'),
            'search_items'=>__('Search Sport','my-aia'),
            'popular_items'=>__('Popular Sport','my-aia'),
            'all_items'=>__('All Sport','my-aia'),
            'parent_items'=>__('Parent Sport','my-aia'),
            'parent_item_colon'=>__('Parent Sport:','my-aia'),
            'edit_item'=>__('Edit Sport','my-aia'),
            'update_item'=>__('Update Sport','my-aia'),
            'add_new_item'=>__('Add New Sport','my-aia'),
            'new_item_name'=>__('New Sport Name','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Sport with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove sports','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used sports','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_SPORT,"taxonomy")
    ));
}