<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_sportbetrokkenheid() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_SPORTBETROKKENHEID,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
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
        'singular_label' => __('Sportbetrokkenheid','my-aia'),
        'labels' => array(
            'name'=>__('Sportbetrokkenheid','my-aia'),
            'singular_name'=>__('Sportbetrokkenheid','my-aia'),
            'search_items'=>__('Search Sportbetrokkenheid','my-aia'),
            'popular_items'=>__('Popular Sportbetrokkenheid','my-aia'),
            'all_items'=>__('All Sportbetrokkenheid','my-aia'),
            'parent_items'=>__('Parent Sportbetrokkenheid','my-aia'),
            'parent_item_colon'=>__('Parent Sportbetrokkenheid:','my-aia'),
            'edit_item'=>__('Edit Sportbetrokkenheid','my-aia'),
            'update_item'=>__('Update Sportbetrokkenheid','my-aia'),
            'add_new_item'=>__('Add New Sportbetrokkenheid','my-aia'),
            'new_item_name'=>__('New Sportbetrokkenheid','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Sportbetrokkenheid with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Sportbetrokkenheid','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Sportbetrokkenheid','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_SPORTBETROKKENHEID,"taxonomy")
    ));
}