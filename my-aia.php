<?php
/*
 * @package MyAIA
 */
/*
Plugin Name: MyAIA
Plugin URI:  http://michielkeijts.nl/my-aia
Description: My AIA is a plugin which combines functionality of Buddypress, Events-Manager and more. 
Version:     0.1
Author:      Michiel Keijts
Author URI:  http://www.mchielkeijts.nl/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages/
Text Domain: my-aia
*/

// Some definitions
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'MY_AIA_VERSION', 0.1);
define( 'MY_AIA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MY_AIA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// start the script by including all files
include ( MY_AIA_PLUGIN_DIR . "include-all.php");

// load admin settings
if (is_admin()) {
    include (MY_AIA_PLUGIN_DIR . 'admin/include-all-admin.php');
}
// As all the stuff happens in the classes..