<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_sport() {
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_SPORT,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Sport Tags'),
        'singular_label' => __('Sport Tag'),
        'labels' => array(
            'name'=>__('Sport','dbem'),
            'singular_name'=>__('Sport','dbem'),
            'search_items'=>__('Search Sport','dbem'),
            'popular_items'=>__('Popular Sport','dbem'),
            'all_items'=>__('All Sport','dbem'),
            'parent_items'=>__('Parent Sport','dbem'),
            'parent_item_colon'=>__('Parent Sport:','dbem'),
            'edit_item'=>__('Edit Sport','dbem'),
            'update_item'=>__('Update Sport','dbem'),
            'add_new_item'=>__('Add New Sport','dbem'),
            'new_item_name'=>__('New Sport Name','dbem'),
            'seperate_items_with_commas'=>__('Seperate Sport with commas','dbem'),
            'add_or_remove_items'=>__('Add or remove events','dbem'),
            'choose_from_the_most_used'=>__('Choose from most used event tags','dbem'),
        ),
        'capabilities' => array(
            'manage_terms'  =>  'edit_sport_categories',
            'edit_terms'    =>  'edit_sport_categories',
            'delete_terms'  =>  'delete_sport_categories',
            'assign_terms'  =>  'edit_sports',
        )
    ));
    
}