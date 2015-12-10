<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// Define actions
register_activation_hook('my-aia-install.php','my_aia_install');

add_action( 'plugins_loaded', 'MY_AIA::load_textdomain', 0 );
add_action( 'init', 'my_aia_register_taxonomy_sport' );
add_action( 'init', 'my_aia_register_taxonomy_sport_level' );
add_action( 'init', 'my_aia_register_taxonomy_taal' );
add_action( 'init', 'my_aia_register_taxonomy_sportweek_eigenschap' );
add_action( 'init', 'my_aia_register_taxonomy_kerkstroming' );
add_action( 'init', 'my_aia_register_taxonomy_overnachting' );
add_action( 'init', 'my_aia_register_posttype_partner' );
add_action( 'init', 'my_aia_register_taxonomy_sportbetrokkenheid' );

add_action ( 'init', 'MY_AIA::init');