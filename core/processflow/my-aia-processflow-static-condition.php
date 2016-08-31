<?php
/**
 * Description of MY_AIA_PROCESSFLOW_STATIC_FUNCTION
 * @author Michiel
 * 
 * Class containing various functions which can be called in the processflow
 * conditions to determine if a hook should be called or executed
 */
class MY_AIA_PROCESSFLOW_STATIC_CONDITION {
	/**
	 * Return a UNIX_TIMESTAMP format date. Use seconds to compare dates.
	 * $input can be automagic:
	 * YYYYmmdd, dd/-mm/-YYYY or YYYY/-mm/-dd: 
	 *		format to YYYY-mm-dd
	 * dd/-mm: format to current year and month+day as described.
	 * 
	 * 
	 * @param string $input
	 * @return int
	 */
	static function date($input='now') {
		if (is_string($input) || strlen($input)==8) {	
			// get the parts of the string
			if (strlen($input)==8) {
				$dates=array(substr($input,0,4),substr($input,4,2),substr($input,6,2));
			} else {
				$dates =  preg_split("/[\/]|[-]|[ ]+/", $input);
			}
			
			// set in right (US) format
			if (count($dates)>2) {
				if (strlen($dates[0]) < strlen($dates[2])) {	
					// assume longer length is year value
					$input=sprintf('%d-%d-%d',$dates[2],$dates[1],$dates[0]);
				}
			} elseif (count($dates)>1) {
				// asume dateformat: dd-mm
				// format to current year, month+day
				$input=sprintf('%d-%d-%d',date('Y'),$dates[1],$dates[0]);
			}
			
			
			$unixtime = strtotime($input);
			if (!is_numeric($unixtime)) 
				return 0;
		} else {
			return $input;
		}
		
		// return converted
		return $unixtime;
	}
	
	/**
	 * Wrapper to return same as input
	 * @param mixed $input
	 * @return mixed $input
	 */
	static function constant($input) {
		return $input;
	}
}
