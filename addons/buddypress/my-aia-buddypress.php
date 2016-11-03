<?php

/* 
 * The file containing all BuddyPress addons for the admin interface
 * part of MY-AIA
 * 
 * (C) Michiel Keijts, 2016
 */

// Including necessary files
include_once 'classes/my-aia-xprofile-change-moderate.php';
include_once 'classes/my-aia-bp-documents.php';
include_once 'classes/my-aia-bp-orders.php';


// add hooks
add_action( 'xprofile_updated_profile',			'my_aia_xprofile_sync_wp_profile'	, 99, 1);			// update user_date with xprofile data
add_action( 'profile_update',					'my_aia_wp_profile_sync_xprofile'	, 99, 2 );			// update xprofile with user_data
//add_action( 'profile_update',					'MY_AIA_XPROFILE_CHANGE_MODERATE::xprofile_before_save', 10, 2 );			// update xprofile with user_data
add_action( 'bp_loaded',						'bp_my_aia_load_core_components' );						// load front-end 
add_action( 'bp_get_the_profile_field_name',	'my_aia_language_wrapper', 10, 1);	// add language to group name
//add_action( 'bp_xprofile_settings_before_save', 'MY_AIA_XPROFILE_CHANGE_MODERATE::xprofile_before_save', '',	1, 2);						// save profile edits to an review table

/**
 * INIT function, called in all child functions
 */
function my_aia_xprofile_edit_init() {
	// include all the files
	include_once 'classes/my-aia-taxonomy-field.php' ;
}

/**
 * Load the BuddyPress Front-End add ons
 * @global \BuddyPress $bp
 */
function bp_my_aia_load_core_components() {
	global $bp;
	
	my_aia_redirect_to_profile();
	
	if (!isset($bp->documents)) {
		include_once 'classes/my-aia-bp-group-extension-location.php';
		include_once 'classes/my-aia-bp-group-extension-group-type.php';
		$bp->documents = new BP_MY_AIA_DOCUMENT_Component();
		bp_register_group_extension('MY_AIA_BP_Group_Extension_Location');
		bp_register_group_extension('MY_AIA_BP_Group_Extension_Group_Type');
	}
	
	if (!isset($bp->orders)) {
		//include_once 'classes/my-aia-bp-group-extension-location.php';
		//include_once 'classes/my-aia-bp-group-extension-group-type.php';
		$bp->orders = new BP_MY_AIA_ORDER_Component();
		//bp_register_group_extension('MY_AIA_BP_Group_Extension_Location');
		//bp_register_group_extension('MY_AIA_BP_Group_Extension_Group_Type');
	}
	
	include_once 'classes/my-aia-bp-template-parser.php';
}

/**
 * Update the custom boxes for the BP Admin
 * 	$fields = array(
		'checkbox'       => 'BP_XProfile_Field_Type_Checkbox',
		'datebox'        => 'BP_XProfile_Field_Type_Datebox',
		.. etc
	);
 * @param type $fields
 */
function my_aia_bp_xprofile_get_field_types($fields) {
	my_aia_xprofile_edit_init();
	
	$fields['taxonomy'] = 'MY_AIA_BUDDYPRESS_TAXONOMY_FIELD';
	
	return $fields;
}

/**
 * Save modifications to the xprofile field
 * @param type $field
 */
function my_aia_xprofile_fields_saved_field($field) {
	if ( ! empty( $_POST['taxonomy_name'] ) ) {
		bp_xprofile_update_field_meta( $field->id, 'taxonomy_name', $_POST['taxonomy_name'] );
	} else {
		bp_xprofile_delete_meta( $field->id, 'field', 'taxonomy_name' );
	}
	
	if ( ! empty( $_POST['taxonomy_display_select'] ) ) {
		bp_xprofile_update_field_meta( $field->id, 'taxonomy_display_select', (int)$_POST['taxonomy_display_select'] );
	} else {
		bp_xprofile_delete_meta( $field->id, 'field', 'taxonomy_display_select' );
	}
		
	return true;
}

/**
 * Sync some user values (hardcoded) from BuddyPress to the userprofile
 * @global \wpdb $wpdb
 * @param int $user_id	user id (empty--> use current logged in user)
 * @return boolean
 */
