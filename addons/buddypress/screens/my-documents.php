<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_my_documents() {
	global $bp;
	do_action( 'bp_my_aia_my_documents' );
	
	add_action( 'bp_template_title', 'my_aia_bp_documents_title' );
	add_action( 'bp_template_content', 'my_aia_bp_documents_content' );
	
	// get contents and save in $self::$_viewVars
	MY_AIA::set('documents', MY_AIA::get_documents(get_current_user_id()));
	
	// hide header
	MY_AIA::hide_buddypressheader();
	
	MY_AIA::set_navigationbar();
	
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/home' ) );
	//my_aia_locate_template('buddypress/members/single/page.php', true);
}
	
function my_aia_bp_documents_title() {
	echo __( 'Mijn Documenten', 'my-aia');
}
/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function my_aia_bp_documents_content() {
	// Create a custom post type (document) from COOKIE vars	
	my_aia_locate_template('buddypress/my-documents.php', true, MY_AIA::$_viewVars);
}