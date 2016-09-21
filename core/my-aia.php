<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/*
 * Main function which holds all the different settings for AIA
 */
class MY_AIA {
	/**
	 * List of all custom taxonomies
	 */
	static $CUSTOM_TAXONOMIES = array(
		MY_AIA_TAXONOMY_SPORT, 
		MY_AIA_TAXONOMY_SPORT_LEVEL,
		MY_AIA_TAXONOMY_SPORTBETROKKENHEID,
		MY_AIA_TAXONOMY_KERKSTROMING,
		MY_AIA_TAXONOMY_OVERNACHTING,
		MY_AIA_TAXONOMY_SPORTWEEK_EIGENSCHAP,
		MY_AIA_TAXONOMY_DOELGROEP,
		MY_AIA_TAXONOMY_TAAL,
		MY_AIA_TAXONOMY_PRODUCT_CATEGORIE
	);
	
	/**
	 * List of all custom post types
	 */
	static $CUSTOM_POST_TYPES = array(
		MY_AIA_POST_TYPE_PARTNER, 
		MY_AIA_POST_TYPE_CONTRACT,
		MY_AIA_POST_TYPE_PRODUCT,
		MY_AIA_POST_TYPE_ORDER,
		MY_AIA_POST_TYPE_INVOICE,
		MY_AIA_POST_TYPE_PAYMENT,
		MY_AIA_POST_TYPE_TEMPLATE,
		//MY_AIA_POST_TYPE_DOCUMENT,
		'wpdmpro'
	);
	
	/*
	 * All the custom roles defined from AIA in the installer (plugin activation)
	 */
	static $ROLES = array(
		'medewerker',
		'sportcommunity_sporter',
		'avonturier',
		'partner',
		'vaandeldrager',
		'high_production_team',
		'total_athlete',
		'member',
		'supporter',
		'fan',
		'public',	// add role for public (for all google bots and so on, to define it clearly)
	);
	
	static $default_options = array(
		'email_server' => '',
		'email_port' => '',
		'email_username' => '',
		'email_password' => '',
		'email_from' => '',
		'mollie_key' => '',
		'mollie_test_mode' => '',
		'webshop_verzendkosten' => 4.95,
		//'' => '',		
	);
	
	/**
	 * Processflow holder
	 * @var \MY_AIA_PROCESSFLOW 
	 */
	static $processflow;
	
	/**
	 * Array holding various settings in key/value pairs
	 * @var array
	 */
	static $settings = array();
	
	/**
	 * Array holding view_vars
	 * @var array
	 */
	static $_viewVars;
	
	/**
	 * $controllers (Custom Post Types) holders
	 * Controlling the Custom Posts
	 * @var array 
	 */
	static $controllers;
	
	/**
	 * pointer to SELF
	 * @var \MY_AIA
	 */
	private static $instance;
	
	/**
	 * Returns an instance (or the instance) of MY_AIA
	 * @return \MY_AIA
	 */
	public function instance() {
		if ( !isset( self::$instance ) && ! (self::$instance instanceof MY_AIA ) ) {
			self::$instance = new MY_AIA();
		}
		return self::$instance;	
	}
	
	/** 
	 * Initializes the MY_AIA
	 * @global \WP_Rewrite $wp_rewrite
	 * @global \WP_Roles $wp_roles
	 * @global \WP_Query $wp_query
	 */
	static function init() {
		global $wp_rewrite, $wp_roles, $wp_query;
		$_GET['wpdmdl'] = -1;
		
		//Upgrade/Install Routine
		if( is_admin() && current_user_can('list_users') ){
			if( MY_AIA_VERSION > get_option('my-aia-version', 0)) {
				my_aia_install();
			}
		}
		
		// get options
		self::$settings = get_option('my-aia-options', self::$default_options);
		
		// pre-parse login settings
		self::apply_register_magic();
		
		// register the post_types 
		self::register_post_types();
	
		// flush rules so everything works!
		$wp_rewrite->flush_rules();

		// register hooks for process flow
		self::register_hooks();
		
		// register javascript and more
		self::enque_scripts();	
		
		
		
		
		// INIT ADMIN
		self::hide_admin_bar_for_users(FALSE); // also hide for admin
		if (is_admin()) $my_aia_admin = new MY_AIA_ADMIN();
	}
	
