			<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
				<ul class="rtm-tabs my-aia-tabs">
					<li class="active">
						<a id="tab-rtmedia-display" title="Algemeen Settings" href="#my-aia-content-common" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>SugarCRM Sync</span>
						</a>
					</li>
				</ul>

				<div class="tabs-content rtm-tabs-content my-aia-tabs-content">
					<div class="rtm-content active" id="my-aia-content-sugarcrm">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("Algemeen","my-aia"); ?></h3>
							<p>
								Deze paginalaad middels AJAX calls steeds een nieuwe sync regel op, totdat het einde is bereikt.
								
								<button onclick="javascript:get_sync()">Go!</button>
							</p>
							<p>
								<fieldset>
									<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-123" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-123" name="user_sync" value="1" type="checkbox" checked><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
									<span class="rtm-tooltip">
										<i class="dashicons dashicons-info rtmicon"></i>
										<span class="rtm-tip">
											User Synchronisatie
										</span>
									</span>
								</fieldset>
								<fieldset>
									<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-1234" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-1234" name="event_sync" value="1" type="checkbox" checked><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
									<span class="rtm-tooltip">
										<i class="dashicons dashicons-info rtmicon"></i>
										<span class="rtm-tip">
											Event Synchronisatie
										</span>
									</span>
								</fieldset>
								<fieldset>
									<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-1235" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-1235" name="registration_sync" value="1" type="checkbox" checked><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
									<span class="rtm-tooltip">
										<i class="dashicons dashicons-info rtmicon"></i>
										<span class="rtm-tip">
											Booking Synchronisatie
										</span>
									</span>
								</fieldset>
								
							</p>
						</div>
					</div>
					
					<div class="rtm-content active" id="my-aia-content-sugarcrm">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("SugarCRM Sync Log","my-aia"); ?></h3>
							<table class="form-table" id="conditions">
							<tbody>
							<tr class="sync_rule_display">
								<th><p></p></th>
								<td>
									<fieldset>
										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							
							</tbody>
							</table>
						</div>
					</div>				
			</div>				
		</div>


<script>
	var sync_counter=0;
	
	get_sync = function() {
		// the actual query
		jQuery.post(
			ajaxurl, 
			{
				'action': 'my_aia_admin_sync_start',
				'controller': 'sync',
				'type': jQuery('#sync_fields').data('type'),
				user_sync: jQuery('input[name=user_sync]').is(':checked')?1:0,
				event_sync: jQuery('input[name=event_sync]').is(':checked')?1:0,
				registration_sync: jQuery('input[name=registration_sync]').is(':checked')?1:0
			}, 
			function(response){
				if (response.sync_profiles_sugar_to_wordpress) {
					var el = jQuery('tr.sync_rule_display:first').clone(true);
					el.find('p').text("# " + sync_counter + "	Sync done. Aantal items: #"+response.count+"	Datum: ("+response.sync_profiles_sugar_to_wordpress+" "+response.sync_events_sugar_to_wordpress+" "+response.sync_registrations_sugar_to_wordpress+")");
					el.insertAfter(jQuery('tr.sync_rule_display:last'));
					sync_counter=sync_counter+1;
					
					if (response.count) {
						// good response, and non zero response, continue
						setTimeout(get_sync, 100);
					}					
				}
			})
			.fail(function(){
				var el = jQuery('tr.sync_rule_display:first').clone(true);
				el.find('p').text("# ERROR .. retrying..");
				el.insertAfter(jQuery('tr.sync_rule_display:last'));
				sync_counter=sync_counter+1;
				
				// good response, and non zero response, continue
				//setTimeout(get_sync, 100);
				
			});
	}
	
</script>
			