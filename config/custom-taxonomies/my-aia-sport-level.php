<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_sport_level() {
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_SPORT_LEVEL,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
        'hierarchical' => false, 
        'public' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => false, // no rewrite//array('slug' => MY_AIA_TAXONOMY_SPORT_SLUG,'with_front'=>false),
        //'update_count_callback' => '',
        //'show_tagcloud' => true,
        //'show_in_nav_menus' => true,
        'label' => __('Sport Levels','my-aia'),
        'singular_label' => __('Sport Level','my-aia'),
        'labels' => array(
            'name'=>__('Sport Level','my-aia'),
            'singular_name'=>__('Sport Level','my-aia'),
            'search_items'=>__('Search Sport Level','my-aia'),
            'popular_items'=>__('Popular Sport Level','my-aia'),
            'all_items'=>__('All Sport Levels','my-aia'),
            'parent_items'=>__('Parent Sport Level','my-aia'),
            'parent_item_colon'=>__('Parent Sport Level:','my-aia'),
            'edit_item'=>__('Edit Sport Level','my-aia'),
            'update_item'=>__('Update Sport Level','my-aia'),
            'add_new_item'=>__('Add New SportLevel','my-aia'),
            'new_item_name'=>__('New Sport Level','my-aia'),
            'seperate_items_with_commas'=>__('Seperate Sport Level with commas','my-aia'),
            'add_or_remove_items'=>__('Add or remove Sport Levels','my-aia'),
            'choose_from_the_most_used'=>__('Choose from most used Sport Levels','my-aia'),
        ),
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_TAXONOMY_SPORT_LEVEL, 'taxonomy')
    ));
}