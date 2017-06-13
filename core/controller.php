<?php
//namespace MY_AIA;
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
class MY_AIA_CONTROLLER {
	/**
	 * View class
	 * @var MY_AIA_ADMIN_VIEW
	 */
	public $view;
	
	
	protected $layout = 'default';
	
	protected $classname = 'app';
	
	/**
	 * To show an attribute form
	 * @var bool
	 */
	public $has_attribute_form = TRUE;
	
	/**
	 * Additional fields to hide on attribute form
	 * @var array
	 */
	public $additional_fields_hidden_on_attribute_form = array();

	
	
	
	public function __construct() {
		$this->view = new MY_AIA_VIEW($this);
		
		//  set SAVE_POST
		$model = strtoupper($this->classname);
		$modelClass = sprintf("MY_AIA_%s",$model);//e.g. MY_AIA_TEMPLATE
		// only if model exists. Makes Controlles more flexible
		if (file_exists(sprintf('%smodels/my-aia-%s.php', MY_AIA_PLUGIN_DIR, $this->classname))) {
			require_once sprintf('%smodels/my-aia-%s.php', MY_AIA_PLUGIN_DIR, $this->classname);	// NO autoload yet.. in future remove
			$this->{$model} = new $modelClass(); 
		}
	}
	
	/**
	 * Output attribute (custom post data) form
	 * @global type $post
	 * @param type $prefix
	 * @return bool and echo the form
	 */
	public function get_attributes_form($prefix="") {
		if (!is_string($prefix)) $prefix="";
		global $post;
		
		if (!$this->get_model()->ID && $post && $post->ID)	$this->get_model()->get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id','order_items','_order_items','total_order_price','parent_type', 'inherit_from'); // hide
		$displayed_fields = array_merge($displayed_fields, $this->additional_fields_hidden_on_attribute_form);// hide other
		
		
		//
		// return data
		$data = array();
		foreach ($this->get_model()->fields as $field):
			// create a dynamic form, to only show some some of the values
			if (!empty($prefix) && strpos($field['name'], $prefix)===FALSE) continue;
			if (in_array($field['name'], $displayed_fields)) continue; // step over already displayed fields..
			$field['label'] = __($field['name'],'my-aia'); //my_aia_get_default_field_type($_field);

			// get value (as usually is an array)
			$value = isset($this->get_model()->{$field['name']}) ? esc_attr($this->get_model()->{$field['name']}, ENT_QUOTES):'';
			//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
			if (!$value) $value="";

			$field['value'] = $value;
			$data[] = $field;
		endforeach; // loop over $fields
		
		return my_aia_add_attributes_form(MY_AIA_POST_TYPE_ORDER, MY_AIA_POST_TYPE_ORDER, $data);
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

		$path = sprintf('%sviews/%s/%s', MY_AIA_PLUGIN_DIR, lowercase_underscore($this->classname), lowercase_underscore($action));
		
		if (!file_exists($path.'.ctp')) {
			return sprintf('error/general');
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
	 * Returns the current model
	 * @return \MY_AIA_MODEL 
	 */
	public function get_model() {
		$modelName = strtoupper($this->classname);
		if (!property_exists($this, $modelName) || !is_a($this->{$modelName}, sprintf('MY_AIA_%s',$modelName)))
			return FALSE;
		return $this->{$modelName};
	}
	
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {	
		if (!is_admin()) return true;
		wp_enqueue_style( 'my-aia-admin-jquery-ui', MY_AIA_PLUGIN_URL . 'assets/css/jquery-ui.min.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin-jstree-default', MY_AIA_PLUGIN_URL . 'assets/css/jstree/default/style.min.css', '', MY_AIA_VERSION );
		
		wp_enqueue_script( 'my-aia-admin-jquery-ui', MY_AIA_PLUGIN_URL . 'assets/js/jquery_ui/jquery-ui.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-tabs', MY_AIA_PLUGIN_URL . 'assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-jstree', MY_AIA_PLUGIN_URL . 'assets/js/jstree.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-admin.js', '', MY_AIA_VERSION );
	
		$this->set('title',$_viewVars["titel"]);
	}
	
	/**
	 * Before filter function. Before starting with rendering, after the request 
	 * is filtered, to alter POST data for example
	 * Called in ADMIN functions only
	 */
	public function before_filter() {
		
	}
	
	/**
	* Works much like <a href="http://codex.wordpress.org/Function_Reference/locate_template" target="_blank">locate_template</a>, except it takes a string instead of an array of templates, we only need to load one.
	* Copied and modified from Events-Manager
	* @param string $template_name
	* @param boolean $load
	* @uses locate_template()
	* @return string
	*/
   function locate_template( $template_name, $load=false, $args = array() ) {
	   //First we check if there are overriding tempates in the child or parent theme
	   $located = locate_template(array(MY_AIA_PLUGIN_DIR.$template_name, $template_name));
	   if( !$located ){
		   if ( file_exists(MY_AIA_PLUGIN_DIR.'/views/default/'.$template_name) ) {
			   $located = MY_AIA_PLUGIN_DIR.'/views/default/'.$template_name;
		   }
	   }
	   $located = apply_filters('my_aia_locate_template', $located, $template_name, $load, $args);
	   if( $located && $load ){
		   if( is_array($args) ) extract($args);
		   include($located);
	   }
	   return $located;
   }
}
