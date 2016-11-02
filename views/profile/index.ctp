		<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
			<ul class="rtm-tabs my-aia-tabs">
				<?php 
					$counter=1; $hook_name='Default';
					//foreach ($data['hooks'] as $hook_name=>$processflows): $counter++;
				?>
				<li class="<?= $counter==1?"active":""; ?>">
					<a id="tab-rtmedia-display" title="<?= $hook_name; ?>" href="#my-aia-content-<?= $hook_name; ?>" class="rtmedia-tab-title display">
						<i class="dashicons-desktop dashicons rtmicon"></i><span data-hook_name='<?= $hook_name; ?>'><?= __($hook_name); ?></span>
					</a>
				</li>
				<?php //endforeach; ?>
			</ul>

			<div class="tabs-content rtm-tabs-content my-aia-tabs-content">
				<div class="rtm-content <?= $counter==1?"active":"hidden"; ?>" id="my-aia-content-<?= $hook_name; ?>">
					<div class="panel panel-default">
						<div class="panel-heading"><h3><?= __('Proces flows verbonden aan','my-aia'), ' ', __($hook_name); ?></h3></div>
						<div class="panel-body">
							<table class="modification_table">
								<thead>
									<td>Approve?</td>
									<td>WP ID</td>
									<td>CRM ID</td>
									<td><?= __('Approved','my-aia'); ?></td>
									<td><?= __('Approved By','my-aia'); ?></td>
									<td><?= __('Done','my-aia'); ?></td>
									<td><?= __('Old Data','my-aia'); ?></td>
									<td><?= __('New Data','my-aia'); ?></td>
									<td><?= __('Date','my-aia'); ?></td>
								</thead>
								
								<tbody>
									<?php $counter=0;	foreach ($data['results'] as $result): $counter++; // list of to moderate fields flow 	?>
									<tr>
										<td>
											<fieldset>
												<span class="rtm-field-wrap">
													<span class="rtm-form-checkbox">
														<label for="rtm-form-checkbox-<?= $result->id; ?>" class="switch">
															<input name="approve_id_<?= $result->id; ?>" value="0" type="hidden">
															<input data-toggle="switch" id="rtm-form-checkbox-<?= $result->id; ?>" name="approve_id_<?= $result->id; ?>" value="1" type="checkbox">
															<span class="switch-label" data-on="Ja" data-off="Nee"></span>
															<span class="switch-handle"></span>
														</label>
													</span>
												</span>
												<p>
												<span class="rtm-tooltip">
													<i class="dashicons dashicons-info rtmicon"></i>
													<span class="rtm-tip">
														Approve -> Wijzigingen worden doorgevoerd in CRM (Sugar), anders wordt de oude data teruggezet
													</span>
												</span></p>
											</fieldset>
										</td>
										
										
										<td><a href="/wp-admin/user-edit.php?user_id=<?= $result->wp_id; ?>" target="_blank"><small><?= $result->wp_id; ?></small></a></td>
										<td><a href="https://sugar.athletesinaction.nl:777/index.php?action=DetailView&module=Contacts&record=<?= $result->crm_id; ?>" target="_blank"><small><?= $result->crm_id; ?></small></a></td>
										<td><?= $result->approved; ?></td>
										<td><?= $result->approved_by_name; ?></td>
										<td><?= $result->done; ?></td>
										<td><?= $result->readable_from; ?></td>
										<td><?= $result->readable_to; ?></td>
										<td><?= $result->created; ?></td>
									</tr>								
									<?php	endforeach; ?>
								</tbody>								
							</table>
							
						</div>
						<div class="panel-footer"><h3></h3></div>
					</div>
				</div>
	
			</div>				
		</div>		