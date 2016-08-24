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
	public $invoice_name;
	public $invoice_address;
	public $invoice_postcode;
	public $invoice_city;
	public $invoice_country;
	
	/**
	 * Template ID used to generate PDF
	 * @var int 
	 */
	public $invoice_template;
	
	/**
	 * Invoice Number, starts with 1
	 * @var int 
	 */
	public $invoice_number = 1;	//initialize
	
	/**
	 * Parent ID is th
	 * @var int the id of the order
	 */
	public $order_id = 1;	//initialize
	
	/**
	 * The PDF or attachment
	 * @var string $attachment 
	 */
	public $attachment = NULL; 
	
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
		'invoice_name'		=> array('name'=>'invoice_name','type'=>'%s'),
		'invoice_address'	=> array('name'=>'invoice_address','type'=>'%s'),
		'invoice_postcode'	=> array('name'=>'invoice_postcode','type'=>'%s'),
		'invoice_city'		=> array('name'=>'invoice_city','type'=>'%s'),
		'invoice_country'	=> array('name'=>'location_country','type'=>'%s'),
		'invoice_number'	=> array('name'=>'invoice_number','type'=>'%d'),
		'invoice_template'	=> array('name'=>'invoice_template','type'=>'%d'),
		'invoice_attachment'=> array('name'=>'invoice_attachment','type'=>'%d'),
		'order_id'			=> array('name'=>'order_id', 'type'=>'%d'),
		'attachment'		=> array('name'=>'attachment', 'type'=>'%d')
		//'assigned_user_id'	=> array('name'=>'assigned_user_id','type'=>'%d'),
		//'bp_group_id'		=> array('name'=>'bp_group_id','type'=>'%d'),
	);
	
	/**
	 * Constructs the Invoice, built from MY_AIA_ORDER
	 * @param int|WP_Post $post
	 */
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
	 * Override Create by defining an invoice number
	 */
	public function create($post_array = NULL) {
		$this->post_title = $this->get_invoice_nr();
		$this->name = $this->post_title;
		$this->invoice_number = $this->post_title;
		$this->post_content = 'Factuur voor order '.$this->order_id;
		
		parent::create($post_array);
	}
		
	/**
	 * Save post hook to update the post_title as order number
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param type $update
	 * @return type
	 */
	public function save_post($post_id, $post, $update) {
		if (!preg_match("/[0-9]+/",$post->post_title)) {
			$this->post_title = $this->get_invoice_nr();
			return $this->save(false);
		}
		parent::save_post($post_id, $post, $update);
	}
	
	/**
	 * Get last invoice number and ads one.
	 * @global \wpdb $wpdb;
	 * @return int invoice number
	 */
	public function get_invoice_nr() {
		global $wpdb;
		$restult = $wpdb->get_results(sprintf('SELECT meta_value FROM %s%s WHERE meta_key="invoice_number" ORDER BY meta_value DESC LIMIT 1', $wpdb->prefix, 'post_meta'));
		if (!$result) {
			return $this->invoice_number;
		}
		
		return $result[0]['meta_value']+1;
	}
	
	/**
	 * Create a PDF
	 * @return string $filename;
	 */
	public function create_invoice_pdf() {
		// makes sure we have an invoice_number
		$this->save_post($this->ID, $this, TRUE);
		
		$pdf = new MY_AIA_TEMPLATE($this->invoice_template);
		$filename = $pdf->parse($this->invoice_template, $this->parent_id, MY_AIA_INVOICE_DIR);
		
		if ($filename) {
			$this->attachment = $filename;
			$this->update_post_meta();
		}
		
		return $filename;
	}
}