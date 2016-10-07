<?php
/**
 * @copyright (c) 2016, Michiel Keijts
 *	@package my-aia
 */


/**
 * Creates a custom group extension for BuddyPress in the MY_AIA plugin
 */
class MY_AIA_BP_Group_Extension_Location extends BP_Group_Extension {
	/**
	 * Location holder
	 * @var \EM_Locations
	 */
	private $location = NULL;
	
	/*
	 * Initialize
	 */
	public function __construct($args= NULL) {
		parent::init(array(
			'slug' => 'Location',
			'name' => 'Location',
			'nav_item_name' => __('Locatie','my-aua')
		));
	}

	/**
	 * Settings Screen
	 * @param int $group_id
	 */
	public function settings_screen($group_id = null) {
		parent::edit_screen($group_id);
	
		// get sportlevel terms
		$term_name = MY_AIA_TAXONOMY_SPORT_LEVEL;
		$terms = get_terms($term_name, array('hide_empty'=>FALSE));

		?>
			<p>Selecteer een sportlevel:</p>
			<select name="sportlevel">
		<?php
			foreach ($terms as $key=>$term) {
				?><option value="<?= $term->name; ?>"><?= $term->name; ?></option><?php
			}
		?>
			</select>
		<?php
	}
	
	/**
	 * Settings Save
	 * @param int $group_id
	 */
	public function settings_screen_save($group_id = null) {
		parent::settings_screen_save($group_id);
		
		$setting = isset( $_POST['group_extension_example_2_setting'] ) ? $_POST['group_extension_example_2_setting'] : '';
        groups_update_groupmeta( $group_id, 'group_extension_example_2_setting', $setting );
    }

}