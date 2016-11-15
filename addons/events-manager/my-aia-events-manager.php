<?php

/** 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * 
 * 
 * This file contains all the custom function for the hooks to add to the
 * Events Manager plugin (events-manager)
 * 
 * 
 */


/**
 * Show the Ninja Form (if isset) to the EM_EVENT form. This adds a lot of extra
 * functionality to the Events.
 * 
 * It does not add post-registration or submit actions of the EM_FORM
 * 
 * @param \EM_Event $EM_Event
 */
function my_aia_em_bookings_show_ninja_form(\EM_Event $EM_Event) {
	if( function_exists( 'ninja_forms_display_form' ) ){ 
		if (isset ($EM_Event->attributes['ninja_forms_form'])) {
			// Ok, now ready to display form, but check if person is not already attending
			//if ($EM_Event->get_bookings()->has_booking() === FALSE) {
				// remove the nonce, as it will overwrite the EM Event nonce. Not desirable
				remove_action("ninja_forms_display_after_open_form_tag", "nf_form_nonce");
				
				// global variable, to only prepend form at reservation page.
				define('MY_AIA_DO_PREPEND_FORM', true);
				ninja_forms_display_form( $EM_Event->attributes['ninja_forms_form'] ); 
			//}
		}
	}
}


/**
 * Parse and display a ninja form, to edit the booking information
 * @param \EM_Booking $EM_Booking
 * @global Ninja_Forms_Processing $ninja_forms_processing
 */
function my_aia_em_bookings_show_ninja_form_from_booking(\EM_Booking $EM_Booking) {
	global $EM_Booking, $ninja_forms_processing;
	$EM_Event = $EM_Booking->get_event();
	
	// set the default value 
	add_action('ninja_forms_field', 'my_aia_em_set_ninja_forms_field_default_value', 10, 2); 
	
	// forward to booking form
	$extra_style = "";
	if (filter_input(INPUT_POST, '_ninja_forms_display_submit') === FALSE) {
		$extra_style="style='display: none;'";
	}
	echo "<div class=stuffbox><h3>Aanmeldformulier</h3>";
	my_aia_em_bookings_show_ninja_form($EM_Event);
	echo "</div></div>";
	
	if (isset($ninja_forms_processing) && is_a($ninja_forms_processing, "Ninja_Forms_Processing") && count($ninja_forms_processing->get_all_errors()) > 0) {
		echo '<script>jQuery(function ($) {
			$(".em-booking-single-info").hide();	
			$(".em-booking-single-edit").show();;
			$(".em-booking-single-status-info").hide();
			$(".em-booking-single-status-edit").show();
		});
		</script>';
	}
}

/**
 * Function to remove a ninja_form. In post_meta of all posts a ninja_forms_form can be located
 * When this is the case, we need to remove the ninja_form, when post_type is EM_POST_TYPE
 * @param \EM_Tickets $ticket
 * @param \EM_Bookings $booking
 * @return \EM_Bookings
 */
function my_aia_em_bookings_remove_ninja_form(\EM_Tickets $ticket, \EM_Bookings $booking) {
	global $ninja_forms_append_page_form_id;
	
	$ninja_forms_append_page_form_id=FALSE;

	return $ticket;
}

/**
 * Function reads user data from the booking form / meta and sets the default_value
 * of the ninja forms field
 * @param array $data
 * @param int $field_id
 * @global \EM_Booking $EM_Booking
 * @return array modified $data
 */
function my_aia_em_set_ninja_forms_field_default_value($data, $field_id) {
	global $EM_Booking, $current_user;
	
	// EM booking meta is always saved as $key = $data['admin_label']
	if (array_key_exists($data['admin_label'], $EM_Booking->booking_meta)) {
		$data[ 'default_value' ] = $EM_Booking->booking_meta[	$data[ 'admin_label' ]	];
	}
	
	return $data;
}




