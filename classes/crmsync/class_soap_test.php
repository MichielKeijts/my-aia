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

// initiate connection
$connection=new SoapManyware("https://mw20ws.manyware.eu/IFundsMW2_116.wsdl");

var_dump($connection->findRelatie(Array('achternaam'=>$_GET['achternaam'])));
//var_dump($connection->findProject());


// show debug
//$connection->show_debug();



?>
