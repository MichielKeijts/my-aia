<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/**
 * Class MY_AIA_BASE
 * - Holder for the MY_AIA class variables, shared among classes.
 * - Registering hooks for processing 
 */
class MY_AIA_PROCESSFLOW {

	public function __construct() {
		
	}
	
	/**
	 * Register the hooks for the process
	 */
	static function register_hooks() {
		add_option('my-aia-registered-hooks', array('save_post'));
		add_option('my-aia-hook-save_post', array('action'=>'update_post'));
		
		
		$hooks = get_option('my-aia-registered-hooks', NULL);
		
		if (empty($hooks) || !is_array($hooks)) return false;
		
		foreach ($hooks as $hook) {
			$hookfile=MY_AIA_PLUGIN_DIR . '/classes/processflow/'.$hook.'.php';
			if (file_exists($hookfile)) {
				include_once $hookfile;
				add_action($hook, array($this,'run'),2);
			}					
		}		
	}
	
	/**
	 * Magic function where it all happens
	 * Called by a hook. This function is a wrapper t
	 */
	public static function run($hook) {
		if (empty($hook)) return false; // no hook specified
		
		// run the action
		$action = get_option('my-aia-hook-'.$hook, FALSE);
		
		// continue to do the action
		if ($action) {
			if (!is_array($action)) $action=array($action); // assume list of action for the hooks
			
			//.. @TODO add logic
		}
	}
}