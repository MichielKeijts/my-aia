<<<<<<< HEAD
<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function my_aia_bp_my_documents() {
	global $bp, $EM_Event;
	/*(if( !is_object($EM_Event) && !empty($_REQUEST['event_id']) ){
		$EM_Event = new EM_Event($_REQUEST['event_id']);
	}*/
	
	do_action( 'my_aia_bp_documents' );
	
	$template_title = 'my_aia_bp_documents_title';
	$template_content = 'my_aia_bp_documents_content';

	if( !empty($_GET['action']) ){
		switch($_GET['action']){
			case 'edit':
				$template_title = 'my_aia_bp_documents_editor_title';
				break;
		}
	}

	add_action( 'bp_template_title', $template_title );
	add_action( 'bp_template_content', $template_content );
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function my_aia_bp_my_documents_title() {
	_e( 'My Documents', 'documents-manager');
}

/**
 * Determines whether to show event page or documents page, and saves any updates to the event or documents
 * @return null
 */
function my_aia_bp_my_documents_content() {
	//bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'buddypress/screens/my-documents' ) );
	em_locate_template('buddypress/my-documents.php', true);
}

function my_aia_bp_my_documents_editor_title() {
	global $EM_Event;
	if( is_object($EM_Event) ){
		if($EM_Event->is_recurring()){
			echo __( "Reschedule Events", 'my-aia')." '{$EM_Event->event_name}'";
		}else{
			echo __( "Edit Event", 'my-aia') . " '" . $EM_Event->event_name . "'";
		}
	}else{
		_e( 'Add Event', 'my-aia');
	}
}
?>
=======
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
>>>>>>> MichielKeijts/master
