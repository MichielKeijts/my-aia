<?php
//namespace MY_AIA;

/**
 * Base class with callable functions like saving, updating, etc.
 */
class MY_AIA_MODEL {
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
	var $post_content;
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
		// Set Save Post handler, for custom post saving
		//add_action('save_post',  array($this, 'save_post'), 99, 3);
		
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
		if ($post instanceof WP_Post && $post->post_type == $this->post_type) {
			//nothing.
		} elseif (is_numeric($post)) {
			$post = get_post($post);
		} elseif (is_array($post)) {
			$post = get_post();
		} 
		
		// if not a valid $post returned.. 
		if (!($post instanceof WP_Post) || $post->post_type != $this->post_type) 
			return FALSE;
		
		$this->apply($post);
	
		$this->get_meta();
	}
	
	/**
	 * Apply $postobject to $this when field exists
	 * @param WP_Post $postobject
	 */
	public function apply($postobject) {
		foreach ($postobject as $key => $value) {
			if (property_exists($this, $key) && $key!='fields') {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * Find objects by WP_Post criteria ($args) see also WP_Query::parse_query 
	 * for full list  of options
	 * 
	 * $args default: 
	 *	post_type = this->post_type
	 *	post_status = 'any' 
	 * 
	 * @param array $args 
	 * @param $returnAsType (TRUE) return as same object as $this
	 */
	public function find($args, $returnAsType = TRUE) {
		global $post; 
		
		$args['post_type'] = $this->post_type;
		$args['post_status'] = 'any';
		$posts = get_posts($args);
		
		if ($posts) {
			if ($returnAsType) {
				$returnObj = array();
				foreach ($posts as $_post) {
					$className = get_class($this);
					$entity = new $className($_post);
					
					// had to use clonse as reference remained the same somehow...
					array_push($returnObj, $entity);
				}
				// return as MY_AIA_<TYPE> array
				return $returnObj;
			} 
			// return as WP_Posts array
			return $posts;
		}
		return array();
	}
	
	/**
	 * Merge the default and the custom fields
	 */
	protected function merge_fields() {
		$this->fields = array_merge($this->fields, $this->_fields);
	}
	
	/**
	 * Get the post meta and saves into $this
	 * wildcard function parse_<child_property) is called if exists
	 * @return boolean
	 */
	protected function get_meta() {
		if (!$this->ID) return false;	// No ID
			
		$meta_data = get_post_meta($this->ID);
		foreach ($this->fields as $key=>$field) { //$meta_data as $key=>$values
			if (isset($meta_data[ $field['name'] ])) {
				if ($field['type']!='%a') {
					if (method_exists($this, 'parse_'.$key)) {
						$this->$key = call_user_func(array($this, 'parse_'.$key), $meta_data[ $field['name']	]);
					}
					$this->$key = reset($meta_data[	$field['name']	]);	// assume just one value per setting(!) if not an array is expected
				} else {
					// check if parse function exists
					if (method_exists($this, 'parse_'.$key)) {
						$this->$key = call_user_func(array($this, 'parse_'.$key), $meta_data[ $field['name']	]);
					} else {call_user_method();
						// just set the values
						$this->$key = $values;
					}
				}
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
		$post_array['post_title'] = !empty($this->name) ? $this->name : $this->post_title;
		$post_array['post_content'] = $this->post_content;
		$post_array['post_excerpt'] = !empty($this->post_excerpt) ? $this->post_excerpt : $this->post_content;
		$post_array['post_status']	= 'draft';
		
		
		if (!$this->ID && !$this->create()) 
			return FALSE;// no ID and not able to create
		
		// set ID
		$post_array['ID'] = $this->ID;
		$tempObj = clone $this;
		$post_saved = wp_update_post($post_array);
		if ($post_saved) {
			$this->apply($tempObj);
			$this->update_post_meta($prepare_post_data);
		}
	}
	
	/**
	 * Function to be called from the save_post hook from WP.
	 * - first initiate the current object
	 * - than updates the meta_data
	 */
	public function save_post($post_id, $post, $update ) {
		//$this->get($post); // not sure why?
		$this->apply($post);
		$this->ID = $post_id; // to be safe
		
		// updates the current data
		if ($update) $this->update_post_meta(TRUE); 		
	}
	
	/**
	 * Custom function to save all the other data than the original post. As
	 * this is already set in the save function 
	 * @param bool $prepare_post_data update with $_POST data
	 */
	public function update_post_meta($prepare_post_data = true) {
		if ($prepare_post_data) $this->prepare_post_data();
		
		foreach ($this->fields as $key=>$value) {
			update_post_meta($this->ID, $value['name'], $this->$key);
			/*if (is_array($this->$key)) {
				foreach ($this->$key as $param) {
					update_post_meta($this->ID, $key, $param);
				}
			} else {
				update_post_meta($this->ID, $key, $this->$key);
			}*/
		}
	}
	
	/**
	 * Update the object with post data, saved in $_POST
	 * Only update if property already exists, ie. not creating new properties
	 */
	protected function prepare_post_data() {
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
	
	/**
	 * Return $this-><fields> as array 
	 */
	private function toArray() {
		$returnAr = array();
		foreach ($this->fields as $field=>$value) {
			$return[$field] = $this->$field;
		}
		
		return $return;
	}
}
