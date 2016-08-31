<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_PRODUCT post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_PRODUCT extends MY_AIA_MODEL {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_PRODUCT;

	/**
	 * Name of the Object
	 * @var string
	 */
	public $size;
	public $price;
	public $vat;
	public $description;
	public $short_description;
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'short_description'	=> array('name'=>'short_description','type'=>'%s'),
		'size'				=> array('name'=>'size','type'=>'%s'),
		'price'				=> array('name'=>'price','type'=>'%f'),
		'vat'				=> array('name'=>'vat','type'=>'%f'),
	);
	
	public function __construct($post = NULL) {
		parent::__construct($post);
	}
	
	public function get_attributes_form() {
		global $post;
		
		if ($post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id'); // hide
		//
		// return data
		$data = array();
		foreach ($this->fields as $field):
			if (in_array($field['name'], $displayed_fields)) continue; // step over already displayed fields..
			$field['label'] = __($field['name'],'my-aia'); //my_aia_get_default_field_type($_field);

			// get value (as usually is an array)
			$var = $field['name'];
			$value = property_exists($this, $var) ? esc_attr( $this->$var, ENT_QUOTES):'';
			//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
			if (!$value) $value="";
;
			$field['value'] = $value;
			$data[] = $field;
		endforeach; // loop over $fields
		
		return my_aia_add_attributes_form(MY_AIA_POST_TYPE_PRODUCT, MY_AIA_POST_TYPE_PRODUCT, $data);
	}
}