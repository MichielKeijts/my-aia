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
add_action( 'wp_head', 'remove_bp_actions' );
function remove_bp_actions(){
	remove_filter( 'bp_group_header_actions', 'bp_group_join_button' );
	remove_filter( 'bp_directory_groups_actions', 'bp_group_join_button' );
}
add_action( 'bp_get_the_profile_field_name',	'my_aia_language_wrapper', 10, 1);	// add language to group name
add_action( 'bp_group_header_actions', 'my_aia_group_join_button', 10 ); // add action to cancel membership request
add_action( 'bp_directory_groups_actions', 'my_aia_group_join_button', 10 ); // add action to cancel membership request
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
		include_once 'classes/my-aia-bp-group-extension-sportlevel.php';
		//include_once 'classes/my-aia-bp-group-extension-documents.php';
		$bp->documents = new BP_MY_AIA_DOCUMENT_Component();
		bp_register_group_extension('MY_AIA_BP_Group_Extension_Location');
		bp_register_group_extension('MY_AIA_BP_Group_Extension_Group_Type');
		bp_register_group_extension('MY_AIA_BP_Group_Extension_SportLevel');
		//bp_register_group_extension('MY_AIA_BP_Group_Extension_Documents');
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
 * Output button to join a group.
 *
 * Modify pending state to cancel request
 *
 * @since 1.0.0
 *
 * @param object|bool $group Single group object.
 */
function my_aia_group_join_button( $group = false ) {
	echo my_aia_get_group_join_button( $group );
}
	/**
	 * Return button to join a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Single group object.
	 * @return mixed
	 */
	function my_aia_get_group_join_button( $group = false ) {
		global $groups_template;

		// Set group to current loop group if none passed.
		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		// Don't show button if not logged in or previously banned.
		if ( ! is_user_logged_in() || bp_group_is_user_banned( $group ) ) {
			return false;
		}

		// Group creation was not completed or status is unknown.
		if ( empty( $group->status ) ) {
			return false;
		}

		// Already a member.
		if ( ! empty( $group->is_member ) ) {

			// Stop sole admins from abandoning their group.
			$group_admins = groups_get_group_admins( $group->id );
			if ( ( 1 == count( $group_admins ) ) && ( bp_loggedin_user_id() === (int) $group_admins[0]->user_id ) ) {
				return false;
			}

			// Setup button attributes.
			$button = array(
				'id'                => 'leave_group',
				'component'         => 'groups',
				'must_be_logged_in' => true,
				'block_self'        => false,
				'wrapper_class'     => 'group-button ' . $group->status,
				'wrapper_id'        => 'groupbutton-' . $group->id,
				'link_href'         => wp_nonce_url( bp_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' ),
				'link_text'         => __( 'Leave Group', 'buddypress' ),
				'link_title'        => __( 'Leave Group', 'buddypress' ),
				'link_class'        => 'group-button leave-group',
			);

		// Not a member.
		} else {

			// Show different buttons based on group status.
			switch ( $group->status ) {
				case 'hidden' :
					return false;

				case 'public':
					$button = array(
						'id'                => 'join_group',
						'component'         => 'groups',
						'must_be_logged_in' => true,
						'block_self'        => false,
						'wrapper_class'     => 'group-button ' . $group->status,
						'wrapper_id'        => 'groupbutton-' . $group->id,
						'link_href'         => wp_nonce_url( bp_get_group_permalink( $group ) . 'join', 'groups_join_group' ),
						'link_text'         => __( 'Join Group', 'buddypress' ),
						'link_title'        => __( 'Join Group', 'buddypress' ),
						'link_class'        => 'group-button join-group',
					);
					break;

				case 'private' :

					// Member has outstanding invitation -
					// show an "Accept Invitation" button.
					if ( $group->is_invited ) {
						$button = array(
							'id'                => 'accept_invite',
							'component'         => 'groups',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'group-button ' . $group->status,
							'wrapper_id'        => 'groupbutton-' . $group->id,
							'link_href'         => add_query_arg( 'redirect_to', bp_get_group_permalink( $group ), bp_get_group_accept_invite_link( $group ) ),
							'link_text'         => __( 'Accept Invitation', 'buddypress' ),
							'link_title'        => __( 'Accept Invitation', 'buddypress' ),
							'link_class'        => 'group-button accept-invite',
						);

					// Member has requested membership but request is pending -
					// show a "Request Sent / Cancel request" button.
					} elseif ( $group->is_pending ) {
						$button = array(
							'id'                => 'membership_requested',
							'component'         => 'groups',
							'must_be_logged_in' => true,
							'block_self'        => true,
							'wrapper_class'     => 'group-button pending ' . $group->status,
							'wrapper_id'        => 'groupbutton-' . $group->id,
							'link_href'      	  => add_query_arg( 'cancel', bp_get_group_permalink( $group ), bp_get_group_cancel_request_link( $group, bp_loggedin_user_id() ) ),
							'link_text'         => __( 'Request sent / Cancel request', 'buddypress' ),
							'link_title'        => __( 'Request sent / Cancel request', 'buddypress' ),
							'link_class'        => 'group-button pending membership-requested',
						);

					// Member has not requested membership yet or has canceled his request -
					// show a "Request Membership" button.
					} else {
						$button = array(
							'id'                => 'request_membership',
							'component'         => 'groups',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'group-button ' . $group->status,
							'wrapper_id'        => 'groupbutton-' . $group->id,
							'link_href'         => wp_nonce_url( bp_get_group_permalink( $group ) . 'request-membership', 'groups_request_membership' ),
							'link_text'         => __( 'Request Membership', 'buddypress' ),
							'link_title'        => __( 'Request Membership', 'buddypress' ),
							'link_class'        => 'group-button request-membership',
						);
					}

					break;
			}
		}

		/**
		 * Filters the HTML button for joining a group.
		 *
		 * @since 1.2.6
		 * @since 2.4.0 Added $group parameter to filter args.
		 *
		 * @param string $button HTML button for joining a group.
		 * @param object $group BuddyPress group object
		 */
		return bp_get_button( apply_filters( 'bp_get_group_join_button', $button, $group ) );
	}

function my_aia_cancel_group_membership_requests( $group_id, $user_id ) {
	global $wpdb;

	$bp = buddypress();

	return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->groups->table_name_members} WHERE group_id = %d AND user_id = %d AND is_confirmed = 0 AND inviter_id = 0 LIMIT 1", $group_id, $user_id ) );
}

/**
 * Output the URL for accepting an invitation to the current group in the loop.
 *
 * @since 1.0.0
 */
function bp_group_cancel_request_link() {
	echo bp_get_group_cancel_request_link();
}
	/**
	 * Generate the URL for canceling a membership request
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: Current group in the loop.
	 * @return string
	 */
	function bp_get_group_cancel_request_link( $group = false, $user = false) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		if ( empty( $user ) ) {
			$user =& bp_loggedin_user_id();
		}

		$bp = buddypress();

		/**
		 * Filters the URL for canceling a membership request
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL for accepting an invitation to a group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'bp_get_group_cancel_request_link', wp_nonce_url( trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/cancel/' . $group->id ), 'group_cancel_request' ), $group, $user );
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

		// get primary (members) menu
		foreach ( $bp->members->nav->get_primary() as $user_nav_item ) {
			if ( empty( $user_nav_item->show_for_displayed_user ) && ! bp_is_my_profile() ) {
				continue;
			}

			if ($user_nav_item->slug == 'profile') continue;

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