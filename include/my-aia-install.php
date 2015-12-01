<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/*
 * Installation File for My AIA
 * - Set capabilities
 * - Update (initiate) options for My AIA
 * - Insert Taxonomy data (register & on install: insert taxonomy tags)
 */

/**
 * Function to install everything (or reinstall)
 */
function my_aia_install() { 
	my_aia_register_capabilities();
	my_aia_insert_taxonomies();
	
	update_option('my_aia_version',MY_AIA_VERSION);
}

function my_aia_register_capabilities() {
	// initiate all capabilities to MY_AIA_ADMIN
	$capabilities = array(MY_AIA_CAPABILITY_ADMIN); 
	
	// add custom post capabilities to array
	foreach (MY_AIA::$CUSTOM_POST_TYPES as $name) 
		$capabilities=array_merge($capabilities,array_values(MY_AIA::get_capabilities($name,'post')));
	
	// add custom taxonomies capabilities to array
	foreach (MY_AIA::$CUSTOM_TAXONOMIES as $name) 
		$capabilities=array_merge($capabilities,array_values(MY_AIA::get_capabilities($name,'taxonomy')));
	
	my_aia_set_mass_roles(MY_AIA::$ROLES);
	my_aia_set_mass_capabilities(array('administrator'), $capabilities);
}

/**
 * Function to insert all the custom taxonomies
 */
function my_aia_insert_taxonomies() {
	
}