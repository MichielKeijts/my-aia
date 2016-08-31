<?php
/** 
 * MY AIA 
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// This script is made to include all files in this and parent directories

// my-aia definitions (e.g. MY_AIA_..)
//include ('includes/my-aia-admin-definitions.php');

// Classes
include ( 'lib/my-aia-admin-functions.php' );
include ( 'lib/my-aia-admin-class-meta-columns.php' );
include ( 'lib/my-aia-admin.php' );

// Finally INIT
include ( 'lib/my-aia-admin-init.php' );

