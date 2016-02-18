<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/**
 * Class MY_AIA_PROCESSFLOW
 * - Parent class for all the different flows (ie. save_post, publish_post, etc)
 * - Registering hooks for processing 
 */
class MY_AIA_PROCESSFLOW {

	/**
	 * Holder for all the processes for this hook
	 * @type array
	 */
	protected static $processes = NULL;	

	protected static $data;		
	
	protected static function parse_processes($hook_name, $data=NULL) {
		if (!empty($data)) self::$data=$data;
		
		self::$processes = get_option('my-aia-hook-'.$hook_name);
		
		if (!is_array(self::$processes)) self::$processes = array(self::$processes);
		
		/**
		 * do all the actions
		 * 'conditions' =>	array()				// list of conditions to met before continuing (or selecting
		 * 'conditional_actions' =>	array()		// list of conditions which parses as actions
		 * 'actions	'	=>	array()				// 
		 */
		foreach (self::$processes as $process) {
			//check conditions
			if (!empty($process['conditions'])) {
				if (!is_array($process['conditions'])) 
					$process['conditions'] = array($process['conditions']);
				
				// parse the conditions and see if we need to continue
				$continue = self::check_conditions($process['conditions']);
				
				if (!$continue) 
					return false;
			}
			
			
			if (!empty($process['filter'])) {
				if (!is_array($process['filter'])) 
					$process['filter'] = array($process['filter']);
				
				// parse the conditions and see if we need to continue
				$continue = self::check_conditions($process['filter']);
				
				if (!$continue) 
					return false;
			}
		}
	}
	
	/**
	 * Parse the conditions for the process. Function return a bool to either 
	 * continue or not. This is a list of functions/constants, etc. 
	 * No SQL queries, possible add on to get a query parameter as constant
	 * 
	 * $conditions = recursive array with structure:
	 * '0..N|AND|OR' => array
	 *		'0..N|AND|OR' => array ..
	 *			array(
	 *				key:	=> name of the field
	 *				value:	
	 *					user_function	referring to a user function
	 *					constant		referring to a constant
	 * @param array $conditions
	 * @return bool Conditions Met
	 */
	protected static function check_conditions($conditions) {
		$conditions_met = TRUE;		// initalisation of retun var
		
		if (empty($conditions) || count($conditions)==0) 
			return $conditions_met;
		
		
		$conditions_met = $conditions_met && self::parse_conditions($conditions);
		
		return $conditions_met;
	}
	
	/**
	 * Recursively parsing the conditions
	 * - default is AND condition for sequential rows in the array, unless
	 *	 indicated otherwise
	 * @param array $conditions see comments check_conditions();
	 * @param string $parameter (default AND)
	 * @return array parsed condition to WP_Query standards
	 */
	protected static function parse_conditions($conditions, $parameter='AND') {
		$return = TRUE;
		foreach ($conditions as $key=>$condition) {
			if (is_numeric($key)) {
				// init or update condition based on parent (AND the children or OR the children with $return)
				
				/**
				 * We assume the condition to be a comparison to a constant
				 * - post variabele comparison
				 * - user_id 
				 * - post_type
				 * - current date
				 * - ..
				 * 
				 * if condition is array with key OR|AND than recursively continue
				 */
				
				// new OR|AND pair, recursively continue
				if (is_array($condition) && (array_key_exists('OR', $condition) || array_key_exists('AND', $condition))) {
					$_return = self::parse_conditions($condition);
				} else {
					/**
					 * array (
					 *	'function1'
					 *  'function2'
					 *  'field1'
					 *  'field2'
					 *  'comparison'
					 * )
					 */
					// try and find if function exists
					if (is_array($condition)) {
						// check if field1|field2 is part of the dataset, if so, set field to data 
						if (is_object(self::$data)) {
							$condition['field1'] = property_exists(self::$data, $condition['field1'])?self::$data->$condition['field1']:$condition['field1'];
							$condition['field2'] = property_exists(self::$data, $condition['field2'])?self::$data->$condition['field2']:$condition['field2'];
						} elseif (is_array(self::$data)) {
							$condition['field1'] = array_key_exists($condition['field1'], self::$data)?self::$data[$condition['field1']]:$condition['field1'];
							$condition['field2'] = array_key_exists($condition['field2'], self::$data)?self::$data[$condition['field2']]:$condition['field2'];
						}
						
						
						
						
						if (!empty($condition['function1']) && method_exists("MY_AIA_PROCESSFLOW", $condition['function1'])) {
							// call function
							if (!empty($condition['field1'])) 
								$condition['field1'] = MY_AIA_PROCESSFLOW::$condition['function1']($condition['field1']);
							else 
								$condition['field1'] = MY_AIA_PROCESSFLOW::$condition['function1']();
						} 
						if (!empty($condition['function2']) && method_exists("MY_AIA_PROCESSFLOW", $condition['function2'])) {
							// call function
							if (!empty($condition['field2'])) 
								$condition['field2'] = MY_AIA_PROCESSFLOW::$condition['function2']($condition['field2']);
							else 
								$condition['field2'] = MY_AIA_PROCESSFLOW::$condition['function2']();
						}  
						
						// do the comparison
						switch ($condition['comparison']) {
							case '!=':
								$_return = $condition['field1'] != $condition['field2'];
								break;
							case '>=':
								$_return = $condition['field1'] >= $condition['field2'];
								break;
							case '<=':
								$_return = $condition['field1'] <= $condition['field2'];
								break;
							case '>':
								$_return = $condition['field1'] > $condition['field2'];
								break;
							case '<':
								$_return = $condition['field1'] < $condition['field2'];
								break;
							default:
								$_return = $condition['field1'] == $condition['field2'];
						}
					} else {
						$_return = TRUE; // not a valid comparison, continue anyway
					}
				}
							
				if ($parameter == 'AND') {
					$return = $return && $_return;	// AND
					if (!$return)	
						return false; // if AND is not met, we do not have to continue further.. it won't turn to TRUE
				} else
					$return = $return || $_return;	// OR
			} else {
				// Key should be AND|OR
				$return = self::parse_conditions($condition, $key);
			}		
		}
		
		
		// give back the return function
		return $return;
	}
	
