<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_taal() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_TAAL,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Talen','my-aia'),
        'singular_label' => __('Taal','my-aia'),
        'labels' => array(
            'name'=>__('Taal','my-aia'),
            'singular_name'=>__('Taal','my-aia'),
            'search_items'=>__('Search Taal','my-aia'),
            'popular_items'=>__('Popular Taal','my-aia'),
            'all_items'=>__('All Taal','my-aia'),
            'parent_items'=>__('Parent Taal','my-aia'),
            'parent_item_colon'=>__('Parent Taal:','my-aia'),
            'edit_item'=>__('Edit Taal','my-aia'),
            'update_item'=>__('Update Taal','my-aia'),
            'add_new_item'=>__('Add New Taal','my-aia'),
            'new_item_name'=>__('New Taal','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Taal with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Taal','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Taal','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_TAAL,"taxonomy")
    ));
}