	/**
	 * Load the Scripts
	 */
	static function enque_scripts() {
		wp_enqueue_script('bootstrap-modal', get_stylesheet_directory_uri() . '/js/modal.js', array('jquery'));
		wp_enqueue_script('my-aia-webshop', get_stylesheet_directory_uri() . '/js/min/aia-webshop.min.js', array('jquery', 'bootstrap-modal'));
	}
	
	/**
	 * Register the hooks for the process
	 */
	static function register_hooks() {
		add_action( 'wp_footer',				"MY_AIA::get_footer_content", 10, 1);
		add_action( 'body_class',				"MY_AIA::body_class_add", 10, 1);
		add_action( 'wp_get_nav_menu_items',	"MY_AIA::get_my_aia_menu", 10, 1);					// show my-aia-menu
		add_action( 'wp_ajax_my_aia_call', "MY_AIA::my_aia_ajax_call", 10, 1	);					// AJAX hook to get Events 	
		add_option('my-aia-registered-hooks', array('save_post'));
		add_option('my-aia-hook-save_post');
		add_feed( 'my-aia-download', "MY_AIA::get_my_download" );
		
		update_option('my-aia-hook-save_post', 
				
		array(
			array(
				'conditions'	=> array(
					'AND' => array(
						array(
							'field1'=>'post_type',
							'field2'=>'partner',
							'function1'=>NULL,
							'function2'=>'turn_to_post',
							'comparison'=>NULL
						),
						array(
							'field1'=>'TRUE',
							'field2'=>'FALSE',
							'function1'=>NULL,
							'function2'=>NULL,
							'comparison'=>'!='
						)
					)
				),
				'filter'		=> array(
					'AND' => array(
						array('post_date'=>'2015-10-20 15:21:26'),
						array('OR'=>array(
							'post_author'=>1,
							'post_type'=>'get_partner'
						))
					)
				),
				'actions'		=> 'publish_post'
			)
		));
		
		
		$hooks = get_option('my-aia-registered-hooks', NULL);
		
		if (empty($hooks) || !is_array($hooks)) return false;
		
		foreach ($hooks as $hook=>$actions) {
			$hookfile=sprintf('%sclasses/processflow/my-aia-%s.php',MY_AIA_PLUGIN_DIR, str_replace('_','-',$hook));
			if (file_exists($hookfile)) {
				include_once $hookfile;
				
				// include actions to the hook
				foreach ($actions as $action) {
					$processflow = sprintf("MY_AIA_PROCESSFLOW_%s",  strtoupper($hook));
					$processflow::create_hook();
				}
			}					
		}		
	}
	
	
    /**
	* Load plugin textdomain.
	*
	* @since 0.1.0
	*/
    static function load_textdomain() {
		load_plugin_textdomain( 'my-aia', false, plugin_basename( dirname( __FILE__ ) ) . '/../config/languages' ); 
	}
	
	/**
	 * Show the bar for the administrator
	 * @param bool $show_for_admin (Default:true)
	 * @return string
	 */
	static function hide_admin_bar_for_users ($show_for_admin = true) {
		if (!$show_for_admin || !current_user_can('administrator') && !is_admin()) {
		  show_admin_bar(false);
		}
	}

	
	/**
	 * Static return array of capabilities
	 * @param string $name Name (slug) of the $type
	 * @param string $type (default post), taxonomy, page, etc..
	 * @param string $parent parent type (for hierarchy)
	 * @return string
	 */
	static function get_capabilities($name, $type="post", $parent=NULL) {
		// if parent not set, copy from $type
		if (empty($parent)) $parent=$type;	
		
		switch ($type) {
			case "taxonomy":
				$capabilities = array(
						'manage_terms' => 'edit_'.$name.'_categories',
						'edit_terms' => 'edit_'.$name.'_categories',
						'delete_terms' => 'delete_'.$name.'_categories',
						'assign_terms' => 'edit_'.$parent,
					);	
				break;
			case "post":
				$capabilities = array(
						'publish_posts' => 'publish_'.$name,
						'edit_posts' => 'edit_'.$name,
						'edit_others_posts' => 'edit_others_'.$name,
						'delete_posts' => 'delete_'.$name,
						'delete_others_posts' => 'delete_others_'.$name,
						'read_private_posts' => 'read_private_'.$name,
						'edit_post' => 'edit_'.$name,
						'delete_post' => 'delete_'.$name,
						'read_post' => 'read_'.$name,		
					);
				break;
		}
		
		return $capabilities;
	}
	
