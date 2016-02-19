		<div class="clearfix">
			<h3>Overzicht van aanwezige hooks</h3>
			<table class="form-table" id="conditions">
			<tbody>
			<tr>
				<th>
					<ul class="my-aia-hook-list">
					<?php foreach ($data['hooks'] as $hook_name=>$children): ?>
						<li class="hook editable">
							<div class="panel">
								<div class="panel-heading"><?= $this->Html->link($hook_name, array('action'=>'index', 'hook_name'=>$hook_name)); ?></div>
								<div class="panel-body hidden">
									<ul class="hook-list">
									<?php foreach ($children as $child): ?>
										<li><a href="#"><?= $child; ?></a></li>
									<?php endforeach; ?>
									</ul>
								</div>
							</div>							
						</li>
					<?php endforeach; ?>		
					</ul>
				</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap">
							<input type="text" name="hook_description" value="" placeholder="Naam">
						</span>

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