
			<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
				<ul class="rtm-tabs">
					<li class="active">
						<a id="tab-rtmedia-display" title="Display" href="#rtmedia-display" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>Display</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-bp" title="rtMedia BuddyPress" href="#rtmedia-bp" class="rtmedia-tab-title buddypress">
							<i class="dashicons-groups dashicons rtmicon"></i><span>BuddyPress</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-types" title="rtMedia Types" href="#rtmedia-types" class="rtmedia-tab-title types">
							<i class="dashicons-editor-video dashicons rtmicon"></i><span>Types</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-sizes" title="rtMedia Formaten" href="#rtmedia-sizes" class="rtmedia-tab-title media-sizes">
							<i class="dashicons-editor-expand dashicons rtmicon"></i><span>Media Sizes</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-privacy" title="rtMedia Privacy" href="#rtmedia-privacy" class="rtmedia-tab-title privacy">
							<i class="dashicons-lock dashicons rtmicon"></i><span>Privacy</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-custom-css-settings" title="rtMedia aangepaste CSS" href="#rtmedia-custom-css-settings" class="rtmedia-tab-title aangepaste-css">
							<i class="dashicons-clipboard dashicons rtmicon"></i><span>Aangepaste CSS</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-general" title="Andere instellingen" href="#rtmedia-general" class="rtmedia-tab-title andere-instellingen">
							<i class="dashicons-admin-tools dashicons rtmicon"></i><span>Andere instellingen</span>
						</a>
					</li>
				</ul>

				<div class="tabs-content rtm-tabs-content">
					<div class="rtm-content active" id="rtmedia-display">				<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Single Media View</h3>
		<?php echo $this->element('show_hooks'); ?>
		<table class="form-table">
			<tbody><tr>
				<th>
					Allow user to comment on uploaded media									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-0" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-0" name="rtmedia-options[general_enableComments]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								This will display the comment form and comment listing on single media pages as well as inside lightbox (if lightbox is enabled).							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">List Media View</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Use lightbox to display media									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-1" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-1" name="rtmedia-options[general_enableLightbox]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								View single media in facebook style lightbox.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Number of media per page									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-0" name="rtmedia-options[general_perPageMedia]" class="rtm-form-number rtmedia-setting-text-box" value="10" min="1" type="number"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Number of media items you want to show per page on front end.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Media display pagination option									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-radio rtmedia-load-more-radio"><label for="rtm-form-radio-0"><input checked="checked" id="rtm-form-radio-0" name="rtmedia-options[general_display_media]" value="load_more" type="radio"> <strong>Meer laden</strong></label><label for="rtm-form-radio-1"><input id="rtm-form-radio-1" name="rtmedia-options[general_display_media]" value="pagination" type="radio"> <strong>Pagination</strong></label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Choose whether you want the load more button or pagination buttons.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Masonry View</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Inschakelen <a href="http://masonry.desandro.com/" target="_blank">Masonry</a> Cascading grid layout														</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox rtm_enable_masonry_view"><label for="rtm-form-checkbox-2" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-2" name="rtmedia-options[general_masonry_layout]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								If you enable masonry view, it is advisable to <a href="http://mijn.athletesinaction.local/wp-admin/update.php?action=install-plugin&amp;plugin=regenerate-thumbnails&amp;_wpnonce=1b837c3563">regenerate thumbnail</a> for masonry view.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

					<div style="" class="rtm-message rtm-notice"><p>You might need to <a id="rtm-masonry-change-thumbnail-info" href="http://mijn.athletesinaction.local/wp-admin/admin.php?page=rtmedia-settings#rtmedia-sizes">change thumbnail size</a> and uncheck the crop box for thumbnails.</p>
