<?php
/**
 * @copyright (c) 2016, Michiel Keijts
 *	@package my-aia
 */


/**
 * Creates a custom group extension for BuddyPress in the MY_AIA plugin
 */
class MY_AIA_BP_Group_Extension_Documents extends BP_Group_Extension {
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
			'slug' => 'documents',
			'name' => 'Documents',
			'nav_item_name' => __('Documents','my-aua')
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
		
		$setting = isset( $_POST['group_extension_example_2_setting'] ) ? $_POST['group_extension_example_2_setting'] : '';
        groups_update_groupmeta( $group_id, 'group_extension_example_2_setting', $setting );
    }

	
	/**
	 * Call the display function
	 * @param int $group_id
	 */
	public function display($group_id = null) {
		MY_AIA::set('documents', MY_AIA::get_my_documents(get_current_user_id(), $group_id));
		echo "<h3>Documenten in deze groep </h3>";
		my_aia_locate_template('buddypress/group-documents.php', true);
		
	}
}