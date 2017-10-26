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
class MY_AIA_ORDER extends MY_AIA_MODEL {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_ORDER;

	/**
	 * Name of the Object
	 * @var string
	 */
	public $phone;
	public $email;
	public $shipping_name;
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
	public $total_amount_btw6;
	public $total_amount_btw21;
	public $total_amount_ex_coupon;
	public $coupon_value;		// obtained via coupon relation
	public $order_status;		// Prefix use MY_AIA_ORDER_STATUS
	public $assigned_user_id;
	
	
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'phone'				=> array('name'=>'phone','type'=>'%s'),
		'email'				=> array('name'=>'email','type'=>'%s'),
		'shipping_name'		=> array('name'=>'shipping_name','type'=>'%s'),
		'shipping_address'	=> array('name'=>'shipping_address','type'=>'%s'),
		'shipping_postcode'	=> array('name'=>'shipping_postcode','type'=>'%s'),
		'shipping_city'		=> array('name'=>'shipping_city','type'=>'%s'),
		'shipping_country'	=> array('name'=>'shipping_country','type'=>'%s'),
		'invoice_name'		=> array('name'=>'invoice_name','type'=>'%s'),
		'invoice_address'	=> array('name'=>'invoice_address','type'=>'%s'),
		'invoice_postcode'	=> array('name'=>'invoice_postcode','type'=>'%s'),
		'invoice_city'		=> array('name'=>'invoice_city','type'=>'%s'),
		'invoice_country'	=> array('name'=>'invoice_country','type'=>'%s'),
		'order_status'		=> array('name'=>'order_status', 'type'=>'%s'),
		'total_amount'		=> array('name'=>'total_amount', 'type'=>'%d'),
		'total_amount_ex_coupon'=>	array('name'=>'total_amount_ex_coupon', 'type'=>'%d'),
		'total_amount_ex_btw'	=>	array('name'=>'total_amount_ex_btw', 'type'=>'%d'),
		'total_amount_btw6'		=>	array('name'=>'total_amount_btw6', 'type'=>'%d'),
		'total_amount_btw21'	=>	array('name'=>'total_amount_btw21', 'type'=>'%d'),
		'assigned_user_id'	=> array('name'=>'assigned_user_id','type'=>'%d'),
		'order_items'		=> array('name'=>'_order_items', 'type'=>'%a'),	// type is array!
		'coupon_id'			=> array('name'=>'coupon_id', 'type'=>'%d'),	// type is array!
		'coupon_value'		=> array('name'=>'coupon_value', 'type'=>'%d'),	// type is array!
		'bp_group_id'		=> array('name'=>'bp_group_id','type'=>'%d'),
	);
	
	/**
	 * Order items. 
	 * array(
	 *		'product_id'	=> ID
	 *		'post_title'	=> inherit from product post
	 *		'count'			=> number of items
	 *		'price'			=> price per item
	 * )
	 * @var array
	 */
	public $order_items = array();
	
	/**
	 * Order 
	 * @var \MY_AIA_INVOICE
	 */
	public $invoice;
	
	/**
	 * Coupon
	 * @var MY_AIA_COUPON 
	 */
	public $coupon;
	
	
	/**
	 * 
	 * @param int|WP_Post $post
	 */
	public function __construct($post = NULL) {
		parent::__construct($post);
	}
	
	/**
	 * Save post hook to update the post_title as order number
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param type $update
	 * @return type
	 */
	public function save_post($post_id, $post, $update) {
		if (!preg_match("/Order [0-9]+/",$post->post_title)) {
			$this->get($post);	// initialize
			$this->ID = $post_id;	// to be save
			
			$this->set_order_nr(TRUE);	// update order number
			return $this->save(true);	// update fields by post data
		}
		parent::save_post($post_id, $post, $update);
	}
	
	/**
	 * Set order nr. Post Name (post-title) is order nr
	 */
	public function set_order_nr($override=FALSE) {
		if (!$override && !empty($this->post_title)) return $this->post_title;
		
		$this->post_title = 'Order '.$this->ID;
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
	 * Wrapper for find() function, to search for any order from the user
	 * @param type $id
	 * @return type
	 */
	public function findByUserID($id, $count=0) {
		return $this->find(array(
			'numberposts' => $count,
			'post_status' => 'any',
			'meta_query' =>array(array('key'=>'assigned_user_id', 'value' =>	$id))
		));
	}
	
	/**** PARSER FUNCTIONS CALLED BY POSTMETA *****/
	
	/**
	 * Parse the order items called by get_post_meta from the Post META Table
	 */
	public function parse_order_items($values) {
		//$_values = maybe_unserialize($values[0]);

		foreach (maybe_unserialize(reset($values)) as $key=>$order_item_raw) {
			if ($order_item_raw) {
				if (!is_array($order_item_raw)) $order_item_raw = maybe_unserialize($order_item_raw);
				
				// in some cases product_id was saved as a $key of the array
				// update the order_item correspondinly
				if (is_numeric($key) && $key>0) 
					if (is_array($order_item_raw) && $order_item_raw['id'] < 0)
						$order_item_raw['product_id'] = $key;
					
				if (!($order_item_raw instanceof MY_AIA_ORDER_ITEM)) {
					$item = new MY_AIA_ORDER_ITEM($order_item_raw, $this->ID);
				} else {
					$item = $order_item_raw;
					$item->set_product();
				}
				
				
				if ($item) {
					array_push($this->order_items, $item);
				}
			}
		}
		return $this->order_items;
	}
	
	/**
	 * Parse function called by get_post_meta
	 * NB: we do not actually update invoice_name, 
	 */
	public function parse_invoice_name($name) {
		$invoice = $this->get_invoice(FALSE);
		if ($invoice) $this->invoice = $invoice;
		
		
		// we will return the name..
		return $name;
	}
	
	/**
	 * Parse function called by get_post_meta
	 * NB: we do not actually update coupon_id, 
	 */
	public function parse_coupon_id($name) {
		if (empty($name) || empty($name[0])) {
			$this->coupon = NULL;
			$this->coupon_value = "";
			return NULL;
		}
		
		if (is_array($name)) $name = reset($name); // $name is post_meta, returned as array!
		
		$coupon = new MY_AIA_COUPON();
		$coupon->get($name);
		
		if ($coupon->ID) { 
			// if coupon is worth more..
			if ($coupon->getCurrentValue($this->ID) >= $this->total_amount_ex_coupon) {
				$this->coupon_value = $this->total_amount_ex_coupon;
			} else {
				$this->coupon_value = $coupon->getCurrentValue($this->ID);
			}
			
			$this->total_amount = $this->total_amount_ex_coupon - $this->coupon_value;
			$this->coupon = $coupon;
			return $name[0];
		} else {
			return NULL;
		}
	}
	
	/**
	 * Either gets or creates a new invoice.
	 * @param bool $create (default true) create when not exists
	 * @return MY_AIA_INVOICE FALSE|MY_AIA_INVOICE
	 */
	public function get_invoice($create = TRUE) {
		$invoice = new MY_AIA_INVOICE();
		$invoices = $invoice->find(array('meta_query'=>array(array('key'=>'order_id', 'value'=>$this->ID))));
		if ($invoices && count($invoices) > 0 ) {
			$invoice->apply($invoices[0]);	// set data
			$invoice->parse_coupon_id($invoice->coupon->ID);
			return $invoice;
		} 
		
		if (!$create) return FALSE;
		
		// create an invoice
		$order_id = $this->ID;
		$invoice->get($this->ID); // prepare from post data
		$invoice->apply($this);
		$invoice->ID = NULL;
		$invoice->order_id = $order_id;
		$invoice->create();
		$invoice->order_id = $order_id;
		$invoice->post_type = MY_AIA_POST_TYPE_INVOICE;
		$invoice->total_amount = $this->total_amount_ex_coupon;
		$invoice->coupon_value = $this->coupon_value;
		$invoice->coupon_id = isset($this->coupon->ID) ? $this->coupon->ID : NULL;
		$invoice->save(FALSE);
		
		return $invoice;
	}
	
	/**
	 * Set user data from BP Xprofile
	 */
	public function set_user_data_from_id($id=NULL) {
		// get name
		$first_name = xprofile_get_field_data('first_name' , $this->assigned_user_id);
		$last_name = xprofile_get_field_data('last_name' , $this->assigned_user_id );
		$middle_name = xprofile_get_field_data('middle_name', $this->assigned_user_id);
		
		$this->invoice_name = sprintf('%s %s', $first_name, trim(sprintf('%s %s', $middle_name, $last_name)));
		$this->shipping_name = $this->invoice_name;
				
		$this->shipping_address = $this->invoice_address = sprintf('%s %s%s', 
			xprofile_get_field_data('primary_address_street', $this->assigned_user_id),
			xprofile_get_field_data('primary_address_number_c', $this->assigned_user_id),
			xprofile_get_field_data('primary_address_number_add_c', $this->assigned_user_id)
		);
		$this->shipping_postcode = $this->invoice_postcode = xprofile_get_field_data('primary_address_postalcode', $this->assigned_user_id);
		$this->shipping_city = $this->invoice_city = xprofile_get_field_data('primary_address_city', $this->assigned_user_id);
		$country = xprofile_get_field_data('primary_address_country', $this->assigned_user_id);
		if (!$country) $country = 'NLD';
		$this->shipping_country = $this->invoice_country = ISO3166::get_full_country_name($country, 'NLD');
	}
	
	/**
	 * Function to retun an array of order items of the current order. 
	 * @return bool
	 */
	public function prepare_shopping_cart_items($shopping_cart) {
		$this->order_items = array();
		$this->total_amount = 0.0;
		$this->total_amount_ex_coupon = 0.0;
		$this->total_amount_btw6 = 0.0;
		$this->total_amount_btw21 = 0.0;
		$this->total_amount_ex_btw = 0.0;
		foreach ($shopping_cart->items as $item) {
			$order_item = new MY_AIA_ORDER_ITEM();
			$order_item->product_id = $item->id;
			$order_item->count = $item->count;
			$order_item->set_product();

			array_push(
					$this->order_items,
					$order_item
			);						

			$subtotal = $order_item->get_product()->price * $order_item->count;
			$btw = intval(trim($order_item->get_product()->vat,'%'))/100;
			
			// 6%
			if ($btw < 0.15) {
				$this->total_amount_btw6 += $subtotal / (1+$btw) * $btw;
			} else {
				$this->total_amount_btw21 += $subtotal / (1+$btw) * $btw;
			}
			
			$this->total_amount_ex_coupon += $subtotal;
			$this->total_amount_ex_btw += $subtotal / (1 + $btw);
		}
		
		$this->total_amount = $this->total_amount_ex_coupon;
		
		$this->assigned_user_id = $user_id;
		$this->set_user_data_from_id();
	
		return TRUE;
	}
}


