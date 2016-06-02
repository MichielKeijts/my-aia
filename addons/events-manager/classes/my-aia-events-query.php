<?php

/** 
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 * 
 * 
 * This is the class definition for the Events Manager query. This file is loaded
 * and returns the query to the events
 * 
 */

class MY_AIA_EVENTS_QUERY {
	/**
	 * Respond as JSON|XML
	 * @var string
	 */
	static $respond_as = 'JSON';	
	
	
	/**
	 * Main function to be called by the AJAX listener. 
	 * gets function name and calls that function and returns the data
	 * @return void
	 */
	static function call_function() {
		$fn = filter_input(INPUT_POST, 'function');
		if (!$fn) return self::send_error(1, 'variable function not defined');
		
		switch ($fn) {
			case 'get_events':
				return self::get_events();
				break;
			case 'list_categories':
				return self::list_categories();
				break; 
			case 'get_locations':
			default:
				return self::send_error(2, 'undefined function name');
		}
	}
	
	/**
	 * Main function which uses the AJAX data parameters to perform a query on 
	 * the events. 
	 * 
	 * 
	 */
	static function get_events() {
		$parameters = filter_input(INPUT_POST, 'data')?$_POST['data']:array();
		$args = self::parse_arguments($parameters);
		$events = EM_Events::get($args);
		
		// if events but no data
		if (!$events) 
			self::send_error(400, 'Error query events');
		
		// convert to array and sendable result
		$events_array = array();
		foreach ($events as $event) {
			$location = new EM_Location($event->location_id);
			$ticket = new EM_Ticket();
			$categories = new EM_Categories($event);
	
			$event_array = $event->to_array() + $location->to_array();
			
			$event_array['post_content']	= "";
			$event_array['permalink']		= $event->get_permalink();
			$event_array['ticket_price']	= 999.99;
			
			$event_array['event_categories'] = array();
			foreach ($categories->categories as $category):
				$event_array['event_categories'][] = array(
					'term_id'	=> $category->term_id,
					'slug'		=> $category->slug,
					'name'		=> $category->name,
				);
			endforeach;
			array_push($events_array, $event_array);
		}
		
		// send success
		return self::send_success($events_array);
	}

	/**
	 * List the event-categories present in the system.
	 * 
	 */
	static function list_categories() {
		$terms = get_terms('event-categories', array('hide_empty' => FALSE));

		// if events but no data
		if (!$terms) 
			self::send_error(400, 'Error query events');
		
		// convert to array and sendable result
		$events_array = array();
		foreach ($terms as $term) {
			array_push($events_array, $term);
		}
		
		// send success
		return self::send_success($events_array);
	}	
	
	
	/**
	 * Parse the search arguments for the Events Query.
	 * @param array $input_data (e.g. $_POST data)
	 * @return array
	 */
	static private function parse_arguments($input_data) {
		$default = array(
			'search'			=>	"",						//	query to all below
			'limit'				=>	10,
			'offset'			=>	0,
			'scope'				=>	'future',				// future|past|all|today|n-months|
			'category'			=>	NULL,					// numeric or by name	"Sportweek,Sportkamp"
			'location_name'		=>	NULL,
			'location_address'	=>	NULL,	
			'location_town'		=>	NULL,
			'location_postcode'	=>	NULL,
			'location_state'	=>	NULL,
			'location_country'	=>	NULL,
			'location_region'	=>	NULL,
			'ajax'				=>	0,
			'near'				=>	false,					// lat, lng Comma Separated e.g. "-33.9139106,18.3751939"
			'near_distance'		=>	40000,					// number
			'near_unit'			=>  'km',					// km or mi
		);
		
		// get new args
		$args = array_merge($default, $input_data);
		
		// convert category to array and convert category names to ID's
		if (strpos($args['category'], ',') !== FALSE) {
			$categories = explode(',', $args['category']);
			$args['category'] = array();
			// check if not numeric, get term ID from name
			foreach ($categories as $key=>$val) {
				if (!is_numeric($val)) {
					$term = get_term_by('name', $val, 'event-categories');
					if (isset($term->ID)) $args['category'][] = $term->term_id;// add ID
				}
			}
		}
		
		return $args;
	}
	
	
	/** 
	 * Send JSON error using wp_send_json
	 * @param array $data
	 */
	static function send_success($data) {
		wp_send_json(array(
			'success'	=>	TRUE,
			'code'		=> 200, 
			'data'		=> $data, 
			'length'	=> count($data)
		));
	}
	
	/** 
	 * Send JSON error using wp_send_json_error
	 * @param int $code
	 * @param string $message
	 */
	static function send_error($code=0, $message='general error') {
		wp_send_json_error(array('code' => $code, 'message'=> $message));
	}
}