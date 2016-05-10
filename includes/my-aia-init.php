<?php
/*
 * @package	my-aia
 * @author Michiel Keijts (c)2016
 * @copyright (C) Normit 2016
 */

// Define actions
register_activation_hook('my-aia-install.php','my_aia_install');

add_action( 'plugins_loaded', 'MY_AIA::load_textdomain', 0 );
add_action( 'init', 'my_aia_register_taxonomy_sport' );
add_action( 'init', 'my_aia_register_taxonomy_sport_level' );
add_action( 'init', 'my_aia_register_taxonomy_taal' );
add_action( 'init', 'my_aia_register_taxonomy_sportweek_eigenschap' );
add_action( 'init', 'my_aia_register_taxonomy_kerkstroming' );
add_action( 'init', 'my_aia_register_taxonomy_overnachting' );
add_action( 'init', 'my_aia_register_posttype_partner' );
add_action( 'init', 'my_aia_register_taxonomy_sportbetrokkenheid' );

// Used for user-taxonomy
add_action( 'bp_custom_profile_edit_fields', 'my_aia_edit_user', 3);						// s
add_action( 'bp_xprofile_get_field_types', "my_aia_bp_xprofile_get_field_types", 99, 1);	// show extra field types for BuddyPress
add_action( 'xprofile_fields_saved_field', "my_aia_xprofile_fields_saved_field", 99, 1);

// Used for adding Ninja Forms to EM_Booking formulier
add_action( 'em_booking_validate'	,	"my_aia_em_validate_ninja_form", 99, 2);						// add ninja form validation to EM Booking process
add_action( 'em_booking_get_post'	,	"my_aia_em_booking_add_from_post", 99, 2);						// add ninja form data to EM Booking object (Meta)
add_action( 'em_booking_form_custom',	"my_aia_em_bookings_show_ninja_form", 99, 1 );					// add ninja form to EM booking form
add_action( 'em_bookings_single_custom',"my_aia_em_bookings_show_ninja_form_from_booking",99, 1);		// add ninja form to customize EM booking
add_action( 'em_bookings_get_tickets',	'my_aia_em_bookings_remove_ninja_form', 99, 2);					// remove ninja form from EM Event page
add_action( 'init', 'my_aia_ninja_form_upload_field_register' );


//add_action( 'edit_user_profile', 'my_aia_edit_user' );

add_action ( 'init', 'MY_AIA::init');

//add_action ('nf_notification_types', 'my_aia_test_notification');