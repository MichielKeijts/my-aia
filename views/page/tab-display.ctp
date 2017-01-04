


<div class="rtm-content active" id="my-aia-display">				
	<div class="rtm-option-wrapper">
		<h3 class="rtm-option-title">Standaard Instellingen</h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th>Highlighet Event ID</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-0" name="my_aia_options[featured_event_post_id]" class="rtm-form-number rtmedia-setting-text-box" type="text" value="<?= $my_aia_options['featured_event_post_id']; ?>"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Dit event wordt als eerste getoond in Mijn AIA (Post ID)
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	
	<div class="rtm-option-wrapper">
		<h3 class="rtm-option-title">Sugar Instellingen</h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th>Event Sugar Sync</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-124" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-124" name="my_aia_options[event_sugar_sync]" value="1" type="checkbox" <?= isset($my_aia_options['event_sugar_sync']) && $my_aia_options['event_sugar_sync']>0 ? 'checked' :''; ?>><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Activeer de sugar sync voor events (bij opslaan)
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>