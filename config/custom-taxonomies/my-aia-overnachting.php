<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_overnachting() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_OVERNACHTING,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
		'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Overnachtingen','my-aia'),
        'singular_label' => __('Overnachting Tag','my-aia'),
        'labels' => array(
            'name'=>__('Overnachting','my-aia'),
            'singular_name'=>__('Overnachting','my-aia'),
            'search_items'=>__('Search Overnachting','my-aia'),
            'popular_items'=>__('Popular Overnachting','my-aia'),
            'all_items'=>__('All Overnachting','my-aia'),
            'parent_items'=>__('Parent Overnachting','my-aia'),
            'parent_item_colon'=>__('Parent Overnachting:','my-aia'),
            'edit_item'=>__('Edit Overnachting','my-aia'),
            'update_item'=>__('Update Overnachting','my-aia'),
            'add_new_item'=>__('Add New Overnachting','my-aia'),
            'new_item_name'=>__('New Overnachting','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Overnachting with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Overnachting','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Overnachting','my-aia'),
        ),
        'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_OVERNACHTING,"taxonomy")
    ));
}