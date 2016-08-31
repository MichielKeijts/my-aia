<?php
// www.normit.nl	(C) 2011
// author:              Michiel Keijts
// contact:             m.keijts@athletesinaction.nl
// 
//
// Projectcode:		AIA
// Omschrijving:	Connecting to and using SOAP methods of AIA
// Functie:
//
//
// ******************************************************
// |	filename:	class_soap_manyware.PHP		|
// |	versie:		0.1                             |
// ******************************************************


// Class definition
class SoapManyware {
        // debug information variable
	private $debug="";  
        // ID key for identification 
	private $cIdKey="";
        
        // SOAP client
	private $SOAP=null;

    // Initiating Connection
	function __construct($url='https://mw20ws.manyware.eu/IFundsMW2_112.wsdl') {
		$this->URL=$url;
		$this->SOAP=new SoapClient($this->URL);

		$this->connect();	// initiate connection
                
		// Login as user
		$this->soapCall('LoginAsUser',array('cUserLogin'=>'WEBSERV', 'cUserPwd'=>'Qmxidc'));
	}

	/** 
	 * Initiate connection using predefined settings
	 * @return bool Connection Set
	 */
	function connect() {
		// TEST OMGEVING
		//$return=$this->SOAP->__soapCall('OpenConnection2Mw2',array('cClientLogin'=>'AGAPTEST', 'cClientPwd'=>'B1U4WS77OY6UYYPHELRIZ58HMXD9OGKAJC4FD89B58LW7ZIMHAGQAJ1E59E4S96G')); // TEST
		
		//PRODUCTIE
		$return=$this->SOAP->__soapCall('OpenConnection2Mw2',array('cClientLogin'=>'AGAP', 'cClientPwd'=>'NFK7II5E0CWSEUUX1H401DFUN5RVV3YTCHLLKWRXVE6ECOM3XJEPJ4ZCIO9T7VON'));//Productie
		$this->debug($return);
		$returnobj=simplexml_load_string($return);
		
		if ($returnobj->Error->Errorno<=0) {
			$this->cIdKey = (string)$returnobj->IdKey;  // Set ID key
			return true;
		}
		return false;

	}

	/**
	 * Main function to do a soapCall. This function has to be called from a
         * function within this class
         * 
	 * @param string $action
	 * @param array $options
	 * @return bool
	 */
	private function soapCall($action, $options) {
		if ($this->cIdKey=="") {
			$this->debug('No cIdKey set');
			return false;
		}

		// put cIdKey in the beginning
		array_unshift($options,$this->cIdKey);
		
		// do Call
		$response=$this->SOAP->__soapCall($action, $options);

		// add to debug
		$this->debug($response);

		// get new cIdKey
		$responseObj=simplexml_load_string($response);

		// update cIdKey
		if ($responseObj->IdKey) {
			// update response key
			$this->cIdKey = (string)$responseObj->IdKey;

			//var_dump($responseObj);
			$this->data=$this->parseResponse($responseObj);

			if ($this->error) {
				switch ($this->errorNo) {
					case 4122:	// Duplicate erro
						$this->debug("duplicate error. ".$this->errorMsg);
						break;
					default:
						$this->show_debug();
						//echo"error occurred: ";
						//echo $this->errorMsg. "\r\n<Br>XML: ";
				}
				return false;
			} else {
				return $this->data;
			}
		} else {
			$this->debug("error: ".$response);
			return false;
		} // error while obtaining IdKey
	}

	/**
	 * Zoeken naar relatie
	 * @param <type> $data
	 */
	function findLatestUpdatedRelaties($data) {
		$srchXML='<zoek>';
                
		// built XML list
		foreach ($data as $item=>$value) {
			$srchXML.="
				<expressie>
					<veld>{$item}</veld>
					<operator>gt</operator>
					<waarde>{$value}</waarde>
                                </expressie>";
		}
		$srchXML.="<expressie>
					<veld>aiarelatie</veld>
					<operator>=</operator>
					<waarde>true</waarde>
				</expressie></zoek>";
		$this->debug($srchXML);
		// soap call
		$return=$this->soapCall('SrchRelatie',  Array('cZoekString'=>$srchXML, 'nMaxRecs'=>100));

		// object
		return $return;
	}
        
