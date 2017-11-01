<?php

/*
 * Copyright (C) 2016 Michiel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


/**
 * Description of my-aia-page-controller
 *
 * @author Michiel
 */
class MY_AIA_PAYMENT_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'payment';
	
	/**
	 * Payment Model
	 * @var MY_AIA_PAYMENT; 
	 */
	public $PAYMENT;
	
	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_get_products', array($this, 'get_products'), 1);	
		add_action( 'wp_ajax_my_aia_admin_create_invoice', array($this, 'create_invoice'), 1);	
		add_action( 'wp_ajax_my_aia_admin_processflow_order_save', array($this, 'processflow_order_save'),1);	
	}
	
	/**
	 * Before Render function
	 * called to include all the styles etc from Wordpress
	 */
	public function before_render() {		
		parent::before_render();
		
		// extra script
		wp_enqueue_script( 'my-aia-admin-conditions', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-conditions.js', '', MY_AIA_VERSION );
		
		// setting the menu bar for this controller
		$menu_bar = array(
			'add' => __('Nieuw','my-aia'),
			'index' => __('Hooks Overzicht'),
		);
		
		$this->set('menu_bar', $menu_bar);
	}
	
	/**
	 * Process Payment by payment_id in GET Var
	 * - Acces control by
	 * - 
	 * @return int $order_id
	 */
	public function payment_processing() {
		$this->PAYMENT->get(filter_input(INPUT_GET, 'payment_id', FILTER_SANITIZE_NUMBER_INT));
		
		// no acces
		if (!(bp_current_user_id() == $this->PAYMENT->assigned_user_id)) {
			wp_die('No Access');
		}		
		
		// try and find order
		
		// get mollie payments, if payment total higher than 0
		if ($this->PAYMENT->total_amount > 0 ) {
			include_once MY_AIA_PLUGIN_DIR . 'vendor/Mollie/API/Autoloader.php';
			$mollie = new Mollie_API_Client;

			$mollie->setApiKey(MY_AIA::$settings['mollie_key']);

			$pmt    = $mollie->payments->get($this->PAYMENT->payment_id);

			if ($pmt) {
				$this->PAYMENT->post_content = $pmt->status;
				$invoice = new MY_AIA_INVOICE($this->PAYMENT->invoice_id);
				if ($invoice) $order = new MY_AIA_ORDER($invoice->order_id);
				if (isset($order) && $order) {
					if ($pmt->isPaid()) {
						wp_publish_post($invoice->order_id);
						$order->order_status = MY_AIA_ORDER_STATUS_PAID;
					} else {
						$order->order_status = MY_AIA_ORDER_STATUS_AWAITING_PAYMENT;
					}
				}
				$order->update_post_meta(FALSE);	// not update via $_POST
				$this->PAYMENT->save(FALSE);		// not update via $_POST
			}
		} else {
			$invoice = new MY_AIA_INVOICE($this->PAYMENT->invoice_id);
			if ($invoice) $order = new MY_AIA_ORDER($invoice->order_id);
			$order->order_status = MY_AIA_ORDER_STATUS_PAID;
			$order->update_post_meta(FALSE);	// not update via $_POST
		}
		
		// send confirmation email
		if (empty($order->payment_confirmation_send_to) || !$order->payment_confirmation_send_to) {
			$confirmation_send_to = $this->send_payment_confirmation_email($order);
			if ($confirmation_send_to !== FALSE) {
				$order->payment_confirmation_send_to = $confirmation_send_to;
				$order->save();
			}
		}
		
		return $order->ID;
		//
		return $this->PAYMENT->get_invoice();
	}
	
	/**
	 * Send the payment confirmation email
	 * @param MY_AIA_ORDER $order
	 */
	public function send_payment_confirmation_email($order) {
		// get a template_id
		$template_id = $order->get_email_payment_confirmation_template_id();
		
		$template = new MY_AIA_TEMPLATE_CONTROLLER();
		$template->TEMPLATE->get($template_id);
		
		$user_info = get_userdata($order->assigned_user_id);
		$to = $user_info->user_email;
		$subject = $template->TEMPLATE->subject;
		$message = $template->parse($template_id, $this->PAYMENT->ID);
		
		$headers = array();
		$headers[] = 'Bcc: ' . MY_AIA::$settings['email_payment_confirmation'];
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
				
		// send mail!
		if (wp_mail($to, $subject, $message, $headers, $attachments)) 
			return $to;
		
		return false;
	}
	
		
	public function confirm_order($id=NULL) {
		if (!$this->ID) $this->ORDER->get($id); 
	
		if (empty($this->ORDER->payment_id)) return FALSE;
		if ($this->ORDER->post_status == MY_AIA_ORDER_PLACED) return TRUE;
		
	}
}
