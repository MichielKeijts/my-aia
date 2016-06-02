<?php
// www.normit.nl	(C) 2010
// auteur:			Michiel Keyts
//
// Projectcode:		P0017
// Omschrijving:	Data afhandeling
// Functie:
//
//
// *******************************************
// |	filename:	#assign.PHP		|
// |	versie:		0.1		|
// *******************************************

include_once("definitions_sugar_manyware.php");
include_once("iso3166.php");

class SoapSugar {
	private $debug="";
	private $IdKey="";
	private $user=USER;
	private $pass=PASS;


	public  $SOAP=null;
	public  $mysql;

	/**
	 * Warning: hardcoded adresses! no localhost, this fails!
	 * @param string $url 
	 */
	function __construct($url, $options=array()) {
		$this->URL=$url;
		$this->converter=new DefinitionsSugarManyware();
		
		foreach ($options as $var=>$value) {
			$this->$var = $value;
		}
		
		
		$this->SOAP=new SoapClient($this->URL,array(
			//'soap_version'   => SOAP_1_2,
			//'trace' => 1,
			'exceptions' => false,
			'cache_wsdl' => WSDL_CACHE_NONE,
			//'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
		));
		

		//$this->connect();	// initiate connection
		$this->login();
		//$this->mysqli=new mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->db);
	}


	/** 
	 * Initiate connection using predefined settings
	 * @return bool
	 */


	/**
	 *
	 * @param string $action
	 * @param array $options
	 * @return bool
	 */
	function login() {
		// do Call
		$response=$this->SOAP->__soapCall('login',array(
			'user_auth'=>Array(
				'user_name'=>$this->user, 
				'password'=>$this->pass, 
				'version'=>'2.0'),
			'application_name'=>'Athletes in Action'
			));

		$this->IdKey=$response->id;

		// update cIdKey
		if ($response->error->number>0) {
			$this->debug("error: ".$responseObj->error->description);
		} else {
			// return data..
		}
	}
	//entry_list

	/**
	 * function createContact (firstname, lastname, email)
	 * @param string $f
	 * @param string $l
	 * @param string $e 
	 */
	function createContact ($f,$l,$e) {
		$f=$this->correctUTF8($f);
		$e=$this->correctUTF8($e);
		$l=$this->correctUTF8($l);
		$response=$this->SOAP->__soapCall('create_contact', Array(
			'user_name'=>$this->user, 
			'password'=>$this->pass, 
			'first_name'=>$f,
			'last_name'=>$l,
			'email_address'=>$e
		));

		$this->debug('create Contact response: '.$response);

		if (strlen($response)>1) {
			return $response;
		}
		// FAIL
		return false;
	}
		
	/**
	 * function createPartner (name, phone, website)
	 * @param type $f
	 * @param type $p
	 * @param type $w
	 * @return boolean
	 */
	function createPartner ($n,$p,$w) {
		$f=$this->correctUTF8($n);
		$e=$this->correctUTF8($p);
		$l=$this->correctUTF8($w);
		$response=$this->SOAP->__soapCall('create_account', Array(
			'user_name'=>$this->user, 
			'password'=>$this->pass, 
			'name'=>$n,
			'phone'=>$p,
			'website'=>$w
		));

		$this->debug('create Contact response: '.$response);

		if (strlen($response)>1) {
			return $response;
		}
		// FAIL
		return false;
	}
        
	/**
	 * function deleteContact ($UUID)
	 * Alleen aanroepen als $this->createContact goed ging en updaten van relatienummer faalt
	 * @param string $UUID
	 */
	/*function deleteContact ($UUID) {
		$this->mysqli->free_result();
		$this->mysqli->query("DELETE FROM ".DB.".contacts WHERE id='{$UUID}' LIMIT 1");

		return true;
	}*/



	/**
	 * 
	 */
	function updateContact($data) {
		// UTF - 8 Failsafe
		$data=$this->correctArray($data); 

		$options=Array('session'=>$this->IdKey, 'module_name'=>'Contacts', 'name_value_list'=>$data);
		$response=$this->SOAP->__soapCall('set_entry', $options);
		return $this->debug($response);
		//return true;
	}
        
