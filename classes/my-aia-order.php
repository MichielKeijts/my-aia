<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

include MY_AIA_PLUGIN_DIR . 'vendor/Mollie/API/Autoloader.php';

/**
 * Definition of the MY_AIA_ORDER post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_ORDER extends MY_AIA_BASE {
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
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'phone'				=> array('name'=>'phone','type'=>'%s'),
		'email'				=> array('name'=>'email','type'=>'%d'),
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
		'assigned_user_id'	=> array('name'=>'assigned_user_id','type'=>'%d'),
		'order_items'		=> array('name'=>'_order_items', 'type'=>'%a'),	// type is array!
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
	 * 
	 * @param int|WP_Post $post
	 */
	public function __construct($post = NULL) {
		parent::__construct($post);
	}
	
	/**
	 * Output attribute (custom post data) form
	 * @global type $post
	 * @param type $prefix
	 * @return bool and echo the form
	 */
	public function get_attributes_form($prefix="") {
		global $post;
		
		if (!$this->ID && $post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id','order_items'); // hide
		//
		// return data
		$data = array();
		foreach ($this->fields as $field):
			// create a dynamic form, to only show some some of the values
			if (!empty($prefix) && strpos($field['name'], $prefix)===FALSE) continue;
			if (in_array($field['name'], $displayed_fields)) continue; // step over already displayed fields..
			$field['label'] = __($field['name'],'my-aia'); //my_aia_get_default_field_type($_field);

			// get value (as usually is an array)
			$value = isset($this->$field['name']) ? esc_attr($this->$field['name'], ENT_QUOTES):'';
			//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
			if (!$value) $value="";

			$field['value'] = $value;
			$data[] = $field;
		endforeach; // loop over $fields
		
		return my_aia_add_attributes_form(MY_AIA_POST_TYPE_ORDER, MY_AIA_POST_TYPE_ORDER, $data);
	}
	
	public function get_order_form() {
		global $post;
		
		if (!$this->ID && $post && $post->ID)	parent::get($post);		
		
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
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		return my_aia_order_form($this->ID, $this->order_items);
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
			return $this->save(false);
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
	 * Parse the order items from the Post META Table
	 */
	public function parse_order_items($values) {
		//$_values = maybe_unserialize($values[0]);

		foreach (maybe_unserialize(reset($values)) as $order_item_raw) {
			if ($order_item_raw) {
				if (!is_array($order_item_raw)) $order_item_raw = maybe_unserialize($order_item_raw);
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
	 * Updates the post_meta for the order object
	 * @param type $prepare_post_data
	 */
	public function update_post_meta($prepare_post_data = true) {
		if ($prepare_post_data) $this->prepare_post_data();
		
		// checks for order item data
		$this->order_items = array(); // start from zero.
		if (isset($_POST['order_items']) && $order_items = $_POST['order_items']) {
			$order_item = new MY_AIA_ORDER_ITEM();
			foreach ($order_items as $product_id=>$values) {
				$order_item->product_id = $product_id;
				$order_item->count = $values['count'];
				$order_item->price = $values['price'];
				$order_item->order_id = $this->ID;
				
				// check if count = 0, delete!
				if ($order_item->count > 0) 
					$this->order_items[] =  $order_item->toString();
			}
		}
		
		parent::update_post_meta(FALSE);	// we already updated post data
	}
	
	/**
	 * Returns an] key=>value array of the templates
	 */
	private function get_invoice_templates() {
		$posts = MY_AIA::$post_types[MY_AIA_POST_TYPE_TEMPLATE]->find(array(
			'post_title'	=> 'Factuur'
		));
		
		return $posts;
	}
	
	/**
	 * Either gets or creates a new invoice.
	 * @param bool $create (default true) create when not exists
	 * @return MY_AIA_INVOICe FALSE|MY_AIA_INVOICE
	 */
	public function get_invoice($create = TRUE) {
		$invoice = new MY_AIA_INVOICE();
		$invoices = $invoice->find(array('meta_query'=>array(array('key'=>'order_id', 'value'=>$this->ID))));
		if ($invoices && count($invoices) > 0 ) {
			$invoice->get($invoices[0]);	// set data
			return $invoice;
		} 
		
		if (!$create) return FALSE;
		
		// create an invoice
		$invoice->get($this); // prepare from post data
		$invoice->order_id = $this->ID;
		$invoice->post_type = MY_AIA_POST_TYPE_INVOICE;
		$invoice->ID = NULL;
		$invoice->create();
		
		return $invoice;
	}
	
	/**
	 * Set the meta boxes
	 */
	public function set_meta_boxes() {
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_ORDER.'-order-items-add-box', __('Order Items','my-aia'), array($this, 'get_order_form'), MY_AIA_POST_TYPE_ORDER, 'normal', 'high');
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_ORDER.'-order-items-box', __('Order Items','my-aia'), array($this, "display_meta_box_order_add_item"), MY_AIA_POST_TYPE_ORDER, 'normal', 'high');
		
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_ORDER.'-order-invoice-box', __('Order Items','my-aia'), array($this, "display_meta_box_order_add_invoice"), MY_AIA_POST_TYPE_ORDER, 'side', 'default');
	}
	
	/** Meta Box Display Functions */
	public function display_meta_box_order_add_item() {
		global $post;
		$this->get($post);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/admin/view/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	public function display_meta_box_order_add_invoice() {
		global $post;
		$this->get($post);
		
		$this->invoice = $this->get_invoice(FALSE);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/admin/view/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	
	
	/**
	 * Function to retun an array of order items of the current order. 
	 */
	public function get_order_items() {
		return $this->order_items;
	}
	
	/**
	 * Function to retun an array of order items of the current order. 
	 */
	public function get_shopping_cart_widget() {
		$view = new MY_AIA_VIEW(new stdClass());
		
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey("test_tFFHbqz89rCuFJJygrwwgJhb963r35");
		
		$payment = $mollie->payments->create(array(
			"amount"      => 10.00,
			"description" => "Test Betaling Athletes in Action",
			"redirectUrl" => "http://www.athletesinaction.nl/members/my-order/12345/",
		));
		
		$view->set('payment', $payment);
		
		return $view->render('post_type_templates/display_shopping_cart_widget', 'empty', FALSE);
	}
	
	/**
	 * Function to retun an array of order items of the current order. 
	 */
	public function get_shopping_cart_items() {
		//$view = new MY_AIA_VIEW(new stdClass());
	
		/**
		 * Simple proces
		 * 1) show Cookie vars, ask for confirmation: 'Place order' 
		 * 2) show adres/bank information, ask for proceed to payment
		 * 3) show thank you information
		 */
		
		// first get all the order items from cookie variable
		if (isset($_COOKIE['my_aia_shopping_cart']) && isset($_REQUEST['create'])) {
			$shopping_cart = json_decode(stripslashes($_COOKIE['my_aia_shopping_cart']));
			if (count($shopping_cart->items) <= 0) {
				$view->set_flash('Selecteer eerst een aantal producten voor je verder gaat', $type);
			}
			
			foreach ($shopping_cart->items as $item) {
				$order_item = new MY_AIA_ORDER_ITEM();
				$order_item->product_id = $item->id;
				$order_item->count = $item->count;
				$order_item->set_product();
				
				array_push(
						$this->order_items,
						$order_item
				);						
			}
		}		
		
		return $this->order_items;
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
				$this-$key = $val;
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