/**
 * Save Metadata to object. Use all of the fields in the Request, which are not 
 * fields object of the booking.
 * 
 * We use this to add all the input variables from the Ninja Form to the 
 * booking_meta key of the EM_Booking object. In this case all the information
 * is saved free.
 * 
 * - filter the REQUEST DATA, when it starts with ninja_forms_.. add it 
 * - the name of the key in the metadata of the Form is the admin_label property
 * of the ninja_forms_field. In other way of saying: the name of the field is 
 * looked up in the database with all the ninja form fields
 * 
 * @param bool						$error current error state in processing the booking
 * @param \EM_Booking				$caller the \EM_Booking object
 * @gloal \Ninja_Forms_Processing	$ninja_forms_processing
 * @return mixed					\EM_Booking object if succesful or FALSE if error occurred
 */
function my_aia_em_booking_add_from_post ($error,\EM_Booking $caller) {
	// see if we have a ninja form.. _form_id must be set
	$ninja_form_id = filter_input(INPUT_POST, '_form_id');
	if (!$ninja_form_id) 
		return $caller; // no form to process
	
	// we assume ninja form has been validates in the Booking proces
	// using hook em_booking_validate

	// loop over all the request variables
	foreach ($_REQUEST as $key=>$val) {
		if (substr($key, 0, 12) == 'ninja_forms_') {
			// try and find the admin label for this form
			$field_id = (int) str_replace('ninja_forms_field_', '', $key);
			$field = ninja_forms_get_field_by_id($field_id);
			
			// option to convert admin label to the right name of the parameter
			if (isset($field['data']['admin_label']) && !empty($field['data']['admin_label'])) {
				$field_name = $field['data']['admin_label'];
			} else 
				$field_name = $key;
			
			if (!is_array($caller->booking_meta)) $caller->booking_meta = array();
			$caller->booking_meta[$field_name] = $val;
			
			if (strtolower($field_name) == 'mailchimp') {
				// save user to mailchimp
				my_aia_save_to_mailchimp();
			}
		}
	}
	
	// SET Global Variable
	$_SESSION['last_booking_id'] = $caller->booking_id;
	
	
	
	return $caller;
}

/**
 * Preprocess the posted fields and add to MailChimp
 * this is a nasty function, because regardless validation, the user is added if
 * mailchimp is true
 */
function my_aia_nf_add_mailchimp() {
	// loop over all the request variables
	foreach ($_REQUEST as $key=>$val) {
		if (substr($key, 0, 12) == 'ninja_forms_') {
			// try and find the admin label for this form
			$field_id = (int) str_replace('ninja_forms_field_', '', $key);
			$field = ninja_forms_get_field_by_id($field_id);
			
			// option to convert admin label to the right name of the parameter
			if (isset($field['data']['admin_label']) && !empty($field['data']['admin_label'])) {
				$field_name = $field['data']['admin_label'];
			} else 
				$field_name = $key;
			
			if (strtolower($field_name) == 'mailchimp' && $val) {
				// save user to mailchimp
				my_aia_save_to_mailchimp();
			}
		}
	}
}


/**
 * Ninja Form validator. Called in the validation of the booking form to add 
 * validation rules for the ninja form. When this validates, it returns the 
 * \EM_Booking object, otherwise it returns FALSE and updates the error holder
 * 
 * @param bool						$error current error state in processing the booking
 * @param \EM_Booking				$caller the \EM_Booking object
 * @gloal \Ninja_Forms_Processing	$ninja_forms_processing
 * @return mixed					\EM_Booking object if succesful or FALSE if error occurred
 */
