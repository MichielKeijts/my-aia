<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package my-aia
 * @author Bernard Bos <bernardbos@gmail.com>
  * @copyright (c) 2017, Bernard Bos
 */

function my_aia_select_js() {
    echo '<script type="text/javascript" id="my-aia-jquery-select" src="' . MY_AIA_PLUGIN_URL . '/vendor/jQuery-Select/js/select2.full.min.js"></script>';
    echo '<script type="text/javascript" id="my-aia-jquery-select-i18n" src="' . MY_AIA_PLUGIN_URL . '/vendor/jQuery-Select/js/i18n/nl.js"></script>';
}
function my_aia_select_css() {
		echo '<link rel="stylesheet" href="' . MY_AIA_PLUGIN_URL . '/vendor/jQuery-Select/css/select2.min.css" />';
}
function my_aia_init_select($user_id) {
	  echo '<script type="text/javascript">jQuery(".multiple-taxonomy-select").select2({ placeholder : \'Klik en begin met typen\', allowClear: true });</script>';
}

// Add hook for admin <head></head>
add_action('admin_head', 'my_aia_select_js');
add_action('admin_head', 'my_aia_select_css');

// Add hook for front-end <head></head>
add_action('wp_head', 'my_aia_select_js');
add_action('wp_head', 'my_aia_select_css');