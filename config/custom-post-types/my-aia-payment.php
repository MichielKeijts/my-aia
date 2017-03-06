<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type PAYMENT
function my_aia_register_post_type_payment(){
	$payment_post_type = array(	
		'public' => false,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => false,
		'show_in_nav_menus'=>false,
		'can_export' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'rewrite' => false,//array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => false,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('editor','author'),
		'capability_type' => MY_AIA_POST_TYPE_PAYMENT,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_PAYMENT),
		'label' => __('Payments','my-aia'),
		'description' => __('Payments are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Payments','my-aia'),
			'singular_name' => __('Payment','my-aia'),
			'menu_name' => __('Payments','my-aia'),
			'add_new' => __('Add Payment','my-aia'),
			'add_new_item' => __('Add New Payment','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Payment','my-aia'),
			'new_item' => __('New Payment','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Payment','my-aia'),
			'search_items' => __('Search Payments','my-aia'),
			'not_found' => __('No Payments Found','my-aia'),
			'not_found_in_trash' => __('No Payments Found in Trash','my-aia'),
			'parent' => __('Parent Payment','my-aia'),
		),
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_PAYMENT, $payment_post_type);
}


if (class_exists("MY_AIA_META_COLUMNS")):	// META COLUMNS
/**
 * Define the class for table format (META COLUMNS)
 * Automatically called in MY_AIA_ADMIN::init();
 */
class MY_AIA_PAYMENT_META_COLUMNS extends MY_AIA_META_COLUMNS {
	public $post_type = MY_AIA_POST_TYPE_PAYMENT;
	/**
	 * @var MY_AIA_PAYMENT
	 */
	private $PAYMENT;


	public function __construct($args = NULL) {
		$this->PAYMENT = new MY_AIA_PAYMENT();
		
		$this->meta_columns = 
			array(
				'payment_status'		=>	__('Order Status'),
				'invoice_link'		=>	__('Factuur'),
				'assigned_user_id'	=>	__('Klant'),
			);
		
		parent::__construct($args);
	}
		
	/* Display the column content for the given column
	 *
	 * @param string $column_name Column to display the content for.
	 * @param int    $post_id     Post to display the column content for.
	 */
	public function column_content( $column_name, $post_id ) {
		if ( $this->is_metabox_hidden() === true ) {
			return;
		}
		
		if ($this->PAYMENT->ID != $post_id) $this->PAYMENT->get($post_id);

		switch ( $column_name ) {
			case 'payment_status' :
				echo $this->parse_status($this->PAYMENT->payment_status);
				break;
			case 'invoice_member' :
				$user = new WP_User($this->PAYMENT->assigned_user_id);
				echo sprintf('<a href="%s">%s</a>', get_edit_user_link($this->PAYMENT->assigned_user_id), $user->user_nicename);
				break;
			case 'invoice_link' :
				if (is_numeric ($this->PAYMENT->invoice_id)) 
					//echo my_aia_get_edit_post_link(__('Bewerk Factuur','my-aia'), '', '', $this->PAYMENT->invoice_id);
					echo my_aia_edit_post_link(__('Bewerk Factuur','my-aia'), '', '', $this->PAYMENT->invoice_id);
				else
					echo __('Niet Aanwezig','my-aia');
				break;
		}
	}
	
	
	private function parse_status($order_status) {
		switch ($order_status) {
			case 'paid':
			case 'paidout':
				$color = '#AAFFAA';
				break;
			case 'awaiting_payment':
				$color = 'orange';
				break;
			default:
				$color='red';
		}
		
		return sprintf('<span class="%s" style="background-color:%s">%s</span>',"", $color, __($order_status,'my-aia'));
	}
}
endif;	// META COLUMNS