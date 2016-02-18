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

	
	protected $classname = 'processflow';
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_static_condition_save', array($this, 'static_condition_save'),1);	
	}
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin-jstree-default', MY_AIA_PLUGIN_URL . 'admin/assets/css/jstree/default/style.min.css', '', MY_AIA_VERSION );
		
		//wp_enqueue_script( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/js/admin.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-tabs', MY_AIA_PLUGIN_URL . 'admin/assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-jstree', MY_AIA_PLUGIN_URL . 'admin/assets/js/jstree.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
	}
	
	/** 
	 * Main index. Overview of the Processflow admin
	 */
	public function index() {
		// only a placeholder..
		//$this->template='index';
	}
	
	/**
	 * Overview of the actual registered hooks in the plugin. This means that the
	 * hooks are editable using the plugin and are therefore flexible. 
	 * This function returns a list of the possible hooks
	 */
	public function hooks_index(){
		
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin-jstree-default', MY_AIA_PLUGIN_URL . 'admin/assets/css/jstree/default/style.min.css', '', MY_AIA_VERSION );
		
		//wp_enqueue_script( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/js/admin.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-tabs', MY_AIA_PLUGIN_URL . 'admin/assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-jstree', MY_AIA_PLUGIN_URL . 'admin/assets/js/jstree.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
		
		$this->view->set('data', array('hooks'=>get_option('my-aia-registered-hooks',array())));
		
		$this->template = 'overview_hooks';
	}
	
	/**
	 * Condition save by ajax call
	 */
	public function static_condition_save() {
		$_REQUEST['hook_id']=$_REQUEST['hook_description']="TEST";
		if (isset($_REQUEST['hook_id'])) {
			// first add hook to the hook list in wordpress
			$hooks = get_option('my-aia-registered-hooks', array());
			
			if (!isset($_REQUEST['hook_name'])) $hooks[$_REQUEST['hook_name']]=array();
			
			if (isset($_REQUEST['hook_order'])) {
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
			}
			
			// save option. Option init is done in plugin installation
			update_option('my-aia-registered-hooks', $hooks);
			
			
			if (MY_AIA_PROCESSFLOW::save_static_condition($_REQUEST['hook_name'], $_REQUEST['data'])) {
				wp_send_json_success();		
			}
		}
		wp_send_json_error();
	}
}
