<?php
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

/**
 * Class MY_AIA_BASE
 * - Holder for the MY_AIA class variables, shared among classes.
 * - 
 */
class MY_AIA BASE {
	public function __construct() {
		
	}
	
	public function render($object) {
		
	}
	
	protected function opti
	
	/**
	 * Render the MY_AIA_SETTINGS
	 *
	 * @access public
	 * @global      string 'buddypress-media'
	 *
	 * @param  type $page
	 * @param  type $option_group
	 *
	 * @return void
	 */
	public function render_page( $page = 'my-aia-settings', $option_group = null ) {
		?>
		<div class="wrap <?php echo $this->get_current_tab(); ?>">
			<div id="icon-my-aia" class="icon32"><br></div>
			<div>
				<h2 class="nav-tab-wrapper">
					<?php $this->render_tabs(); ?>
					<span class="alignright by">
						<a class="my-aia-link" href="http://normit.nl" target="_blank" title="Normit : <?php _e( 'Custom Webapplication with a heart', 'my-aia' ); ?>">
							<img src="<?php echo MY_AIA_PLUGIN_URL; ?>admin/assets/img/my-aia-logo.png" alt="My AIA" />
						</a>
					</span>
				</h2>
			</div>

			<div class="clearfix rtm-row-container">
				<div id="bp-media-settings-boxes" class="bp-media-settings-boxes-container rtm-setting-container">
					<form id="bp_media_settings_form" name="bp_media_settings_form" method="post" enctype="multipart/form-data">
						<div class="bp-media-metabox-holder">
							<div class="rtm-button-container top">
									<?php if ( isset( $_GET[ 'settings-saved' ] ) && $_GET[ 'settings-saved' ] ) { ?>
										<div class="rtm-success rtm-fly-warning rtm-save-settings-msg"><?php _e( 'Settings saved successfully!', 'buddypress-media' ); ?></div>
									<?php } ?>
									<input type="hidden" name="rtmedia-options-save" value="true">
									<input type="submit" class="rtmedia-settings-submit button button-primary button-big" value="<?php _e( 'Save Settings', 'buddypress-media' ); ?>">
								</div>
								<?php
								settings_fields( $option_group );
								if ( 'rtmedia-settings' == $page ) {
									echo '<div id="rtm-settings-tabs">';
									$sub_tabs = $this->settings_sub_tabs();
									RTMediaFormHandler::rtForm_settings_tabs_content( $page, $sub_tabs );
									echo '</div>';
								} else {
									do_settings_sections( $page );
								}
								?>

								<div class="rtm-button-container bottom">
									<div class="rtm-social-links alignleft">
										<a href="http://twitter.com/rtcamp" class="twitter" target= "_blank"><span class="dashicons dashicons-twitter"></span></a>
										<a href="https://www.facebook.com/rtCamp.solutions" class="facebook" target="_blank"><span class="dashicons dashicons-facebook"></span></a>
										<a href="http://profiles.wordpress.org/rtcamp" class="wordpress" target= "_blank"><span class="dashicons dashicons-wordpress"></span></a>
										<a href="https://rtcamp.com/feed" class="rss" target="_blank"><span class="dashicons dashicons-rss"></span></a>
									</div>

									<input type="hidden" name="rtmedia-options-save" value="true">
									<input type="submit" class="rtmedia-settings-submit button button-primary button-big" value="<?php _e( 'Save Settings', 'buddypress-media' ); ?>">
								</div>
							</div>
						</form>
					<?php endif; ?>
				</div>

				<div class="metabox-holder bp-media-metabox-holder rtm-sidebar">
					<?php //$this->admin_sidebar(); ?>
				</div>

			</div>

		</div><!-- .bp-media-admin --><?php
	}
	
	private function get_current_tab() {
		if (!isset($_REQUEST['tab'])) $_REQUEST['tab']="";
		
		switch ($_REQUEST['tab']) {
			case "members":	return "members";
			default:		return "";
		}
	}
	
	/**
	 * Display the various tabs
	 */
	private function render_tabs() {
		//nav-tab-active
		foreach ($this->TABS as $slug=>$tab) {
			?><a href="/wp-admin/admin.php?page=my-aia-admin&tab=<?= $slug ?>" class="nav-tab <?php if ($this->get_current_tab()==$slug) echo "nav-tab-active"; ?>"><?= $tab ?></a><?php
		}
	}
	
	public function show_admin_menu() {
		echo "Welkom in het ADMIN menu voor Mijn AIA";
		return true;
	}
	
	/**
	 * Reset the MY AIA Parameters (basically perform reinitiation of the plugin
	 */
	public function reset() {
		echo "<br>Resetting preferences...";
		my_aia_install();
		echo "<br>Complete.";
	}
	
	public function show_menu() {
		add_menu_page(
			__('My AIA','my-aia'), 
			__('My AIA','my-aia'), 
			'manage_options', 
			'my-aia-admin',
			array($this, 'render_page'), 
			plugins_url( 'my-aia/assets/images/my-aia24.png' ), 
			10
		);
		add_submenu_page('my-aia-admin',__('Reset','my-aia'),		__('Reset','my-aia'), 'manage_options', 'my-aia-reset', 'MY_AIA_ADMIN::reset' );
		add_submenu_page('my-aia-admin',__('Partners','my-aia'),	__('Partners','my-aia'), 'my_aia_admin', 'my-aia-partners', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Settings','my-aia'),	__('Settings','my-aia'), 'my_aia_admin', 'my-aia-settings', 'MY_AIA_ADMIN::show_admin_menu' );
		add_submenu_page('my-aia-admin',__('Sportweken','my-aia'),	__('Sportweken','my-aia'), 'my_aia_admin', 'my-aia-sportweken', 'MY_AIA_ADMIN::show_admin_menu' );
	}
}