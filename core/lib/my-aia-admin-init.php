<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// Define actions

$_counter_for_post_filter =0 ;
add_action( 'pre_get_posts', 'posts_filter', 99, 1);
/**
 * Filter out all posts with a inherit_from value NOT NULL
 * @return Void
 */
 function posts_filter( $query ){
	 global $post_type, $_counter_for_post_filter;

	 if ($post_type != MY_AIA_POST_TYPE_PRODUCT) 
		return;

	 if ($_counter_for_post_filter++ > 0)
		return;

		 $query->set( 'meta_query', array(
		'relation' => 'OR',
		array(
			'key' => 'inherit_from',
			'value' => 1,
			'compare' => '<'
		),
		array(
			'key' => 'inherit_from',
			'compare' => 'NOT EXISTS',
			'value' => 'null',
		)
	));
}



// init action in construct
//$my_aia_admin = new MY_AIA_ADMIN();