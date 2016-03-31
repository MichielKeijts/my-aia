		<div class="row">
			<div class="col-md-12">
				<div class="col-md-3">
					<div class="panel panel-warning">
						<div class="panel-heading"><h3><?= __("Wordpress Fields",'my-aia'); ?></h3></div>
						<div class="panel-body">
							<ul id="wordpress_fields" class="field_container internal_fields">
								<?php foreach ($internal_fields as $field): ?>
								<li class="draggable field ui-draggable internal"><i class="glyphicon glyphicon-menu-hamburger"></i><?= $field; ?></li>
								<?php endforeach; ?>
							</ul>							
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-success">
						<div class="panel-heading"><h3><?= __("Sync Connection",'my-aia'); ?> <button type="button" class="button button-primary" id="add_sync_rule">Nieuwe Sync Regel</button></h3></div>
						<div class="panel-body">
							<ul id="sync_fields" data-type="profile_sync">
								<?php if (is_array($data)) foreach ($data as $element): ?>
								<li class="sync_field">
									<div class="remove"><i class="glyphicon glyphicon-remove-circle"></i></div>
									<div class="droppable droppable_field internal"><?= $element['internal_field']; ?></div>
									<div class="field_connection"><i class="glyphicon glyphicon-refresh"></i> in sync met </div>
									<div class="droppable droppable_field external right"><?= $element['external_field']; ?></div>
								</li>
								<?php endforeach; // ALWAYS add empty below: ?>
								<li class="sync_field">
									<div class="remove"><i class="glyphicon glyphicon-remove-circle"></i></div>
									<div class="droppable droppable_field internal"></div>
									<div class="field_connection"><i class="glyphicon glyphicon-refresh"></i> in sync met </div>
									<div class="droppable droppable_field external right"></div>
								</li>
							</ul>							
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="panel panel-info">
						<div class="panel-heading"><h3><?= __("Sugar Fields",'my-aia'); ?></h3></div>
						<div class="panel-body">
							<ul id="sugar_fields" class="field_container external_fields">
								<?php foreach ($external_fields as $field): ?>
								<li class="draggable field ui-draggable external"><i class="glyphicon glyphicon-menu-hamburger"></i><?= $field; ?></li>
								<?php endforeach; ?>
							</ul>							
						</div>
					</div>
				</div>
			</div>	
		</div>

		