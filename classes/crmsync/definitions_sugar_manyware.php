<?php

/**
 * Keys are in Manyware, Values in Sugar
 *
 * @author Michiel
 */

/*
 * Original 2012
 * Updated 2016
 * Version 2.1
 */

define('UPDATE_INTERVAL',6000);	    // SYNC interval for SUGAR/MANYWARE (seconds)
/*define('WEBSERVICE_USER_ID', 'e5ff1792-a5c8-d056-a68c-4ecb9c44df43');*/
define('WEBSERVICE_USER_ID', '1');  // ADMIN

class DefinitionsSugarManyware {
	  public $manyware_xml_col = Array(
		'relatienummer'=>'manyware_relatienummer_c',
		'achternaam'=>'last_name',
		'voorletters'=>'voorletters_c',
		'voorvoegsel'=>'middle_name',
		'voornamen'=>'first_name',
		'straatnaam'=>'primary_address_street',
		'huisnummer'=>'primary_address_number_c',
		'huisnummertoevoeging'=>'primary_address_number_add_c',
		'woonplaats'=>'primary_address_city',
		'postcode'=>'primary_address_postalcode',
		'banknummer'=>'bankrekeningnummer_c',
		'geboortedatum'=>'birthdate',
		'geslacht'=>'gender_c',
		'emailadres'=>'email1',
		'telefoonnummer'=>'phone_home',
		'mobiel'=>'phone_mobile',
		'herkomstsegmentnr'=>'manyware_herkomstsegmentnr_c',
		'aiarelatie'=>'manyware_aiarelatie_c',
		'am'=>'manyware_aia_magazine_c',
		'homerun'=>'manyware_homerun_c',
		'herkomstsegmentnr'=>'manyware_herkomstsegmentnr_c',
		'debiteurnr'=>'debiteurnr_c',
		'laatstemutatiedatumtijd'=>'manyware_herkomstdatumtijd_c'
	);

	public $manyware_deelname_xml_col = Array(
		//'regnr'=>'relatienummer',
		'poms'=>'systeemnaam',	    // London 2011
		'pcode'=>'projectcode',	   // 2011_HBE001
		//''=>'manyware_herkomstdatumtijd',
		//'1'=>'project_id',
		//'remarks'=>'remarks', REMARKS passen niet in veld
		//'begindatum'=>Array(substr(date('c'), 0,-6)=>Array('constant',null)),
		'gratis'=>'gratis',
		'formuliero'=>'formulieren_ok',
		'paspoortok'=>'paspoort_ok',
		'medisch_ok'=>'medisch_ok',
		'minderjari'=>'minderjarig_ok',
		//'vrijwaring'=>'',
		'factuur_be'=>'projectprijs',
		'art_code'=>'manyware_projectid',
		'actnr'=>'manyware_actnr',
		'bnkgir'=>'bankrekeningnummer',
		'rekeningho'=>'bankrekeninghouder'
	  );

	  public $manyware_aiarelatie_xml_col = Array(
		'regnr'=>'manyware_relatienummer_c',
		'actnr'=>'actnr_c',		   // activiteitenbestandnr AIA_relaties
		'music' => 'muziekinstrument_c',
		'dieet' => 'dieet_c',
		//'dieet' => 'wensen_c',		// rare naam:)
		'getrouwd' => 'getrouwd_c',
		'kledingmaa'=> 'kledingmaat_c',
		'ehbo'=>'ehbo_c',
		'rijbewijs'=>'rijbewijs_c',
		'beroep_opl'=>'opleiding_beroep_c'
	  );
	  
	  public $manyware_check_data = Array(
		'laatstemutatiedatumtijd',
		'mtijd'
	  );
	  
	  
	  // CONVERSION TO SUGAR FUNCTIONS
	  // -----------------------------------
	  function geboortedatum($record) {
			return date('Y-m-d',strtotime($record['geboortedatum']));
	  }
	  
