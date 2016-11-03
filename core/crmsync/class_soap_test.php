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
include("class_soap_manyware.php");
include("class_soap_sugar.php");

// initiate connection
//$connection=new SoapManyware("https://mw20ws.manyware.eu/IFundsMW2_114.wsdl");

//var_dump($connection->findRelatie(Array('achternaam'=>$_GET['achternaam'])));
//var_dump($connection->findProject());


// show debug
//$connection->show_debug();

$sugar= new SoapSugar(
			'https://sugar.athletesinaction.nl:777/soap.php?wsdl',
			array(
				'user' => 'admin',
				'pass' => '#admin3'
			)
		);



$formated_sugar_data =
		array (
  1 => 
  array (
    'name' => 'deelnemertype',
    'value' => 'Teamlid',
  ),
  2 => 
  array (
    'name' => 'bankrekeningnummer',
    'value' => '150010486',
  ),
  3 => 
  array (
    'name' => 'date_modified',
    'value' => '2012-01-06 16:46:41',
  ),
  4 => 
  array (
    'name' => 'aia_ministry_project_id_c',
    'value' => '92a6ac03-a011-7981-77d2-4ef07b7312b2',
  ),
  5 => 
  array (
    'name' => 'contact_id_c',
    'value' => '3d1ec996-64a2-b605-539d-4f070e094638',
  ),
  6 => 
  array (
    'name' => 'contact_name',
    'value' => 'Michiel Keijts',
  ),
  7 => 
  array (
    'name' => 'aia_ministry_project_name',
    'value' => '2005__Israel',
  ));


$items = $sugar->searchCommon(
					
					"AIA_ministry_deelnames.contact_id_c <>'' AND UNIX_TIMESTAMP(AIA_ministry_deelnames.date_modified) > ".  time()-86400*7,
					"AIA_ministry_deelnames",
					10,
					0,
					'date_modified ASC'	// order by
				);
//var_export($items);

$formated_sugar_data[] = ['name'=>'contact_name','value'=>'Michiel Keijts'];
$formated_sugar_data[] = ['name'=>'aia_ministry_project_name','value'=>'2005__Israel'];
//
var_export($formated_sugar_data);
var_export($sugar->updateDeelname($formated_sugar_data));

?>
