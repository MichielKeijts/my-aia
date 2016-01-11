<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/**
 * Class MY_AIA_SESSION
 * set session data and read session data.
 */
class MY_AIA_SESSION {
	static $INITIATED = false;
	/**
	 * @var \WP_User
	 */
	static $USERINFO;
	static function initiate() {
		if (MY_AIA_SESSION::$INITIATED)
			return;
		
		self::$USERINFO = wp_get_current_user();
		
		$data = get_user_meta(self::$USERINFO->ID, 'my-aia-session');		
		$_SESSION['my-aia'] = $data;		
	}
	
	/**
	 * Read SESSION data or DATABASE data
	 * @param string $var
	 * @param mixed $val Default value to return if empty
	 * @return mixed
	 */
	static function read($var, $val="") {
		$this->initiate();
		
		// read session
		if (isset($_SESSION['my-aia'][$var])) 
			return $_SESSION['my-aia'][$var];
		
		// reload from DB
		$_SESSION['my-aia'] = get_user_meta(self::$USERINFO->ID, 'my-aia-session');
		if (isset($_SESSION['my-aia'][$var])) 
			return $_SESSION['my-aia'][$var];
		
		return $val;
	}
	
	/**
	 * Writes data to SESSION and DATABASE
	 * @param sting $var variabele name
	 * @param mixed $val value to be set (and returned)
	 * @return mixed return $val if set
	 */
	static function write($var, $val) { 
		$_SESSION['my-aia'][$var] = $val;
		add_user_meta(self::$USERINFO->ID, $_SESSION['my-aia'], true);
		
		if (isset($_SESSION['my-aia'][$var])) 
			return $_SESSION['my-aia'][$var];
	}
}