	// random test function
	static function get_partner () {
		return 'partner';
	}
	
	/**
	 * Ajax Call 
	 */
	static function my_aia_ajax_call() {
		include_once MY_AIA_PLUGIN_DIR . 'addons/events-manager/classes/my-aia-events-query.php';
		
		
		if (filter_input(INPUT_GET,'post_type') === 'event' || 
			filter_input(INPUT_POST,'post_type') === 'event') {
			
			header("Access-Control-Allow-Origin: *");
			
			MY_AIA_EVENTS_QUERY::call_function();
		}
	}
	
	/**
	 * Magically loads all the custom post types and its custom classes (Models)
	 * when exists.
	 * @param mixed $type name/array of post_type(s) to register (default: all)
	 * @param book $register_save_post_hooks register the custom post save_post  hook (default: TRUE)
	 */
	static function register_post_types($type='all', $register_save_post_hooks = TRUE) {
		$definitionDir =		MY_AIA_PLUGIN_DIR . 'config/custom-post-types/';
		$controllerDir = MY_AIA_PLUGIN_DIR . 'controllers/';	
		
		$post_types = self::$CUSTOM_POST_TYPES;
		if ($type!='all')  {
			// in this way the intersection of existing post_types and the 
			$post_types = is_array($type) ? array_intersect($post_types, $type) : array_intersect($post_types, array($type));
		}
		// 
		foreach ($post_types as $post_type) {
			$cstm_file = sprintf('%smy-aia-%s.php',$definitionDir, $post_type);
			if (file_exists($cstm_file)) include_once $cstm_file;
			
			$fn = 'my_aia_register_post_type_'.$post_type;
			if (function_exists($fn)) {
				call_user_func($fn);
			}
			
			// skip register function if custom post type exists via other plugin
			// LOAD CONTROLLER
			$cstm_file = sprintf('%smy-aia-%s-controller.php',$controllerDir, $post_type);
			$model_file = sprintf('%s../models/my-aia-%s.php',$controllerDir, $post_type);
			if (file_exists($cstm_file)) {
				include_once($cstm_file);
				include_once($model_file);

				$className = strtoupper(sprintf("MY_AIA_%s_CONTROLLER",$post_type));
				self::$controllers[$post_type] = new $className();
			}
			
		}
		
		// save post action
		add_action('save_post', 'MY_AIA::post_save_action', 99, 3);
	}
	
	/**
	 * Function which calls custom post types to save in case a custom post class
	 * exists.
	 * Magically loads the self::$models definitions
	 */
	function post_save_action($post_id, $post, $update) {
		$className = strtoupper("MY_AIA_{$post->post_type}_CONTROLLER");
		if (!(isset(self::$controllers[$post->post_type]) && is_a(self::$controllers[$post->post_type], $className))) {
			// check if model exists, otherwise leave
			$controllerDir = MY_AIA_PLUGIN_DIR . 'controllers/';	
			$cstm_file = sprintf('%smy-aia-%s.php',$controllerDir, $post->post_type);
			if (file_exists($cstm_file)) {
				include_once($cstm_file);
				self::$controllers[$post_type] = new $className();
			} else {
				return FALSE;
			}
		} 			
		
		$post = self::$controllers[$post->post_type]->get_model();
		$post->save_post($post_id, $post, $update);
		return TRUE;
	}
	
/** 
 * Remove Scroll on front page
 */
	static function body_class_add ( $classes ) {
		if (empty(buddypress()->current_component) && 
			strpos($_SERVER['REQUEST_URI'], '/shop') === FALSE &&		
			strpos($_SERVER['REQUEST_URI'], '/product') === FALSE
		) return $classes;
		return array_merge( $classes, array( 'no-scroll', 'scroll') );
	}
	
