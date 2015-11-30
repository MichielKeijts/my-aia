<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

class MY_AIA {
    /**
	* Load plugin textdomain.
	*
	* @since 1.0.0
	*/
    static function load_textdomain() {
		load_plugin_textdomain( 'my-aia', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
	}
}