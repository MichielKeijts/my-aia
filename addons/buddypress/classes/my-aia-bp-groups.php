<?php
/*
 * @copyright 2016 Michiel Keijts
 * @package my-aia
 */
class BP_MY_AIA_GROUP_Component extends BP_Component {

	function my_aia_get_all_group_membership_requests( $user_id ) {
		global $wpdb;

		$bp = buddypress();

		return $wpdb->get_col( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_confirmed = 0 AND inviter_id = 0", $user_id ) );
	}
}
add_action( 'bp_before_groups_invites_content', 'my_aia_get_all_group_membership_requests' ); // add template to cancel membership request

