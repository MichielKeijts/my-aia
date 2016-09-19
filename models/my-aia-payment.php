<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_PAYMENT post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_PAYMENT extends MY_AIA_MODEL {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_PAYMENT;

	/**
	 * Name of the Object
	 * @var string
	 */
	public $assigned_user_id;
	
	/**
	 * Name of the Object
	 * @var string
	 */
	public $invoice_id;
	
	/**
	 * Name of the Object
	 * @var \MY_AIA_INVOICE
	 */
	public $invoice;
			
	/**
	 * Amount of the payment
	 * @var float
	 */
	public $total_amount = 0.0;

	/**
	 * Mollie Payment Type
	 * @var string
	 */
	public $mollie_type;
	
	/**
	 * Molli Payment ID
	 * @var string 
	 */
	public $payment_id;
	
	/**
	 * OPEN|PAID|..
	 * @var string 
	 */
	public $payment_status = 'open';
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		//'name'				=> array('name'=>'name','type'=>'%s'),
		//'description'		=> array('name'=>'description','type'=>'%s'),
		'payment_status'	=> array('name'=>'description','type'=>'%s'),	// PAID or PAIDOUT(overgemaakt) https://www.mollie.com/nl/docs/status
		'payment_id'		=> array('name'=>'name', 'type'=>'%s'),			// POST NAME
		'total_amount'		=> array('name'=>'total_amount','type'=>'%f'),
		'mollie_type'		=> array('name'=>'mollie_type','type'=>'%d'),	// iDEAL
		'invoice_id'		=> array('name'=>'invoice_id','type'=>'%s'),
	);
	
	public function __construct($post = NULL) {
		parent::__construct($post);
	}

	public function get_payment_provider () {
		include MY_AIA_PLUGIN_DIR . 'vendor/Mollie/API/Autoloader.php';
	}
	
	/**
	 * Return a Mollie Link for iDeal
	 * @param type $amount
	 */
	public function get_mollie_link($amount = -1) {
		include_once MY_AIA_PLUGIN_DIR . 'vendor/Mollie/API/Autoloader.php';
		
		if ($amount<0) $amount = $this->total_amount;
		
		$user = new WP_User(get_current_user_id());
		
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey(MY_AIA::$settings['mollie_key']);
		
		$payment = $mollie->payments->create(array(
			"amount"		=> $amount,
			"description"	=> "Betalen voor je bestelling bij Athletes in Action met ID ".$this->invoice_id,
			"redirectUrl"	=> "http://www.athletesinaction.local/mijn-aia/members/".$user->user_nicename."/orders/status/?payment_id=".$this->ID,
			"metadata"		=> array(
				'invoice_id'	=> $this->invoice_id
			)
		));
		
		// if payment exists
		if ($payment->id) {
			$this->payment_id = $payment->id;
			$this->name = $payment->id;
			$this->post_content = $payment->status;
			$this->payment_url = $payment->links->paymentUrl;	// not saved
			
			// save ID and so on
			$this->save();
			return $this->payment_url;
		}
		
		//FAIL.
		return FALSE;		
	}
	
}