	  function huisnummertoevoeging($record) {
			return $record['huisnummertoevoeging']=str_replace ('-', '', $record['huisnummertoevoeging']);
	  }
	  
	  function aiarelatie($record) {
			return 1;
	  }
	  
	  function getrouwd($record) {
		return ($record['getrouwd']=='true')?1:0;
	  }
	  function ehbo($record) {
			if (!isset($record['ehbo'])) return 0;
			return ($record['ehbo']=='true')?1:0;
	  }
	  function formuliero($record) {
			return ($record['formuliero']=='true')?1:0;
	  }
	  function passport_ok($record) {
			return ($record['passport_ok']=='true')?1:0;
	  }
	  function medisch_ok($record) {
			return ($record['medisch_ok']=='true')?1:0;
	  }
	  function minderjari($record) {
			return ($record['minderjari']=='true')?1:0;
	  }
	  function geslacht($record) {
			return (strtolower($record['geslacht'])=='v')?'female':'male';
	  }
	  function rijbewijs($record) {
			if (!isset($record['rijbewijs'])) return 0;
			return ($record['rijbewijs']=='true')?1:0;
	  }
	  function am($record) {
			return ($record['am']=='true')?1:0;
	  }
	  function homerun($record) {
			return ($record['homerun']=='true')?1:0;
	  }
	  
	  function gratis($record) {
			// NB: two way function!

			if (isset($record['poms'])) return ($record['gratis']=='true')?1:0;

			return ($record['gratis']==1)?'true':'false';
	  }
	  
	  
	  // CONVERSION TO MANYWARE FUNCTIONS
	  // -----------------------------------
	  function birthdate($record) {
			return substr(date('c',strtotime($record['birthdate'])),0,-6);
	  }
	  
	  function manyware_aiarelatie_c($record) {
			return 'true';
	  }
	  
	  function manyware_aia_magazine_c($record) {
			return 'true';
	  }
	  
	  function manyware_homerun_c($record) {
			return 'true';
	  }

	  function debiteurnr_c($record) {
			if (!isset($record['debiteurnr_c']) || strlen($record['debiteurnr_c'])<2) {
				return 0;
			} else {
				return $record['debiteurnr_c'];
			}
	  }
	  
	  function primary_address_number_add_c($record) {
			if (substr($record['primary_address_number_add_c'],0,1)!='-') 
				$record['primary_address_number_add_c']='-'.$record['primary_address_number_add_c'];
			return $record['primary_address_number_add_c'];
	  }
	  
	  function gender_c($record) {
			return (strtolower($record['gender_c'])=='female')?'V':'M';
	  }
	  
	  function manyware_projectid($record) {
			return (int)$record['manyware_projectid'];
	  }
	  
	  function manyware_actnr($record) {
			// return 0 instead of ""
			return (int)$record['manyware_actnr'];
	  }
	  
	  function phone_mobile($record) {
			if ($record['phone_mobile']=='F') return "";
			return str_replace('-','',$record['phone_mobile']);
	  }
	  
	  function phone_home($record) {
			if ($record['phone_home']=='F') return "";
			return str_replace('-','',$record['phone_home']);
	  }
	  
	  // voorletters met puntjes
	  function voorletters_c ($record) {
			$voorletters=mb_eregi_replace('\.', '',$record['voorletters_c']);
			$voorletters=mb_eregi_replace(' ', '',$voorletters);
			$vrltr="";
			for ($i=0; $i<mb_strlen($voorletters); $i++){
				$vrltr.=mb_substr($voorletters, $i, 1).'.';
			}
			return $vrltr;
	  }
	  
	  /**
	   * return postcode in 1234 AB formaat
	   * @param type $record
	   * @return type r
	   */
	  function primary_address_postalcode($record) {
			if (strlen($record['primary_address_postalcode'])==6) {
				return substr($record['primary_address_postalcode'],0,4).' '.substr($record['primary_address_postalcode'],4,2);
			}
			return $record['primary_address_postalcode'];
	  }
}

?>