	/**
	 * Magically loads all the custom taxnomies in the directory
	 * when exists.
	 * @param mixed $type name/array of post_type(s) to register (default: all)
	 * @param book $register_save_post_hooks register the custom post save_post  hook (default: TRUE)
	 */
	/*static function register_taxonomies($type='all', $register_save_post_hooks = TRUE) {
		$definitionDir =		MY_AIA_PLUGIN_DIR . 'config/custom-taxonomies/';
		
		$taxonomies = self::$CUSTOM_TAXONOMIES;

		foreach ($taxonomies as $tax) {
			$cstm_file = sprintf('%smy-aia-%s.php',$definitionDir, $tax);
			if (file_exists($cstm_file)) include_once $cstm_file;
			
			$fn = 'my_aia_register_taxonomy_'.$tax;
			if (function_exists($fn)) {
				call_user_func($fn);
			}
		}
	}*/
	
	/**
	 * Return Events for current_user 
	 * @param int $user_id
	 */
	static function get_my_events($user_id = 0, $future_events = FALSE) {
		em_get_my_bookings();
		$bookings = EM_Bookings::get(array(
			'owner' => false,
			'person' => $user_id,
			'scope'	=> $future_events ? 'future':'past',			
		));
		
		$my_events = array();
		foreach ($bookings as $booking) {
			$event = $booking->get_event();
			$event->link = $event->output("#_EVENTLINK"); 
			$event->start =date('d/m', $event->start ); 
			$my_events[] = $event;			
		}
		
		return $my_events;
	}
	
	static function get_my_orders($user_id = 0, $count=3) {
		$orders = my_aia_order()->ORDER->findByUserID($user_id, $count);
		
		if (!$orders) {
			$orders = array(); //safety, prevent PHP warning, show empty list
		}

		// return the orders
		return $orders;
	}
	
	/**
	 * Returns a list of events recommended for the user
	 * @param int $user_id
	 * @return EM_Event array()
	 */
	static function get_recommended_events($user_id = 0) {
		if ($user_id == 0) $user_id = get_current_user_id ();
		
		$events = EM_Events::get(array(
			'limit' => 5,
			//'scope' => 'all', //@TODO only for testing purpose
			//'order' => 'event_start_date',
			'orderby' => 'DESC'
		));
		
		return $events;
	}
	
	/**
	 * Return object with  new and total count
	 * 
	 * obj->new
	 * obj->total
	 * 
	 * @param int $user_id (optional, default current_logged in user
	 * @return \stdClass
	 */
	static function get_my_messages_count($user_id = 0) {
		if ($user_id == 0) $user_id = get_current_user_id ();
		
		$obj = new stdClass();
		$obj->messages = messages_get_unread_count($user_id);
		$tmp = BP_Notifications_Notification::get_current_notifications_for_user();
		$obj->notifications = $tmp['total'];
		
		return $obj;
	}
	
	/**
	 * Display the My AIA navigaton bar. Possibly in different than buddypress format
	 */
	static function navigation_bar($extra_arguments = array()) {
		if (!is_array($extra_arguments)) return FALSE;
		if (self::$settings['navigation_bar'] && is_array(self::$settings['navigation_bar'])) 
			$extra_arguments = $extra_arguments + self::$settings['navigation_bar'];
		include MY_AIA_PLUGIN_DIR . "views/default/buddypress/common/navigation-bar.php";
	}
	
	/**
	 * Return whether to display the buddypress header or not.
	 * Default true
	 * override by setting: MY_AIA::hide_buddypressheader();
	 * @return bool
	 */
	static function display_buddypressheader() {
		return !(isset(self::$settings['hide_buddypressheader']) && self::$settings['hide_buddypressheader']);
	}
	
	static function hide_buddypressheader() {
		self::$settings['hide_buddypressheader'] = TRUE;
	}
	static function show_buddypressheader() {
		self::$settings['hide_buddypressheader'] = FALSE;
	}
	
