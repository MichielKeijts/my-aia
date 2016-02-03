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
	 * View class
	 * @var \MY_AIA_ADMIN_VIEW
	 */
	private $view;
	
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
	
	
	
	public function __construct() {
		add_action( 'admin_menu', array($this,'show_menu'));
		
		$this->view = new MY_AIA_ADMIN_VIEW();
	}
	
	/**
	 * Render the MY_AIA_SETTINGS
	 *
	 * @access public
	 * @global      string 'buddypress-media'
	 *
	 * @param  type $page
	 * @param  type $option_group
	 *
	 * @return void
	 */
	public function settings() {
		wp_enqueue_style( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/css/admin.css', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/js/admin.js', '', MY_AIA_VERSION );
		wp_enqueue_script( 'my-aia-admin', MY_AIA_PLUGIN_URL . 'admin/assets/js/vendors/tabs.js', '', MY_AIA_VERSION );
		$this->view->render('tests/test');		
	}
	
	private function get_current_tab() {
		if (!isset($_REQUEST['tab'])) $_REQUEST['tab']="";
		
		switch ($_REQUEST['tab']) {
			case "members":	return "members";
			default:		return "";
		}
	}
	
	/**
	 * Display the various tabs
	 */
	private function render_tabs() {
		//nav-tab-active
		foreach ($this->TABS as $slug=>$tab) {
			?><a href="/wp-admin/admin.php?page=my-aia-admin&tab=<?= $slug ?>" class="nav-tab <?php if ($this->get_current_tab()==$slug) echo "nav-tab-active"; ?>"><?= $tab ?></a><?php
		}
	}
	
	public function show_admin_menu() {
		echo "Welkom in het ADMIN menu voor Mijn AIA";
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
	
	public function show_menu() {
		add_menu_page(
			__('My AIA','my-aia'), 
			__('My AIA','my-aia'), 
			'manage_options', 
			'my-aia-admin',
			array($this, 'settings'), 
			plugins_url( 'my-aia/assets/images/my-aia24.png' ), 
			10
		);
		add_submenu_page('my-aia-admin',__('Reset','my-aia'),		__('Reset','my-aia'), 'manage_options', 'my-aia-reset', array($this, 'reset') );
		add_submenu_page('my-aia-admin',__('Partners','my-aia'),	__('Partners','my-aia'), 'my_aia_admin', 'my-aia-partners', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Settings','my-aia'),	__('Settings','my-aia'), 'my_aia_admin', 'my-aia-settings', array($this, 'settings') );
		add_submenu_page('my-aia-admin',__('Sportweken','my-aia'),	__('Sportweken','my-aia'), 'my_aia_admin', 'my-aia-sportweken', 'MY_AIA_ADMIN::show_admin_menu' );
	}
}