<p>To set gallery for fixed width, set image height to 0 and width as per your requirement and vice-versa.</p>
</div>				</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Directe upload</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Directe upload activeren									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-3" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-3" name="rtmedia-options[general_direct_upload]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Upload media van zodra deze geselecteerd hebt							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
				</div><div class="rtm-content hide" id="rtmedia-bp">				<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Integration With BuddyPress Features</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Enable media in profile									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-4" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-4" name="rtmedia-options[buddypress_enableOnProfile]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Sta media toe in BuddyPress profiel							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Enable media in group									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-5" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-5" name="rtmedia-options[buddypress_enableOnGroup]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Sta media toe in BuddyPress groepen							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Allow upload from activity stream									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtmedia-bp-enable-activity" class="switch"><input checked="checked" data-toggle="switch" id="rtmedia-bp-enable-activity" name="rtmedia-options[buddypress_enableOnActivity]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Allow upload using status update box present on activity stream page							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Number of media items to show in activity stream									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-1" name="rtmedia-options[buddypress_limitOnActivity]" class="rtm-form-number rtmedia-setting-text-box rtmedia-bp-activity-setting" value="0" min="0" type="number"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								With bulk uploads activity, the stream may get flooded. You can control the maximum number of media items or files per activity. This limit will not affect the actual number of uploads. This is only for display. <em>0</em> means unlimited.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Enable media notification									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-6" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-6" name="rtmedia-options[buddypress_enableNotification]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								This will enable notifications to media authors for media likes and comments.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Create activity for media likes									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-7" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-7" name="rtmedia-options[buddypress_mediaLikeActivity]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Enabling this setting will create BuddyPress activity for media likes.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Create activity for media comments									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-8" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-8" name="rtmedia-options[buddypress_mediaCommentActivity]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Enabling this setting will create BuddyPress activity for media comments.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Album Settings</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Organize media into albums									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtmedia-album-enable" class="switch"><input checked="checked" data-toggle="switch" id="rtmedia-album-enable" name="rtmedia-options[general_enableAlbums]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								This will add 'album' tab to BuddyPress profile and group depending on the ^above^ settings.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
				</div><div class="rtm-content hide" id="rtmedia-types">		<div class="rtm-option-wrapper">
			
			<h3 class="rtm-option-title">
				Media Types Settings			</h3>

			<table class="form-table">

				
				<tbody><tr>
					<th><strong>Mediatype</strong></th>

					<th>

						<span class="rtm-tooltip bottom">
							<strong class="rtm-title">Upload toestaan</strong>
							<span class="rtm-tip-top">
								Stelt u in staat om een bepaald media type in uw bericht toe te voegen							</span>
						</span>
					</th>

					<th>

						<span class="rtm-tooltip bottom">
							<strong class="rtm-title">Zet als uitgelicht</strong>
							<span class="rtm-tip-top">
								Place a specific media as a featured content on the post.							</span>
						</span>
					</th>

									</tr>

				
						<tr>
							<td>
								Foto									<span class="rtm-tooltip rtm-extensions">
										<i class="dashicons dashicons-info rtmicon"></i>
										<span class="rtm-tip">
											<strong>Bestands-extensies</strong><br>
											<hr>
											jpg, jpeg, png, gif										</span>
									</span>
																</td>

							<td>
								<span class="rtm-field-wrap">
									<span class="rtm-form-checkbox"><label for="rtm-form-checkbox-9" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-9" name="rtmedia-options[allowedTypes_photo_enabled]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
							</td>

							<td>
								<span class="rtm-form-checkbox"><label for="rtm-form-checkbox-10" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-10" name="rtmedia-options[allowedTypes_photo_featured]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span>							</td>

													</tr>

						
						
						<tr>
							<td>
								Video									<span class="rtm-tooltip rtm-extensions">
										<i class="dashicons dashicons-info rtmicon"></i>
										<span class="rtm-tip">
											<strong>Bestands-extensies</strong><br>
											<hr>
											mp4										</span>
									</span>
																</td>

							<td>
								<span class="rtm-field-wrap">
									<span class="rtm-form-checkbox"><label for="rtm-form-checkbox-11" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-11" name="rtmedia-options[allowedTypes_video_enabled]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
							</td>

							<td>
								<span class="rtm-form-checkbox"><label for="rtm-form-checkbox-12" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-12" name="rtmedia-options[allowedTypes_video_featured]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span>							</td>

													</tr>

						
						
						<tr>
							<td>
								Muziek									<span class="rtm-tooltip rtm-extensions">
										<i class="dashicons dashicons-info rtmicon"></i>
										<span class="rtm-tip">
											<strong>Bestands-extensies</strong><br>
											<hr>
											mp3										</span>
									</span>
																</td>

							<td>
								<span class="rtm-field-wrap">
									<span class="rtm-form-checkbox"><label for="rtm-form-checkbox-13" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-13" name="rtmedia-options[allowedTypes_music_enabled]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
							</td>

							<td>
								<span class="rtm-form-checkbox"><label for="rtm-form-checkbox-14" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-14" name="rtmedia-options[allowedTypes_music_featured]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span>							</td>

													</tr>

						
									</tbody></table>
		</div>
		</div><div class="rtm-content hide" id="rtmedia-sizes">
		<div class="rtm-option-wrapper rtm-img-size-setting">
			<h3 class="rtm-option-title">
				Media Size Settings			</h3>

			<table class="form-table">
				<tbody><tr>
					<th><strong>Categorie</strong></th>
					<th><strong>Entiteit</strong></th>
					<th><strong>Breedte</strong></th>
					<th><strong>Hoogte</strong></th>
					<th><strong>Crop</strong></th>
				</tr>

										<tr>
															<td class="rtm-row-title" rowspan="3">
									Photo								</td>
															<td>
								Thumbnail							</td>
							<td><input id="rtm-form-number-2" name="rtmedia-options[defaultSizes_photo_thumbnail_width]" class="rtm-form-number small-text large-offset-1" value="150" type="number"></td><td><input id="rtm-form-number-3" name="rtmedia-options[defaultSizes_photo_thumbnail_height]" class="rtm-form-number small-text large-offset-1" value="150" type="number"></td><td><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-15" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-15" name="rtmedia-options[defaultSizes_photo_thumbnail_crop]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></td>						</tr>
												<tr>
														<td>
								Medium							</td>
							<td><input id="rtm-form-number-4" name="rtmedia-options[defaultSizes_photo_medium_width]" class="rtm-form-number small-text large-offset-1" value="320" type="number"></td><td><input id="rtm-form-number-5" name="rtmedia-options[defaultSizes_photo_medium_height]" class="rtm-form-number small-text large-offset-1" value="240" type="number"></td><td><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-16" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-16" name="rtmedia-options[defaultSizes_photo_medium_crop]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></td>						</tr>
												<tr>
														<td>
								Large							</td>
							<td><input id="rtm-form-number-6" name="rtmedia-options[defaultSizes_photo_large_width]" class="rtm-form-number small-text large-offset-1" value="800" type="number"></td><td><input id="rtm-form-number-7" name="rtmedia-options[defaultSizes_photo_large_height]" class="rtm-form-number small-text large-offset-1" value="0" type="number"></td><td><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-17" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-17" name="rtmedia-options[defaultSizes_photo_large_crop]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></td>						</tr>
												<tr>
															<td class="rtm-row-title" rowspan="2">
									Video								</td>
															<td>
								ActivityPlayer							</td>
							<td><input id="rtm-form-number-8" name="rtmedia-options[defaultSizes_video_activityPlayer_width]" class="rtm-form-number small-text large-offset-1" value="320" type="number"></td><td><input id="rtm-form-number-9" name="rtmedia-options[defaultSizes_video_activityPlayer_height]" class="rtm-form-number small-text large-offset-1" value="240" type="number"></td>						</tr>
												<tr>
														<td>
								SinglePlayer							</td>
							<td><input id="rtm-form-number-10" name="rtmedia-options[defaultSizes_video_singlePlayer_width]" class="rtm-form-number small-text large-offset-1" value="640" type="number"></td><td><input id="rtm-form-number-11" name="rtmedia-options[defaultSizes_video_singlePlayer_height]" class="rtm-form-number small-text large-offset-1" value="480" type="number"></td>						</tr>
												<tr>
															<td class="rtm-row-title" rowspan="2">
									Music								</td>
															<td>
								ActivityPlayer							</td>
							<td colspan="3"><input id="rtm-form-number-12" name="rtmedia-options[defaultSizes_music_activityPlayer_width]" class="rtm-form-number small-text large-offset-1" value="320" type="number"></td>						</tr>
												<tr>
														<td>
								SinglePlayer							</td>
							<td><input id="rtm-form-number-13" name="rtmedia-options[defaultSizes_music_singlePlayer_width]" class="rtm-form-number small-text large-offset-1" value="640" type="number"></td>						</tr>
												<tr>
															<td class="rtm-row-title" rowspan="1">
									Featured								</td>
															<td>
								Default							</td>
							<td><input id="rtm-form-number-14" name="rtmedia-options[defaultSizes_featured_default_width]" class="rtm-form-number small-text large-offset-1" value="100" type="number"></td><td><input id="rtm-form-number-15" name="rtmedia-options[defaultSizes_featured_default_height]" class="rtm-form-number small-text large-offset-1" value="100" type="number"></td><td><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-18" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-18" name="rtmedia-options[defaultSizes_featured_default_crop]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></td>						</tr>
									</tbody></table>

		</div>

		
		<div class="rtm-option-wrapper">
					<h3 class="rtm-option-title">Image Quality</h3>
					
		<table class="form-table">
			<tbody><tr>
				<th>
					JPEG/JPG image quality (1-100)									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-16" name="rtmedia-options[general_jpeg_image_quality]" class="rtm-form-number rtmedia-setting-text-box" value="90" min="1" max="100" type="number"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Enter JPEG/JPG Image Quality. Minimum value is 1. 100 is original quality.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

				</div>

		</div><div class="rtm-content hide" id="rtmedia-privacy">				<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Privacy Settings</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Privacy inschakelen									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtmedia-privacy-enable" class="switch"><input checked="checked" data-toggle="switch" id="rtmedia-privacy-enable" name="rtmedia-options[privacy_enabled]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Enable privacy in rtMedia							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table style="" class="form-table" data-depends="privacy_enabled">
			<tbody><tr>
				<th>
					Default privacy									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-radio"><label for="rtm-form-radio-2"><input id="rtm-form-radio-2" name="rtmedia-options[privacy_default]" value="60" type="radio"> Prive - Enkel zichtbaar voor de gebruiker</label><label for="rtm-form-radio-3"><input id="rtm-form-radio-3" name="rtmedia-options[privacy_default]" value="40" type="radio"> Vrienden - Zichtbaar voor vrienden</label><label for="rtm-form-radio-4"><input checked="checked" id="rtm-form-radio-4" name="rtmedia-options[privacy_default]" value="20" type="radio"> Ingelogde gebruikers - Zichtbaar voor geregistreerde gebruikers</label><label for="rtm-form-radio-5"><input id="rtm-form-radio-5" name="rtmedia-options[privacy_default]" value="0" type="radio"> Publiek - Zichtbaar voor de hele wereld</label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Set default privacy for media							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table style="" class="form-table" data-depends="privacy_enabled">
			<tbody><tr>
				<th>
					Allow users to set privacy for their content														</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-19" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-19" name="rtmedia-options[privacy_userOverride]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								If you choose this, users will be able to change privacy of their own uploads.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

					<div class="rtm-message rtm-notice"><p>For group uploads, BuddyPress groups privacy is used.</p>
