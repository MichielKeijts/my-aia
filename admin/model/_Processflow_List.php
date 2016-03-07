<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Processflow_List
 *
 * @author Michiel
 */
class Processflow_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Proces Flow', 'my-aia' ), //singular name of the listed records
			'plural'   => __( 'Proces Flows', 'my-aia' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );
	}
	
	/**
	 * Get the Processflows from the option table
	 * @global type $wpdb
	 * @param type $per_page
	 * @param type $page_number
	 * @return type
	 */
	public static function get_processflows( $per_page = 10, $page_number = 1 ) {
		global $wpdb;

		$processflows = get_option('my-aia-registered-hooks',array());
		
		if ( ! empty( $_REQUEST['orderby'] ) ) {
		  $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		  $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}
}
