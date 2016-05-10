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
	echo "<div class='em-booking-single-edit' {$extra_style}>";
	my_aia_em_bookings_show_ninja_form($EM_Event);
	echo "</div>";
	
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
	global $EM_Booking;
	
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
		}
	}
	
	return $caller;
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
	global $EM_Event, $wp_meta_boxes;
	
	// remove the old box, as we are called before!
	// remove_meta_box('em-event-attributes', EM_POST_TYPE_EVENT, 'normal');
	// above not working, is in sorted part, not being removed by remove_meta_box(!)
	if (array_key_exists('em-event-attributes', $wp_meta_boxes[EM_POST_TYPE_EVENT]['normal']['sorted'])) {
		unset($wp_meta_boxes[EM_POST_TYPE_EVENT]['normal']['sorted']['em-event-attributes']);
	}
	
	$attributes = em_get_attributes();
	
	$fields = $attributes['names'];
	$values = $attributes['values'];
	
	// strip fields which are not used!
	$displayed_fields = array('ninja_forms_form')
	
	?>
		<table class="form-data">
			<thead>
				<tr>
					<td valign="top"><?= __('Attribute Name','my-aia'); ?></td>
					<td valign="top"><?= __('Attribute Value','my-aia'); ?></td>
				</tr>
			</thead>
			<tbody>
	<?php
		foreach ($fields as $_field):
			if (in_array($_field, $displayed_fields)) continue; // step over already displayed fields..
			$field = my_aia_events_manager_get_default_field($_field);
		
			// get value (as usually is an array)
			$value = array_key_exists($field['id'], $EM_Event->event_attributes) ? esc_attr($EM_Event->event_attributes[$field['id']], ENT_QUOTES):'';
			//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
			if (!$value) $value="";
			
			switch ($field['type']) {
				case "%b":
					?>
					<tr>
						<td><label for="<?= $field['name']; ?>"><?= $field['label']; ?>:</label></td>
						<td><input type='checkbox' id="<?= $field['name']; ?>" name="em_attributes[<?= $field['name']; ?>" <?= $value>0?'checked':''; ?>  /></td>
					</tr>
					<?php
					break;
				case "%s":
				default:
					?>
					<tr>
						<td><label for="<?= $field['name']; ?>"><?= $field['label']; ?>:</label></td>
						<td><input type='text' name="em_attributes[<?= $field['name']; ?>]" value="<?= $value; ?>" /></td>
					</tr>
					<?php
			}
		endforeach; // loop over $fields
	?>
			</tbody>
		</table>
				
	<?php 
	return true;
}

/**
 * Get the default (init) set of the input field.
 * 
 * @param string $field Fieldname
 * @return array (type: .. ,name:..
 */
function my_aia_events_manager_get_default_field($field) {
	return array('type'=>'%s','id'=>$field,'name'=>$field,'label'=>$field);
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