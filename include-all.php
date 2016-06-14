<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// This script is made to include all files in this and parent directories

// my-aia definitions (e.g. MY_AIA_..) 
// .. and helper functions
include ('includes/my-aia-definitions.php');
include ('includes/my-aia-functions.php');

// include installer file
include ('includes/my-aia-install.php');

// Include all Custom Field types
//include ('includes/custom-field-types/');

// Include all custom Post Types
include ('includes/custom-post-types/my-aia-partner.php');
include ('includes/custom-post-types/my-aia-contract.php');

// Include custom taxonomies
include ('includes/custom-taxonomies/my-aia-doelgroep.php');
include ('includes/custom-taxonomies/my-aia-kerkstroming.php');
include ('includes/custom-taxonomies/my-aia-overnachting.php');
include ('includes/custom-taxonomies/my-aia-sport-level.php');
include ('includes/custom-taxonomies/my-aia-sport.php');
include ('includes/custom-taxonomies/my-aia-taal.php');
include ('includes/custom-taxonomies/my-aia-sportweek-eigenschap.php');
include ('includes/custom-taxonomies/my-aia-sportbetrokkenheid.php');

// Classes: Load MY_AIA class
include ( 'classes/my-aia-processflow.php' );
include ( 'classes/processflow/my-aia-processflow-static-condition.php' );
include ( 'classes/my-aia-html-helper.php' );
include ( 'classes/my-aia-view.php' );
include ( 'classes/my-aia.php' );

// Classes: MODELS
include ( 'classes/my-aia-base.php' );
include ( 'classes/my-aia-partner.php' );
include ( 'classes/my-aia-contract.php' );

// Modifications: Load modifications for other plugins (add ons)
include ( 'addons/buddypress/my-aia-buddypress.php' );
include ( 'addons/events-manager/my-aia-events-manager.php' );
include ( 'addons/ninja-forms/my-aia-ninja-forms.php' );

// FINALLY: Initiate my-aia
include ( 'includes/my-aia-init.php' );