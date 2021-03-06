<?php
/*
 * @package	my-aia
 * @author Michiel Keijts (c)2016
 * @copyright (C) Normit 2016
 */

// Define actions
register_activation_hook('my-aia-install.php','my_aia_install');

add_action( 'plugins_loaded', 'MY_AIA::load_textdomain', 0 );
add_action( 'add_meta_boxes', 'MY_AIA::admin_add_metaboxes', 999 );

// Register taxnonomies
add_action( 'init', 'my_aia_register_taxonomy_sport' );
add_action( 'init', 'my_aia_register_taxonomy_sport_level' );
add_action( 'init', 'my_aia_register_taxonomy_taal' );
add_action( 'init', 'my_aia_register_taxonomy_sportweek_eigenschap' );
add_action( 'init', 'my_aia_register_taxonomy_kerkstroming' );
add_action( 'init', 'my_aia_register_taxonomy_overnachting' );
add_action( 'init', 'my_aia_register_taxonomy_product_categorie' );
add_action( 'init', 'my_aia_register_taxonomy_sportbetrokkenheid' );

// Used for user-taxonomy
add_action( 'bp_custom_profile_edit_fields', 'my_aia_edit_user', 3);						// s
add_action( 'bp_xprofile_get_field_types', "my_aia_bp_xprofile_get_field_types", 99, 1);	// show extra field types for BuddyPress
add_action( 'xprofile_fields_saved_field', "my_aia_xprofile_fields_saved_field", 99, 1);
add_action( 'xprofile_updated_profile', "MY_AIA_XPROFILE_CHANGE_MODERATE::xprofile_updated_profile", 10, 5);

// Used for adding Ninja Forms to EM_Booking formulier
add_action( 'em_booking_validate'	,	"my_aia_em_validate_ninja_form", 99, 2);						// add ninja form validation to EM Booking process
add_action( 'em_booking_get_post'	,	"my_aia_em_booking_add_from_post", 99, 2);						// add ninja form data to EM Booking object (Meta)
add_action( 'em_booking_form_custom',	"my_aia_em_bookings_show_ninja_form", 99, 1 );					// add ninja form to EM booking form
add_action( 'ninja_forms_display_show_form', 'my_aia_ninja_forms_display_show_form', 10,2);				// return bool to show or hide form
add_action( 'em_bookings_single_custom',"my_aia_em_bookings_show_ninja_form_from_booking",99, 1);		// add ninja form to customize EM booking
add_action( 'em_events_build_sql_conditions',"my_aia_events_manager_group_recurrence",99, 1);			// add ninja form to customize EM booking
add_action( 'em_bookings_get_tickets',	'my_aia_em_bookings_remove_ninja_form', 99, 2);					// remove ninja form from EM Event page
add_action( 'em_person_display_summary','my_aia_events_manager_profile_display_summary', 99, 2);
add_action( 'em_booking_set_status',	'my_aia_events_manager_registration_sugar_sync', 10, 2);		// update SUGARCRM 
add_action( 'em_booking_delete',		'my_aia_events_manager_registration_sugar_sync', 10, 2);		// update SUGARCRM, with deleted..
add_action( 'em_event_save',			'my_aia_events_post_to_sugarcrm', 10, 2);						// update and save event to SUGARCRM
add_action( 'em_event_save',			'my_aia_events_manager_remove_hook_bp_em_group_event_save',1,1);// remove post save hook

add_action( 'nf_email_notification_attachments',			'my_aia_nf_add_pdf_attachment', 10, 2);		// hook to attach PDF to conformation email, using current booking
add_action( 'em_booking_email_notification_attachments',	'my_aia_nf_add_pdf_attachment', 10, 3);		// hook to attach PDF to conformation email, using current booking
add_action( 'nf_email_notification_attachment_types',		'my_aia_nf_add_attachment_types', 10, 1);	// hook to register other attachment options
add_action( 'ninja_forms_pre_process',						'my_aia_nf_add_mailchimp', 10, 1);			// preprocess and check for mailchimp field
add_action( 'ninja_forms_before_form_display',				'my_aia_show_default_profile_values_for_user_add_to_registration_form', 10,1);

//add_action( 'nf_email_notification_process_setting', 'my_aia_ninja_forms_email_to_address_group_leader', 10, 3);
add_action( 'init', 'my_aia_ninja_forms_upload_field_register' );
add_action( 'init', 'my_aia_ninja_forms_term_field_register' );

// Hooks for custom post save --> not enabled yet
//add_action( 'save_post',  'my_aia_post_save_action', 99, 2);

// Ajax Functions for Front Page
add_action( 'wp_ajax_my_aia_call', "MY_AIA::my_aia_ajax_call", 10, 1	);					// AJAX hook to get Events 	
add_action( 'wp_ajax_nopriv_my_aia_call', "MY_AIA::my_aia_ajax_call", 10, 1	);				// AJAX hook to get Events, no login

// Filter function for BLOG
add_action( 'pre_get_posts', "MY_AIA::get_my_blog_items_query", 10, 1	);				// AJAX hook to get Events, no login

//add_action( 'edit_user_profile', 'my_aia_edit_user' );

// Main init function, before anything else
add_action ( 'init', 'MY_AIA::init', -1, 1);