function my_aia_xprofile_sync_wp_profile( $user_id = 0) {
	global $wpdb;
	
	//$field_names = array('first_name','middle_name', 'last_name', 'display_name');
	
	if ($user_id == 0) return FALSE;
	
	if ( empty( $user_id ) ) {
		$user_id = bp_loggedin_user_id();
	}
	
	if ( empty( $user_id ) ) {
		return false;
	}
	
	// get fullname
	$fullname = xprofile_get_field_data( bp_xprofile_fullname_field_id(), $user_id );
	$first_name = xprofile_get_field_data('first_name' , $user_id);
	$last_name = xprofile_get_field_data('last_name' , $user_id );
	$middle_name = xprofile_get_field_data('middle_name', $user_id);
	

	// update user_meta (wrapper from BP)
	if (!empty($fullname) && $fullname) bp_update_user_meta( $user_id, 'nickname',   $fullname  );
	if (!empty($first_name) && $first_name) bp_update_user_meta( $user_id, 'first_name', $first_name );
	if (!empty($last_name) && $last_name) bp_update_user_meta( $user_id, 'middle_name', $middle_name );
	if (!empty($middle_name) && $middle_name) bp_update_user_meta( $user_id, 'last_name',  $last_name  );

	// update wp_user data
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->users} SET display_name = %s WHERE ID = %d", $fullname, $user_id ) );
}


/**
 * Update XProfile (BuddyPress) With userdata
 * @param int $user_id
 * @param array $old_user_data
 * @return boolean
 */
function my_aia_wp_profile_sync_xprofile( $user_id =0 , $old_user_data = NULL) {
	// only update if profile.php direct
	if (strpos($_SERVER['REQUEST_URI'], 'profile.php') === FALSE) return FALSE;
		
	// fields to update
	$field_names = array('first_name','middle_name', 'last_name', 'display_name');
	
	if ($user_id == 0) return FALSE;
	
	if ( empty( $user_id ) ) {
		$user_id = bp_loggedin_user_id();
	}
	
	if ( empty( $user_id ) ) {
		return false;
	}
	
	foreach ($field_names as $field_name) {
		$value = get_user_meta($user_id, $field_name, TRUE);
		xprofile_set_field_data( $field_name, $user_id, $value );
	}
}


/**
 * Check url for Buddypress URL and redirect if so
 */
function my_aia_redirect_to_profile() {
	global $post;
	
	if (get_current_user_id() > 0) {
		if ($_SERVER['REQUEST_URI'] =='/mijn-aia/' || $_SERVER['REQUEST_URI'] == '/mijn-aia') {
			// redirect to profile page
			header(sprintf('Location: /%s/%s/%s', MY_AIA_BP_ROOT, MY_AIA_BP_MEMBERS,  bp_core_get_username( get_current_user_id() )));
			exit(); // redirect..
		}
	}
	
	return  true;
}



/**
 * Function is a filter function which also adds the groups result from BuddyPress
 * - nasty solution, normally should be via class-bp-members-suggestions style
 * @param array $results
 * @param BP_Members_Suggestions $bp_members_suggestions
 */
function my_aia_admin_group_mention($results, $bp_members_suggestions) {
	// check if we need to add 
	if (empty($_GET['term']) || strlen($_GET['term']) < 2) return $results;
	
	// find groups
	$bp_group = new BP_Groups_Group();
	$groups = $bp_group->get(array('name'=>  filter_input(INPUT_GET, 'term')));
	if (count($groups['groups']) > 0) {
		foreach ($groups['groups'] as $group) {
			$result        = new stdClass();
			$result->ID    = $group->slug;
			$result->image = bp_core_fetch_avatar(
					array( 
						'html' => false, 
						'item_id' => $group->id, 
						'avatar_dir' => 'group-avatars', 
						'object'     => 'group') 
				);
			$result->name  = $group->id;
			array_push($results, $result);
		}
	}
	return $results;
}
add_filter('bp_members_suggestions_get_suggestions', 'my_aia_admin_group_mention', 10, 2);

/**
 * Wrap the domain around ($name), for translation:
 * __($name, $domain
 * @param string $name
 * @param string $domain (default = 'my-aia')
 * @return string
 */
function my_aia_language_wrapper ($name, $domain='my-aia') {
	return __($name, $domain);
}


/**
 * Render the navigation markup for the displayed user.
 * This function displayes the user information 
 *	- cached
 *  - for any location
 *  - no ECHO, return string
 * 
 * @overrides bp_get_displayed_user_nav()
 * @since 1.1.0
 * 
 * @return string
 */
