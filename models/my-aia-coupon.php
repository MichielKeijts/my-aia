<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_PRODUCT post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_COUPON extends MY_AIA_MODEL {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_COUPON;

	/**
	 * Value of the coupon
	 * @var float
	 */
	public $value;
	
	/**
	 * Value used at this moment, static value, use in lists, not to calculate
	 * the amount left for an order, as the order itself is included
	 * @var float
	 */
	public $value_used = 0.0; 
	
	/**
	 * Size of the coupon
	 * @var type 
	 */
	private $coupon_length = 12;

	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'value'				=> array('name'=>'value','type'=>'%f'),
		'value_used'		=> array('name'=>'value_used','type'=>'%f'),
	);
	
	/**
	 * Save post hook to update the post_title as order number
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param type $update
	 * @return type
	 */
	public function save_post($post_id, $post, $update) {
		$this->get($post_id);
		if (!preg_match("/[A-Z0-9]{".$this->coupon_length."}/", $this->post_title)) {
			//$this->get($post);	// initialize
			$this->ID = $post_id;	// to be save
			
			// get a new coupon code
			$this->post_title = $this->getNewCouponCode($this->coupon_length);
			
			return $this->save(false);	// update fields by post data
		}
		parent::save_post($post_id, $post, $update);
	}
	

	/**
	 * get a random code
	 * @param int $length even number of digits
	 * @retun string
	 */
	public function getNewCouponCode($length = 12) {
		$not_unique = true;
	
		while ($not_unique) {
			$code = strtoupper(bin2hex(random_bytes(ceil($length/2))));
			
			$versions = $this->getByCode($code, FALSE);
			
			$not_unique = count($versions) > 0;
		}	
		
		return $code;
	}
	
	/**
	 * Return 
	 * @param string $code
	 * @param bool $only_open
	 * @return MY_AIA_COUPON or NULL
	 */
	public function getByCode($code_string = NULL, $only_open = TRUE) {
		$codes = $this->find(array(
			'numberposts'	=> 1,
			's'	=> $code_string	//instead of post_type
		));		
		
		if (!empty($codes)) {
			$code = reset($codes);
		}
		
		if ($only_open) {
			if (round($code->value,0) <= round($code->value_used,0))
				return NULL;
		}
		
		return $code;
	}
	
	/**
	 * Get orders where this coupon is used in
	 * @return MY_AIA_ORDER[]
	 */
	public function getOrders() {
		global $wpdb;
		$result = $wpdb->get_results(sprintf("
				SELECT * FROM %s%s WHERE meta_key = 'coupon_value' && post_id IN (
					SELECT 
						id
					FROM
						%s%s AS posts
							INNER JOIN
						%s%s AS postmeta ON posts.ID = postmeta.post_id
					WHERE
						posts.post_type = '%s' 
						AND meta_key = 'coupon_id'
						AND meta_value = %s
				)", 
				$wpdb->prefix, 'postmeta', 
				$wpdb->prefix, 'posts', 
				$wpdb->prefix, 'postmeta', 
				MY_AIA_POST_TYPE_ORDER,
				$this->ID));		
		
		return empty($result) ? [] : $result;
	}
	
	/**
	 * Get the open value for the coupon, not including the current order!
	 * @return float Currency
	 */
	public function getCurrentValue($order_id = NULL) {
		$orders = $this->getOrders();
		
		$value_used = 0.0;
		foreach ($orders as $order) {
			if ($order->post_id == $order_id) 
				continue;
			$value_used+=$order->meta_value;
		}
		return round($this->value - $value_used, 2);
	}
}