function my_aia_em_validate_ninja_form ($error,\EM_Booking $caller) {
	global $ninja_forms_processing;
	
	// see if we have a ninja form.. _form_id must be set
	$ninja_form_id = filter_input(INPUT_POST, '_form_id');
	if (!$ninja_form_id) 
		return $caller; // no form to process
	
	// try to submit (process) ninja form and see if we get errors in return
	// 1. setup the processing class
	my_aia_ninja_forms_setup_processing_class ($ninja_form_id);	
	
	if (!my_aia_ninja_forms_validate()) {
		// not validated, show the errors (add to the \EM_Booking object
		//		$key is unique ID (<field_type>_<field_id>)
		foreach ($ninja_forms_processing->get_all_errors() as $key=>$error) {
			$matches = array();
			// try and find value of field
			if (preg_match('/([0-9]+)/', $key, $matches)) {
				$field_id = (int) $matches[1];
				$field = ninja_forms_get_field_by_id($field_id);
				$caller->errors[] = sprintf('<b>%s</b>: %s', $field['data']['label'], $error['msg']);				
			} /*else 
				$caller->errors[] = $error['msg'];*/
			// Ignore other errors, they give same information mostly
		}	
		
		// return false (instead of \EM_Booking object
		return false;
	}
	
	// all fine	
	return $caller;
}

/**
 * Setup the Form Processing class for validating the Ninja Form Fields
 * from the Event Manager submit form
 * @global \Ninja_Forms_Processing $ninja_forms_processing
 * @param int $form_id
 */
function my_aia_ninja_forms_setup_processing_class( $form_id = '' ){
	global $ninja_forms_processing;

	//Initiate our processing class with our designated global variable.
	$ninja_forms_processing = new Ninja_Forms_Processing($form_id);
	$ninja_forms_processing->setup_submitted_vars();
}

/**
 * Try to process the ninja form, basically same as validating.
 * if fail: 
 *	$ninja_forms_processing->get_all_errors() 
 * is an array with all the errors
 * @global Ninja_Forms_Processing $ninja_forms_processing
 * @return bool on validate succesfully
 */
function my_aia_ninja_forms_validate(){
	global $ninja_forms_processing;

	if(!$ninja_forms_processing->get_all_errors()){
		do_action('ninja_forms_pre_process');	// actual validation
	}

	return (bool) !$ninja_forms_processing->get_all_errors();
}


/**
 * Adds the Ninja Forms widget to the booking form
 */
function my_aia_events_manager_add_form_widget() {
	// ninja form
	add_meta_box('em-event-attributes-ninja-form', __('Aanmeldformulier','my-aia'), "ninja_forms_inner_custom_box", EM_POST_TYPE_EVENT, 'normal', 'high');
	
	
	// custom attributes form
	remove_meta_box('em-event-attributes', EM_POST_TYPE_EVENT, 'normal');
	add_meta_box('em-event-attributes2', __('Attributes','my-aia'), "my_aia_events_manager_add_attributes_form", EM_POST_TYPE_EVENT, 'normal', 'high');
}

function my_aia_events_manager_add_attributes_form() {
	global $EM_Event;
		
	$attributes = em_get_attributes();
	
	$fields = $attributes['names'];
	$values = $attributes['values'];
	
	// strip fields which are not used!
	$displayed_fields = array('ninja_forms_form');

	// return data
	$data = array();
	foreach ($fields as $_field):
		if (in_array($_field, $displayed_fields)) continue; // step over already displayed fields..
		$field = my_aia_get_default_field_type($_field);
		$field['name'] = "em_attributes[{$field['name']}]";
		
		// get value (as usually is an array)
		$value = array_key_exists($field['id'], $EM_Event->event_attributes) ? esc_attr($EM_Event->event_attributes[$field['id']], ENT_QUOTES):'';
		//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
		if (!$value) $value="";

		$field['value'] = $value;
		
		$data[] = $field;
	endforeach; // loop over $fields

	return my_aia_add_attributes_form('em-event-attributes', EM_POST_TYPE_EVENT, $data);
}

/**
 * Filter the Events and group to recurrence
 * @param string $conditions
 * @param mixed $args
 * @global \wpdb $wpdb
 * @return $conditions
 */
