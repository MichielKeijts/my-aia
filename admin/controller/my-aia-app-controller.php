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
 * Description of my-aia-app-controller
 *
 * @author Michiel
 */
class MY_AIA_APP_CONTROLLER {
	/**
	 * View class
	 * @var \MY_AIA_ADMIN_VIEW
	 */
	protected $view;
	
	protected $layout = 'default';
	
	protected $classname = 'app';


	/**
	 * lowercased_underscored name of the controller
	 * @var type 
	 */
	//protected $classname = 'app_controller';	


	public function __construct() {
		$this->view = new MY_AIA_VIEW($this);
	}
	
	/**
	 * Render function.
	 * wrapper for the underlying view render
	 * @param string $action (default:NULL)
	 */
	public function render($action=NULL) {
		if (empty($this->template)) 
			$this->template = $this->find_template($action);
		else 
			// update the directory
			$this->template = $this->find_template($this->template); 
		
		$this->view->render($this->template, $this->layout, TRUE);
	}	
	
	/**
	 * Try and find the view file for the current controller
	 * @param type $name
	 */
	private function find_template($action=NULL) {
		if (empty($action)) $action = $this->classname;

		$path = sprintf('%sadmin/view/%s/%s', MY_AIA_PLUGIN_DIR, lowercase_underscore($this->classname), lowercase_underscore($action));
		
		if (!file_exists($path.'.ctp')) {
			return sprintf('%sadmin/view/error/general', MY_AIA_PLUGIN_DIR);
		}
		
		return sprintf('%s/%s', lowercase_underscore($this->classname), lowercase_underscore($action));
	}
	
	/**
	 * Wrapper for $this->view->set. Set view variables
	 * @param string $var name of variable
	 * @param mixed $val vlue of the variabele
	 */
	protected function set($var, $val=NULL) {
		$this->view->set($var, $val);
	}
		
	/**
	 * Returns the current controller name
	 * @return string
	 */
	public function get_controller_name() {
		return $this->classname;
	}
	
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {		
		wp_enqueue_style( 'my-aia-admin-jquery-ui', MY_AIA_PLUGIN_URL . 'admin/assets/css/jquery-ui.min.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin-jstree-default', MY_AIA_PLUGIN_URL . 'admin/assets/css/jstree/default/style.min.css', '', MY_AIA_VERSION );
		
		wp_enqueue_script( 'my-aia-admin-jquery-ui', MY_AIA_PLUGIN_URL . 'admin/assets/js/jquery_ui/jquery-ui.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-tabs', MY_AIA_PLUGIN_URL . 'admin/assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-jstree', MY_AIA_PLUGIN_URL . 'admin/assets/js/jstree.min.js', '', MY_AIA_VERSION );
	
		$this->set('title',$_viewVars["titel"]);
	}
}
