<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_my_order_edit() {
	return my_aia_order()->my_aia_bp_my_order_edit();
	
	global $bp;
	do_action( 'bp_my_aia_my_orders' );
	
	add_action( 'bp_template_title', 'my_aia_bp_my_order_edit_title' );
	add_action( 'bp_template_content', 'my_aia_bp_my_order_edit_content' );
	
	// check for make payment
	if (filter_input(INPUT_GET, '_method') === 'make_payment') {
		if ($order_id = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT)) {
			$order = my_aia_order()->ORDER->get($oder_id);
			if ($order->assigned_user_id == bp_current_user_id());
		}
	}
	
	// get contents
	MY_AIA::$controllers[MY_AIA_POST_TYPE_ORDER]->prepare_shopping_cart_items(bp_current_user_id());
	
	if (filter_input(INPUT_POST, '_method') === 'create') {
		my_aia_order()->create_and_place_order();
	}
		
	// hide header
	MY_AIA::hide_buddypressheader();
	
	MY_AIA::set_navigationbar(array(
		'current_title' =>	__( 'Mijn Bestelling', 'my-aia'),
		'nav'			=>	NULL,
		'title'			=>	'Winkelwagen',
	));
	
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/home' ) );
	//my_aia_locate_template('buddypress/members/single/page.php', true);
}
	
function my_aia_bp_my_order_edit_title() {
	__( 'Mijn Bestelling', 'my-aia');
}
/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function my_aia_bp_my_order_edit_content() {
	// Create a custom post type (order) from COOKIE vars	
	my_aia_locate_template('buddypress/my-order-edit.php', true, MY_AIA::$_viewVars);
}
?>