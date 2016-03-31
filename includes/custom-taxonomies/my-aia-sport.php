<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

function my_aia_register_taxonomy_sport() {
	global $wp_rewrite;
	// create a new taxonomy
    register_taxonomy(MY_AIA_TAXONOMY_SPORT,array(EM_POST_TYPE_EVENT,'event-recurring','user'),array( 
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

/**
 * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to 
 * select a sport from a checkbox of terms from the sport taxonomy.  This is just one example of 
 * many ways this can be handled.
 *
 * @param object $user The user object currently being edited.
 */
function my_aia_edit_user( $user) {
	return false;
	return my_aia_edit_user_taxonomy_metabox($user);
	/*add_meta_box(
		'metabox_id',
		__( 'Metabox Title', 'buddypress' ),
		'my_aia_edit_user_taxonomy_metabox', // function that displays the contents of the meta box
		get_current_screen()->id
	);*/
}

function my_aia_edit_user_taxonomy_metabox($user, $taxonomy='sport') {
	global $current_user;
	if (!is_a($user, "WP_user")) {
		$user = $current_user;
	}
	
	$tax = get_taxonomy( $taxonomy );

	/* Make sure the user can assign terms of the sport taxonomy before proceeding. */
	if ( !current_user_can( $tax->cap->assign_terms ) ) {
		echo "GEEN TOEGAGN";
	}
		//return "GEEN TOEGANG";
	
	/* Get the terms of the 'sport' taxonomy. */
	$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) ); ?>

	<h3><?php _e( 'Sport' ); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="sport"><?php _e( 'Select Sport' ); ?></label></th>

			<td><?php

			/* If there are any sport terms, loop through them and display checkboxes. */
			if ( !empty( $terms ) ) {

				foreach ( $terms as $term ) { ?>
					<input type="radio" name="sport" id="sport-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( true, is_object_in_term( $user->ID, 'sport', $term ) ); ?> /> <label for="sport-<?php echo esc_attr( $term->slug ); ?>"><?php echo $term->name; ?></label> <br />
				<?php }
			}

			/* If there are no sport terms, display a message. */
			else {
				_e( 'There are no sports available.' );
			}

			?></td>
		</tr>

	</table>
<?php }