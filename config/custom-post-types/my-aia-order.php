<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * @copyright (c) 2016, Normit, Michiel Keijts
 */

// Registration of Custom Post Type
function my_aia_register_post_type_order(){
	$order_post_type = array(	
		'public' => false,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => 'my-aia-admin',
		'show_in_nav_menus'=>true,
		'can_export' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'rewrite' => false,//array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => false,//get_option('my-aia_cp_events_has_archive', false) == true,
		'supports' => array('title','editor','author'),
		'capability_type' => MY_AIA_POST_TYPE_ORDER,
		'capabilities' => MY_AIA::get_capabilities(MY_AIA_POST_TYPE_ORDER),
		'label' => __('Orders','my-aia'),
		'description' => __('Orders are agreements with partners for AIA.','my-aia'),
		'labels' => array (
			'name' => __('Orders','my-aia'),
			'singular_name' => __('Order','my-aia'),
			'menu_name' => __('Orders','my-aia'),
			'add_new' => __('Add Order','my-aia'),
			'add_new_item' => __('Add New Order','my-aia'),
			'edit' => __('Edit','my-aia'),
			'edit_item' => __('Edit Order','my-aia'),
			'new_item' => __('New Order','my-aia'),
			'view' => __('View','my-aia'),
			'view_item' => __('View Order','my-aia'),
			'search_items' => __('Search Orders','my-aia'),
			'not_found' => __('No Orders Found','my-aia'),
			'not_found_in_trash' => __('No Orders Found in Trash','my-aia'),
			'parent' => __('Parent Order','my-aia'),
		),
		'query_var'=>'',
		//'menu_icon' => 'my-aia-icon',
		'yarpp_support'=>true
	);
	
    

    // Register Post Type
    register_post_type(MY_AIA_POST_TYPE_ORDER, $order_post_type);
}



if (class_exists("MY_AIA_META_COLUMNS")):	// META COLUMNS
/**
 * Define the class for table format (META COLUMNS)
 * Automatically called in MY_AIA_ADMIN::init();
 */
class MY_AIA_ORDER_META_COLUMNS extends MY_AIA_META_COLUMNS {
	/**
	 * Post Type name
	 * @var string 
	 */
	protected $post_type = MY_AIA_POST_TYPE_ORDER;
	
	/**
	 * @var MY_AIA_ORDER
	 */
	private $order;


	public function __construct($args = NULL) {
		$this->ORDER = new MY_AIA_ORDER();
		$this->meta_columns = 
			array(
				'order_status'		=>	__('Order Status'),
				'order_invoice'		=>	__('Factuur'),
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
		
		if ($this->ORDER->ID != $post_id) $this->ORDER->get($post_id);

		switch ( $column_name ) {
			case 'order_status' :
				echo $this->parse_order_status($this->ORDER->order_status);
				break;
			case 'order_member' :
				$user = new WP_User($this->ORDER->assigned_user_id);
				echo sprintf('<a href="%s">%s</a>', get_edit_user_link($this->ORDER->assigned_user_id), $user->user_nicename);
				break;
			case 'order_invoice' :
				if ($this->ORDER->invoice && is_numeric ($this->ORDER->invoice->ID)) 
					echo edit_post_link($this->ORDER->invoice->ID);
				else
					echo __('Niet Aanwezig','my-aia');
				break;
		}
	}
	
	
	private function parse_order_status($order_status) {
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