        /**
	 * Search for relatie
	 * @param <type> $data
	 */
	function findRelatie($data) {
		$srchXML='<zoek>';

		// built XML list
		foreach ($data as $item=>$value) {
			$srchXML.="
				<expressie>
					<veld>{$item}</veld>
					<operator>=</operator>
					<waarde>{$value}</waarde>
				</expressie>";
		}
		$srchXML.="</zoek>";
		$this->debug($srchXML);
		// soap call
		$return=$this->soapCall('SrchRelatie',  Array('cZoekString'=>$srchXML, 'nMaxRecs'=>250000));

		// object
		return $return;
	}

	/**
	 * Search for relatie
	 * @param <type> $data
	 */
	function findRelatieById($data) {
		// soap call
		$return=$this->soapCall('GetRelatieByPk',  Array('nRegnr'=>$data));

		// object
		return $return;
	}
        
        /**
         * Obtain Gift information by ID
         * @param type $id
         * @return type 
         */
        function getGift($id=null) {
            if ($id==null) return false;
            
            $gift=$this->soapCall('GetActivtRecByPk',  Array('cTablename'=>'TNT_Gifts', 'nActnr'=>$id));
            return $gift;
        }
        
        /**
         * Obtain Gift information by Date
         * @param date $date (FORMAT: YYYY-MM-DD H:i:s) CET
         * @return type 
         */
        function getGiftsFromDate($date=null) {
            if ($id==null) return false;
            
            $gift=$this->soapCall('GetActivtRecByPk',  Array('cTablename'=>'TNTgifts', 'nActnr'=>$id));
            return $gift;
        }
        

	/**
	 * Search for AIA_relatie
	 * @param <type> $data
	 */
	function findAIARelatieById($id) {
		// soap call
		$return=$this->soapCall('GetActivtRecByRegnr',  Array('cTablename'=>'AIA_relaties', 'nRegnr'=>$id,'nOption'=>0));

		// object
		return $return;
	}
        
        /**
	 * Search for AIA_deelname
	 * @param <type> $data
	 */
	function findAIADeelnameById($id) {
		// soap call
		$return=$this->soapCall('GetActivtRecByRegnr',  Array('cTablename'=>'AIA_deelnames', 'nRegnr'=>$id,'nOption'=>0));

		// object
		return $return;
	}
        
        /**
	 * Search for AIA_deelname
	 * @param <type> $data
	 */
	function findAIADeelnameByAct($id) {
		// soap call
		$return=$this->soapCall('GetActivtRecByPk',  Array('cTablename'=>'AIA_deelnames', 'nActnr'=>$id));

		// object
		return $return;
	}

	/**
	 * Search for project in activiteitenbestand
	 * @param <type> $data
	 */
	function findProjectPrijs($id) {

		// soap call
		if ($id!=null) {
			//$return=$this->soapCall('GetItemPrice',  Array('cItemCode'=>$id, 'nItemCount'=>1, 'cDatum'=>date('c')));
			$srchXML='<zoek><expressie><veld>art_nr</veld><operator>=</operator><waarde>'.$id.'</waarde></expressie></zoek>';
			$return=$this->soapCall('SrchGeneral',Array('cTablename'=>'Artikelen','SrchXML'=>$srchXML, 'nMaxRecs'=>1));
		}
		else return null;
		// object
		return $return;
	}

	/**
	 * Zoeken naar project in activiteitenbestand
	 * @param <type> $data
	 */
	function findProject($data, $id=null) {

		// soap call
		if ($id!=null) {
			$return=$this->soapCall('GetCodeTable',  Array('cTablename'=>'Artikelcode', 'cFilter'=>'Art_code=\''.$id.'\''));
		} else {
			$return=$this->soapCall('GetCodeTable',  Array('cTablename'=>'Artikelcode', 'cFilter'=>'Art_Oms LIKE("%'.$data.'%")'));
		}
		
		// object
		return $return;
	}

	/**
	 * Get woonplaats, straatnaam from postcode
	 * @param string $postcode
	 * @param string $huisnr
	 * @return data
	 */
	function findPCW ($postcode,$huisnr) {
		$return=$this->soapCall('GetStraatWoonplaats', Array('cPostcode'=>$postcode, 'cHuisnummer'=>$huisnr));
		return $return;
	}

	/**
	 *
	 * @param array $data
	 * @return bool
	 */
	function insertSport($data) {
                //@TODO
		return true;
	}


