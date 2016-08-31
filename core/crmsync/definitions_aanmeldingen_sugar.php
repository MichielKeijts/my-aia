<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Keys are in Manyware, Values in Sugar
 *
 * @author Michiel
 */

class DefinitionsSugarManyware {
	  public $sugar_website_contact = Array(
		'id'=>'id',
		'last_name'=>'last_name',
		'voorletters_c'=>'voorletters_c',
		'middle_name'=>'middle_name',
		'first_name'=>'first_name',
		'primary_address_street'=>'primary_address_street',
		'primary_address_number_c'=>'primary_address_number_c',
		'primary_address_number_add_c'=>'primary_address_number_add_c',
		'primary_address_city'=>'primary_address_city',
		'primary_address_postalcode'=>'primary_address_postalcode',
		'primary_address_country'=>'primary_address_country',
		'bankrekeningnummer_c'=>'bankrekeningnummer_c',
		'birthdate'=>'birthdate',
		'gender_c'=>'gender_c',
		'email1'=>'email1',
		'phone_home'=>'phone_home',
		'phone_mobile'=>'phone_mobile',
		'paspoortnr_c'=>'passpoortnumber_c',
		//'herkomstsegmentnr'=>'manyware_herkomstsegmentnr_c',!!
		//'aiarelatie'=>'manyware_aiarelatie_c',
		//'am'=>'manyware_aia_magazine_c',
		//'homerun'=>'manyware_homerun_c',
		//'herkomstsegmentnr'=>'manyware_herkomstsegmentnr_c',
	);

	public $sugar_website_project= Array(
		'contact_id_c'=>'',
		'aia_ministry_project_id_c'=>'',
		//'formuliero'=>'formulieren_ok',
		//'paspoortok'=>'paspoort_ok',
		//'medisch_ok'=>'medisch_ok',
		//'minderjari'=>'minderjarig_ok',
		//'factuur_be'=>'projectprijs',
		'actnr'=>'manyware_actnr',
		'bankrekeningnummer'=>'bankrekeningnummer',
		'bankrekeninghouder'=>'bankrekeninghouder',
			'extra_optie_1'=>'extra_optie_1_c',
			'extra_optie_2'=>'extra_optie_2_c'
	  );
	  
	  
	  // CONVERSION TO SUGAR FUNCTIONS
	  // -----------------------------------
	  /*function geboortedatum($record) {
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
	  }*/
	  function am($record) {
		return 1;
	  }
	  function homerun($record) {
		return 1;
	  }
	  
	  function gratis($record) {
		// NB: two way function!
		
		if (isset($record['poms'])) return ($record['gratis']=='true')?1:0;
		
		return ($record['gratis']==1)?'true':'false';
	  }
	  
	  
	  // CONVERSION TO MANYWARE FUNCTIONS
	  // -----------------------------------
	  /*function birthdate($record) {
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
		return str_replace('-','',$record['phone_mobile']);
	  }
	  
	  function phone_home($record) {
		return str_replace('-','',$record['phone_home']);
	  }*/
}

?>
