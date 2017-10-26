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


	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 */
	public function before_filter(){
		parent::before_filter();
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