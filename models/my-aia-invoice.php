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
class MY_AIA_INVOICE extends MY_AIA_MODEL {
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
	
	public $total_amount;
	public $total_amount_ex_btw;
	public $total_amount_btw;
	public $total_amount_ex_coupon;
	
	public $order_items;
	
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
	public $order_id;	//initialize
	
	/**
	 * The PDF or attachment
	 * @var string $attachment 
	 */
	public $attachment = NULL; 
	
	/**
	 * User ID attached..
	 * @var int 
	 */
	public $assigned_user_id;
	
	/**
	 *
	 * @var MY_AIA_ORDER 
	 */
	public $order;
	
	/**
	 *
	 * @var MY_AIA_COUPON 
	 */
	public $coupon;
	
	public $coupon_value = 0;
	
	public $btw; //placeholder to call function in template
	
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
		'total_amount'		=> array('name'=>'total_amount', 'type'=>'%d'),
		'total_amount_ex_btw'	=> array('name'=>'total_amount_ex_btw', 'type'=>'%d'),
		'total_amount_ex_coupon'	=> array('name'=>'total_amount_ex_coupon', 'type'=>'%d'),
		'attachment'		=> array('name'=>'attachment', 'type'=>'%d'),
		'assigned_user_id'	=> array('name'=>'assigned_user_id','type'=>'%d'),
		'coupon_id'			=> array('name'=>'coupon_id', 'type'=>'%d'),
		'coupon_value'		=> array('name'=>'coupon_value', 'type'=>'%d'),
		
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
		
		parent::create();
	}
	/**
	 * Save post hook to update the post_title as order number
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param type $update
	 * @return type
	 */
	public function save_post($post_id, $post, $update) {
		if ($post_id >0 && $this->ID != $post_id) {
			$this->get($post_id);
			$this->apply($post);
		}
		if (!preg_match("/[0-9]+/",$post->post_title)) {
			$this->post_title = $this->get_invoice_nr();
			$_POST['post_title'] = $this->post_title;
			return $this->save(true);
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
		$result = $wpdb->get_results(sprintf('SELECT meta_value FROM %s%s WHERE meta_key="invoice_number" ORDER BY meta_value DESC LIMIT 1', $wpdb->prefix, 'postmeta'));
		if (!$result) {
			return $this->invoice_number;
		}
		
		return (int)$result[0]->meta_value + 1;
	}
	
	/**
	 * Create a PDF
	 * @return string $filename;
	 */
	public function create_invoice_pdf() {
		// makes sure we have an invoice_number
		$this->save_post($this->ID, $this, TRUE);
		
		$pdf = new MY_AIA_TEMPLATE_CONTROLLER();
		$pdf->TEMPLATE->get($this->invoice_template);
		$filename = $pdf->parse($this->invoice_template, $this->ID, MY_AIA_INVOICE_DIR);
		
		if ($filename) {
			$this->attachment = $filename;
			$this->update_post_meta(FALSE);
		}
		
		return $filename;
	}
	
	/**
	 * Return a String URL to Mollie API for iDEAL payment
	 * @return string|bool FALSE if failed
	 */
	public function create_payment_link($amount = -1) {
		if ($amount<0) $amount = $this->total_amount;
		$payment = new MY_AIA_PAYMENT();
		$payment->invoice_id = $this->ID;
		$payment->total_amount = $amount;
		$payment->post_content = 'open';
		$payment->assigned_user_id = $this->assigned_user_id;
		$payment->name = 'waiting for mollie ID'.$this->ID;
		$payment->create();
		$payment->save();
		
		// get mollie_link
		if ($payment->total_amount <=0) {
			$payment->post_content = MY_AIA_ORDER_STATUS_PAID;
			$payment->name = 'total amount less or equal than zero. Possibly used a coupon?';
			$payment->save(false);
			$url = $payment->getOrderStatusUriByPaymentID(); // get a redirect url for the flow
		} else {
			$url = $payment->get_mollie_link();
		}
		
		if ($url) return $url;
		return FALSE;
	}
	
	/**
	 * Return the link to the Invoice PDF File
	 * @return string
	 */
	public function pdf_link() {
		if (empty($this->attachment)) return "#";
		//return str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $this->attachment);
		return sprintf('%s/?post=%d', MY_AIA_DOWNLOAD_SLUG, $this->ID);
	}
	
	/**
	 * Checks the total payments done for this invoice. 
	 * Returns TRUE if enough amount is paid. FALSE otherwise
	 * @return boolean
	 */
	public function check_payment_status() {
		// get all payments
		$payment = new MY_AIA_PAYMENT();
		$payments = $payment->find(array(
				'post_content' => array('paid','paidout'),
				'meta_query'=>
					array(
						array('key'=>'invoice_id','value'=>$this->ID),
					)
				));
		
		// loop over payments 
		if ($payments && count($payments) > 0) {
			$total_amount_done = 0.0;
			foreach ($payments as $pmt) {
				$total_amount_done += $pmt->amount;
			}
		} else {
			return FALSE;
		}
		
		// check total amount done
		return $total_amount_done >= (float)$this->total_amount && $this->total_amount!=0;
	}
	
	/**
	 * Output template for order_items
	 */
	public function template_order_items() {
		$this->order = new MY_AIA_ORDER($this->order_id);
		
		if ($this->order) {
			$view = new MY_AIA_VIEW(new MY_AIA_CONTROLLER());
			$view->set('order', $this->order);
			return $view->render("/post_type_templates/" . __FUNCTION__, 'empty',false);
		}
	}
	
	
	public function total_amount_ex_btw() {
		if (!$this->order) $this->order = new MY_AIA_ORDER($this->order_id);
		
		if ($this->order) {
			return $this->order->total_amount_ex_btw;
		}
	} 
	
	/**
	 * Template parse function 
	 * @return string
	 */
	public function template_btw() {
		return $this->total_amount_ex_coupon - $this->total_amount_ex_btw;
	} 
	
	/**
	 * Parse function called by get_post_meta
	 * get the coupon
	 */
	public function parse_coupon_id($id) {
		if (empty($id) || empty($id[0])) {
			$this->coupon = NULL;
			$this->coupon_value = "";
			return NULL;
		}
		
		if (is_array($id)) $id = reset($id); // $name is post_meta, returned as array!
		
		$coupon = new MY_AIA_COUPON();
		$coupon->get($id);
		
		if ($coupon->getCurrentValue($this->order_id) > 0) {
		
			// if coupon is worth more..
			if ($coupon->getCurrentValue($this->order_id) >= $this->total_amount_ex_coupon) {
				$this->coupon_value = $this->total_amount_ex_coupon;
			} else {
				$this->coupon_value = $coupon->getCurrentValue($this->order_id);
			}
			
			$this->total_amount = $this->total_amount_ex_coupon - $this->coupon_value;
			
			$this->coupon = $coupon;
			return $coupon;
		} else {
			return NULL;
		}
	}
}