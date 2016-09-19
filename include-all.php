<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// This script is made to include all files in this and parent directories

// my-aia definitions (e.g. MY_AIA_..) 
// .. and helper functions
include_once ('config/my-aia-definitions.php');
include_once ('config/my-aia-functions.php');

// include installer file
include_once ('config/my-aia-install.php');

// Include custom taxonomies
include_once ('config/custom-taxonomies/my-aia-doelgroep.php');
include_once ('config/custom-taxonomies/my-aia-kerkstroming.php');
include_once ('config/custom-taxonomies/my-aia-overnachting.php');
include_once ('config/custom-taxonomies/my-aia-sport-level.php');
include_once ('config/custom-taxonomies/my-aia-sport.php');
include_once ('config/custom-taxonomies/my-aia-taal.php');
include_once ('config/custom-taxonomies/my-aia-sportweek-eigenschap.php');
include_once ('config/custom-taxonomies/my-aia-sportbetrokkenheid.php');
include_once ('config/custom-taxonomies/my-aia-product-categorie.php');

// Classes: Load MY_AIA class
include_once ( 'controllers/my-aia-processflow.php' );
include_once ( 'core/processflow/my-aia-processflow-static-condition.php' );
include_once ( 'controllers/my-aia-html-helper.php' );
include_once ( 'core/my-aia-view.php' );
include_once ( 'core/controller.php' );
include_once ( 'core/model.php' );
include_once ( 'core/my-aia.php' );

// Classes: MODELS
//include_once ( 'core/my-aia-partner.php' );
//include_once ( 'core/my-aia-contract.php' );

// Modifications: Load modifications for other plugins (add ons)
include_once ( 'addons/buddypress/my-aia-buddypress.php' );
include_once ( 'addons/events-manager/my-aia-events-manager.php' );
include_once ( 'addons/ninja-forms/my-aia-ninja-forms.php' );

// All other necessary files
include_once ( 'core/crmsync/iso3166.php');
include_once ( 'core/lib/Download.php');

// FINALLY: Initiate my-aia
include_once ( 'config/my-aia-init.php' );