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
		$lines =  file(MY_AIA_PLUGIN_DIR . 'include/definitions/' . $filename);
		foreach ($lines as $term) {
			// insert term 
			// wp does not overwrite term with same name
			wp_insert_term($term, $name);
		}
	}
	
	return true;
}

/**
 * Create the Sync Table for CRM (Sugar in this case)
 */
function my_aia_create_crm_sync_table() {
	global $wpdb;
	
	// execute;
	dbDelta("CREATE TABLE ".$wpdb->prefix."_my_aia_crm_sync` (
			`id` BIGINT NOT NULL AUTO_INCREMENT,
			`wp_id` BIGINT NOT NULL DEFAULT 0,
			`crm_id` VARCHAR(36) NOT NULL DEFAULT 0 COMMENT 'ID of the CRM Object (or 0 if not existing, thus creating)',
			`approved` TINYINT(1) NULL DEFAULT 0,
			`approved_by` BIGINT NULL,
			`done` TINYINT NOT NULL DEFAULT 0,
			`from_object` VARCHAR(255) NULL COMMENT 'from table (',
			`to_object` VARCHAR(255) NULL,
			`fields` MEDIUMTEXT NULL COMMENT 'serialized set of fields (array, names)',
			`old_values` LONGTEXT NULL COMMENT 'serialized set of values (array, values) (for verification)\n',
			`new_values` LONGTEXT NULL COMMENT 'serialized set of fields (array, values)',
			`modifed` DATETIME NULL,
			`created` DATETIME NULL,
			PRIMARY KEY (`id`))
		  ENGINE = InnoDB
		  DEFAULT CHARACTER SET = utf8
		  COMMENT = 'Sync Table for My_AIA and CRM software';
		  ");
	
	
	return true;
}