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
	echo "<br>Capabilities inserted";
	
	my_aia_insert_taxonomies();
	
	echo "<br>Taxonomies written";
	
	delete_option('my_aia_version');
	delete_option('my-aia-registered-hooks');
	
	add_option('my-aia-version',MY_AIA_VERSION);
	add_option('my-aia-registered-hooks', array());
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
 * Function to insert all the custom taxonomies based on defaults
 * (default: ./inlude/custom-taxonomies/defaults/
 */
function my_aia_insert_taxonomies() {
	// loop over all custom taxonomies
	foreach (MY_AIA::$CUSTOM_TAXONOMIES as $name) {
		$filename = sprintf("my-aia-%s.txt", strtolower($name));
		$lines =  file(MY_AIA_PLUGIN_DIR . 'include/custom-taxonomies/defaults/' . $filename);
		foreach ($lines as $term) {
			// insert term 
			// wp does not overwrite term with same name
			wp_insert_term($term, $name);
		}
	}
	
	return true;
}