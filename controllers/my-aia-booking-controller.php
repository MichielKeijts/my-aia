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
 * Description of my-aia-booking-controller
 *
 * @author Michiel
 */
class MY_AIA_BOOKING_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'booking';
	
	/**
	 * @var \MY_AIA_BOOKING
	 */
	public $BOOKING;
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {

	}
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {		

	}
		
	
	public function get_model() {
		$model = parent::get_model();
		
		$model->set_fields();
		
		return $model;
	}
}
