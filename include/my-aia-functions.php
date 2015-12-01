<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 * 
 * 
 * List of Functions, which are not object oriented
 */


/**
 * Set Capabitilities for roles
 * @global \WP_Roles $wp_roles
 * @param array $roles
 * @param array $caps
 */
function my_aia_set_mass_capabilities( $roles, $caps ){
	global $wp_roles;
	foreach( $roles as $user_role ){
		foreach($caps as $cap){
			$wp_roles->add_cap($user_role, $cap);
		}
	}
}

/*
 * Set Roles 
 * @param array $roles roles to declare
 * @param array $caps caps to initiate (default: empty array)
 */
function my_aia_set_mass_roles( $roles, $capabilities = array()) {
	foreach ($roles as $role) {
		remove_role( $role, __($role), $capabilities );
		add_role( $role, __($role), $capabilities );
	}
}