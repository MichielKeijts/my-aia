<?php

/**
 * (C) Michiel Keijts 2016
 * Some functions to edit the template for Buddypress
 * 
 * 
 * This file loads a few extra styles for the template of AIA.
 * To prevent this, give the name of the BuddyPress component which is loaded
 * so no extra HTML is added
 */


/***_____ REGISTRATION PAGES _____****/
/**
 * Set Title , add 2 divs
 */
function my_aia_bp_before_register_page() {
	?>
	<div id="register-page-container">
		<div class="block">
	<h1><?= __('Registreer voor Athletes in Action'); ?></h1>
	<?php
}
/**
 * Close the 2 div's
 */
function my_aia_bp_after_register_page() {
	?>
		</div>
	</div>
	<?php
}
/**
 * Add 'More information
 */
function my_aia_bp_before_registration_submit_buttons() {
	?>
	<div class="" style='float: left;margin-top: 25px;'><a href='<?= wp_login_url(MY_AIA_BP_ROOT); ?>'><?= __('Ik heb al een login','my-aia'); ?></a></div>
	<div class="submit"><a href="#" class="button blauw" id="registration-profile-open-button">Verder &gt;&gt;</a></div>
	<?php
}
add_filter( 'bp_before_register_page', 'my_aia_bp_before_register_page');
add_filter( 'bp_after_register_page', 'my_aia_bp_after_register_page');
add_filter( 'bp_before_registration_submit_buttons', 'my_aia_bp_before_registration_submit_buttons' );

/***_____  END REGISTRATION PAGES _____****/





function do_template_modification() {
	// array of components to not modify:
	$_do_not_load = array('orders', 'documents');
	
	if (in_array(buddypress()->current_component, $_do_not_load)) return FALSE;
	return TRUE;
}


function my_aia_is_orders() {
	return (bool) bp_is_current_component('orders');
}

function my_aia_is_documents() {
	return (bool) bp_is_current_component('documents');
}

/**
 * Load
 */
function my_aia_bp_before_template_content() {
	if (!defined("MY_AIA_BEFORE_TEMPLATE_LOADED"))
		return my_aia_bp_before_profile_content ();
	return "";
}


/**
 * Create div tiles in Buddypress
 */
function my_aia_bp_before_member_body_default() {
	if (!do_template_modification()) return "";
	?>
		<section class="buddy-press buddypress-tiles">
			<div class="column-wrapper">
				<div class="column-4-1 column-md-3-1 column-sm-1">
					<div class="raster events-filter-label">
						<div class="column-inner">
	<?php
}

function my_aia_bp_before_profile_content() {
	if (!do_template_modification()) return "";
	// break column-4-1
	// get new class
	?>
						</div>
					</div>
				</div>
				<div class="column-4-3 column-md-3-2 column-sm-1">
					<div class="raster buddypress-content">
						<div class="column-inner">
						
	<?php
}

/**
 * Above GROUPS
 * @return string
 */
function my_aia_bp_before_groups_content() {
	if (!do_template_modification() || defined("MY_AIA_BEFORE_TEMPLATE_LOADED")) return "";
	
	define("MY_AIA_BEFORE_TEMPLATE_LOADED", TRUE); // execute once!
	// break column-4-1
	// get new class
	?>
						</div>
					</div>
				</div>
				<div class="column-4-3 column-md-3-2 column-sm-1">
					<div class="raster buddypress-content">
						<div>
						
	<?php
}

function my_aia_bp_after_member_body_default () {
	if (!do_template_modification()) return "";
	?>
						</div>
					</div>
				</div>
			</div>
		</section>
	<?php
}

global $EM_Notices;
add_action( 'bp_before_member_activity_post_form', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_profile_content', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_member_groups_content', 'my_aia_bp_before_groups_content');
add_action( 'bp_before_member_notifications_content', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_member_messages_content', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_group_plugin_template', 'my_aia_bp_before_profile_content');
add_action( 'bp_template_title', 'my_aia_bp_before_template_content', -1);
add_action( 'bp_after_members_directory_order_options', 'my_aia_bp_before_profile_content');

// groups
add_action( 'bp_before_group_activity_post_form', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_group_admin_content', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_group_members_content', 'my_aia_bp_before_profile_content');


//add_action( 'bp_before_events_content', 'my_aia_bp_before_profile_content');
add_action( 'bp_before_member_body', 'my_aia_bp_before_member_body_default');
add_action( 'bp_after_member_body', 'my_aia_bp_after_member_body_default');
add_action( 'bp_before_directory_members_tabs', 'my_aia_bp_before_member_body_default');
add_action( 'bp_after_directory_members', 'my_aia_bp_after_member_body_default');
add_action( 'bp_before_group_body', 'my_aia_bp_before_member_body_default');
add_action( 'bp_after_group_body', 'my_aia_bp_after_member_body_default');