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
 * Description of my-aia-template-controller
 *
 * @author Michiel
 */
class MY_AIA_TEMPLATE_CONTROLLER extends MY_AIA_APP_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'template';
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_get_template_fields', array($this, 'ajax_get_template_fields'), 1);	
	}

	/**
	 * Get the template fields for the post type defined in GET['post_type']
	 * on the admin interface.
	 * Sends JSON output
	 * @retun void
	 */
	public function ajax_get_template_fields() {
		if ($term = filter_input(INPUT_GET, 'post_type')) {
			// get the option data
			$fields = MY_AIA::$post_types[MY_AIA_POST_TYPE_TEMPLATE]->get_parent_type_fields($term);
						
			wp_send_json_success(array('fields'=>$fields));		
		}
		wp_send_json_error();
	}
}