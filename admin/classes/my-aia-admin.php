<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

class MY_AIA_ADMIN {
	
	static function show_admin_menu() {
		echo "Welkom in het ADMIN menu voor Mijn AIA";
		
		return true;
	}
	
	/**
	 * Reset the MY AIA Parameters (basically perform reinitiation of the plugin
	 */
	static function reset() {
		echo "<br>Resetting preferences...";
		
		my_aia_install();
		
		
		echo "<br>Complete.";
	}
	
	static function show_menu() {
		add_menu_page(
			__('My AIA','my-aia'), 
			__('My AIA','my-aia'), 
			'manage_options', 
			'my-aia-admin',
			'MY_AIA_ADMIN::show_admin_menu', 
			plugins_url( 'my-aia/assets/images/my-aia24.png' ), 
			10
		);
		add_submenu_page('my-aia-admin',__('Reset','my-aia'),		__('Reset','my-aia'), 'manage_options', 'my-aia-reset', 'MY_AIA_ADMIN::reset' );
		add_submenu_page('my-aia-admin',__('Partners','my-aia'),	__('Partners','my-aia'), 'my_aia_admin', 'my-aia-partners', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Settings','my-aia'),	__('Settings','my-aia'), 'my_aia_admin', 'my-aia-settings', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Sportweken','my-aia'),	__('Sportweken','my-aia'), 'my_aia_admin', 'my-aia-sportweken', 'MY_AIA_ADMIN::show_admin_menu' );
	}
}