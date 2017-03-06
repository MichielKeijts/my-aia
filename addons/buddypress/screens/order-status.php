<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_my_order_status() {
	return my_aia_order()->my_aia_bp_my_order_status();
	global $bp;
	do_action( 'bp_my_aia_my_orders' );
	
	// get contents
	if (is_numeric(filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT))) {
		// set POST
		my_aia_order()->get(filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT));
		
		// no acces
		if (!(bp_current_user_id() == my_aia_order()->assigned_user_id || is_admin())) {
			wp_die('No Access');
		}
		
		my_aia_order()->confirm_order();
	} else {return FALSE;}
	
	add_action( 'bp_template_title', 'my_aia_bp_my_order_status_title' );
	add_action( 'bp_template_content', 'my_aia_bp_my_order_status_content' );
		
	// hide header
	//MY_AIA::hide_buddypressheader();
	
	//MY_AIA::set_navigationbar();
	
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/home' ) );
	//my_aia_locate_template('buddypress/members/single/page.php', true);
}
	
function my_aia_bp_my_order_status_title() {
	__( 'Mijn Order Status', 'my-aia');
}
/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function my_aia_bp_my_order_status_content() {
	// Create a custom post type (order) from COOKIE vars	
	my_aia_locate_template('buddypress/my-order-status.php', true, MY_AIA::$_viewVars);
}
?>