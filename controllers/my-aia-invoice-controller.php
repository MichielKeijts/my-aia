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
class MY_AIA_INVOICE_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'invoice';
	
	/**
	 * @var \MY_AIA_INVOICE
	 */
	public $INVOICE;
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_get_products', array($this, 'get_products'), 1);	
		add_action( 'wp_ajax_my_aia_admin_create_invoice', array($this, 'create_invoice'), 1);	
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
	 * 
	 * @retun string json
	 */
	public function create_invoice() {
		$parent_id = filter_input(INPUT_POST, 'parent_id');
		$template_id = filter_input(INPUT_POST, 'template_id');
		
		if ($parent_id !== FALSE && $template_id !== FALSE) {
			$order = new MY_AIA_ORDER($parent_id); // first get order
			
			if ($order) {
				$invoice = $order->get_invoice(); // gets or creates invoice
			} else {
				wp_send_json_error(array('message'=>'could not find an order with this ID'));
			}
			
			// get the option data
			$invoice->invoice_template = $template_id;
			$invoice->parent_id = $parent_id;
			
			// get a PDF
			$filename = $invoice->create_invoice_pdf();
			
			if ($filename) {
				// filename exists, create the invoice WP_Post
				$invoice->ID = NULL;
				$invoice->attachment = $filename;
				$invoice->create($invoice);
				
				// always publish
				wp_publish_post($invoice->ID);
				
				wp_send_json_success(array(
					'invoice_number'		=>	$invoice->invoice_number,
					'ID'					=>  $invoice->ID,
					'attachment_permalink'	=>	str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $filename)	// replace DIR with URL
				));
			}
			
			//$invoice->g			
			if ($products) {
				$jsonAr = Array();
				foreach ($products as $p) {
					$jsonAr[] = array(
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
	 * Set the meta boxes
	 */
	public function set_meta_boxes() {
		add_meta_box('my-aia-'.MY_AIA_POST_TYPE_INVOICE.'-invoice-box', __('Factuur','my-aia'), array($this, "display_meta_box_invoice_add_pdf"), MY_AIA_POST_TYPE_INVOICE, 'side', 'default');
	}
	
	/**
	 * Returns an] key=>value array of the templates
	 */
	private function get_invoice_templates() {
		$post = new MY_AIA_TEMPLATE();
		
		$templates = $post->find(array(
			'post_title'	=> 'Factuur',
		));
		
		return $templates;
	}
	
	/**
	 * Show Meta Box for the Invoice custom post type
	 * @global type $post
	 */
	public function display_meta_box_invoice_add_pdf() {
		global $post;
		$this->INVOICE->get($post);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
}
