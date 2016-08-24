<?php
/**
 * MY_AIA BuddyPress Manager component for BuddyPress
 * @author marcus, Michiel Keijts
 * @since 5.0
 */
class BP_MY_AIA_Component extends BP_Component {
	
	function __construct() {
		global $bp;
		parent::start('documents',	__('Documents', 'my-aia'), MY_AIA_PLUGIN_DIR);
		$this->includes();
		//TODO make BP component optional
		$bp->active_components[$this->id] = '1';
	}

	function includes( $includes = array() ) {
		// Files to include
		$includes = array(
			//'addons/buddypress/bp-my-aia-activity.php',
			//'addons/buddypress/bp-my-aia-templatetags.php',
			'addons/buddypress/my-aia-bp-notifications.php',
			//'addons/buddypress/screens/profile.php',
			'addons/buddypress/screens/my-documents.php',
			//'addons/buddypress/screens/my-locations.php',
			//'addons/buddypress/screens/attending.php',
			//'addons/buddypress/screens/my-bookings.php',
			//'addons/buddypress/screens/my-order.php'
		);
		if( bp_is_active('groups') ){
			$includes[] = 'addons/buddypress/screens/group-documents.php';
			$includes[] = 'addons/buddypress/classes/my-aia-bp-groups.php';
		}
		parent::includes( $includes );
		//TODO add admin pages for extra BP specific settings
	}

	/**
	 * Sets up the global MY_AIA Manager BuddyPress Components
	 */
	function setup_globals( $args = array() ) {
		global $bp, $wpdb;
		// Define a slug constant that will be used to view this components pages
		if ( !defined( 'BP_MY_AIA_SLUG' ) )
			define ( 'BP_MY_AIA_SLUG', str_replace('/','-', 'documents') );

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_MY_AIA_SLUG,
			'has_directory'         => false, //already done by EM
			'notification_callback' => 'my_aia_bp_format_notifications',
			'search_string'         => sprintf(__( 'Search %s...', 'my-aia'),__('Documents','events-manager')),
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );

