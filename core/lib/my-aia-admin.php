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
	
	/**
	 * Is checked before semi automagic run of request render
	 * @var book 
	 */
	private $isMY_AIAcall = FALSE;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'show_menu'));
		//add_action( 'init', array($this,'request_render'), 999,1 );	// this calls the automatic render of the controller
		// handle the request
		$this->request_handler();		
		
		$this->em_request_handler();
	}
	
	/** 
	 * Holder for the Meta Classes
	 * @var array 
	 */
	private $_meta_classes = array();
	
	/**
	 * Render the MY_AIA_SETTINGS
	 *
	 * @return void
	 */
	public function settings() {
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_style( 'my-aia-admin-jstree-default', MY_AIA_PLUGIN_URL . 'assets/css/jstree/default/style.min.css', '', MY_AIA_VERSION );
		
		//wp_enqueue_script( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'assets/js/admin.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-tabs', MY_AIA_PLUGIN_URL . 'assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-jstree', MY_AIA_PLUGIN_URL . 'assets/js/jstree.min.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
				
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
		
		echo $this->controller->view->render('tests/test');		
	}
	
	/**
	 * MAIN FUNTION 
	 * deals with all the requests and parses the right controller
	 */
	public function request_handler() {
		// if not a call to MY_AIA admin... 
		if (!isset($_REQUEST['controller'])) return false;
		
		// default:
		$this->isMY_AIAcall = TRUE;
		$controller = 'page';
		$this->action = 'index';
		
		if (isset($_REQUEST['controller']))	$controller = $_REQUEST['controller'];
		if (isset($_REQUEST['action']))		$this->action = $_REQUEST['action'];
		
		// try and see if class/file exists
		$classfilename = sprintf('%scontrollers/my-aia-%s-controller.php', MY_AIA_PLUGIN_DIR, $controller);
		$this->className = sprintf('MY_AIA_%s_CONTROLLER', strtoupper($controller));
		if (!file_exists($classfilename)) 
			return false; // @TODO error handler

		// load class and perform action
		if (isset(MY_AIA::$controllers[$controller])) {
			$this->controller = MY_AIA::$controllers[$controller];
		} else {
			include_once ($classfilename);
			$this->controller = new $this->className();
		}
		
		// sets the AJAX listeners, so call before INIT hook is called
		call_method_if_exists($this->controller, 'before_filter'); 
		
		// done. other handling is done in $this->request_render();
		//$this->request_render();
	}
	
	/**
	 * Deals with the EXPORT CSV function of the BOOKING
	 * @global wpdb $wpdb
	 */
	private function em_request_handler() {
		global $wpdb;
	
		if (filter_input(INPUT_POST,'action') == 'export_bookings_csv' && $_POST['action'] != 'my_aia_export_bookings_csv') {
			$_REQUEST['action'] = 'my_aia_export_bookings_csv';
			$_POST['action'] = 'my_aia_export_bookings_csv';
		} elseif (filter_input(INPUT_POST,'action') == 'my_aia_export_bookings_csv') {
			$filter='';
			if (filter_input(INPUT_POST,'event_id')) $filter .= 'AND event_id = '.filter_input(INPUT_POST,'event_id');
			if (filter_input(INPUT_POST,'status')) $filter .= 'AND booking_status = '.(filter_input(INPUT_POST,'event_id') == 'confirmed' ? 1 : 0);
			if (filter_input(INPUT_POST,'scope')) {
				switch (filter_input(INPUT_POST,'event_id')) {
					case 'future':
						$filter .= 'AND booking_data > NOW()';
						break;
					case 'past':
						$filter .= 'AND booking_data < NOW()';
						break;
					default: //'all'
				}
			}
				
			
			
			
			// get bookings
			$bookings = $wpdb->get_results(sprintf("SELECT booking_id, event_id FROM {$wpdb->prefix}em_bookings WHERE 1 %s", $filter));
			$csv = array();
			foreach ($bookings as $booking_id) {
				$booking = new MY_AIA_BOOKING();
				$booking->get($booking_id->booking_id); //fill with results
				
				$event = new EM_Event($booking_id->event_id);	
				$booking->event_name = $event->event_name;
				$booking->event_code = $event->event_attributes['projectcode'];
				$booking->fields['event_name'] = array('name'=>'Event');
				$booking->fields['event_code'] = array('name'=>'Event Code');
				
				$_csv = array(); $cols = array();
				foreach ($booking->fields as $key=>$val) {
					if ($key == 'EM__BOOKING__booking_meta') continue;
					if ($row == 0) {
						$cols[] = $val['name'];
					}
					
					$_csv[]=  isset($booking->{$key})?html_entity_decode($booking->{$key}):'';
				}
				
				if ($row++==0) array_push($csv, $cols);
				array_push($csv, $_csv);
			}
			
			
			
			// output CSV
			header("Content-Type: application/octet-stream; charset=utf-8");
			$file_name = date('YmdHis'). '-' . (!empty($EM_Event->event_slug) ? $EM_Event->event_slug:get_bloginfo());
			header("Content-Disposition: Attachment; filename=".sanitize_title($file_name)."-bookings-export.csv");
			echo "\xEF\xBB\xBF"; // UTF-8 for MS Excel (a little hacky... but does the job)
			
			$delimiter = !defined('EM_CSV_DELIMITER') ? ',' : EM_CSV_DELIMITER;
			$delimiter = apply_filters('em_csv_delimiter', $delimiter);
			//Rows
			$handle = fopen("php://output", "w");
			foreach ($csv as $row) fputcsv($handle, $row, $delimiter);
			fclose($handle);
			// stop execution!
			die();
		}
	}
	
	/** 
	 * Filter the request. First parse the events
	 * - before_filter
	 * - before_render
	 * - render function
	 * - afterRender callback
	 */
	public function request_render() {
		if (defined('MY_AIA_REQUEST_RENDERED')) return FALSE;
		if (!$this->isMY_AIAcall || defined('DOING_AJAX') && DOING_AJAX) return FALSE;
		
		define('MY_AIA_REQUEST_RENDERED', TRUE);
		//call_method_if_exists($this->controller, 'before_filter');
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
			plugins_url( 'my-aia/assets/img/my-aia24.png' ), 
			10
		);
		
		// add submenu_pages
		add_submenu_page('my-aia-admin',__('Settings','my-aia'),	__('Settings','my-aia'), 'manage_options', 'my-aia-admin&controller=page', 	array($this, 'request_render') );
		add_submenu_page('my-aia-admin',__('Reset','my-aia'),	__('Reset','my-aia'), 'manage_options', 'reset', 	array($this, 'reset') );
		add_submenu_page('my-aia-admin',__('Hooks Overzicht','my-aia'),	__('Hooks Overzicht','my-aia'), 'manage_options', 'my-aia-admin&controller=processflow', 	array($this, 'request_render'));
		add_submenu_page('my-aia-admin',__('Products','my-aia'),	__('Products','my-aia'), MY_AIA_POST_TYPE_PRODUCT, 'edit.php?post_type='.MY_AIA_POST_TYPE_PRODUCT, 	NULL);
		add_submenu_page('my-aia-admin',__('Orders','my-aia'),	__('Orders','my-aia'), MY_AIA_POST_TYPE_ORDER, 'edit.php?post_type='.MY_AIA_POST_TYPE_ORDER, 	NULL);
		add_submenu_page('my-aia-admin',__('Partners','my-aia'),	__('Partners','my-aia'), MY_AIA_POST_TYPE_PARTNER, 'edit.php?post_type='.MY_AIA_POST_TYPE_PARTNER, 	NULL);
		add_submenu_page('my-aia-admin',__('Templates','my-aia'),	__('Templates','my-aia'), MY_AIA_POST_TYPE_TEMPLATE, 'edit.php?post_type='.MY_AIA_POST_TYPE_TEMPLATE, 	NULL);
		
		
		// enqueue scripts
		//wp_enqueue_script( 'my-aia-admin-custom-post-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-ui.js', '', MY_AIA_VERSION );
			
		//$this->add_metaboxes_to_post_types();
		
		add_action('em_bookings_admin_booking_person', "my_aia_events_manager_add_booking_meta_single");
		remove_action( 'admin_notices', 'update_nag', 3 );
		
		$this->em_request_handler();
	}

	/**
	 * Adds metaboxes to the various admin interface pages
	 * Metabox is described in the custom post type library, found in the classes directory
	 */
	public function add_metaboxes_to_post_types() {
		my_aia_events_manager_add_form_widget();		// Enable Events Manager Addons
		my_aia_post_type_partner_add_metaboxes();
		
		
		// initiate attribute (custom post) forms
		foreach (MY_AIA::$CUSTOM_POST_TYPES as $post_type) {
			// check if attribute form != false: otherwise no attribute form
			if(MY_AIA::$controllers[$post_type]->has_attribute_form === TRUE) {
				add_meta_box('my-aia-'.$post_type.'-attribute-box', __('Attributes','my-aia'), array(MY_AIA::$controllers[$post_type],'get_attributes_form'), $post_type, 'normal', 'high');
			}
			
			// set meta_boxes when they exists for custom post type
			call_method_if_exists(MY_AIA::$controllers[$post_type], 'set_meta_boxes');
			
			// set post filter
			$classMeta = sprintf('MY_AIA_%s_META_COLUMNS', strtoupper($post_type));
			if (class_exists($classMeta)) $this->_meta_classes[] = new $classMeta();
		}		
	}
	
	
	/**
	 * Wrapper for $this->view->set. Set view variables
	 * @param string $var name of variable
	 * @param mixed $val vlue of the variabele
	 */
	private function set($var, $val=NULL) {
		$this->controller->view->set($var, $val);
	}
	
	/**
	 * Wrapper for $this->view->set. Set view variables
	 * @param string $var name of variable
	 * @param mixed $val vlue of the variabele
	 */
	private function set_flash($title, $type='info')  {
		$this->controller->view->set_flash($title, $type);
	}
}