<?php

/**
 * Base class with callable functions like saving, updating, etc.
 */
class MY_AIA_BASE {
	/**
	 * @var int WP Post ID
	 */
	public $ID	=	null;	
	
	/**
	 * Name of the Object
	 * @var string
	 */
	public $name;
	/**
	 * Description of the Object
	 * @var string
	 */
	public $description;
	
	/**
	 * Assigned User ID
	 * @var int
	 */
	public $assigned_user_id;
	
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $_fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'assigned_user_id'	=> array('name'=>'assigned_user_id','type'=>'%d'),
		
	);
	/**
	 * extendable in child class...
	 * @var array private fields 
	 */
	public $fields = array();
	
	/* Post Variables - copied out of post object for easy IDE reference */
	//var $ID;
	var $post_author;
	var $post_date;
	var $post_date_gmt;
	var $post_title;
	var $post_excerpt;
	var $post_status = 'draft';
	var $comment_status;
	var $ping_status;
	var $post_password;
	var $post_name;
	var $to_ping;
	var $pinged;
	var $post_modified;
	var $post_modified_gmt;
	var $post_content_filtered;
	var $post_parent;
	var $guid;
	var $menu_order;
	var $post_type	=	'post';
	var $post_mime_type;
	var $comment_count;
	var $ancestors;
	var $filter;

	/**
	 * Base class with callable functions like saving, updating, etc.
	 * Set the contents of $post to $thimerge_fieldss
	 */
	public function __construct($post = NULL) {
		if ($post) {
			$this->get($post);
		}
		
		$this->merge_fields();
	}
	
	/**
	 * Get the post data based on input and applies to object
	 * @param mixed $post WP_Post|ID 
	 */
	public function get($post) {
		if (is_numeric($post)) {
			$post = get_post($post);
		}
		
		//if (is_a($post,'WP_Post') || is_a($this, 'MY_AIA_BASE')) {
		 //if && $post->post_type = $this->post_type
			foreach ($post as $key => $value) {
				if (property_exists($this, $key)) {
					$this->$key = $value;
				}
			}
		//}
		
		$this->get_meta();
	}
	
	/**
	 * Merge the default and the custom fields
	 */
	protected function merge_fields() {
		$this->fields = array_merge($this->fields, $this->_fields);
	}
	
	/**
	 * Get the post meta and saves into $this
	 * @return boolean
	 */
	protected function get_meta() {
		if (!$this->ID) return false;	// No ID
			
		$meta_data = get_post_meta($this->ID);
		foreach ($meta_data as $key=>$values) {
			if (in_array($key, array_keys($this->fields))) {
				$this->$key = reset($values);	// assume just one value per setting(!)
			}
		}
		
		return true;
	}
	
	
	/**
	 * Create a new object
	 */
	public function create($post_array = NULL) {
		if (!$post_array) {
			$post_array['post_type'] = $this->post_type;
			$post_array['post_title'] = $this->name;
			$post_array['post_content'] = $this->post_content;
			$post_array['post_excerpt'] = !empty($this->post_excerpt) ? $this->post_excerpt : $this->post_content;
			$post_array['post_status']	= $this->post_status;
		}
		
		// insert the POST and create
		$id = wp_insert_post($post_array);
		if (!$id) return false;
		
		// set merge_fields
		$this->ID = $id;
		return true;
	}
	
	/**
	 * Save the post type and its meta
	 * @param boolean $prepare_post_data (default true) to update object with post data
	 * @return boolean
	 */
	public function save($prepare_post_data = true) {
		if ($prepare_post_data) $this->prepare_post_data();
		
		$post_array['post_type'] = $this->post_type;
		$post_array['post_title'] = $this->name;
		$post_array['post_content'] = $this->post_content;
		$post_array['post_excerpt'] = !empty($this->post_excerpt) ? $this->post_excerpt : $this->post_content;
		$post_array['post_status']	= 'draft';
		
		
		if (!$thois->ID && !$this->create()) 
			return FALSE;// no ID and not able to create
		
		// set ID
		$post_array['ID'] = $this->ID;
		$post_saved = wp_update_post($post_array);
		if ($post_saved) {
			$this->update_post_meta($prepare_post_data);
		}
	}
	
	/**
	 * Custom function to save all the other data than the original post. As
	 * this is already set in the save function 
	 * @param bool $prepare_post_data update with $_POST data
	 */
	public function update_post_meta($prepare_post_data = true) {
		if ($prepare_post_data) $this->prepare_post_data ();
		
		foreach ($this->fields as $key=>$value) {
			update_post_meta($this->ID, $key, $this->$key);
		}
	}
	
	/**
	 * Update the object with post data, saved in $_POST
	 */
	private function prepare_post_data() {
		foreach ($_POST as $key=>$value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * Apply the $value to $this->$key
	 * @todo: check if key is allowed to be set
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		$this->$key = $value;
	}
}