	/**
	 * Update a module with name $module in SugarCRM.
	 * using set_entry
	 * @param array $data array('name'=> <name>, 'value'=> <value>)
	 * @param string $module default Email
	 * @return string UUID
	 */
	function updateModule($data, $module='Email') {
		// UTF - 8 Failsafe
		$data=$this->correctArray($data); 

		$options=Array('session'=>$this->IdKey, 'module_name'=>$module, 'name_value_list'=>$data);
		$response=$this->SOAP->__soapCall('set_entry', $options);
		return $response->id;
		//return true;
	}

	/**
	 * 
	 */
	function updateDeelname($data) {
		// UTF - 8 Failsafe
		$data=$this->correctArray($data); 

		$options=Array('session'=>$this->IdKey, 'module_name'=>'AIA_ministry_deelnames', 'name_value_list'=>$data);
		$response=$this->SOAP->__soapCall('set_entry', $options);
		$this->debug($response);
		return $response;
	 }

	/**
	 * 
	 */
	function createDeelname() {
		$table='aia_ministry_deelnames';
		$this->mysqli->free_result();
		$this->mysqli->multi_query("INSERT INTO {$table}
					(id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, assigned_user_id)
			VALUES (UUID(),' ',NOW(),NOW(), 1, 1, ' ', 0, 1);
			SELECT id FROM {$table} ORDER BY date_entered DESC LIMIT 1;");

		$result=$this->mysqli->use_result();

		if ($result) {
			while ($data=$result->fetch_assoc()) {
			   $this->debug($data['id']);
				return $data['id'];
			}
		} else 
			return false;
	}
	
	/*function createPartnership() {
		$table='aia_partnership';

		$this->mysqli->free_result();
		$this->mysqli->multi_query("INSERT INTO {$table}
					(id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, assigned_user_id)
			VALUES (UUID(),'Partnership', NOW(),NOW(), 1, 1, ' ', 0, 1);
			SELECT id FROM {$table} ORDER BY date_entered DESC LIMIT 1;");

		$result=$this->mysqli->use_result();

		if ($result) {
			while ($data=$result->fetch_assoc()) {
			   $this->debug($data['id']);
				return $data['id'];
			}
		} else 
			return false;
	}*/

	/**
	 * Set relationship
	 * @param String $a module1
	 * @param String $b module1_id
	 * @param String $c module2
	 * @param String $d module2_id
	 * @return String
	 */
	function setRelationship ($a,$b,$c,$d) {
		$options=Array('session'=>$this->IdKey, array('module1'=>$a,'module1_id'=>$b, 'module2'=>$c, 'module2_id'=>$d));
		$response=$this->SOAP->__soapCall('set_relationship', $options);
		$this->debug($response);
		//var_dump($response);
		//var_dump($a,$b,$c,$d);
		return $response;
	}

	function findContactByRelatienummer($regnr) {
		$users=$this->searchContact("(contacts_cstm.manyware_relatienummer_c='{$regnr}')");
		// @TODO warning: meerdere relatienummers: fout melden!!

		// als geen array, foutje, geen relatinummer gevonden
		if (!is_array($users) || count($users)<1) return false;
		return $users[0];
	}

	/**
	 * $srchQuery iets als (contacts.first_name="Michiel")
	 * of (contacts.manyware_relatienummer=251097)
	 * @param string $srchQuery
	 * @return type 
	 */
	function searchContact($srchQuery) {
		$response=$this->SOAP->__soapCall('get_entry_list', 
				Array(
					'session'=>$this->IdKey,
					'module_name'=>'Contacts',
					'query'=>$srchQuery,
					'order_by'=>'',
					'offset'=>0,
					'select_fields'=>'*',//$this->converter->manyware_xml_col,
					'max_results'=>15,
					'deleted'=>0)
				);
		return $this->convert_name_value_list($response->entry_list);
	}
		
	/**
	 * $srchQuery iets als (contacts.first_name="Michiel")
	 * of (contacts.manyware_relatienummer=251097)
	 * @param string $srchQuery
	 * @return type 
	 */
	function searchContactByEmail($srchQuery) {
		$response=$this->SOAP->__soapCall('get_entry_list', 
				Array(
					'session'=>$this->IdKey,
					'module_name'=>'Contacts',
					'query'=>'Contacts.id IN (
						SELECT 
						  eabr.bean_id
						FROM 
						  email_addr_bean_rel eabr 
						JOIN 
						  email_addresses ea 
						ON 
						  eabr.email_address_id = ea.id 
						WHERE 
						  eabr.bean_module = \'Contacts\'
						AND 
						  ea.email_address = \''.$srchQuery.'\'
					  )',
					'order_by'=>'',
					'offset'=>0,
					'select_fields'=>'*',
					'max_results'=>15,
					'deleted'=>0)
				);
		return $this->convert_name_value_list($response->entry_list);
	}

