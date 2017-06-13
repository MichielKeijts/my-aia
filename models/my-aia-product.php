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
	//public $size;
	public $price;
	public $vat;
	public $description;
	public $short_description;
	
	/**
	 * Inherit from a parent post, in order to create 'sub' products
	 * @var int 
	 */
	public $inherit_from = NULL;
	
	/**
	 * Name of the groep (e.g. size/colour/..)
	 * @var string 
	 */
	public $group_by_name = NULL;
	
	/**
	 * Comma seperated list of possilbe options for this list.
	 * @var string 
	 */
	public $group_by_options;
	
	/**
	 * Comma seperated list of possilbe options for this list.
	 * @var string 
	 */
	public $group_by_option;
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'short_description'	=> array('name'=>'short_description','type'=>'%s'),
		//'size'				=> array('name'=>'size','type'=>'%s'),
		'price'				=> array('name'=>'price','type'=>'%f'),
		'vat'				=> array('name'=>'vat','type'=>'%f'),
		'inherit_from'		=> array('name'=>'inherit_from','type'=>'%d'),
		'group_by_name'		=> array('name'=>'group_by_name','type'=>'%s'),		// for example a group like 'colour' or 'size'
		'group_by_options'	=> array('name'=>'group_by_options','type'=>'%s'),	// group by options, like 'red,green,blue' or 'S,M,XL,XXK'
		'group_by_option'	=> array('name'=>'group_by_option','type'=>'%s'),	// group by option, if a inherit_from, this will be 'red' or 'green' or 'S' or 'M'
	);
	
	public function __construct($post = NULL) {
		parent::__construct($post);
	}
	
	public function get_attributes_form() {
		global $post;
		
		if ($post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id', 'inherit_from'); // hide
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
	
	/**
	 * Save post hook, save download ID
	 */
	public function save_post($post_id, $post, $update) {
		if (isset($_REQUEST['download_id']) || $_REQUEST['download_id'] > 0) {
			$download = new MY_AIA_WPDMPRO();
			$download->get($_REQUEST['download_id']);
			if ($download->ID) {
				$download->product_id = $post_id;
				$download->update_post_meta(FALSE);
			}
		}

		// call parent
		parent::save_post($post_id, $post, $update);
		
		// check if a post group has to be made...
		if (isset($_REQUEST['group_by_options']) || !empty($_REQUEST['group_by_options']) &&
			isset($_REQUEST['group_by_name']) || !empty($_REQUEST['group_by_name'])) {
			
			$group_name = $_REQUEST['group_by_name']; 
			$group_options = $_REQUEST['group_by_options'];
			unset($_REQUEST['group_by_name']);		// make sure this global variable is removed
			unset($_REQUEST['group_by_options']);	// make sure this global variable is removed
			
			$this->create_grouped_post($post, cleanstring($group_name), trim($group_options));			
		}
	}
	
	/**
	 * 
	 * @param MY_AIA_PRODUCT $parent
	 * @param string $group_name
	 * @param string $group_options
	 */
	protected function create_grouped_post($parent, $group_name, $group_options) {
		$options = explode(',', $group_options);
		
		if (count($options) <=0 )
			return FALSE; // could not find valid options

		// go over all options, to check if a post exists
		// otherwise create it
		foreach ($options as $option) {
			$option = trim($option);
			
			$product = $this->find(array(
				'numberposts'	=> 1,
				'meta_query'	=> array(
					array('key'=>'inherit_from',	'value'=>$parent->ID),
					array('key'=>'group_by_option', 'value'=>$option),
				)
			));
			
			
			$new_product = new MY_AIA_PRODUCT();
			$new_product->apply($parent);				// we initialize with all options, so we don't miss anything
			
			if (empty($product)) {
				$new_product->ID = NULL;
			} else 
				$new_product->ID = $product[0]->ID;
			
			$new_product->post_title = sprintf('%s - %s', $parent->post_title, $option);
			$new_product->group_by_options = "";
			$new_product->group_by_name = "";
			$new_product->group_by_option = $option; // set this identifier
			$new_product->inherit_from = $parent->ID; // set parent ID
			
			$new_product->save(FALSE);			
		}
	}
	
	/**
	 * Get all the versions of the product
	 * @return MY_AIA_PRODUCT[]
	 */
	public function get_versions() {
		if (empty($this->group_by_name)) 
			return [];
		
		$versions = $this->find(array(
				'numberposts'	=> 100,
				'meta_query'	=> array(
					array('key'=>'inherit_from',	'value'=>$this->ID),
				)
			)
		);
		
		return $versions;
	}
}