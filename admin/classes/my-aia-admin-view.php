<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 */


/**
 * Class MY_AIA_ADMIN_VIEW
 * View render for the Administration interface
 */
class MY_AIA_ADMIN_VIEW {
	/**
	 * Instance pointing to itself
	 * @var \MY_AIA_ADMIN_VIEW
	 */
	private $_instance = NULL;
	
	/**
	 * Array holding the VIEW variabeles as key=>value pairs 
	 * @var Array 
	 */
	private $_viewVars = NULL;

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
	
	/**
	 * Create an ADMIN_VIEW
	 */
	public function __construct() {
		//self::$_instance = self;
		$this->_viewVars = Array();
	}
	
	/**
	 * Return a new or the instance of self
	 * @return \MY_AIA_ADMIN_VIEW
	 */
	public static function getInstance() {
        return $this;
    }
	
	/**
	 * Set Variables for the VIEW
	 * @param mixed $var Name of the var, or Array(var_name=>var_value)
	 * @param mixed $val Value of the var
	 */
	public function set($var, $val=NULL) {
		if (!is_array($var)) {
			$var = array($var=>$val);
		}
		
		foreach ($var as $key=>$val) {
			$this->_viewVars[$var]=$val;
		}
	}
	
	/**
	 * Render function for the admin display.
	 * @param string $template name of template relative to /admin/views/
	 * @param string $layout name of layout relative to /admin/views/layouts/
	 * @return string rendered HTML
	 */
	public function render($template, $layout='default') {
		$template	= sprintf('%sadmin/views/%s.ctp', MY_AIA_PLUGIN_DIR, $template); // path to absolute dir
		$layout		= sprintf('%sadmin/views/layouts/%s.ctp', MY_AIA_PLUGIN_DIR, $layout); // path to absolute dir
				
		// set the variables
		foreach ($this->_viewVars as $var=>$key) ${$var}=$key;

		// garbage collection of HTML and header output
		$_current_output=ob_get_clean();
		ob_start();
		if (file_exists($template)) {
			include ($template);	
		} else {
			echo "<p class=erro>ERROR: Cannot Find Template File: {$template}</p>";
		}
		$content = ob_get_clean();
		ob_start();
		
		
		
		// include layout (which holds $content)
		include ($layout);
		$output = ob_get_clean();
		
		// restore $_current_output;
		if ($_current_output)	
			echo $_current_output;
		
		
		return $output;
	}
	
	/**
	 * Render element $name
	 * @param string $name name of element
	 * @param array $vars (key=>value pairs)
	 * @return string rendered element
	 */
	public function element($name, $vars=NULL) {
		$name	= sprintf('%sadmin/views/elements/%s.ctp', MY_AIA_PLUGIN_DIR, $name); // path to absolute dir
		
		$_current_output=ob_get_clean();
		ob_start();
		
		if (file_exists($name)) {
			include ($name);
		}
		// get output
		$output = ob_get_clean();
		
		// restore $_current_output;
		if ($_current_output)	
			echo $_current_output;
		
		// return 
		return $output;
	}
}
 