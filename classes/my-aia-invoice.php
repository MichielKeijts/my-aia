<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_INVOICE post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_INVOICE extends MY_AIA_BASE {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_INVOICE;

	/**
	 * Name of the Object
	 * @var string
	 */
	public $phone;
	public $website;
	public $email;
	public $shipping_address;
	public $shipping_postcode;
	public $shipping_city;
	public $shipping_country;
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
		'phone'				=> array('name'=>'phone','type'=>'%s'),
		'website'			=> array('name'=>'website','type'=>'%d'),
		'email'				=> array('name'=>'email','type'=>'%d'),
		'location_address'	=> array('name'=>'location_address','type'=>'%sd'),
		'location_postcode'	=> array('name'=>'location_postcode','type'=>'%s'),
		'location_city'		=> array('name'=>'location_city','type'=>'%s'),
		'location_country'	=> array('name'=>'location_country','type'=>'%s'),
		'assigned_user_id'	=> array('name'=>'assigned_user_id','type'=>'%d'),
		'bp_group_id'		=> array('name'=>'bp_group_id','type'=>'%d'),
	);
	
	public function __construct($post = NULL) {
		parent::__construct($post);
	}
	
	public function get_attributes_form() {
		global $post;
		
		if ($post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id','bp_group_id'); // hide
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
		
		return my_aia_add_attributes_form(MY_AIA_POST_TYPE_INVOICE, MY_AIA_POST_TYPE_INVOICE, $data);
	}
		
	/**
	 * Save post hook to update the post_title as order number
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param type $update
	 * @return type
	 */
	public function save_post($post_id, $post, $update) {
		if (!preg_match("/AIA[0-9]+/",$post->post_title)) {
			$this->create($post);	// initialize
			$this->ID = $post_id;	// to be save
			
			$this->set_order_nr();	// update order number
			return $this->save(false);
		}
		parent::save_post($post_id, $post, $update);
	}
	
	/**
	 * Set order nr. Post Name (post-title) is order nr
	 */
	public function set_order_nr($override=FALSE) {
		if (!$override && !empty($this->post_title)) return $this->post_title;
		
		$this->post_title = 'AIA'.$this->get_increment_order_nr();
	}
	
	/**
	 * Set order nr. Post Name (post-title) is order nr
	 * @return mixed post_name (ordernr) or FALSE if empty
	 */
	public function get_order_nr() {
		if (empty($this->post_title)) return FALSE;
		return $this->post_title;
	}
	
	
	/**
	 * Returns the next order nr
	 * @return int
	 */
	private function get_increment_order_nr() {
		return (int)date('YmdHis');
	}
}