/**
 * Class for linking orders to order items.
 * No custom post type exists and is needed(!)
 */
class MY_AIA_ORDER_ITEM {
	
	private $product;
	public $post_title;
	public $product_id;
	public $order_id;
	public $count;
	public $price;
	
	
	/**
	 * Initiates a MY_AIA_ORDER_ITEM from the post_meta
	 * @param mixed $order_item
	 * @return boolean
	 */
	public function __construct($order_item=NULL, $order_id=NULL) {
		if ($order_item == NULL) return TRUE;
		
		if ($order_item instanceof MY_AIA_ORDER_ITEM) {
			foreach ($order_item as $key=>$val) {
				$this->$key = $val;
			}
		} else {
			if ($order_item && ($order_item['product_id'] || isset($order_item['id'])) && ($order_item['order_id'] || $order_id)) {
				$this->product_id = $order_item['product_id'] ? $order_item['product_id']:$order_item['id'];
				$this->order_id = $order_item['order_id'] ? $order_item['order_id']:$order_id;
				$this->price = is_numeric($order_item['price']) ? $order_item['price'] : $this->product->price;
				$this->count = is_numeric($order_item['count']) ? $order_item['count'] : 1;
			}
		}
		
		//try and find product
		if (!$this->set_product()) return FALSE;

		// apply variables 
		$this->post_title = $this->product->post_title;
		$this->price = is_numeric($this->price) ? $this->price : $this->product->price;
		$this->count = is_numeric($this->count) ? $this->count : 1;
		$this->order_id = $this->order_id;
		$this->product_id = $this->product->ID;
	}
	
	/**
	 * Obtains and the product (WP_Post)
	 * @return \MY_AIA_PRODUCT
	 */
	public function set_product() {
		if (!($this->product = new MY_AIA_PRODUCT($this->product_id))) {
			return FALSE; // no product found, but no return possible.. in construct..
		}
		
		// set post_title (Order Item)
		$this->post_title = $this->product->post_title;
				
		return $this->product;
	}
	
	/**
	 * Returns the product
	 * @return \MY_AIA_PRODUCT
	 */
	public function get_product() {
		if (!$this->product && $this->product_id) {
			if (!$this->set_product()) return FALSE;
		}
		return $this->product;
	}
	
	/**
	 * Get serialized form
	 * @return type
	 */
	public function toString() {
		$temp = $this;
		unset($temp->product);
		return serialize($this);
	}
}