function my_aia_events_manager_group_recurrence($conditions, $args=NULL) {
	global $wpdb;
	if (is_admin() && !defined( 'DOING_AJAX' ) && DOING_AJAX)	return $conditions;	// no modifications (yet) for admin interface
	
	/*
	 *  @Todo: find a solution for this:
	 * The complete query with subquery does not work all at once:
	 * SELECT 
    *
			FROM
				aia_dev.aia_em_events AS qw
			WHERE	
				(qw.recurrence_interval = 0 OR qw.recurrence_interval IS NULL) OR
				((qw.recurrence_interval = 1) AND
				qw.event_id IN(SELECT event_id FROM aia_em_events AS qwe WHERE qwe.event_start_date > DATE_FORMAT(NOW(), "%Y-%m-%d") AND qwe.recurrence_id > 0 GROUP BY qwe.recurrence_id))


	 *	Thats why we first try to get the events ids from the subquery and then continue
	 */
	
	$event_ids = wp_cache_get('my_aia_events_manager_group_recurrence','my-aia');
	if (!$event_ids) {
		$_event_ids = array();
		// find all recurrent events, which are closest to today (in future)
		$query = sprintf('SELECT event_id FROM %sem_events WHERE event_start_date > DATE_FORMAT(NOW(), "%%Y-%%m-%%d") AND recurrence_id IS NOT NULL GROUP BY recurrence_id', $wpdb->prefix);
		$results = $wpdb->get_results($query, OBJECT);
		
		// loop throu results and add to $event_ids array
		foreach ($results as $result) array_push($_event_ids, $result->event_id);
		
		// create string
		$event_ids = implode(',', $_event_ids);
		
		wp_cache_set('my_aia_events_manager_group_recurrence',$event_ids,'my-aia', 3600); // one hour
	}
	
	// group recurrence, grouped events are in $event_id
	$conditions['recurrence'] =	sprintf("(recurrence_interval = 0 OR recurrence_interval IS NULL OR	event_id IN(%s))",$event_ids);
		
	return $conditions;
}

/**
 * Display the contents of the form submission (meta content) of the booking,
 * done by Ninja Forms. Saved in the form:
 *	$Ninjaforms::field::ADMIN_LABEL  =  {data}
 * 
 * This is loaded from the $EM_Booking->booking_meta[$key] = $value
 * 
 * @param \EM_Booking $EM_Booking
 */
function my_aia_events_manager_add_booking_meta_single(\EM_Booking $EM_Booking) {
	$EM_Event = $EM_Booking->get_event();
	
	?>
						</div>
					</div> 	
					<div class="stuffbox">
						<h3>
							<?php esc_html_e( 'Reserverings Details uit formulier (Ninja Formulier)', 'my-aia'); ?>
						</h3>
						<div class="inside">
							<div class="em-booking-person-details">
								<table class="em-form-fields">
									<tr>
										<td style="padding-left:10px; vertical-align: top;">
											<table>
												<?php foreach ($EM_Booking->booking_meta as $key=>$value): ?>
												<tr><th><?php _e($key,'my-aia'); ?> : </th><td><?php echo $value; ?></td></tr>
												<?php endforeach; ?>
											</table>
										</td>
									</tr>
								</table>
							</div>
	<?php	// LEAVE the </div> (2x), as whe are hooked inside the <div class="stuffbox"> !	
}


/**
 * Override the display function for the User Profile in the Events Manager booking info page.
 * @param EM_Person $person
 * @return string HTML output
 */
