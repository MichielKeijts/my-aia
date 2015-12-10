<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// This script is made to include all files in this and parent directories

// my-aia definitions (e.g. MY_AIA_..) 
// .. and helper functions
include ('include/my-aia-definitions.php');
include ('include/my-aia-functions.php');

// include installer file
include ('include/my-aia-install.php');

// Include all Custom Field types
//include ('include/custom-field-types/');

// Include all custom Post Types
include ('include/custom-post-types/my-aia-partner.php');

// Include custom taxonomies
include ('include/custom-taxonomies/my-aia-doelgroep.php');
include ('include/custom-taxonomies/my-aia-kerkstroming.php');
include ('include/custom-taxonomies/my-aia-overnachting.php');
include ('include/custom-taxonomies/my-aia-sport-level.php');
include ('include/custom-taxonomies/my-aia-sport.php');
include ('include/custom-taxonomies/my-aia-taal.php');
include ('include/custom-taxonomies/my-aia-sportweek-eigenschap.php');
include ('include/custom-taxonomies/my-aia-sportbetrokkenheid.php');

// Classes: Load MY_AIA class
include ( 'classes/my-aia.php' );

// FINALLY: Initiate my-aia
include ( 'include/my-aia-init.php' );