function my_aia_bp_get_displayed_user_nav() {
	$key = 'user_menu_'.get_current_user_id();
	$menu = wp_cache_get($key, 'my-aia');
	
	// cache found..
	if ($menu) {
		return $menu;
	} 
	
	// try and locate the menu
	$bp = buddypress();

	$menu = "";
	
	
	$current_item = bp_current_item();
	
	if ($current_item && $current_item != 'members') {
		$menu = my_aia_get_secondary_menu();
	} else {
	
	
		foreach ( $bp->members->nav->get_primary() as $user_nav_item ) {
			if ( empty( $user_nav_item->show_for_displayed_user ) && ! bp_is_my_profile() ) {
				continue;
			}

			$selected = '';
			if ( bp_is_current_component( $user_nav_item->slug ) ) {
				$selected = ' class="current selected"';
			}

			if ( bp_loggedin_user_domain() ) {
				// modification, use normal link
				$link = $user_nav_item->link;
				//$link = str_replace( bp_loggedin_user_domain(), bp_displayed_user_domain(), $user_nav_item->link );
			} else {
				$link = trailingslashit( bp_displayed_user_domain() . $user_nav_item->link );
			}

			/**
			 * Filters the navigation markup for the displayed user.
			 *
			 * This is a dynamic filter that is dependent on the navigation tab component being rendered.
			 *
			 * @since 1.1.0
			 *
			 * @param string $value         Markup for the tab list item including link.
			 * @param array  $user_nav_item Array holding parts used to construct tab list item.
			 *                              Passed by reference.
			 */
			$menu = sprintf('%s%s', $menu, apply_filters_ref_array( 'bp_get_displayed_user_nav_' . $user_nav_item->css_id, array( '<li id="' . $user_nav_item->css_id . '-personal-li" ' . $selected . '><a id="user-' . $user_nav_item->css_id . '" href="' . $link . '">' . $user_nav_item->name . '</a></li>', &$user_nav_item ) ));
		}
	}

	// set cache, expire one day
	wp_cache_set($key, $menu, 'my-aia', 86400);
	
	// return, no echo!
	return $menu;
}

/**
 * Return secondary menu (groups, ..)
 * @return string
 */
function my_aia_get_secondary_menu() {
	$bp = buddypress();

	// If we are looking at a member profile, then the we can use the current
	// component as an index. Otherwise we need to use the component's root_slug.
	$component_index = !empty( $bp->displayed_user ) ? bp_current_component() : bp_get_root_slug( bp_current_component() );
	$selected_item   = bp_current_action();

	// Default to the Members nav.
	if ( ! bp_is_single_item() ) {
		// Set the parent slug, if not provided.
		if ( empty( $parent_slug ) ) {
			$parent_slug = $component_index;
		}

		$secondary_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $parent_slug ) );

		if ( ! $secondary_nav_items ) {
			return false;
		}

	// For a single item, try to use the component's nav.
	} else {
		$current_item = bp_current_item();
		$single_item_component = bp_current_component();

		// Adjust the selected nav item for the current single item if needed.
		if ( ! empty( $parent_slug ) ) {
			$current_item  = $parent_slug;
			$selected_item = bp_action_variable( 0 );
		}

		// If the nav is not defined by the parent component, look in the Members nav.
		if ( ! isset( $bp->{$single_item_component}->nav ) ) {
			$secondary_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $current_item ) );
		} else {
			$secondary_nav_items = $bp->{$single_item_component}->nav->get_secondary( array( 'parent_slug' => $current_item ) );
		}

		if ( ! $secondary_nav_items ) {
			return false;
		}
	}
	
	$menu = "";
	// Loop through each navigation item.
	foreach ( $secondary_nav_items as $subnav_item ) {
		if ( empty( $subnav_item->user_has_access ) ) {
			continue;
		}

		// If the current action or an action variable matches the nav item id, then add a highlight CSS class.
		if ( $subnav_item->slug === $selected_item ) {
			$selected = ' class="current selected"';
		} else {
			$selected = '';
		}

		// List type depends on our current component.
		$list_type = bp_is_group() ? 'groups' : 'personal';

		/**
		 * Filters the "options nav", the secondary-level single item navigation menu.
		 *
		 * This is a dynamic filter that is dependent on the provided css_id value.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value         HTML list item for the submenu item.
		 * @param array  $subnav_item   Submenu array item being displayed.
		 * @param string $selected_item Current action.
		 */
		
		$menu = sprintf('%s%s', $menu, apply_filters( 'bp_get_options_nav_' . $subnav_item->css_id, '<li id="' . esc_attr( $subnav_item->css_id . '-' . $list_type . '-li' ) . '" ' . $selected . '><a id="' . esc_attr( $subnav_item->css_id ) . '" href="' . esc_url( $subnav_item->link ) . '">' . $subnav_item->name . '</a></li>', $subnav_item, $selected_item ));
	}
	
	return $menu;
}