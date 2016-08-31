<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_doelgroep() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_DOELGROEP,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Doelgroepen','my-aia'),
        'singular_label' => __('Doelgroep','my-aia'),
        'labels' => array(
            'name'=>__('Doelgroep','my-aia'),
            'singular_name'=>__('Doelgroep','my-aia'),
            'search_items'=>__('Search Doelgroep','my-aia'),
            'popular_items'=>__('Popular Doelgroep','my-aia'),
            'all_items'=>__('All Doelgroep','my-aia'),
            'parent_items'=>__('Parent Doelgroep','my-aia'),
            'parent_item_colon'=>__('Parent Doelgroep:','my-aia'),
            'edit_item'=>__('Edit Doelgroep','my-aia'),
            'update_item'=>__('Update Doelgroep','my-aia'),
            'add_new_item'=>__('Add New Doelgroep','my-aia'),
            'new_item_name'=>__('New Doelgroep','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Doelgroep with commas','my-aia'),
            'add_or_remove_items'=>__('Add or Doelgroep','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Doelgroep','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities("doelgroep","taxonomy")
    ));
}