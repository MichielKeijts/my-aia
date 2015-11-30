<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

class MY_AIA_ADMIN {
	
	static function show_admin_menu() {
		echo "Welkom in het ADMIN menu voor Mijn AIA";
	}
	
	static function show_menu() {
		add_menu_page(
			__('My AIA','my-aia'), 
			__('My AIA','my-aia'), 
			'manage_options', 
			'my-aia',
			'MY_AIA_ADMIN::show_admin_menu', 
			plugins_url( 'my-aia/assets/images/my-aia24.png' ), 
			10
		);
		
		add_submenu_page('my-aia',__('Partners','my-aia'), __('Partners','my-aia'), 'manage_options', 'my-aia', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia',__('Settings','my-aia'), __('Settings','my-aia'), 'manage_options', 'my-aia', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia',__('Sportweken','my-aia'), __('Sportweken','my-aia'), 'manage_options', 'my-aia', 'MY_AIA_ADMIN::show_admin_menu' );


	}
}