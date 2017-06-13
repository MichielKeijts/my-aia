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
class MY_AIA_PRODUCT_CONTROLLER extends MY_AIA_CONTROLLER {

	public $classname = 'product';
	
	/**
	 * @var MY_AIA_PRODUCT
	 */
	public $PRODUCT;


	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 */
	public function before_filter(){
		add_action( 'pre_get_posts', 'posts_filter', 99, 1);
		parent::before_filter();
	}
	
	public function index() {
		
	}
	
		
	/**
	 * Set the meta boxes
	 */
	public function set_meta_boxes() {
		global $post;
		
		$this->PRODUCT->get($post);
		
		if (!empty($this->PRODUCT->inherit_from))
			add_meta_box('my-aia-'.$this->classname.'-display-warning-inherit-from', __('Waarschuwing!','my-aia'), array($this, 'display_meta_box_product_warning_inherit_from'), $this->classname, 'side', 'high');
		
		add_meta_box('my-aia-'.$this->classname.'-display-add-box', __('Download (Webshop)','my-aia'), array($this, 'display_meta_box_product_add_wpdmpro'), $this->classname, 'side', 'high');
		
		if (!empty($this->PRODUCT->group_by_name))
			add_meta_box('my-aia-'.$this->classname.'-display-add-product-group-box', __('Productgroep (Maat/Kleur/..)','my-aia'), array($this, 'display_meta_box_product_add_product_group'), $this->classname, 'side', 'high');
	}
	
	/** Meta Box Display Functions */
	public function display_meta_box_product_add_wpdmpro() {
		global $post;
		
		$this->download = new MY_AIA_WPDMPRO();
		$this->download->findByProduct($post->ID);
				
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	/** Meta Box Display Functions */
	public function display_meta_box_product_add_product_group() {
	
		// get all the version of this post
		$versions = $this->PRODUCT->get_versions();
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	/**
	 * Show a warning box showing that this post is inherit from. If the master post is saved.. 
	 * all is overwritten
	 */
	public function display_meta_box_product_warning_inherit_from() {
		global $post;
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	
	/**
	 * Filter out all posts with a inherit_from value NOT NULL
	 * @return Void
	 */
	public function posts_filter( $query ){
		
		$query->query_vars['meta_key'] = 'inherit_from';
		$query->query_vars['meta_value'] = NULL;
			
	}
}