		//quick link shortcut - may need to revisit this
		$bp->{$this->id}->link = trailingslashit($bp->loggedin_user->domain).BP_MY_AIA_SLUG.'/';
	}
	
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
		global $blog_id; 
		//check multisite or normal mode for correct permission checking
		if(is_multisite() && $blog_id != BP_ROOT_BLOG){
			//FIXME MS mode doesn't seem to recognize cross subsite caps, using the proper functions, for now we use switch_blog.
			$current_blog = $blog_id;
			switch_to_blog(BP_ROOT_BLOG);
			$can_manage_events = current_user_can_for_blog(BP_ROOT_BLOG, 'edit_events');
			$can_manage_locations = current_user_can_for_blog(BP_ROOT_BLOG, 'edit_locations');
			$can_manage_bookings = current_user_can_for_blog(BP_ROOT_BLOG, 'manage_bookings');
			switch_to_blog($current_blog);
		}else{
			$can_manage_events = current_user_can('edit_events');
			$can_manage_locations = current_user_can('edit_locations');
			$can_manage_bookings = current_user_can('manage_bookings');
		}
		/* Add 'Events' to the main user profile navigation */
		$main_nav = array(
			'name' => __( 'Documents', 'my-aia'),
			'slug' => 'documents',
			'position' => 90,
			'screen_function' => 'bp_my_aia_documents',
			'default_subnav_slug' => 'profile'
		);

		$my_aia_link = trailingslashit( bp_displayed_user_domain() . 'documents' );
		
		/* Create SubNav Items */
		$sub_nav[] = array(
			'name' => __( 'My Profile', 'my-aia'),
			'slug' => 'profile',
			'parent_slug' => 'documents',
			'parent_url' => $my_aia_link,
			'screen_function' => 'bp_my_aia_documents',
			'position' => 10
		);
		
		/*$sub_nav[] = array(
			'name' => __( 'Events I\'m Attending', 'events-manager'),
			'slug' => 'attending',
			'parent_slug' => em_bp_get_slug(),
			'parent_url' => $em_link,
			'screen_function' => 'bp_em_attending',
			'position' => 20,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		);
	
		if( $can_manage_events ){
			$sub_nav[] = array(
				'name' => __( 'My Events', 'events-manager'),
				'slug' => 'my-events',
				'parent_slug' => em_bp_get_slug(),
				'parent_url' => $em_link,
				'screen_function' => 'bp_em_my_events',
				'position' => 30,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
		
		if( $can_manage_locations && get_option('dbem_locations_enabled') ){
			$sub_nav[] = array(
				'name' => __( 'My Locations', 'events-manager'),
				'slug' => 'my-locations',
				'parent_slug' => em_bp_get_slug(),
				'parent_url' => $em_link,
				'screen_function' => 'bp_em_my_locations',
				'position' => 40,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
		
		if( $can_manage_bookings && get_option('dbem_rsvp_enabled') ){
			$sub_nav[] = array(
				'name' => __( 'My Event Bookings', 'events-manager'),
				'slug' => 'my-bookings',
				'parent_slug' => em_bp_get_slug(),
				'parent_url' => $em_link,
				'screen_function' => 'bp_em_my_bookings',
				'position' => 50,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}*/
	
		if( bp_is_active('groups') ){
			/* Create Profile Group Sub-Nav */
			$sub_nav[] = array(
				'name' => __( 'Documents', 'events-manager'),
				'slug' => 'group-documents',
				'parent_slug' => bp_get_groups_slug(),
				'parent_url' =>trailingslashit( bp_displayed_user_domain() . bp_get_groups_slug() ),
				'screen_function' => 'bp_my_aia_group_documents',
				'position' => 70,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
		
		parent::setup_nav( $main_nav, $sub_nav );
		add_action( 'bp_init', array(&$this, 'setup_group_nav') );
	}
	
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		global $bp, $blog_id;
	
		// Prevent debug notices
		$wp_admin_nav = array();
	
		// Menus for logged in user
		if ( is_user_logged_in() ) {
			//check multisite or normal mode for correct permission checking
			if(is_multisite() && $blog_id != BP_ROOT_BLOG){
				//FIXME MS mode doesn't seem to recognize cross subsite caps, using the proper functions, for now we use switch_blog.
				$current_blog = $blog_id;
				switch_to_blog(BP_ROOT_BLOG);
				$can_manage_events = current_user_can_for_blog(BP_ROOT_BLOG, 'edit_events');
				$can_manage_locations = current_user_can_for_blog(BP_ROOT_BLOG, 'edit_locations');
				$can_manage_bookings = current_user_can_for_blog(BP_ROOT_BLOG, 'manage_bookings');
				switch_to_blog($current_blog);
			}else{
				$can_manage_events = current_user_can('edit_events');
				$can_manage_locations = current_user_can('edit_locations');
				$can_manage_bookings = current_user_can('manage_bookings');
			}

			$em_link = trailingslashit( bp_loggedin_user_domain() . em_bp_get_slug() );
			
			/* Add 'Events' to the main user profile navigation */
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-my-aia' . $this->id,
				'title'  => __( 'Events', 'events-manager'),
				'href'   => $em_link
			);
			
			/* Create SubNav Items */
			$wp_admin_nav[] = array(
				'parent' => 'my-my-aia' . $this->id,
				'id'     => 'my-my-aia' . $this->id .'-profile',
				'title'  => __( 'My Profile', 'events-manager'),
				'href'   => $em_link.'profile/'
			);
			
			$wp_admin_nav[] = array(
				'parent' => 'my-my-aia' . $this->id,
				'id'     => 'my-my-aia' . $this->id .'-attending',
				'title'  => __( 'Events I\'m Attending', 'events-manager'),
				'href'   => $em_link.'attending/'
			);
			
			if( $can_manage_events ){
				$wp_admin_nav[] = array(
					'parent' => 'my-my-aia' . $this->id,
					'id'     => 'my-my-aia' . $this->id .'-my-events',
					'title'  => __( 'My Events', 'events-manager'),
					'href'   => $em_link.'my-events/'
				);
			}
			
			if( $can_manage_locations && get_option('dbem_locations_enabled') ){
				$wp_admin_nav[] = array(
					'parent' => 'my-my-aia' . $this->id,
					'id'     => 'my-my-aia' . $this->id .'-my-locations',
					'title'  => __( 'My Locations', 'events-manager'),
					'href'   => $em_link.'my-locations/'
				);
			}
			
			if( $can_manage_bookings && get_option('dbem_rsvp_enabled') ){
				$wp_admin_nav[] = array(
					'parent' => 'my-my-aia' . $this->id,
					'id'     => 'my-my-aia' . $this->id .'-my-bookings',
					'title'  => __( 'My Event Bookings', 'events-manager'),
					'href'   => $em_link.'my-bookings/'
				);
			}
			
			if( bp_is_active('groups') ){
				/* Create Profile Group Sub-Nav */
				$wp_admin_nav[] = array(
					'parent' => 'my-account-groups',
					'id'     => 'my-account-groups-' . $this->id ,
					'title'  => __( 'Events', 'events-manager'),
					'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() ) . 'group-events/'
				);
			}			
		}
	
		parent::setup_admin_bar( $wp_admin_nav );
	}
	
	function setup_group_nav(){
		global $bp;	
		/* Add some group subnav items */
		$user_access = false;
		$group_link = '';
		if( bp_is_active('groups') && !empty($bp->groups->current_group) ){
			$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
			$user_access = $bp->groups->current_group->user_has_access;
			if( !empty($bp->current_component) && $bp->current_component == 'groups' ){
				$count = EM_Events::count(array('group'=>$bp->groups->current_group->id));
				if( empty($count) ) $count = 0;
			}
			bp_core_new_subnav_item( array( 
				'name' => __( 'Documents', 'events-manager') . " <span>$count</span>",
				'slug' => 'documents', 
				'parent_url' => $group_link, 
				'parent_slug' => $bp->groups->current_group->slug,
				'screen_function' => 'my_aia_bp_group_documents', 
				'position' => 50, 
				'user_has_access' => $user_access, 
				'item_css_id' => 'forums' 
			));
		}
	}
}

