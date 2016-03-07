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
include ( 'modifications/buddypress/my-aia-admin-buddypress.php' );
include ( 'lib/my-aia-admin.php' );


// Finally INIT
include ( 'lib/my-aia-admin-init.php' );

