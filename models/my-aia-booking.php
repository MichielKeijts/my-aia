<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_ORDER post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_BOOKING extends MY_AIA_MODEL {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = "booking";
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=>	array('name'=>'ID','type'=>'%d'),
		'user_email'		=>	array('name'=>'user_email')
	);

	/**
	 * 
	 * @param int|WP_Post $post
	 */
	public function __construct($post = NULL) {
		//parent____construct($post);
		$this->has_attribute_form = FALSE;
		
		
	}
	
	/**
	 * Set the fields as part of this object, which are loaded to be used in
	 * the template function.
	 * 
	 * For example: $this->em__booking__booking_price is now set as value.
	 */
	public function set_fields() {
		$buddypress_fields=my_aia_get_buddy_press_xprofile_fields(FALSE);
		
		foreach ($buddypress_fields as $id=>$field) $this->fields['BuddyPress__'.$id.'__'.$field] = array('name'=>$field);
		
		// get all booking meta fields --> NinjaForms Fields (admin label)
		// get all fields, with an admin label not empty. Such fields can be
		// used for the sync to CRM
		$nf_fields = ninja_forms_get_all_fields();
		
		//$internal_fields = array();
		// create form: <TYPE>__<ID>__<READABLE NAME>
		foreach (em_get_booking()->fields as $field=>$val) $this->fields['EM__BOOKING__'.$field] = array('name'=>$field);
		foreach ($nf_fields as $field) {
			if (!empty($field['data']['admin_label'])) {
				$this->fields['EM__BOOKING_META__'.$field['data']['admin_label']] = array('name'=>$field['data']['admin_label']);
			}
		}
	}


	/**
	 * Get the EM_Booking, apply to $this context
	 * @param mixed $post
	 */
	public function get($post) {
		if (!is_numeric($post)) return FALSE;
		
		// update field list
		$this->set_fields();
		
		// get all the data and set all the data
		$booking = new EM_Booking($post);
		if (!$booking) return FALSE;
		
		$user_id = $booking->person_id;
		$this->user_email = get_user_by('id', $user_id)->user_email;
		
		// set the public fields
		foreach ($this->fields as $field=>$options) {
			if (strstr($field,'BuddyPress')) {
				$fieldinfo = explode('__',$field);
				$this->{$field} = xprofile_get_field_data($fieldinfo[1], $user_id);
			} else
			if (isset($booking->{$options['name']})) $this->{$field} = $booking->{$options['name']};
			if (isset($booking->booking_meta[$options['name']])) $this->{$field} = $booking->booking_meta[$options['name']];
		}
		
		return TRUE;
	}
	
	/**
	 * Template field EM__BOOKING__BOOKING_PRICE parser
	 */
	public function template_em__booking__booking_price($value) {
		return em_get_currency_formatted($value);
	}
	
	/**
	 * NULL function, to avoid savind
	 * @param type $prepare_post_data
	 * @return boolean
	 */
	public function save($prepare_post_data = true) {
		return true;
	}
}