<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_orders() {
	global $bp;
	do_action( 'bp_my_aia_my_orders' );
	
	add_action( 'bp_template_title', 'my_aia_bp_orders_title' );
	add_action( 'bp_template_content', 'my_aia_bp_orders_content' );
	
	// get contents and save in $self::$_viewVars
	MY_AIA::set('orders', MY_AIA::get_my_orders(get_current_user_id()));
	
	// hide header
	MY_AIA::hide_buddypressheader();
	
	MY_AIA::set_navigationbar();
	
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/home' ) );
	//my_aia_locate_template('buddypress/members/single/page.php', true);
}
	
function my_aia_bp_orders_title() {
	echo __( 'Mijn Orders', 'my-aia');
}
/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function my_aia_bp_orders_content() {
	// Create a custom post type (order) from COOKIE vars	
	my_aia_locate_template('buddypress/my-orders.php', true, MY_AIA::$_viewVars);
}

?>