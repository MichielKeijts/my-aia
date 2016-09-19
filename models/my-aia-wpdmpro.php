<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_ORDER post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_WPDMPRO extends MY_AIA_MODEL {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = "wpdmpro";
	
	

	/**
	 * Name of the Object
	 * @var string
	 */
	public $product_id;
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'product_id'		=> array('name'=>'product_id','type'=>'%s'),
	);

	/**
	 * Order 
	 * @var \MY_AIA_INVOICE
	 */
	public $product = FALSE;
	
	/**
	 * 
	 * @param int|WP_Post $post
	 */
	public function __construct($post = NULL) {
		parent::__construct($post);
		$this->has_attribute_form = FALSE;
	}
	
	/**
	 * Save post hook to update the post_title as order number
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param type $update
	 * @return type
	 */
	/*public function save_post($post_id, $post, $update) {
		if (!preg_match("/Order [0-9]+/",$post->post_title)) {
			$this->get($post);	// initialize
			$this->ID = $post_id;	// to be save
			
			$this->set_order_nr(TRUE);	// update order number
			return $this->save(false);
		}
		parent::save_post($post_id, $post, $update);
	}*/
	
	/**** PARSER FUNCTIONS CALLED BY POSTMETA *****/
	
	/**
	 * Parse function called by get_post_meta
	 * NB: we do not actually update invoice_name, 
	 */
	public function parse_product_id($id) {
		$this->product = new MY_AIA_PRODUCT($id[0]);
		// we will return the name..
		return $id;
	}
	
	/**
	 * Wrapper for find() function, to search for any order from the user
	 * @param type $id
	 * @return type
	 */
	public function findByProduct($id) {
		return $this->find(array(
			'post_status' => 'any',
			'meta_query' =>array(array('key'=>'product_id', 'value' =>	$id))
		));
	}
}