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
class MY_AIA_COUPON_CONTROLLER extends MY_AIA_CONTROLLER {

	public $classname = 'coupon';
	
	/**
	 * @var MY_AIA_COUPON
	 */
	public $COUPON;


	public function __construct() {
		parent::__construct();
				
		add_filter( 'manage_'.MY_AIA_POST_TYPE_COUPON.'_posts_columns', array($this,'add_new_columns'));
		add_action( 'manage_'.MY_AIA_POST_TYPE_COUPON.'_posts_custom_column' , array($this,'custom_columns'), 10, 2  );
	}


	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 */
	public function before_filter(){
		parent::before_filter();
	}
	
	/**
	* Add new columns to the post table
	*
	* @param Array $columns - Current columns on the list post
	*/
   function add_new_columns( $columns ) {
	   $column_meta1 = array( 'value' => __('Value') );
	   $column_meta2 = array( 'value_used' => __('Value Used') );
	   $columns = array_slice( $columns, 0, 2, true ) + $column_meta1 + $column_meta2 + array_slice( $columns, 2, NULL, true );
	   return $columns;
   }
   
	function custom_columns( $column, $post_id ) {
		if (!$this->COUPON->ID || $this->COUPON->ID != $post_id) {
			$this->COUPON->get($post_id);
		}
		switch ( $column ) {
		  case 'value':
			echo '€', number_format($this->COUPON->value, 2, ',', '.');
			break;
		  case 'value_used':
			echo '€', number_format($this->COUPON->value - $this->COUPON->getCurrentValue(), ',', '.');
			break;
		}
	}
	
	public function index() {
		
	}
	
		
	/**
	 * Set the meta boxes
	 */
	public function set_meta_boxes() {
		global $post;
		
		$this->COUPON->get($post);
		
		add_meta_box('my-aia-'.$this->classname.'-display-status', __('Coupon status (Webshop)','my-aia'), array($this, 'display_meta_box_coupon_status'), $this->classname, 'side', 'high');
	}
	
	/** Meta Box Display Functions */
	public function display_meta_box_coupon_status() {
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
}