	/**
	 * $srchQuery iets als (contacts.first_name="Michiel")
	 * of (contacts.manyware_relatienummer_c=251097)
	 * @param string $srchQuery
	 * @return type 
	 */
	function searchDeelnames($srchQuery) {
		$response=$this->SOAP->__soapCall('get_entry_list', 
				Array(
					'session'=>$this->IdKey,
					'module_name'=>'AIA_ministry_deelnames',
					'query'=>$srchQuery,
					'order_by'=>'',
					'offset'=>0,
					'select_fields'=>array("aia_ministry_projecten.projectcode"),
					'max_results'=>15,
					'deleted'=>0)
				);
		return $this->convert_name_value_list($response->entry_list);
	}

	/**
	 * $srchQuery iets als (contacts.first_name="Michiel")
	 * of (contacts.manyware_relatienummer_c=251097)
	 * $module iets als Contacts
	 * @param string $srchQuery
	 * @return type 
	 */
	function searchCommon($srchQuery,$module="Contacts", $max_results=30, $offset=0, $order_by='' ) {
		$response=$this->SOAP->__soapCall('get_entry_list', 
				Array(
					'session'=>$this->IdKey,
					'module_name'=>$module,
					'query'=>$srchQuery,
					'order_by'=>$order_by,
					'offset'=>$offset,
					'select_fields'=>'aia_ministry_deelnames.*, aia_ministry_deelnames_cstm.*',
					'max_results'=>$max_results,
					'deleted'=>0)
				);
		return $this->convert_name_value_list($response->entry_list);
	}

	/**
	 * Function to convert the entrylist name_values to an assoc. array 
	 * @param type $nvl
	 * @return array 
	 */        
	function convert_name_value_list($nvl) {
		$list=array();

		if (!is_array($nvl)) $nvl=Array($nvl);  // nasty!
		foreach ($nvl as $obj) {
			$t_list=Array('id'=>$obj->id);
			foreach ($obj->name_value_list as $fields) {
				$t_list[$fields->name]=$fields->value;  // set assoc values
			}
			// add to list
			$list[]=$t_list;
		}

		return $list;
	}
	

	/**
	 * Add debug message
	 * @param string $str
	 * @return bool
	 */
	function debug($str) {
		// add debug to debug array;
		$this->debug[]=$str;
		return true;
	}

	function show_debug() {
		var_dump($this->debug);
		return true;
	}
        
	/**
	 * Check alle fouten in Manyware: no UTF8 string return
	 * Also: Trim to remove spaces in the conversion
	 * @param string $input
	 * @return string trim($input)
	 */
	function correctUTF8($input) {
		if (mb_check_encoding($input, 'UTF-8'))
			return trim($input);
		else 
			return utf8_encode(trim($input));
	}

	/**
	 * Correct Data Array (Max dept 10) for UTF-8
	 * @param array $data
	 * @param int $nr =0
	 * @return array 
	 */
	function correctArray ($data, $nr=0) {
		if ($nr==10) {
			echo "Maximale diepte correctArray ($nr) bereikt";
			var_dump($data);
			return $data;
		}

		foreach ($data as &$item) {
			if (is_array($item)) {
				// recursive, met maximum van $nr=10 diepte;
				$item=$this->correctArray($item,$nr+1);
				continue;
			}

			$item=$this->correctUTF8($item);
		}
		return $data;
	}        
}

?>
