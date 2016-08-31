<?php
/**
 * MY_AIA BuddyPress Manager component for BuddyPress
 * @author marcus, Michiel Keijts
 * @since 5.0
 */
class BP_MY_AIA_ORDER_Component extends BP_Component {
	
	function __construct() {
		global $bp;
		parent::start('orders',	__('Orders', 'my-aia'), MY_AIA_PLUGIN_DIR);
		$this->includes();
		//TODO make BP component optional
		$bp->active_components[$this->id] = '1';
	}

	function includes( $includes = array() ) {
		// Files to include
		$includes = array(
			//'addons/buddypress/bp-my-aia-activity.php',
			//'addons/buddypress/bp-my-aia-templatetags.php',
			//'addons/buddypress/my-aia-bp-notifications.php',
			//'addons/buddypress/screens/profile.php',
			'addons/buddypress/screens/orders.php',
			'addons/buddypress/screens/my-order-edit.php',
			'addons/buddypress/screens/order-status.php',
			//'addons/buddypress/screens/attending.php',
			//'addons/buddypress/screens/my-bookings.php',
			//'addons/buddypress/screens/my-order.php'
		);
		parent::includes( $includes );
		//TODO add admin pages for extra BP specific settings
	}

	/**
	 * Sets up the global MY_AIA Manager BuddyPress Components
	 */
	function setup_globals( $args = array() ) {
		global $bp, $wpdb;
		// Define a slug constant that will be used to view this components pages
		if ( !defined( 'BP_MY_AIA_ORDERS_SLUG' ) )
			define ( 'BP_MY_AIA_ORDERS_SLUG', str_replace('/','-', 'orders') );

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_MY_AIA_ORDERS_SLUG,
			'has_directory'         => false, //already done by EM
			'notification_callback' => 'my_aia_bp_format_notifications',
			'search_string'         => sprintf(__( 'Search %s...', 'my-aia'),__('Orders','events-manager')),
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );

		//quick link shortcut - may need to revisit this
		$bp->{$this->id}->link = trailingslashit($bp->loggedin_user->domain).BP_MY_AIA_ORDERS_SLUG.'/';
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
			'name' => __( 'Mijn Orders', 'my-aia'),
			'slug' => BP_MY_AIA_ORDERS_SLUG,
			'position' => 90,
			'screen_function' => 'my_aia_bp_orders',
			'default_subnav_slug' => 'none',
			//'user_has_access' => bp_is_my_profile()
		);

		$my_aia_link = trailingslashit( bp_displayed_user_domain() . 'orders' );
		
		/* Create SubNav Items */
		$sub_nav[] = array(
			'name' => __( 'Mijn Order Aanpassen', 'my-aia'),
			'slug' => 'my-order-edit',
			'parent_slug' => BP_MY_AIA_ORDERS_SLUG,
			'parent_url' => $my_aia_link,
			'screen_function' => 'my_aia_bp_my_order_edit',
			'position' => 10,
			'user_has_access' => true,//bp_is_my_profile()
		);
		
		$sub_nav[] = array(
			'name' => __( 'Bestelling Status', 'my-aia'),
			'slug' => 'status',
			'parent_slug' => BP_MY_AIA_ORDERS_SLUG,
			'parent_url' => $my_aia_link,
			'screen_function' => 'my_aia_bp_my_order_status',
			'position' => 20,
			'user_has_access' => true,//bp_is_my_profile(), // Only the logged in user can access this on his/her profile
		);
	
		/*if( $can_manage_events ){
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
		
		parent::setup_nav( $main_nav, $sub_nav );
		//add_action( 'bp_init', array(&$this, 'setup_group_nav') );
	}
	
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		global $bp, $blog_id;
		return parent::setup_admin_bar( $wp_admin_nav );
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
}


//CSS and JS Loading
function bp_my_aia_orders_enqueue_scripts( ){
	/*if( bp_is_current_component('events') || (bp_is_current_component('groups') && bp_is_current_action('group-events')) ){
	    add_filter('option_dbem_js_limit', create_function('$args','return false;'));
	    add_filter('option_dbem_css_limit', create_function('$args','return false;'));
	}*/
	
}
add_action('wp_enqueue_scripts','bp_em_enqueue_scripts',1);

function bp_my_aia_order_messages_js_compat() {
	/*if(bp_is_messages_compose_screen()){
		wp_deregister_script( 'events-manager' );
	}*/
}
add_action( 'wp_print_scripts', 'bp_em_messages_js_compat', 100 );


define('MY_AIA_BP_MY_ORDERS',true); //so we know
?>