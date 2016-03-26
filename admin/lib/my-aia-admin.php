<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/**
 * Class MY_AIA_ADMIN
 * Holder for the Administration pages
 */
class MY_AIA_ADMIN {

	
	/**
	 * $TABS
	 * @var array ('slug','name')
	 */
	private $TABS = array(
		''	=> 'My AIA',
		'members'	=> 'Leden',
	);
	
	private $SUBPAGES = array(
		'my-aia'	=> '',
		
	);
	private $action;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'show_menu'));
		// handle the request
		$this->request_handler();
	}
	
	/**
	 * Render the MY_AIA_SETTINGS
	 *
	 * @return void
	 */
	public function settings() {
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin-jstree-default', MY_AIA_PLUGIN_URL . 'admin/assets/css/jstree/default/style.min.css', '', MY_AIA_VERSION );
		
		//wp_enqueue_script( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/js/admin.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-tabs', MY_AIA_PLUGIN_URL . 'admin/assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-jstree', MY_AIA_PLUGIN_URL . 'admin/assets/js/jstree.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
				
		// make subset of filters to be defined as hook
		$hooks=array();
		foreach ($GLOBALS['wp_filter'] as $filter=>$val) {
			if (strpos($filter, 'save')===FALSE)
				continue;
			array_push($hooks, $filter);
		}	
		
		
		$this->set('menu_bar',array('Nieuw'=>'','Overzicht'=>''));
		
		$this->set_flash('Dit is een belangrijk bericht!');
		$this->set('hooks', $hooks);
		
		$this->set('hook_name', $_REQUEST['hook_name']);
		
		echo $this->view->render('tests/test');		
	}
	
	/**
	 * MAIN FUNTION 
	 * deals with all the requests and parses the right controller
	 */
	public function request_handler() {
		// default:
		$controller = 'page';
		$this->action = 'index';
		
		if (isset($_REQUEST['controller']))	$controller = $_REQUEST['controller'];
		if (isset($_REQUEST['action']))		$this->action = $_REQUEST['action'];
		
		// try and see if class/file exists
		$classfilename = sprintf('%sadmin/controller/my-aia-%s-controller.php', MY_AIA_PLUGIN_DIR, $controller);
		$this->className = sprintf('MY_AIA_%s_CONTROLLER', strtoupper($controller));
		if (!file_exists($classfilename)) 
			return false; // @TODO error handler

		// load class and perform action
		include_once ($classfilename);
		$this->controller = new $this->className();
		
		call_method_if_exists($this->controller, 'before_filter'); 
		
		// done. other handling is done in $this->request_render();
	}
	
	/** 
	 * Filter the request. First parse the events
	 * - before_filter
	 * - before_render
	 * - render function
	 * - afterRender callback
	 */
	public function request_render() {
		call_method_if_exists($this->controller, 'before_filter');
		call_method_if_exists($this->controller, 'before_render'); 
		
		// call the action and render
		$this->controller->{$this->action}();
		
		// call the render function
		$this->controller->render($this->action);
		
		
		call_method_if_exists($this->controller, 'after_render');
	}
	
	private function get_current_tab() {
		if (!isset($_REQUEST['tab'])) $_REQUEST['tab']="";
		
		switch ($_REQUEST['tab']) {
			case "members":	return "members";
			default:		return "";
		}
	}
	
	public function show_admin_menu() {
		//echo "Welkom in het ADMIN menu voor Mijn AIA";
		return true;
	}
	
	/**
	 * Reset the MY AIA Parameters (basically perform reinitiation of the plugin
	 */
	public function reset() {
		echo "<br>Resetting preferences...";
		my_aia_install();
		echo "<br>Complete.";
	}
	
	/**
	 * Show Menu and more. Basically, most of the stuff adding callable actions
	 * is declared here.
	 */
	public function show_menu() {
		add_menu_page(
			__('My AIA','my-aia'), 
			__('My AIA','my-aia'), 
			'manage_options', 
			'my-aia-admin',
			array($this, 'request_render'), 
			plugins_url( 'my-aia/assets/images/my-aia24.png' ), 
			10
		);
		add_submenu_page('my-aia-admin',__('Reset','my-aia'),		__('Reset','my-aia'), 'manage_options', 'my-aia-reset', array($this, 'reset') );
		add_submenu_page('my-aia-admin',__('Partners','my-aia'),	__('Partners','my-aia'), 'my_aia_admin', 'my-aia-partners', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Settings','my-aia'),	__('Settings','my-aia'), 'my_aia_admin', 'my-aia-settings', array($this, 'settings') );
		add_submenu_page('my-aia-admin',__('Sportweken','my-aia'),	__('Sportweken','my-aia'), 'my_aia_admin', 'my-aia-sportweken', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Hooks Overzicht','my-aia'),	__('Hooks Overzicht','my-aia'), 'my_aia_admin', 'my-aia-hooks-index', array($this, 'hooks_index'));
		
		// Enable Events Manager Addons
		my_aia_events_manager_add_ninja_form_widget();
		add_action('em_bookings_admin_booking_person', "my_aia_events_manager_add_booking_meta_single");
		
		
		remove_action( 'admin_notices', 'update_nag', 3 );
	}

	
	
	/**
	 * Wrapper for $this->view->set. Set view variables
	 * @param string $var name of variable
	 * @param mixed $val vlue of the variabele
	 */
	private function set($var, $val=NULL) {
		$this->controller->view->set($var, $val);
	}
}

/**
 * Try and find a method of a function and call it. 
 * @param class $obj
 * @param string $method
 * @return mixed FALSE if function not exists, function output otherwise 
 */
function call_method_if_exists($obj, $method) {
	if (method_exists($obj, $method))
		return $obj->$method();
	
	return FALSE;
}