function my_aia_events_manager_profile_display_summary($output, $person, $no_user = FALSE) {
	$no_user = $no_user || get_option('dbem_bookings_registration_disable') && $person->ID == get_option('dbem_bookings_registration_user');
	
	// get XProfile Data From BuddyPress
	bp_has_profile(array('user_id' => $person->ID)); // intiate
		
	ob_start();
	?>
	<table class="em-form-fields">
		<tr>
			<td stye="width: 100px; display: block;"><?php echo get_avatar($person->ID); ?></td>
			<td style="padding-left:10px; vertical-align: top;">
				<table>
					<?php if( $no_user ): ?>
					<tr><th><?php _e('Name','events-manager'); ?> : </th><th><?php echo $person->get_name(); ?></th></tr>
					<?php else: ?>
					<tr><th><?php _e('Name','events-manager'); ?> : </th><th><a href="<?php echo $person->get_bookings_url(); ?>"><?php echo $person->get_name(); ?></a></th></tr>
					<?php endif; ?>
					<tr><th><?php _e('Email','events-manager'); ?> : </th><td><?php echo $person->user_email; ?></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">				
				<?php while ( bp_profile_groups() ) : bp_the_profile_group();?>
				<h4><?php bp_the_profile_group_name(); ?><div class="my_aia_form_opener dropdown_menu down"></div></h4>				
				<table>
					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
						<tr>
							<th><?= __(bp_get_the_profile_field_name(),'my-aia'); ?> : </th>
							<td><?= strip_tags(bp_get_the_profile_field_value()); ?></td>
						</tr>
						<?php do_action( 'bp_profile_field_item' ); ?>
					<?php endwhile; ?>
				</table>
				<?php endwhile; ?>
			</td>
		</tr>
	</table>
	<?php
	return ob_get_clean();
}

/**
 * Shows a form with the current values of the logged in user, to be displayed on top of the form
 * @global WP_Post $post
 */
function my_aia_show_default_profile_values_for_user_add_to_registration_form($form_id) {
	global $post;
	
	if (defined('MY_AIA_DO_PREPEND_FORM') && get_post_meta($post->ID, '_event_id', true) > 0) {
	
	// continue looking up person
	$person = new EM_Person(get_current_user_id());
	
	?>
							<h4>Automatisch ingevulde gegevens</h4>
							<p>De volgende gegevens worden meegezonden met je aanvraag:</p>
	<?php
	
	echo 
		my_aia_events_manager_profile_display_summary("", $person, TRUE),
		sprintf("<p>Als deze informatie niet klopt, dan kun je deze in je <a href='%sprofile/edit/'>profiel</a> aanpassen.</p>", bp_core_get_user_domain( get_current_user_id() )),
		'<br><h3>Overige gegevens</h3>';
	
	}	
}


/**
 * Check if MY_AIA prevents displaying a NINJA FORM:
 * -	prevent if the form is attached to a EM_EVENT
 * -	otherwise, enable
 * @global EM_Event $EM_Event
 * @param type $display
 * @param type $form_id
 * @return boolean
 */
function my_aia_ninja_forms_display_show_form($display, $form_id) {
	global $EM_Event;
	
	// first check if the EM_Event has a form to display, which is form_id
	if (!isset($EM_Event->attributes['ninja_forms_form']) || filter_var($EM_Event->attributes['ninja_forms_form'] , FILTER_VALIDATE_INT) != $form_id) 
		return $display;
	
	// return is event has a form
	return $display && ($EM_Event->event_rsvp == 1);
}


/**
 * Connection from the events manager plugin to update sugar;
 * Either create a registration in sugar, or update or delete.
 * @param boolean $result
 * @param \EM_Booking $booking
 * @param \wpdb $wpdb database interface
 * return boolean
 */
