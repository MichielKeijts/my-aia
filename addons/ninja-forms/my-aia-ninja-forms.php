<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package my-aia
 * @author Michiel Keijts <michiel@normit.nl>
 * @copyright (c) 2016, Michiel Keijts
 */


// include other files for Ninja Forms
include_once 'fields/my-aia-ninja-form-upload-field.php';
include_once 'fields/my-aia-ninja-form-term-field.php';
//include_once 'custom-notifications/custom-post-notification.php';
//include_once 'custom-notifications/custom-post-notification.php';	// DO NOT auto include this file, it is loaded in notification_type filter for Ninja Forms



/**
 * Add other options for attachments to the list of attachment options in the EMAIL
 * @param array $attachments
 * @return array $attachments
 */
function my_aia_nf_add_attachment_types($attachments) {
	$attachments['attach_pdf'] = __('Add PDF to Confirmation Email','my-aia');
	
	return $attachments;
}

/**
 * Hook to the attachment settings of the Email (sender) and create PDF
 * @param array $attachments
 * @param int $form_id
 * @param EM_Booking $em_booking_object
 * @return array (filenames)
 */
function my_aia_nf_add_pdf_attachment($attachments, $form_id, $em_booking_object = NULL) {
	/**
	 * Get Template PDF and parse the template
	 * 
	 * @param int $template_id
	 * @return \MY_AIA_TEMPLATE_CONTROLLER
	 */
	function get_pdf ($template_id) {
		$booking_id = $_SESSION['last_booking_id'];
		if (!$booking_id) return FALSE;
		
		$pdf = new MY_AIA_TEMPLATE_CONTROLLER();
		$pdf->TEMPLATE->get($template_id);
		$filename = $pdf->parse($template_id, $booking_id, MY_AIA_INVOICE_DIR);
		
		return $filename;
	}
	
	// set booking_id
	if ($em_booking_object) {
		$_SESSION['last_booking_id'] = $em_booking_object->booking_id;
	}
	
	// get the templates
	$templates = Ninja_Forms()->notification( $form_id )->get_setting( 'pdf_attachment' );
	if ($em_booking_object || !empty($templates) && $templates != FALSE) {
		//$templates = explode(',', $templates);
		
		// TEMP: $templates = array(all the posts where postmeta key 'partner_type' EVENT
		$t = new MY_AIA_TEMPLATE();
		$results = $t->find(array('meta_query' =>array(array('key'=>'parent_type', 'value' => MY_AIA_POST_TYPE_BOOKING))));
		
		$templates = array($results[0]->ID);// TEMP!!
		
		foreach ($templates as $template) {
			$template_id = trim($template);
			
			$fname = get_pdf($template_id);
			if ($fname) {
				$attachments[] = $fname;
			}
		}
	}
	
	return $attachments;
}