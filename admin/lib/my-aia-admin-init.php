<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// Define actions
add_action( 'bp_xprofile_get_field_types', "my_aia_bp_xprofile_get_field_types", 99, 1);


// init action in construct
$my_aia_admin = new MY_AIA_ADMIN();