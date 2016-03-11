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
		if (isset ($EM_Event->attributes['NINJA_FORM_ID'])) {
			// remove the nonce, as it will overwrite the EM Event nonce. Not desirable
			remove_action("ninja_forms_display_after_open_form_tag", "nf_form_nonce");
			ninja_forms_display_form( $EM_Event->attributes['NINJA_FORM_ID'] ); 
		}
	}
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