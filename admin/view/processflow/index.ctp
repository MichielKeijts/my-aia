		<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
			<ul class="rtm-tabs my-aia-tabs">
				<?php 
					$counter=0;
					foreach ($data['hooks'] as $hook_name=>$processflows): $counter++;
				?>
				<li class="<?= $counter==1?"active":""; ?>">
					<a id="tab-rtmedia-display" title="<?= $hook_name; ?>" href="#my-aia-content-<?= $hook_name; ?>" class="rtmedia-tab-title display">
						<i class="dashicons-desktop dashicons rtmicon"></i><span data-hook_name='<?= $hook_name; ?>'><?= __($hook_name); ?></span>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>

			<div class="tabs-content rtm-tabs-content my-aia-tabs-content">
				<?php 
					$counter=0;
					foreach ($data['hooks'] as $hook_name=>$processflows): $counter++;
						// list of process flow in draggable order
				?>
				<div class="rtm-content <?= $counter==1?"active":"hidden"; ?>" id="my-aia-content-<?= $hook_name; ?>">
					<div class="panel panel-default">
						<div class="panel-heading"><h3><?= __('Proces flows verbonden aan','my-aia'), ' ', __($hook_name); ?></h3></div>
						<div class="panel-body">
							<ol class="list-group ui-sortable sortable">
							<?php foreach ($processflows as $processflow): ?>
								<li class="list-group-item" data-id='<?= $processflow['id']; ?>'>
									<?= $this->Html->link($processflow['description'], array('action'=>'edit','id'=>$processflow['id'])); ?>
									<?= $this->Html->link(__('Delete'), array('action'=>'edit','id'=>$processflow['id']), array('class'=>'button right')); ?>
								</li>
							<?php endforeach; ?>
							</ol>
						</div>
						<div class="panel-footer"><h3></h3></div>
					</div>
				</div>
				<?php endforeach; ?>				
			</div>				
		</div>		