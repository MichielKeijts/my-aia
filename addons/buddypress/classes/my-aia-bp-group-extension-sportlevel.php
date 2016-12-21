<?php
/**
 * @copyright (c) 2016, Michiel Keijts
 *	@package my-aia
 */


/**
 * Creates a custom group extension for BuddyPress in the MY_AIA plugin
 */
class MY_AIA_BP_Group_Extension_SportLevel extends BP_Group_Extension {
	/**
	 * Location holder
	 * @var 
	 */
	private $sportlevel = NULL;
	
	/*
	 * Initialize
	 */
	public function __construct($args= NULL) {
		parent::init(array(
			'slug' => 'sportlevel',
			'name' => __('Sport Niveau','my-aia'),
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
		
		// get group meta
		$setting = groups_get_groupmeta( $group_id, 'sportlevel', true );

		?>
			<p>Selecteer een sportlevel:</p>
			<select name="sportlevel">
				<option value="-1"> N.V.T. </option>				
				<?php
					foreach ($terms as $key=>$term) {
						?><option value="<?= $term->name; ?>" <?= $setting == $term->name ? 'selected':''; ?>><?= $term->name; ?></option><?php
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
		
		$setting = isset( $_POST['sportlevel'] ) ? $_POST['sportlevel'] : '';
        groups_update_groupmeta( $group_id, 'sportlevel', $setting );
    }

}