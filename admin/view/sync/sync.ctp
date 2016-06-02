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
	jQuery(document).ready(function(){
		setTimeout("get_sync()", 100); //start
	});
	
	function get_sync() {
		// the actual query
		jQuery.post(
			ajaxurl, 
			{
				'action': 'my_aia_admin_sync_start',
				'controller': 'sync',
				'type': jQuery('#sync_fields').data('type'),
				'data':  {}
			}, 
			function(response){
				if (response.sync_profiles_sugar_to_wordpress) {
					if (response.count) {
						// good response, and non zero response, continue
						setTimeout("get_sync()", 100);
					}
					
					var el = jQuery('tr.sync_rule_display').clone(true);
					el.find('p').text("# " + sync_counter + "	Sync done. Aantal items: #"+response.count+"	Datum: ("+response.sync_profiles_sugar_to_wordpress+" "+response.sync_events_sugar_to_wordpress+" "+response.sync_registrations_sugar_to_wordpress+")");
					el.appendTo(jQuery('tr.sync_rule_display').parent());
					sync_counter=sync_counter+1;
				}
			}
		);
	}
	
</script>
			