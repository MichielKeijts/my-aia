<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_CONTRACT post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_CONTRACT extends MY_AIA_BASE {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_CONTRACT;

	/**
	 *
	 * @var \MY_AIA_PARTNER
	 */
	var $Partner;	// Partner holder

	/**
	 * Name of the Object
	 * @var string
	 */
	public $partner_id;
	public $website;
	public $em;
	public $start_date;
	public $end_date;
	public $contact_name;
	public $contact_role;
	public $location_address;
	public $location_postcode;
	public $location_city;
	public $location_country;
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'start_date'		=> array('name'=>'description','type'=>'%s'),
		'end_date'			=> array('name'=>'description','type'=>'%s'),
		//'contact_name'		=> array('name'=>'name','type'=>'%s'),
		//'contact_name'		=> array('name'=>'name','type'=>'%s'),
		'contact_name'		=> array('name'=>'name','type'=>'%s'),
		'contact_role'		=> array('name'=>'name','type'=>'%s'),	// role in partner
		'date_signed'		=> array('name'=>'name','type'=>'%s'),
		//'name'				=> array('name'=>'name','type'=>'%s'),
		//'name'				=> array('name'=>'name','type'=>'%s'),
		'location_address'	=> array('name'=>'location_address','type'=>'%sd'),
		'location_postcode'	=> array('name'=>'location_postcode','type'=>'%s'),
		'location_city'		=> array('name'=>'location_city','type'=>'%s'),
		'location_country'	=> array('name'=>'location_country','type'=>'%s'),
		'partner_id'		=> array('name'=>'bp_group_id','type'=>'%d'),
	);
	
	public function __construct($post = NULL) {
		$this->start_date = date('Y-m-d');
		$this->end_date = date('Y-m-d', strtotime('+1 year'));	// one year agreement
		
		parent::__construct($post);		
	}
	
	public function get_attributes_form() {
		global $post;
		
		if ($post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','partner_id'); // hide
		//
		// return data
		$data = array();
		foreach ($this->fields as $field):
			if (in_array($field['name'], $displayed_fields)) continue; // step over already displayed fields..
			$field['label'] = __($field['name'],'my-aia'); //my_aia_get_default_field_type($_field);

			// get value (as usually is an array)
			$value = isset($this->$field['name']) ? esc_attr($this->$field['name'], ENT_QUOTES):'';
			//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
			if (!$value) $value="";

			$field['value'] = $value;
			$data[] = $field;
		endforeach; // loop over $fields
		
		return my_aia_add_attributes_form('contract', MY_AIA_POST_TYPE_CONTRACT, $data);
	}
	
	/**
	 * Get the Partner object (Custom Post) Based on the partner_id
	 * @return \MY_AIA_PARTNER
	 */
	private function get_partner() {
		if ($this->Partner) return $this->Partner;
		
		if (is_numeric($this->partner_id) && $this->partner_id > 0) {
			$this->Partner = new MY_AIA_PARTNER($this->partner_id);
		} else {
			$this->Partner = new MY_AIA_PARTNER();	// initialize empty
		}

		return $this->Partner;
	}
	
	/**
	 * Creat a title for the current contract
	 * @return type
	 */
	private function create_title() {
		if (!$this->Partner) $this->Partner = $this->get_partner();
		
		$partner_name = $this->post_title;
		
		return sprintf('Athletes in Action %s - %s - %s tot %s', __('Partner Contract'), $partner_name, $this->start_date, $this->end_date);
	}
}