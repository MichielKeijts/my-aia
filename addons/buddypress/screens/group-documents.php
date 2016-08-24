<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_group_documents() {
	global $bp;
	do_action( 'bp_my_aia_group_documents' );
	
	//plug into EM admin code (at least for now)
	//include_once(EM_DIR.'/admin/em-admin.php');
	
	add_action( 'bp_template_title', 'my_aia_bp_group_documents_title' );
	add_action( 'bp_template_content', 'my_aia_bp_group_documents_content' );
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' ) );
}

function my_aia_bp_group_documents_title() {
	_e( 'Group Documents', 'my-aia');
}
/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function my_aia_bp_group_documents_content() {
	my_aia_locate_template('buddypress/group-documents.php', true);
}

?>