</div>				</div>
				</div><div class="rtm-content hide" id="rtmedia-custom-css-settings">				<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Custom CSS settings</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					rtMedia standard opmaak stijl									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtmedia-disable-styles" class="switch"><input checked="checked" data-toggle="switch" id="rtmedia-disable-styles" name="rtmedia-options[styles_enabled]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Load default rtMedia styles. You need to write your own style for rtMedia if you disable it.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table class="form-table">
			<tbody><tr>
				<th>
					Plak je CSS code									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><textarea id="rtmedia-custom-css" name="rtmedia-options[styles_custom]" class="rtm-form-textarea"></textarea></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Custom rtMedia CSS container							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
				</div><div class="rtm-content hide" id="rtmedia-general">				<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Admin instellingen</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Admin bar menu integratie.									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-20" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-20" name="rtmedia-options[general_showAdminMenu]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Add rtMedia menu to WordPress admin bar for easy access to settings and moderation page (if enabled).							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">API Settings</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Enable JSON API														</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-21" class="switch"><input checked="checked" data-toggle="switch" id="rtm-form-checkbox-21" name="rtmedia-options[rtmedia_enable_api]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								This will allow handling API requests for rtMedia sent through any mobile app.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

					<div style="" class="rtm-message rtm-notice"><p>You can refer to the API document from <a href="http://docs.rtcamp.com/rtmedia/developers/json-api.html">hier</a></p>
</div>				</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Miscellaneous</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Allow usage data tracking									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-22" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-22" name="rtmedia-options[general_AllowUserData]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								To make rtMedia better compatible with your sites, you can help the rtMedia team learn what themes and plugins you are using. No private information about your setup will be sent during tracking.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

						</div>
								<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title">Footer Link</h3>
		
		<table class="form-table">
			<tbody><tr>
				<th>
					Add a link to rtMedia in footer									</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-23" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-23" name="rtmedia-options[rtmedia_add_linkback]" value="1" type="checkbox"><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Help us promote rtMedia.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

		
		<table style="display: none;" class="form-table" data-depends="rtmedia_add_linkback">
			<tbody><tr>
				<th>
					Also add my affiliate-id to rtMedia footer link														</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-text-0" name="rtmedia-options[rtmedia_affiliate_id]" class="rtm-form-text" value="" type="text"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Add your affiliate-id along with footer link and get rewarded by our affiliation program.							</span>
						</span>
					</fieldset>
				</td>
			</tr>
		</tbody></table>

					<div class="rtm-message rtm-notice"><p>Signup for rtMedia affiliate program <a href="https://rtcamp.com/affiliates">hier</a></p>
</div>				</div>
				</div>				</div>

			</div>
			