if( !is_admin() || ( defined('DOING_AJAX') && !empty($_REQUEST['is_public'])) ){
	/*
	 * Links and URL Rewriting
	 */
	function my_aia_bp_rewrite_edit_url($url, $EM_Event){
		global $bp;
		return $bp->events->link.'my-events/?action=edit&event_id='.$EM_Event->event_id;
	}
	if( !get_option('dbem_edit_events_page') ){
		add_filter('em_event_get_edit_url','em_bp_rewrite_edit_url',10,2);
	}	
	
	function my_aia_bp_rewrite_bookings_url($url, $EM_Event){
		global $bp;
		return $bp->events->link.'my-bookings/?event_id='.$EM_Event->event_id;
	}
	if( !get_option('dbem_edit_bookings_page') ){
		add_filter('em_event_get_bookings_url','em_bp_rewrite_bookings_url',10,2);
	}
	
	function my_aia_bp_rewrite_edit_location_url($url, $EM_Location){
		global $bp;
		return $bp->events->link.'my-locations/?action=edit&location_id='.$EM_Location->location_id;
	}
	if( !get_option('dbem_edit_locations_page') ){
		add_filter('em_location_get_edit_url','em_bp_rewrite_edit_location_url',10,2);
	}
}

function my_aia_bp_group_setting_meta_box() {
	add_meta_box( 'bp_group_settings_category', _x( 'Settings', 'group admin edit screen', 'buddypress' ), 'my_aia_bp_groups_admin_edit_metabox_category', get_current_screen()->id, 'normal', 'core' );
	add_meta_box( 'bp_group_settings_location', _x( 'Settings', 'group admin edit screen', 'buddypress' ), 'my_aia_bp_groups_admin_edit_metabox_location', get_current_screen()->id, 'normal', 'core' );
}
add_action('bp_groups_admin_meta_boxes',  'my_aia_bp_group_setting_meta_box');

