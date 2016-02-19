<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 */


/**
 * Class MY_AIA__VIEW
 * View render class for the Interfaces
 */
class MY_AIA_VIEW {
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
	 * Status messages holder
	 * @var array 
	 */
	private $_status_messages = NULL;
	
	/**
	 * Flash messages holder
	 * @var array 
	 */
	private $_flash_messages = array();
	
	/**
	 * Holder for the rendered body
	 * @var string
	 */
	private $_rendered_body = "";
	
	/**
	 * Reference to the caller (controller)
	 * @var \MY_AIA_APP_CONTROLLER
	 */
	private $controller;
	
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
	 * HTML helper holder
	 * @var \MY_AIA_HTML_HELPER 
	 */
	private $Html;
	
	/**
	 * Create an ADMIN_VIEW
	 * @param \MY_AIA_APP_CONTROLLER $controller
	 */
	public function __construct(&$controller) {
		// self::$_instance = self;
		$this->_viewVars = Array();
		
		// copy controller
		$this->controller = &$controller;
		
		// initiate helper
		$this->Html = new MY_AIA_HTML_HELPER($this->controller);
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
			$this->_viewVars[$key]=$val;
		}
	}
	
	/**
	 * Render function for the admin display.
	 * @param string $template name of template relative to /admin/views/
	 * @param string $layout name of layout relative to /admin/views/layouts/
	 * @return string rendered HTML
	 */
	public function render($template, $layout='default', $do_output=FALSE) {
		if (!empty($this->_rendered_body)) return "";
		
		$template	= sprintf('%sadmin/view/%s.ctp', MY_AIA_PLUGIN_DIR, $template); // path to absolute dir
		$layout		= sprintf('%sadmin/view/layouts/%s.ctp', MY_AIA_PLUGIN_DIR, $layout); // path to absolute dir
				
		// set the variables
		extract($this->_viewVars);
		
		// garbage collection of HTML and header output
		if (ob_get_length())
			$_current_output=ob_get_clean();
		else {
			//init the outputbuffer
			$done = ob_start();
			$_current_output = "";
		}
		
		if (file_exists($template)) {
			include ($template);	
		} else {
			echo "<p class=error>ERROR: Cannot Find Template File: {$template}</p>";
		}
		
		// Get Content
		$content = ob_get_clean();
		
		// include layout (which holds $content)
		include ($layout);
		$output = ob_get_clean();
		$this->_rendered_body = $output;
		
		
		// restore $_current_output;
		if ($_current_output)	
			echo $_current_output;
		
		if ($do_output) echo $output;
		return $output;
	}
	
	/**
	 * Render element $name
	 * @param string $name name of element
	 * @param array $vars (key=>value pairs)
	 * @return string rendered element
	 */
	public function element($name, $vars=NULL) {
		$name	= sprintf('%sadmin/view/elements/%s.ctp', MY_AIA_PLUGIN_DIR, $name); // path to absolute dir
		
		// set the variables
		extract($this->_viewVars);
		
		if (ob_get_length())
			$_current_output=ob_get_clean();
		else 
			$_current_output=NULL;
		
		ob_start();
		if (file_exists($name)) {
			include ($name);
		}
		// get output
		$output = ob_get_clean();
		ob_start();
		// restore $_current_output;
		if ($_current_output)	
			echo $_current_output;
		
		// return 
		return $output;
	}
	
	/**
	 * Set flash information below the menu bar of the MY_AIA plugin view. Used
	 * to display information. 
	 * @param string $title
	 * @param string $type (info/error/notice)
	 */
	public function set_flash($title, $type='info')  {
		//@TODO integrate with AJAX-> dynamic content based on user input
		if (!is_array($this->_flash_messages)) {
			$this->_flash_messages=array(array($type=>$title));
			return true;
		}
		return array_push($this->_flash_messages, array($type=>$title));
	}
	
	/**
	 * Set statusbar information in the bottom bar of the MY_AIA plugin view. Used
	 * to display information. 
	 * @param string $title
	 * @param string $type (info/error/notice)
	 */
	public function set_status($title, $type='info')  {
		//@TODO integrate with AJAX-> dynamic content based on user input
		if (!is_array($this->_status_messages)) {
			$this->_status_messages=array(array($type=>$title));
			return true;
		}
		return array_push($this->_status_messages, array($type=>$title));
	}
}