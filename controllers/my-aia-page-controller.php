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
class MY_AIA_PAGE_CONTROLLER extends MY_AIA_CONTROLLER {

	public $classname = 'page';
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 */
	public function before_filter(){

	}
	
	/**
	 * Display the settings page
	 */
	public function index() {
		if (filter_input(INPUT_POST, '_method') == 'post') {
			$options = filter_input(INPUT_POST, 'my_aia_options', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			if ($options) {
				update_option('my-aia-options', $options, false);
			}
		}
		
		// set options
		$this->view->set('my_aia_options', get_option('my-aia-options', MY_AIA::$default_options));
	}
}
