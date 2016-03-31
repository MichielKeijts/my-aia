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
class MY_AIA_PROCESSFLOW_CONTROLLER extends MY_AIA_APP_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'processflow';
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_hook_save', array($this, 'hook_save'),1);	
		add_action( 'wp_ajax_my_aia_admin_processflow_order_save', array($this, 'processflow_order_save'),1);	
		
		add_action( 'wp_ajax_my_aia_admin_static_condition_load', array($this, 'static_condition_load'),1);	
	}
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {		
		parent::before_render();
		
		// extra script
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
		
		// setting the menu bar for this controller
		$menu_bar = array(
			'add' => __('Nieuw','my-aia'),
			'index' => __('Hooks Overzicht'),
		);
		
		$this->set('menu_bar', $menu_bar);
	}
	
	/** 
	 * Main index. Overview of the Processflow admin
	 */
	public function add() {
		$this->set('title', __('Hook Toevoegen','my-aia'));
		
		// make subset of filters to be defined as hook
		$hooks=array();
		foreach ($GLOBALS['wp_filter'] as $filter=>$val) {
			if (strpos($filter, 'save')===FALSE)
				continue;
			$hooks[$filter]=$filter;
		}	
		
		$this->view->set_flash('Dit is een belangrijk bericht!');
		$this->set('hooks', $hooks);
		$this->set('data', array('id'=>  uniqid(),'hook_name'=>'save_post'));
		// only a placeholder..
		//$this->template='index';
		$this->template = 'edit';
	}
	
	/**
	 * Overview of the actual registered hooks in the plugin. This means that the
	 * hooks are editable using the plugin and are therefore flexible. 
	 * This function returns a list of the possible hooks
	 */
	public function index(){
		$this->set('title', __('Overzicht van alle geregistreede Hooks','my-aia'));
		
		// edit /
		$_hooks = get_option('my-aia-registered-hooks', array());
		/*$_hooks = array(
			'save_post'=>array(uniqid(),uniqid(),uniqid(),uniqid()), 
			'update_user'=>array(uniqid(),uniqid(),uniqid(),uniqid(),uniqid(),uniqid()),
			'cron_job'=>array(uniqid(),uniqid(),uniqid())
		); */
		
		// fill hooks with description data
		$hooks = array();
		if (!empty($_hooks)) {
			foreach ($_hooks as $hook_name=>$processflows) {
				foreach ($processflows as $processflow) {
					$hook_info = get_option('my-aia-registered-hook-'.$processflow, array('description'=>'Random String'));
					$hooks[$hook_name][] = array('id'=>$processflow, 'description'=>$hook_info['description']);
				}
			}
		}
		$this->view->set('data', array('hooks'=>$hooks));
	}
	
	/**
	 * Overview of the actual registered hooks in the plugin. This means that the
	 * hooks are editable using the plugin and are therefore flexible. 
	 * This function returns a list of the possible hooks
	 */
	public function edit() {
		if (isset($_REQUEST['id'])) {
			$this->set('title', __('Hook Aanpassen','my-aia'));

			// make subset of filters to be defined as hook
			$hooks=array();
			foreach ($GLOBALS['wp_filter'] as $filter=>$val) {
				if (strpos($filter, 'save')===FALSE)
					continue;
				$hooks[$filter]=$filter;
			}	
			
			$this->view->set('data', array('hooks'=>get_option('my-aia-registered-hooks',array())));

			// get data
			$data = get_option('my-aia-registered-hook-'.$_REQUEST['id'], false);
			if ($data) {	
				$this->view->set('data',$data);
				
				$this->view->set_flash('Dit is een belangrijk bericht!');
				$this->set('hooks', $hooks);
			} else {
				$this->view->set_flash('Kan ID niet vinden in de database!');
			}
		} else {
			// cannot find the hook
			$this->view->set_flash('Geen ID meegegeven!');
			return $this->add();
		}
		$this->template = 'edit';
	}
	
	/**
	 * Load the static condition
	 */
	public function static_condition_load() {
		if (isset($_REQUEST['id'])) {
			$json_data = MY_AIA_PROCESSFLOW::get_static_condition($_REQUEST['id']);
			
			if (!empty($json_data)) 
				wp_send_json_success($data);
			else 
				wp_send_json_error($data);
		}
	}
	
	/**
	 * Condition save by ajax call
	 * We save the id in option_name my-aia-hook-id
	 */
	public function hook_save() {
		if (isset($_REQUEST['id'])) {
			// first add hook to the hook list in wordpress
			$hooks = get_option('my-aia-registered-hooks', array());
			
			if (!isset($_REQUEST['hook_name'])) $hooks[$_REQUEST['hook_name']]=array();
			
			$hooks[$_REQUEST['hook_name']][]=$_REQUEST['id'];
			// see if the order is set as well
			/*if (isset($_REQUEST['hook_order'])) {
				// first remove old hook entry
				foreach ($hooks[$_REQUEST['hook_name']] as $key=>$hook) {
					if ($hook==$_REQUEST['hook_id']) 
						unset($hooks[$_REQUEST['hook_name']][$key]);
				}
				
				//update_hooks to right order
				$hooks[$_REQUEST['hook_name']][$_REQUEST['hook_order']]=TRUE;
				
			} else {
				// add hooks
				$hooks[$_REQUEST['hook_name']][]=TRUE;
			}*/
			
			// save option. Option init is done in plugin installation
			update_option('my-aia-registered-hooks', $hooks);
			
			
			if (MY_AIA_PROCESSFLOW::save_condition($_REQUEST['id'], $_REQUEST['hook_name'], $_REQUEST['description'], $_REQUEST['data'])) {
				wp_send_json_success();		
			}
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
	public function processflow_order_save() {
		if (isset($_POST['hook_name'])) {
			// get the option data
			
			$hook_info = get_option(MY_AIA_REGISTERED_HOOKS, false);
			
			if (!$hook_info || isset($hook_info[$_POST['hook_name']]) || empty($hook_info[$_POST['hook_name']])) {
				// cannot happen, but send json_error
				$msg=__('Cannot find hooks under the supplied condition');
				wp_send_json_error($msg);
			}
			
			$hook_info[$_POST['hook_name']] = array();
			
			// update hooks
			foreach ($_POST['data'] as $id) {
				$hook_info[$hook_name][] = $id; // set ID				
			}
			
			update_option(MY_AIA_REGISTERED_HOOKS, $hook_info);
			
			wp_send_json_success();		
		}
		wp_send_json_error();
	}
}
