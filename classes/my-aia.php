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
		MY_AIA_TAXONOMY_TAAL
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
	
	/**
	 * Processflow holder
	 * @var \MY_AIA_PROCESSFLOW 
	 */
	static $processflow;
	
	/**
	 * post_types holder
	 * @var array 
	 */
	static $post_types;
	
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
	 */
	static function init() {
		global $wp_rewrite, $wp_roles;
		
		//Upgrade/Install Routine
		if( is_admin() && current_user_can('list_users') ){
			if( MY_AIA_VERSION > get_option('my-aia-version', 0)) {
				my_aia_install();
			}
		}
		
		// register the post_types 
		self::register_post_types();
	
		// flush rules so everything works!
		$wp_rewrite->flush_rules();

		// register hooks for process flow
		self::register_hooks();
	}
	
	/**
	 * Register the hooks for the process
	 */
	static function register_hooks() {
		add_action( 'wp_ajax_my_aia_call', "MY_AIA::my_aia_ajax_call", 10, 1	);					// AJAX hook to get Events 	
		add_option('my-aia-registered-hooks', array('save_post'));
		add_option('my-aia-hook-save_post');
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
		load_plugin_textdomain( 'my-aia', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
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
		$definitionDir =		MY_AIA_PLUGIN_DIR . 'includes/custom-post-types/';
		$modelDir = MY_AIA_PLUGIN_DIR . 'classes/';	
		
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
				
				$cstm_file = sprintf('%smy-aia-%s.php',$modelDir, $post_type);
				if (file_exists($cstm_file)) {
					include_once($cstm_file);
					
					$className = "MY_AIA_$post_type";
					self::$post_types[$post_type] = new $className();
					
					if ($register_save_post_hooks) {
						// Hooks for custom post save
						//add_action( 'save_post',  sprintf('MY_AIA_%s::save', strtoupper($post_type)), 99, 2);
						add_action( 'save_post',  "my_aia_post_save_action", 99, 3);
					}
				}
			}
		}
	}
}


/**
 * Wrapper to get the MY_AIA Class
 * @return \MY_AIA
 */
function MY_AIA_INIT() {
    return MY_AIA::instance();
}