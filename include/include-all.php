<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// This script is made to include all files in this and parent directories

// my-aia definitions (e.g. MY_AIA_..)
include ('./my-aia-definitions.php');

// Include all Custom Field types
include ('./custom-field-types/');

// Include all custom Post Types
include ('./custom-post-types/my-aia-partner.php');

// Include custom taxonomies
include ('./custom-taxonomies/my-aia-doelgroep.php');
include ('./custom-taxonomies/my-aia-kerkstroming.php');
include ('./custom-taxonomies/my-aia-overnachting.php');
include ('./custom-taxonomies/my-aia-sport-level.php');
include ('./custom-taxonomies/my-aia-sport.php');
include ('./custom-taxonomies/my-aia-taal.php');