function my_aia_events_manager_registration_sugar_sync($result, $booking) {
	if (defined('DOING_SYNC')) return TRUE; // stop if syncing 
	
	global $wpdb;
	// logic to sync the sugarsettings
	// options: 
	/*$booking->status_array = array(
			0 => __('Pending','events-manager'),		// DO NOTHING
			1 => __('Approved','events-manager'),		
			2 => __('Rejected','events-manager'),
			3 => __('Cancelled','events-manager'),	// REMOVE
			4 => __('Awaiting Online Payment','events-manager'),
			5 => __('Awaiting Payment','events-manager')
		);*/
	
	// get the sync controller
	$sync = new MY_AIA_SYNC_CONTROLLER();
	
	$current_result = TRUE; // check for right processing
	
	// previos not approved, now approved: SUGAR INSERT
	if ($booking->previous_status != 1 && $booking->booking_status==1) {
		$current_result = $sync->sugar_update_aia_ministry_deelname($booking, TRUE);
		$booking->feedback_message .= sprintf(__('Booking created to SugarCRM.','mya-aia'));
	} 
	// Booking from approved to pending (0) rejected  / cancelled (4/5 is not implemented) -> Remove from SUGAR
	// Deleted booking: booking_status === FALSE
	elseif ($booking->booking_status === FALSE || $booking->previous_status == 1 && ($booking->booking_status>1 || $booking->booking_status ==0)) {
		if (strlen($booking->booking_meta['sugar_id']) > 10) {
			$current_result = $sync->sugar_remove_aia_ministry_deelname($booking);
			$booking->feedback_message .= sprintf(__('Booking removed from SugarCRM.','mya-aia'));
		}
	} 
	// Booking only modified --> update Sugar
	elseif ($booking->previous_status == $booking->booking_status) {
		$current_result = $sync->sugar_update_aia_ministry_deelname($booking);	
		$booking->feedback_message .= sprintf(__('Booking updated to SugarCRM.','mya-aia'));
	} 
	
	// something with sugar went wrong, set back:
	if (!$current_result) {
		$booking->booking_status = $booking->previous_status; // return status
		$booking->previous_status = 0; // pending...
		
		//using wpdb prevents do_action () filters...
		$result = $wpdb->query($wpdb->prepare('UPDATE '.EM_BOOKINGS_TABLE.' SET booking_status=%d WHERE booking_id=%d', array($booking->booking_status, $booking->booking_id)));
		
		// feedback
		$booking->feedback_message = sprintf(__('Booking could not be inserted to SugarCRM.','events-manager'));
		$booking->add_error(sprintf(__('Booking could not be updated to SugarCRM.','events-manager')));
		$result =  false;
		
		// give back to filter
		return FALSE;
	}
	
	return $result && $current_result;
}


/**
 * Hook to save the event to sugarcrm, after save.
 * @param bool $result
 * @param EM_Event $event
 * @return bool	resultaat
 */
function my_aia_events_post_to_sugarcrm($result, $event) {
	if (defined('DOING_SYNC')) return TRUE; // stop if syncing 
	if (!$event) return FALSE;
	
	// get the sync controller
	$sync = new MY_AIA_SYNC_CONTROLLER();

	$current_result = TRUE; // check for right processing
	
	$sync->sync_events_wordpress_to_sugar($event);	// push to SUGAR
	
	return $result;
}


/**
 * Removes the hook which set the event to private.
 */
function my_aia_events_manager_remove_hook_bp_em_group_event_save() {
	//add_action('em_event_save','bp_em_group_event_save',1,2); --> bp-em-groups.php
	
	remove_action('em_event_save','bp_em_group_event_save',1,2);
}



/**
 * Save User to Mailchimp
 * @global WP_User
 */
function my_aia_save_to_mailchimp() {
	global $current_user;
	
	$options = get_option('my-aia-options');
	$first_name = xprofile_get_field_data('first_name', get_current_user_id());
	$midde_name = xprofile_get_field_data('middle_name', get_current_user_id());
	$last_name = trim($midde_name." ". xprofile_get_field_data('last_name', get_current_user_id()));
	
	$chimp = new MY_AIA_MAILCHIMP_CONTROLLER();
	$chimp->startup2($options['mailchimp_api_key']);
	$result = $chimp->call('lists/subscribe', array(
					'id'                => $options['mailchimp_list_id'],
					'email'             => array('email'=>$current_user->user_email),
					'merge_vars'        => array('FNAME'=>$first_name, 'LNAME'=>$last_name),
					'double_optin'      => false,
					'update_existing'   => true,
					'replace_interests' => false,
					'send_welcome'      => false));
	
	return true;
}