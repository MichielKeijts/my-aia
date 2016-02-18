<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// This script is made to include all files in this and parent directories

// my-aia definitions (e.g. MY_AIA_..)
//include ('include/my-aia-admin-definitions.php');

// Classes
include ( 'controller/my-aia-app-controller.php' );
include ( 'classes/my-aia-admin.php' );

// Finally INIT
include ( 'include/my-aia-admin-init.php' );

