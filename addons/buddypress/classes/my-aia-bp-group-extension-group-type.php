<?php
/**
 * @copyright (c) 2016, Michiel Keijts
 *	@package my-aia
 */


/**
 * Creates a custom group extension for BuddyPress in the MY_AIA plugin
 */
class MY_AIA_BP_Group_Extension_Group_Type extends BP_Group_Extension {
	private $default_group_type = 'sportcommunity'; // DEFAULT
	
	
	public function __construct($args=NULL) {
		parent::init(array(
			'slug' => 'group-type',
			'name' => __('Group Type','my-aia'),
			'enable_nav_item' => FALSE,
			//'nav_item_name' => __('Type'),
		));
	}

	/**
	 * Display the settings screen
	 * @param type $group_id
	 */
	public function settings_screen($group_id = null) {
		parent::edit_screen($group_id);
		$setting = groups_get_groupmeta($group_id, 'type', true);
		?>
			<p>Selecteer een type:</p>
			<select name="group_setting_group_type">
				<option value="default" <?= 'default'==$setting ? 'selected':''; ?>><?= __('Default','my-aia'); ?></option>
				<option value="huddle" <?= 'huddle'==$setting ? 'selected':''; ?>><?= __('Huddle','my-aia'); ?></option>
				<option value="sportcommunity" <?= 'sportcommunity'==$setting ? 'selected':''; ?>><?= __('Sportcommunity','my-aia'); ?></option>
				<option value="sport_specifiek" <?= 'sport_specifiek'==$setting ? 'selected':''; ?>><?= __('Sport Specifiek','my-aia'); ?></option>
			</select>
		<?php
	}
	
	
	/**
	 * Save the Group meta
	 * @param type $group_id
	 */
	public function settings_screen_save($group_id = null) {
		parent::settings_screen_save($group_id);
		
		$setting = filter_input(INPUT_POST, 'group_setting_group_type');
		if (!$setting) $setting = $this->default_group_type;
        
		// update meta
		groups_update_groupmeta( $group_id, 'type', $setting );
    }

}