	/**
	 * Parse the conditions for the process. Function return a bool to either 
	 * continue or not
	 * 
	 * $conditions = recursive array with structure:
	 * '0..N|AND|OR' => array
	 *		'0..N|AND|OR' => array ..
	 *			array(
	 *				key:	=> name of the field
	 *				value:	
	 *					user_function	referring to a user function
	 *					constant		referring to a constant
	 * @param array $conditions
	 * @return bool Conditions Met
	 */
	protected static function build_filter($filterlist) {
		$conditions_met = TRUE;		// initalisation of retun var
		
		if (empty($conditions) || count($conditions)==0) 
			return $conditions_met;
		
		
		$args = self::parse_conditions($conditions, $var);
		var_dump($args);exit();
		
		return $conditions_met;
	}
	
	/**
	 * Recursively parsing the conditions
	 * - default is AND condition for sequential rows in the array, unless
	 *	 indicated otherwise
	 * @param array $conditions
	 * @param string parameter (default AND)
	 * @return array parsed condition to WP_Query standards
	 */
	protected static function parse_filter($filterlist, $parameter='AND') {
		$return = array();
		foreach ($filterlist as $key=>$filter) {
			if (is_numeric($key)) {
				// init or update AND condition
				$return['relation'] = $parameter;
				
				// add to the AND condition
				array_push($return, self::parse_conditions($condition));	// recursively add condition
			} else {
				/**
				 * A named field, either AND, OR, <field> 
				 * The last option <field> means that the condition is either a 
				 * function or what 
				 */
				switch (strtolower($key)) {
					case "and":	
						if (!array_key_exists('relation', $return))
							array_push($return, self::parse_conditions($condition, 'AND'));	// recursively add condition
						
						// error already an OR conditon exist.. cannot be met..
						// do nothing
						break;
					case "or":
						if (!array_key_exists('relation', $return))
							array_push($return, self::parse_conditions($condition, 'OR'));	// recursively add condition
						
						// error already an OR conditon exist.. cannot be met..
						// do nothing
						break;
					default:
						// a user function, constant or so.
						if (method_exists('MY_AIA_PROCESSFLOW', $condition)) {
							$const = self::$condition();
						}
						
						$return['key'] = $key;
						$return['value'] = $const;
				}
			}
		}
		
		
		// give back the return function
		return $return;
	}
	
	/**
	 * Save the list of $data
	 * @param string $hook_name name of the hook
	 * @param array $data to be saved
	 * @return boolean
	 */
	public static function save_static_condition($hook_name, $data) {
		self::$data=$data;	// set data
		$option = self::save_static_condition_get_next('#');		
		
		// save option and no autoload, update or add new
		if (get_option('my-aia-hook-'.$hook_name,FALSE)) 
			return update_option('my-aia-hook-'.$hook_name, $option, FALSE);
		
		return add_option('my-aia-hook-'.$hook_name, $option, NULL, FALSE);
	}
	
	/**
	 * Recursively step into conditions
	 * @param string $id
	 */
	private static function save_static_condition_get_next($id) {
		if (empty(self::$data[$id]['children'])) 
			return self::$data[$id];	// return the settings

		if (!is_array(self::$data[$id]['children']))
			self::$data[$id]['children']=array(self::$data[$id]['children']);

		// loop through children
		$text=(isset(self::$data[$id]['text']) && self::$data[$id]['text']=='OR')?'OR':'AND';
		$return=array($text=>array());
		foreach (self::$data[$id]['children'] as $child) {
			if (self::$data[$child]['type']=='#' || self::$data[$child]['type']=='folder') {
				array_push($return[$text], self::save_static_condition_get_next($child));
			} elseif (self::$data[$child]['type']=='condition') {
				array_push($return[$text], self::$data[$child]);
			}
		}
		// return the part of the array
		return $return;
	}
}