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
	private $sportlevel = NULL;
	
	/*
	 * Initialize
	 */
	public function __construct($args= NULL) {
		parent::init(array(
			'slug' => 'location',
			'name' => 'Locations',
			'enable_nav_item' => FALSE,
			//'nav_item_name' => __('Location','my-aua')
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
		
		// get group meta settings
		$setting = groups_update_groupmeta( $group_id, 'location', true );
		?>
			<p>Selecteer een locatie:</p>
			<select name="location">
				<?php foreach ($locations as $loc): ?>
				<option value="<?= $loc->location_id; ?>" <?= $loc->location_id == $setting ? 'selected':''; ?>><?= $loc->location_name; ?></option>
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
		
		$setting = isset( $_POST['location'] ) ? $_POST['location'] : '';
        groups_update_groupmeta( $group_id, 'location', $setting );
    }

}