	/**
	 * Set arguments for the navigation bar
	 * parameters:
	 * 
	 *	'current_title' =>	<string>
		'nav'			=>	<li></li>
		'title'			=> <string>
	 * @param array $extra_arguments
	 * @return boolean
	 */
	static function set_navigationbar($extra_arguments= array()) {
		if (!is_array($extra_arguments)) return FALSE;
		self::$settings['navigation_bar'] = $extra_arguments;
	}
	
	/**
	 * Footer content for lay over (login menu)
	 */
	static function get_footer_content() {
		?>
		<div id="join-us-modal" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <!--<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  </div>-->
			  <div class="modal-body">
			  </div>
			</div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		<div class="page-cover display-none"></div>
		<?php	
	}
	
	/**
	 * Extra menu items: 
	 * - Mijn AIA
	 * - Login / Logout
	 * - Join Us / Members
	 */
	static function get_my_aia_menu($items, $menu = NULL, $args=NULL) {
		if (get_current_user_id()!=0);
		
		/**
		 * Nasty Solution: Override the last 3 post elements
		 */
		$id = count($items) - 3;
		
		
		/*
		 * If User is logged in menu
		 */
		if (get_current_user_id()!=0) {
			$items[$id]->title = 'Mijn AIA';
			$items[$id]->url = bp_core_get_user_domain( get_current_user_id() );
			
			$id++;
			$items[$id]->title = __('Uitloggen','my-aia');
			$items[$id]->url = wp_logout_url('/');
			
			$id++;
			$items[$id]->title = __('Members','my-aia');
			$items[$id]->url = sprintf('/%s/%s/', MY_AIA_BP_ROOT, MY_AIA_BP_MEMBERS);
		} else {
			$items[$id]->title = 'Mijn AIA';
			$items[$id]->url = '/mijn-aia/join-us/';
			
			$id++;
			$items[$id]->title = __('Inloggen','my-aia');
			$items[$id]->url = wp_login_url(MY_AIA_BP_ROOT);

			$id++;
			$items[$id]->title = __('Join Us','my-aia');
			$items[$id]->url = '/mijn-aia/join-us/';
		}
		return $items;
	}
	
	
	/**
	 * Set Variables for the VIEW
	 * @param mixed $var Name of the var, or Array(var_name=>var_value)
	 * @param mixed $val Value of the var
	 */
	static function set($var, $val=NULL) {
		if (!is_array($var)) {
			$var = array($var=>$val);
		}
		
		foreach ($var as $key=>$val) {
			self::$_viewVars[$key]=$val;
		}
	}
	
	/**
	 * Get Variables for the VIEW
	 * @param mixed $var Name of the var, or Array(var_name=>var_value)
	 * @param mixed $val Value of the var
	 */
	static function get($key, $default=NULL) {
		if (isset(self::$_viewVars[$key])) return self::$_viewVars[$key];
		
		return $default;
	}
	
	/**
	 * Apply some register magic, so login without some variables is possible
	 */
	static function apply_register_magic() {
		$data = filter_input(INPUT_POST, '_wp_http_referer');
		if ($data && strpos($data, '/mijn-aia/join-us')!==FALSE) {
			if (empty($_POST['field_1'])) {
				$_POST['field_1'] = $_POST['signup_username'];
			}	
		}
	}
	
	/**
	 * Function redirects to download file, if user has acces
	 * @global wpdb $current_user
	 */
	static function get_my_download($post_id = NULL) {
		global $current_user;
		if (!$post_id) $post_id = filter_input(INPUT_GET, 'post');
		if (!$post_id) throw_404();
		
		// get user access
		$post = new MY_AIA_INVOICE($post_id);
		
		if ($post->assigned_user_id && $post->assigned_user_id == $current_user->ID 
				|| is_admin()
				|| current_user_can('moderate',$post_id)) {
			
			// get download link
			WPFB_Download::SendFile($post->attachment);
		}
		
		return throw_404();
	}
}


/**
 * Wrapper to get the MY_AIA Class
 * @return \MY_AIA
 */
function MY_AIA_INIT() {
    return MY_AIA::instance();
}

