<?php
/**
 * @copyright (c) 2016, Michiel Keijts
 *	@package my-aia
 */


/**
 * Creates a custom group extension for BuddyPress in the MY_AIA plugin
 */
include_once BUDDYPRESS_DIR . 'bp-groups/classes/class-bp-group-extension.php';
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
	
		$location = new EM_Locations;
		$locations = $location->get();
		?>
			<p>Selecteer een locatie:</p>
			<select name="location">
				<option value="temp">Amersfoort</option>
				<?php foreach ($locations as $loc): ?>
				<option value="<?= $loc->location_id; ?>"><?= $loc->location_name; ?></option>
				<?php endforeach; ?>
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