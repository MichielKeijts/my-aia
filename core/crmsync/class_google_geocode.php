<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_google_geocode
 *
 * @author michiel
 */
class class_google_geocode {
	/**
	 * Google API string
	 * @var string 
	 */
	private $key;
	
	/**
	 * Google Maps API url
	 * @var string 
	 */
	private $url;	
				
	/**
	 * 
	 * @param string $key
	 * @param string $url='https://maps.googleapis.com/maps/api/geocode/json' 
	 */
	public function __construct($key='AIzaSyDtMwN3eT9t_-Hut-qKqtAOewl_Px0iaa8', $url='https://maps.googleapis.com/maps/api/geocode/json') {
		$this->key = $key;
		$this->url = $url;
	}
	
	/**
	 * 
	 * Gets result in form:
	 * {
   "results" : [
      {
         "address_components" : [
            {
               "long_name" : "1600",
               "short_name" : "1600",
               "types" : [ "street_number" ]
            },
            {
               "long_name" : "Amphitheatre Parkway",
               "short_name" : "Amphitheatre Pkwy",
               "types" : [ "route" ]
            },
            {
               "long_name" : "Mountain View",
               "short_name" : "Mountain View",
               "types" : [ "locality", "political" ]
            },
            {
               "long_name" : "Santa Clara County",
               "short_name" : "Santa Clara County",
               "types" : [ "administrative_area_level_2", "political" ]
            },
            {
               "long_name" : "California",
               "short_name" : "CA",
               "types" : [ "administrative_area_level_1", "political" ]
            },
            {
               "long_name" : "United States",
               "short_name" : "US",
               "types" : [ "country", "political" ]
            },
            {
               "long_name" : "94043",
               "short_name" : "94043",
               "types" : [ "postal_code" ]
            }
         ],
         "formatted_address" : "1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA",
         "geometry" : {
            "location" : {
               "lat" : 37.4224497,
               "lng" : -122.0840329
            },
            "location_type" : "ROOFTOP",
            "viewport" : {
               "northeast" : {
                  "lat" : 37.4237986802915,
                  "lng" : -122.0826839197085
               },
               "southwest" : {
                  "lat" : 37.4211007197085,
                  "lng" : -122.0853818802915
               }
            }
         },
         "place_id" : "ChIJ2eUgeAK6j4ARbn5u_wAGqWA",
         "types" : [ "street_address" ]
      }
   ],
   "status" : "OK"
}
	 * @param mixed $search (string|array(key=>data))
	 * @return mixed $results | FALSE
	 */
	public function get_result($search) {
		if (is_array($search)) {
			$search = $search + array('key'=>$this->key);
			
			// more data: use POST automatically
			$results = $this->get_response($search, 'post');
		} else {
			// use get
			$results = $this->get_response($search, 'get');
		}
		
		if ($results)
			return $this->get_data_formatted($results);
		
		return FALSE;
	}
	
	private function get_response($data, $method = 'get') {
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER	=>	1,
			CURLOPT_POST			=>	(strtolower($method)=='post')
		));
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		
		if (strtolower($method) == 'post') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		} else {
			curl_setopt($ch, CURLOPT_URL, $this->url . '?address=' . urlencode($data) . '&key=' . $this->key); // GET by querystring
		}
		$info = curl_getinfo($ch);
		$response =  json_decode(curl_exec($ch));
		$error = curl_error($ch);
		
		curl_close($ch);
		
		if (isset($response->status) && $response->status == 'OK') {
			return $response->results;
		} 
		
		return FALSE;
	}
	
	
	/**
	 * From:	Google data , 
	 * To:		Array Key=>Value
	 */
	private function get_data_formatted ($data) {
		$data = reset($data); // data is array of all possibilities, only use first..
		$result = array();
		// address components
		foreach ($data->address_components as $obj) {
			$key = reset($obj->types);
			if ($key == 'country') $result[ $key ] = $obj->short_name; // use abbreviation
			else $result[ $key ] = $obj->long_name;
		}
		
		// geometry
		$result[ 'latitude' ] = $data->geometry->location->lat;
		$result[ 'longitude' ] = $data->geometry->location->lng;
		$result[ 'formatted_address' ] = $data->formatted_address;
		
		return $result;
	}
		
}