	/**
	 * iunsert or update relatie
	 * @param mixed $data
	 */
	function insertRelatie($data) {
		$xml=
			"<data><relaties>";
		foreach ($data as $key=>$value) {
			//if ($key!='emailadres')
			//$value=htmlspecialchars($value);
			$xml.="<{$key}>{$value}</{$key}>";
		}
		$xml.="</relaties></data>";
		$this->debug($xml);
		$this->soapCall('PutRelatieByPk', Array('cXMLRecord'=>$xml ,'nCheckFormatOption'=>0,'cCheckExistOption'=>'','nVervolgOption'=>0));

		// kan gebeuren dat no error en toch geen relatienummer, dan niets geupdate.
		if ($this->error) {
			return -1;
		} elseif (count($this->data)<1) {
			return 0;
		} 
		return $this->data[0]['relatienummer'];	// always give back
	}

	/**
	 * Insert or update relatie
	 * @param mixed $data
	 */
	function insertDeelname($data) {
		$xml=
			"<data><AIA_deelnames><actnr>0</actnr>";	// altijd als nieuw toevoegen, maar check op al bestaande
		foreach ($data as $key=>$value) {
			//$value=htmlspecialchars($value);
			$xml.="<{$key}>{$value}</{$key}>";
		}
		$xml.="</AIA_deelnames></data>";
		$this->debug($xml);
		$this->soapCall('PutActivtRecByPk', Array('cTableName'=>'AIA_deelnames', 'cXMLRecord'=>$xml, 'nCheckFormatOption'=>0,'cCheckExistOption'=>'RA','nVervolgOption'=>0));

		//return $this->data[0]['actnr'];	// always give back
		return true;
	}
        
        /**
	 * iunsert or update relatie
	 * @param mixed $data
	 */
	function updateDeelname($data) {
		$xml=
			"<data><AIA_deelnames>";	
		foreach ($data as $key=>$value) {
                        if ($key!='poms') $value=htmlspecialchars($value);
			$xml.="<{$key}>{$value}</{$key}>";
		}
		$xml.="</AIA_deelnames></data>";
		$this->debug($xml);
		$this->soapCall('PutActivtRecByPk', Array('cTableName'=>'AIA_deelnames', 'cXMLRecord'=>$xml, 'nCheckFormatOption'=>0,'cCheckExistOption'=>'RA','nVervolgOption'=>0));
                $this->debug($this->data);
                
                // kan gebeuren dat no error en toch geen relatienummer, dan niets geupdate.
		if ($this->error) {
			return -1;
		} elseif (count($this->data)<1) {
			return -2;
		} 
		return $this->data[0]['Actnr'];	// always give back
		//return true;
	}

	/**
	 * iunsert or update relatie
	 * @param mixed $data
	 */
	function insertAIARelatie($data) {
		$xml=
			"<data><AIA_relaties>";
		foreach ($data as $key=>$value) {
			if ($key=='relatienummer') continue; // skip this one
			//$value=htmlspecialchars($value);
			$xml.="<{$key}>{$value}</{$key}>";
		}
		$xml.="</AIA_relaties></data>";
		$this->debug($xml);
		$this->soapCall('PutActivtRecByPk', Array('cTableName'=>'AIA_relaties', 'cXMLRecord'=>$xml, 'nCheckFormatOption'=>0,'cCheckExistOption'=>'R','nVervolgOption'=>0));
		return true;
	}


	/**
	 * Parse response and convert to ANSI data
         * 
	 * @param array $responseData ANSI
	 * @return array $responseData ANSI
	 */
	function parseResponse($responseData) {
		$this->error=($responseData->Error->Errorno!=0)&&($responseData->Error->Errorno!=2); // no records found is no error
		$this->errorNo=$responseData->Error->Errorno;
		$this->errorMsg=$responseData->Error->ErrorMess;

		$data=Array();
		$c=0;
		foreach ($responseData->Data as $obj) {	
			foreach ($obj as $key1=>$table) {

				//var_dump($table);
				foreach ($table as $key=>$value) {
					$data[$c][$key]=(string) utf8_decode($value);
				}
				$c++;
			}
		}
		return $data;
	}
        
        /**
	 * Return JSON data for EXTJS
	 * @param mixed $data
	 * @return string JSON
	 */
	function toExt($data=null) {
		if ($data==null) {
			$data=$this->data;
		}

		// create JSON obj
		$jsonobj=Array(
					'success'	=>true,
					'totalCount'=>count($data),
					'data'		=>$data
					//'data'		=> Array('data[Room][id]'=>1)
				);
		// encode to JSON
		return $jsonobj;
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

        /**
         * Showing the debug
         * @return true
         */
	function show_debug() {
		var_dump($this->debug);
		return true;
	}
        
        /**
         * Returning the debug Array
         * @return Array
         */
	function get_debug() {
		return $this->debug;
	}
}

?>
