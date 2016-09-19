<?php

/*
 * Copyright (C) 2016 Michiel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


/**
 * Description of my-aia-page-controller
 *
 * @author Michiel
 */
class MY_AIA_ORDER_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'order';
	
	/**
	 * Order Model
	 * @var MY_AIA_ORDER
	 */
	public $ORDER;
	
	public $message = "";
	public $error = FALSE;
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_get_products', array($this, 'get_products'), 1);	
		add_action( 'wp_ajax_my_aia_admin_create_invoice', array($this, 'create_invoice_ajax'), 1);	
		add_action( 'wp_ajax_my_aia_admin_processflow_order_save', array($this, 'processflow_order_save'),1);	
	}
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {		
		parent::before_render();
		
		// extra script
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
		
		// setting the menu bar for this controller
		$menu_bar = array(
			'add' => __('Nieuw','my-aia'),
			'index' => __('Hooks Overzicht'),
		);
		
		$this->set('menu_bar', $menu_bar);
	}
		
	/**
	 * Save the order of the processflows. They can be reordered by drag and drop
	 * on the admin interface.
	 * Save by Ajax Call
	 * 
	 * @retun string json
	 */
	public function get_products() {
		if ($term = filter_input(INPUT_GET, 'term')) {
			// get the option data
			$product = new MY_AIA_PRODUCT();
			$products = $product->find(
					array(
						'p'=>$term, 
						's'=>$term,
						'fields'=>array('post_title','price','post_id','ID')
					)
				);
			
			
			
			if ($products) {
				$jsonAr = Array();
				foreach ($products as $p) {
					$jsonAr[] = array(
						'name'	=>	$p->post_title,
						'value'  => $p->post_title,
						'price' => $p->price,
						'id'	=> $p->ID,
					);
				}
				echo json_encode($jsonAr);
				wp_die();
			}
			
			wp_send_json_success();		
		}
		wp_send_json_error();
	}
	
	/**
	 * Save the order of the processflows. They can be reordered by drag and drop
	 * on the admin interface.
	 * Save by Ajax Call
	 * @param int	$template_id
	 * @retun MY_AIA_INVOICE $invoice
	 */
	public function create_invoice($template_id = 0) {
		global $wpdb;
		if (!($this->ORDER->invoice instanceof MY_AIA_INVOICE)) 
			$this->ORDER->invoice = $this->ORDER->get_invoice(TRUE); // gets or creates invoice
			
		// get the option data
		$this->ORDER->invoice->invoice_template = $template_id;
		$this->ORDER->invoice->parent_id = $this->ORDER->ID;

		// get a PDF
		$filename = $this->ORDER->invoice->create_invoice_pdf();

		if ($filename) {
			// filename exists, create the invoice WP_Post
			$this->ORDER->invoice->attachment = $filename;
			$this->ORDER->invoice->save();

			// always publish
			$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $this->ORDER->invoice->ID ) );

			return $this->ORDER->invoice;
		}		
		
		return FALSE;
	}
	
	/**
	 * Save the order of the processflows. They can be reordered by drag and drop
	 * on the admin interface.
	 * Save by Ajax Call
	 * 
	 * @retun string json
	 */
	public function create_invoice_ajax($parent_id = 0, $template_id = 0) {
		if ($parent_id ==0 ) $parent_id = filter_input(INPUT_POST, 'parent_id');
		if ($template_id ==0 ) $template_id = filter_input(INPUT_POST, 'template_id');
		
		if ($parent_id !== FALSE && $template_id !== FALSE) {
			$this->ORDER = new MY_AIA_ORDER($parent_id); // first get order

			if (empty($this->ORDER->ID))
				wp_send_json_error(array('message'=>'could not find an order with this ID'));
			
			$invoice = $this->create_invoice($template_id); // returns FALS on fail
			
			if ($invoice) {
				wp_send_json_success(array(
					'invoice_number'		=>	$invoice->invoice_number,
					'ID'					=>  $invoice->ID,
					'attachment_permalink'	=>	str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $invoice->attachment)	// replace DIR with URL
				));
			}
			
			wp_send_json_success();		
		}
	
		wp_send_json_error();
	}
	
	public function get_order_form() {
		global $post;
		
		if (!$this->ID && $post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id','bp_group_id','total_order_price'); // hide
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
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		return my_aia_order_form($this->ID, $this->order_items);
	}
	
	/**
	 * Updates the post_meta for the order object
	 * @param type $prepare_post_data
	 */
	public function update_post_meta($prepare_post_data = true) {
		if ($prepare_post_data) $this->prepare_post_data();
		
		// checks for order item data
		if (!is_array($this->order_items) || count($this->order_items)<=0) {
			$this->order_items = array(); // start from zero.
			if (isset($_POST['order_items'])) $this->order_items = $_POST['order_items'];
		
			$order_item = new MY_AIA_ORDER_ITEM();
			foreach ($this->order_items as $product_id=>$values) {
				$order_item->product_id = $product_id;
				$order_item->count = $values['count'];
				$order_item->price = $values['price'];
				$order_item->order_id = $this->ID;
				
				// check if count = 0, delete!
				if ($order_item->count > 0) 
					$this->order_items[] =  $order_item->toString();
			}
		}
		// to string to save
		$_order_items = $this->order_items;
		foreach ($this->order_items as $order_item) 
			$this->order_items[] =  $order_item->toString();		
	
		parent::update_post_meta(FALSE);	// we already updated post data
		
		$this->order_items = $_order_items;
	}
	
	/**
	 * Returns an] key=>value array of the templates
	 */
	private function get_invoice_templates() {
		$posts = $this->invoice->find(array(
			'post_title'	=> 'Factuur'
		));
		
		return $posts;
	}
	
	/**
	 * Set the meta boxes
	 */
	public function set_meta_boxes() {
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_ORDER.'-order-items-add-box', __('Order Items','my-aia'), array($this, 'get_order_form'), MY_AIA_POST_TYPE_ORDER, 'normal', 'high');
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_ORDER.'-order-items-box', __('Order Items Toevoegen','my-aia'), array($this, "display_meta_box_order_add_item"), MY_AIA_POST_TYPE_ORDER, 'normal', 'high');
		
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_ORDER.'-order-invoice-box', __('Factuur','my-aia'), array($this, "display_meta_box_order_add_invoice"), MY_AIA_POST_TYPE_ORDER, 'side', 'default');
	}
	
	/** Meta Box Display Functions */
	public function display_meta_box_order_add_item() {
		global $post;
		$this->ORDER->get($post);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	public function display_meta_box_order_add_invoice() {
		global $post;
		$this->ORDER->get($post);
		
		$this->ORDER->invoice = $this->ORDER->get_invoice(FALSE);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
		
	/**
	 * Function to retun an array of order items of the current order. 
	 */
	public function get_order_items() {
		return $this->ORDER->order_items;
	}
	
	/**
	 * Function to retun an array of order items of the current order. 
	 */
	public function get_shopping_cart_widget() {
		return $this->view->render('post_type_templates/display_shopping_cart_widget', 'empty', FALSE);
	}
	
	/**
	 * Function to retun an array of order items of the current order. 
	 * @return array \MY_AIA_ORDER_ITEM
	 */
	public function prepare_shopping_cart_items($user_id) {
		/**
		 * Simple proces
		 * 1) show Cookie vars, ask for confirmation: 'Place order' 
		 * 2) show adres/bank information, ask for proceed to payment
		 * 3) show thank you information
		 */
		
		// first get all the order items from cookie variable
		if (isset($_COOKIE['my_aia_shopping_cart']) && (isset($_REQUEST['create']) || filter_input(INPUT_POST, '_method')!=FALSE)) {
			$shopping_cart = json_decode(stripslashes($_COOKIE['my_aia_shopping_cart']));
			if (count($shopping_cart->items) <= 0) {
				$this->view->set_flash('Selecteer eerst een aantal producten voor je verder gaat', $type);
			}
			
			$this->ORDER->prepare_shopping_cart_items($shopping_cart);
		}
		return TRUE;
	}
	
	/**
	 * Function to retun an array of order items of the current order. 
	 */
	public function create_and_place_order($user_id = NULL) {
		if (!$user_id) $user_id = get_current_user_id();
	
		// check variables
		if (count($this->ORDER->order_items) <= 0)			return false;
		
		// check all
		$this->message = __('Nog niet alle velden zijn juist ingevoerd, pas dit aan voor dat je verder kunt.','my-aia');
		foreach ($this->ORDER->fields as $field) {
			if (strpos($field['name'], 'shipping') === FALSE && strpos($field['name'], 'invoice') === FALSE) continue;
			if (!filter_input(INPUT_POST, $field['name']) || empty(filter_input(INPUT_POST, $field['name']))	) {
				// not all values set
				$this->message .='<br>'. $field['name'];
				$this->error=true;
				continue;
			}
			$this->{$field['name']} = filter_input(INPUT_POST, $field['name'], FILTER_SANITIZE_STRING);
		}
		if ($this->error) return false;
		
		// check if there is an old draft status order
		$draft_post = $this->ORDER->find(array(
			'numberposts'	=> 1,
			'post_name'		=> '',
			'name'			=> '',
			'meta_query'	=> array(
				array('key'=>'assigned_user_id', 'value'=>$user_id),
				array('key'=>'order_status', 'value'=>MY_AIA_ORDER_STATUS_AWAITING_PAYMENT)
			)
		));
		// if not finalized order exists, update		
		if ($draft_post) {
			$this->ORDER->ID = $draft_post[0]->ID;
			delete_post_meta($this->ORDER->ID, '_order_items');
			$this->prepare_shopping_cart_items($user_id);
		}
				
		// first get all the order items from cookie variable
		if (!$this->ORDER->ID) {
			$this->ORDER->name			=	$this->ORDER->set_order_nr(TRUE);
			$this->ORDER->post_content =	'Geen Meldingen';
			$this->ORDER->create();
		}
		
		// set other data
		$this->ORDER->order_status = MY_AIA_ORDER_STATUS_AWAITING_PAYMENT;		
		$this->ORDER->assigned_user_id = $user_id;
		
		$this->ORDER->save();		
	}
	
	/**
	* Controller for the event views in BP (using mvc terms here)
	*/
   function my_aia_bp_my_order_edit() {
	   global $bp;
	   do_action( 'bp_my_aia_my_orders' );

	   add_action( 'bp_template_title', array($this, 'my_aia_bp_my_order_edit_title') );
	   add_action( 'bp_template_content', array($this, 'my_aia_bp_my_order_edit_content' ));

	   // check for make payment
	   if (filter_input(INPUT_GET, 'make_payment') === '') {
		   if ($order_id = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT)) {
			   $this->ORDER->get($order_id);
			   if ($this->ORDER->assigned_user_id == bp_current_user_id()) {
				   if (!$this->ORDER->invoice) $this->create_invoice(4936);
				   if (!$this->ORDER->invoice->check_payment_status()) {
					   // remove cookie
					   unset($_COOKIE['my_aia_shopping_cart']);
					   setcookie('my_aia_shopping_cart', NULL, time()-36000);
					   
					   $url = $this->ORDER->invoice->create_payment_link();
					   header('Location: '.$url);
					   exit();
				   }
			   }
		   }
	   }

	   // get contents
	   MY_AIA::$controllers[MY_AIA_POST_TYPE_ORDER]->prepare_shopping_cart_items(bp_current_user_id());

	   if (filter_input(INPUT_POST, '_method') === 'create') {
		   $this->create_and_place_order();
	   }

	   // hide header
	   MY_AIA::hide_buddypressheader();

	   MY_AIA::set_navigationbar(array(
		   'current_title' =>	__( 'Mijn Bestelling', 'my-aia'),
		   'nav'			=>	NULL,
		   'title'			=>	'Winkelwagen',
	   ));


	   /* Finally load the plugin template file. */
	   bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/home' ) );
	   //my_aia_locate_template('buddypress/members/single/page.php', true);
   }

   function my_aia_bp_my_order_edit_title() {
	   __( 'Mijn Bestelling', 'my-aia');
   }
   /**
	* Determines whether to show event page or events page, and saves any updates to the event or events
	* @return null
	*/
   function my_aia_bp_my_order_edit_content() {
	   // Create a custom post type (order) from COOKIE vars	
	   my_aia_locate_template('buddypress/my-order-edit.php', true, MY_AIA::$_viewVars);
	}
	
	
	/**
	 * 
	 * @global type $bp
	 * @return boolean
	 */
	public function my_aia_bp_my_order_status() {
		global $bp;
		do_action( 'bp_my_aia_my_orders' );

		// get contents
		if (is_numeric(filter_input(INPUT_GET, 'payment_id', FILTER_SANITIZE_NUMBER_INT))) {
			//redirect to controller
			$order_id = my_aia_payment()->payment_processing();
		} elseif (is_numeric(filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT))){
			$order_id = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT);
		} else { wp_die('no access'); }
		
		if ($order_id) {
			$this->ORDER->get($order_id);
			if ($this->ORDER && $this->ORDER->assigned_user_id!=NULL && $this->ORDER->assigned_user_id !=  get_current_user_id()) { 
				wp_die('no access'); 
			}
		} 		

		add_action( 'bp_template_title', array($this, 'my_aia_bp_my_order_status_title' ));
		add_action( 'bp_template_content', array($this, 'my_aia_bp_my_order_status_content' ));

		// hide header
		//MY_AIA::hide_buddypressheader();

		//MY_AIA::set_navigationbar();


		/* Finally load the plugin template file. */
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/home' ) );
		//my_aia_locate_template('buddypress/members/single/page.php', true);
	}

	function my_aia_bp_my_order_status_title() {
		__( 'Mijn Order Status', 'my-aia');
	}
	/**
	 * Determines whether to show event page or events page, and saves any updates to the event or events
	 * @return null
	 */
	function my_aia_bp_my_order_status_content() {
		// Create a custom post type (order) from COOKIE vars	
		$this->locate_template('buddypress/my-order-status.php', true, MY_AIA::$_viewVars);
	}
}
