<?php
/**
 * Keys are in Manyware, Values in Sugar
 *
 * @author		Michiel Keitjs
 * @copright	(C) 2016
 */

/**
 * Convert variables from sugar to a uniform set.
 * 
 * e.g. 
 * -	Numbers are in 0.0f format
 * -	integers are rounded and cast as int
 * -	boolean is boolean
 * -	..
 * 
 * also the place to make some specific values constant, in either conversion to or from Sugar.
 * 
 * call: ConversionHelper::fromSugar($key, $dataset)
 * 
 */
class ConversionHelper {
	static $sugar_vars = Array();
	
	static $wordpress_vars = Array();
	  

	/**
	 * Converts a Sugar Variable to the correct uniform one.
	 * $dataset is the whole dataset, to be used, so the function can make use
	 * of the whole dataset
	 * @param string $key name of the variable
	 * @param array $dataset with $key=>$val
	 */
	static function from_sugar($key, $dataset) {
		// we will call a function (method) if exists
		if (method_exists(get_class(), $key)) {
			return self::$key($dataset);
		}
		
		// return the variable
		return $dataset[$key];
	}
	
	
	/**
	 * Converts a Wordpress Variable to the correct uniform one.
	 * $dataset is the whole dataset, to be used, so the function can make use
	 * of the whole dataset
	 * @param string $key name of the variable
	 * @param array $dataset with $key=>$val
	 */
	static function from_wordpress($key, $value, $dataset) {
		// check for right key
		if (strpos($key,'::') !== FALSE) {
			$_keys = explode('::', $key);
			$_key = $_keys [ count ($_keys) - 1];
		}
		// we will call a function (method) if exists
		if (method_exists(get_class(), $_key)) {
			return self::$_key($dataset, $key);
		}
		
		// return the variable
		return $value;
	}
	
	/**
	 * Return a name value for AIA_ministry_projecten
	 * @param array $data
	 * @return string
	 */
	static function sugar_name($data, $key) {
		if (
			(!isset($data[$key]) || strlen($data[$key])<2 )
			&& isset($data['EM::start_date::start_date']))
			return date('Y', strtotime ($data['EM::start_date::start_date'])) . ' ' .$data['EM::name::name'];
			
		return $data[$key];
	}
	
	
	/**
	 * Location Country
	 * @param array $data
	 * @param string $key
	 * @return array
	 */
	static function location_country($data, $key) {
		return ISO3166::from_2to3_characters($data[$key], 'NLD');	// default Nederland
	}
	
	/**
	 * Format number to 1.000,00 from 1,000.00 
	 * @param mixed $data
	 * @return float
	 */
	static function ticket_price($data, $key) {
		if (is_numeric($data[$key]))
			return number_format($data[$key], 2, ',', '.');
		
		return $data[$key];
	}

	/**
	 * @param mixed $data
	 * @return float
	 */
	static function projectprijs($data) {
		$prijs = str_replace('.', '', $data['projectprijs']); // remove thousand sep.
		$prijs = str_replace(',', '.', $prijs);
		
		return (float) $prijs;
	}
	
	/**
	 * @param mixed $data
	 * @return float
	 */
	static function price($data) {
		return number_format($data['price'], 2, ',', '.');
	}
	
	/**
	 * @param mixed $data
	 * @return float
	 */
	static function termijn_1_prijs($data, $key=NULL) {
		if (func_num_args() > 1) return self::ticket_price($data, $key);
		return self::projectprijs($data);
	}
	/**
	 * @param mixed $data
	 * @return float
	 */
	static function termijn_2_prijs($data, $key=NULL) {
		if (func_num_args() > 1) return self::ticket_price($data, $key);
		return self::projectprijs($data);
	}
	/**
	 * @param mixed $data
	 * @return float
	 */
	static function termijn_3_prijs($data, $key=NULL) {
		if (func_num_args() > 1) return self::ticket_price($data, $key);
		return self::projectprijs($data);
	}
	
	/**
	 * @param mixed $data
	 * @return float
	 */
	static function projectcode($data) {
		// format: NL16-24
		if (empty($data['projectcode'])) {
			// try and built..
		}
		return $data['projectcode'];
	}

	/**
	 * 
	 * @param type $record
	 * @return int
	 */
	static function am($record) {
		return 1;
	}
	
	/**
	 * 
	 * @param type $record
	 * @return int
	 */
	static function aia_relatie($record) {
		return 1;
	}
	
	/**
	 * 
	 * @param type $record
	 * @return int
	 */
	static function homerun($record) {
		return 1;
	}
	  
	static function gratis($record) {
		// NB: two way function!
		
		if (isset($record['poms'])) return ($record['gratis']=='true')?1:0;
		
		return ($record['gratis']==1)?'true':'false';
	}
	
	
	/*static_function location_country() {
	
	ISO3166::from_3to2_characters($dataset['EM']['location_country']);*/
}
?>