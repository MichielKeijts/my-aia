		<div class="wrap <?php //echo $this->get_current_tab(); ?>">
			<div id="icon-my-aia" class="icon32"><br></div>
			<div>
				<?= $this->element('nav-tabs'); ?>
			</div>

			<div class="clearfix rtm-row-container">
				<div id="bp-media-settings-boxes" class="bp-media-settings-boxes-container rtm-setting-container">
					<form id="bp_media_settings_form" name="bp_media_settings_form" method="post" enctype="multipart/form-data" action="">
						<div class="bp-media-metabox-holder">
							<div class="rtm-button-container top">
								<h2><?= $title; ?></h2>
									<?php if ( isset( $_GET[ 'settings-saved' ] ) && $_GET[ 'settings-saved' ] ) { ?>
										<div class="rtm-success rtm-fly-warning rtm-save-settings-msg"><?php _e( 'Settings saved successfully!', 'buddypress-media' ); ?></div>
									<?php } ?>
									<input type="hidden" name="rtmedia-options-save" value="true">
									<input type="submit" class="my_aia_submit_button button button-primary button-big" value="<?php _e( 'Save Settings', 'buddypress-media' ); ?>">
									<?php echo $this->element('_flash'); ?>
								</div>
								<?= $content; ?>
								<?php
								//settings_fields( $option_group );
								/*if ( 'rtmedia-settings' == $page ) {
									echo '<div id="rtm-settings-tabs">';
									$sub_tabs = $this->settings_sub_tabs();
									RTMediaFormHandler::rtForm_settings_tabs_content( $page, $sub_tabs );
									echo '</div>';
								} else {
									do_settings_sections( $page );
								}*/
								?>
								<div class="rtm-button-container bottom">
									<div class="rtm-social-links alignleft">
										<a href="http://twitter.com/rtcamp" class="twitter" target= "_blank"><span class="dashicons dashicons-twitter"></span></a>
										<a href="https://www.facebook.com/rtCamp.solutions" class="facebook" target="_blank"><span class="dashicons dashicons-facebook"></span></a>
										<a href="http://profiles.wordpress.org/rtcamp" class="wordpress" target= "_blank"><span class="dashicons dashicons-wordpress"></span></a>
										<a href="https://rtcamp.com/feed" class="rss" target="_blank"><span class="dashicons dashicons-rss"></span></a>
									</div>

									<input type="hidden" name="rtmedia-options-save" value="true">
									<input type="submit" class="my_aia_submit_button button button-primary button-big" value="<?php _e( 'Save Settings', 'buddypress-media' ); ?>">
								</div>
							</div>
						</form>
					<?php //endif; ?>
				</div>

				<div class="metabox-holder bp-media-metabox-holder rtm-sidebar">
					<?php //$this->admin_sidebar(); ?>
				</div>

			</div>

		</div><!-- .bp-media-admin -->
