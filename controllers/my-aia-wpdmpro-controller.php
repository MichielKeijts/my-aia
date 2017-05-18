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
class MY_AIA_WPDMPRO_CONTROLLER extends MY_AIA_CONTROLLER {

	public $classname = 'wpdmpro';
	public $has_attribute_form = FALSE;
	
	
	public function __construct() {
		parent::__construct();
		
		// create child controller
		$this->roles = new MY_AIA_ROLE_CONTROLLER();
	}
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 */
	public function before_filter(){
		add_action( 'wp_ajax_my_aia_admin_get_products_wpdmpro', array($this, 'get_products'), 1);	
		add_action( 'wp_ajax_my_aia_admin_get_wpdmpro_products', array($this, 'get_downloads'), 1);	
	}
	
	public function index() {
		
	}
	
	/**
	 * Set the meta boxes
	 */
	public function set_meta_boxes() {
		add_meta_box('my-aia-'.$this->classname.'-display-add-box', __('Product (Webshop)','my-aia'), array($this, 'display_meta_box_wpdmpro_add_product'), $this->classname, 'side', 'high');
		add_meta_box('my-aia-'.$this->classname.'-display-document-roles', __('Toegangsregels','my-aia'), array($this, 'display_meta_box_wpdmpro_add_roles'), $this->classname, 'normal', 'high');
	}
	
	/** Meta Box Display Functions */
	public function display_meta_box_wpdmpro_add_product() {
		global $post;
		
		$this->get_model()->get($post);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	
	/**
	 * display the metabox to add roles to the product to give groups access
	 */
	public function display_meta_box_wpdmpro_add_roles() {
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
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
	 * 
	 * @retun string json
	 */
	public function get_downloads() {
		if ($term = filter_input(INPUT_GET, 'term')) {
			// get the option data
			$product = new MY_AIA_WPDMPRO();
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
}