function my_aia_bp_groups_admin_edit_metabox_location() {
	echo "Here comes the Location info";
}

function my_aia_bp_groups_admin_edit_metabox_category() {
	echo "Here comes the Category info";
}

/**
 * Handle the display of a group's admin/group-settings page.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_location() {

	if ( 'group-settings' != bp_get_group_current_admin_tab() )
		return false;

	if ( ! bp_is_item_admin() )
		return false;

	$bp = buddypress();

	// If the edit form has been submitted, save the edited details.
	if ( isset( $_POST['save'] ) ) {
		$enable_forum   = ( isset($_POST['group-show-forum'] ) ) ? 1 : 0;

		// Checked against a whitelist for security.
		/** This filter is documented in bp-groups/bp-groups-admin.php */
		$allowed_status = apply_filters( 'groups_allowed_status', array( 'public', 'private', 'hidden' ) );
		$status         = ( in_array( $_POST['group-status'], (array) $allowed_status ) ) ? $_POST['group-status'] : 'public';

		// Checked against a whitelist for security.
		/** This filter is documented in bp-groups/bp-groups-admin.php */
		$allowed_invite_status = apply_filters( 'groups_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
		$invite_status	       = isset( $_POST['group-invite-status'] ) && in_array( $_POST['group-invite-status'], (array) $allowed_invite_status ) ? $_POST['group-invite-status'] : 'members';

		// Check the nonce.
		if ( !check_admin_referer( 'groups_edit_group_settings' ) )
			return false;

		if ( !groups_edit_group_settings( $_POST['group-id'], $enable_forum, $status, $invite_status ) ) {
			bp_core_add_message( __( 'There was an error updating group settings. Please try again.', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Group settings were successfully updated.', 'buddypress' ) );
		}

		/**
		 * Fires before the redirect if a group settings has been edited and saved.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the group that was edited.
		 */
		do_action( 'groups_group_settings_edited', $bp->groups->current_group->id );

		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin/group-settings/' );
	}

	/**
	 * Fires before the loading of the group admin/group-settings page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_settings', $bp->groups->current_group->id );

	/**
	 * Filters the template to load for a group's admin/group-settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's admin/group-settings template.
	 */
	bp_core_load_template( apply_filters( 'groups_template_group_admin_settings', 'groups/single/home' ) );
}
add_action( 'bp_screens', 'groups_screen_group_admin_location' );


//CSS and JS Loading
function bp_my_aia_enqueue_scripts( ){
	if( bp_is_current_component('events') || (bp_is_current_component('groups') && bp_is_current_action('group-events')) ){
	    add_filter('option_dbem_js_limit', create_function('$args','return false;'));
	    add_filter('option_dbem_css_limit', create_function('$args','return false;'));
	}
	
}
add_action('wp_enqueue_scripts','bp_em_enqueue_scripts',1);

function bp_my_aia_messages_js_compat() {
	if(bp_is_messages_compose_screen()){
		wp_deregister_script( 'events-manager' );
	}
}
add_action( 'wp_print_scripts', 'bp_em_messages_js_compat', 100 );

/**
 * Delete events when you delete a user.
 */
function bp_my_aia_remove_data( $user_id ) {
	$EM_Events = EM_Events::get(array('scope'=>'all','owner'=>$user_id, 'status'=>false));
	EM_Events::delete($EM_Events);
}
add_action( 'wpmu_delete_user', 'bp_my_aia_remove_data', 1 );
add_action( 'delete_user', 'bp_my_aia_remove_data', 1 );

define('MY_AIA_BP_LOADED',true); //so we know
?>