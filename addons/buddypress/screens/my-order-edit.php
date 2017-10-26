<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_my_order_edit() {
	return my_aia_order()->my_aia_bp_my_order_edit();
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