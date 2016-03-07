			<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
				<ul class="rtm-tabs my-aia-tabs">
					<li class="active">
						<a id="tab-rtmedia-display" title="Selecteer een Hook" href="#my-aia-content-hook-select" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>(1) Selecteer Hook</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-display" title="Definiëer voorwaarden" href="#my-aia-content-criteria" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>(2) Criteria</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-display" title="Conditionele Acties" href="#my-aia-content-conditional-actions" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>(3) Conditionele Acties</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-display" title="Acties" href="#my-aia-content-actions" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>(4) Acties</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-display" title="Overige" href="#my-aia-content-other" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>(5) Overige</span>
						</a>
					</li>
				</ul>

				<div class="tabs-content rtm-tabs-content my-aia-tabs-content">
					<div class="rtm-content active" id="my-aia-content-hook-select">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("Selecteer een hook waarop getriggered moet worden","my-aia"); ?></h3>
							<table class="form-table" id="conditions">
							<tbody>
							<tr>
								<th><p>Naam (Omschrijving voor herkenbaarheid)</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('id', array('type'=>'hidden')); ?>
											<?= $this->Html->input('description',array('placeholder'=>__('Naam'))); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												Geef hier een naam aan de hook, puur ter omschrijving van de functie
											</span>
										</span>
										</fieldset>
									</div>
								</td>
							</tr>
							<tr>
								<th><p>Selecteer de Wordpress hook waarop getriggered dient te woren.</th>
								<td>
									<div class="panel panel-default">
										<div class="panel-heading">
											Mogelijke hooks
										</div>
										<fieldset>
											<span class="rtm-field-wrap">
												<?= $this->Html->select('hook_name',array('placeholder'=>__('Naam'), 'options'=>$hooks)); ?>
											</span>

											<span class="rtm-tooltip">
												<i class="dashicons dashicons-info rtmicon"></i>
												<span class="rtm-tip">
													Selecteer de hook (lijst toont mogelijke hooks)<br><Br>Wordpress kent veel meer hooks, maar deze hebben vooralsnog geen functionaliteit in deze plugin.
												</span>
											</span>
										</fieldset>
									</div>
								</td>
							</tr>
							</tbody>
							</table>
						</div>
					</div>
					
					<div class="rtm-content hidden" id="my-aia-content-criteria">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("Select Conditions","my-aia"); ?></h3>
							<button class='button button-primary button-big' id='condition-add-folder'><i class='glyphicon glyphicon-plus-sign'></i> Groep Toevoegen</button>
							<button class='button button-primary button-big' id='condition-add'><i class='glyphicon glyphicon-plus-sign'></i> Voorwaarde Toevoegen</button>
							<button class='button button-cancel button-big' id='condition-delete'><i class='glyphicon glyphicon-remove'></i> Verwijderen</button>
							
							<table class="form-table" id="conditions">
							<tbody>
							<tr>
								<th><div id='conditions_tree' class="tree"></div></th>
								<td>
									<div class="panel panel-default">
										<div class="panel-heading">
											Criteria
										</div>
										<div class="panel-body hidden" id='condition_folder_edit'>
											<fieldset>
												<span class="rtm-field-wrap">
													<select name="folder">
														<option value="OR">OR operator</option>
														<option value="AND" selected>AND operator (default)</option>
													</select>
												</span>
												
												<span class="rtm-tooltip">
													<i class="dashicons dashicons-info rtmicon"></i>
													<span class="rtm-tip">
														Selecteer de operator die op de vergelijking wordt losgelaten. Bij AND worden moeten alle onderliggende
														voorwaarden WAAR zijn voordat verder wordt gegaan. Bij OR moet een van de onderliggende voorwaarden WAAR zijn.
														<br>
														Bijv: post_type is partner AND vandaag is in mei
													</span>
												</span>
											</fieldset>
										</div>
										<div class="panel-body" id='condition_edit'>
											<fieldset>
												(Expressie1)
												<span class="rtm-field-wrap">
													<span class="rtm-form-select">
														<select name='function1'>
															<?= $this->element('_static_functions'); ?>
														</select>
													</span>
													<span class="rtm-tooltip">
														<i class="dashicons dashicons-info rtmicon"></i>
														<span class="rtm-tip">
															(optioneel) Selecteer een functie die uitgevoerd wordt op het eerste veld. Bijv: de datum van vandaag
														</span>
													</span>
												</span>
												
												<span class="rtm-field-wrap">
													<span class="rtm-form-text">
														<input type='text' name='field1'>
													</span>
													<span class="rtm-tooltip">
														<i class="dashicons dashicons-info rtmicon"></i>
														<span class="rtm-tip">
															(optioneel) Vul een constante waarde in, of selecteer een variabele die met de hook te maken heeft. Bijvoorbeeld: post_name als naam van de post
														</span>
													</span>
												</span>												
											</fieldset>								

											<fieldset>
												<span class="rtm-field-wrap">
													<select name="operator">
														<option value="!="> != Ongelijk </option>
														<option value=">="> >= Groter dan of gelijk aan </option>
														<option value="<"> <= Kleiner dan of gelijk aan </option>
														<option value=">"> > Groter dan </option>
														<option value="<"> < Kleiner dan </option>
														<option value="==" selected> == Gelijk (default) </option>
													</select>
												</span>
												
												<span class="rtm-tooltip">
													<i class="dashicons dashicons-info rtmicon"></i>
													<span class="rtm-tip">
														Selecteer de operator die op de vergelijking wordt losgelaten
													</span>
												</span>
											</fieldset>

											<fieldset>
												(Expressie2)
												<span class="rtm-field-wrap">
													<span class="rtm-form-select">
														<select name='function2'>
															<?= $this->element('_static_functions'); ?>
														</select>
													</span>
													<span class="rtm-tooltip">
														<i class="dashicons dashicons-info rtmicon"></i>
														<span class="rtm-tip">
															(optioneel) Selecteer een functie die uitgevoerd wordt op het eerste veld. Bijv: de datum van vandaag
														</span>
													</span>
												</span>

												<span class="rtm-field-wrap">
													<span class="rtm-form-text">
														<input type='text' name='field2'>
													</span>
													<span class="rtm-tooltip">
														<i class="dashicons dashicons-info rtmicon"></i>
														<span class="rtm-tip">
															(optioneel) Vul een constante waarde in, of selecteer een variabele die met de hook te maken heeft. Bijvoorbeeld: post_name als naam van de post
														</span>
													</span>
												</span>	
											</fieldset>

										</div>
									</div>									
								</td>
							</tr>
							</tbody>
						</table>
					<!--<div class="rtm-message rtm-notice"><p>Signup for rtMedia affiliate program <a href="https://rtcamp.com/affiliates">hier</a></p></div>-->
					</div>
					</div>
						
					<div class="rtm-content hidden" id="my-aia-content-conditional-actions">				
						<div class="rtm-option-wrapper">
						</div>
					</div><!--//rtm-content-->

					<div class="rtm-content hidden" id="my-aia-content-actions">				
						<div class="rtm-option-wrapper">
							
							<h3 class="rtm-option-title"><?= __("Selecteer Acties","my-aia"); ?></h3>
							<p> De onderstaande acties worden uitgevoerd in de volgorde zoals hieronder aangegeven, maar ze hebben directe invloed op elkaar. Maw: je kunt niet
							de ouput van de bovenliggende gebruiken in de onderliggende. </p>
							
							
							<div class="panel panel-default">
								<div class="panel-heading">
									<button class='button button-primary button-big' id='condition-add-folder'><i class='glyphicon glyphicon-plus-sign'></i> Actie Toevoegen</button>
									<button class='button button-cancel button-big' id='condition-delete'><i class='glyphicon glyphicon-remove'></i> Verwijderen</button>
								</div>
								<div class="panel-body">
									<ol class="list-group ui-sortable sortable">
									<?php foreach ($data['acties'] as $actie): ?>
										<li class="list-group-item" data-id='<?= $actie['id']; ?>'>
											<?= $this->Html->link($processflow['description'], array('action'=>'edit','id'=>$processflow['id'])); ?>
											<?= $this->Html->link(__('Delete'), array('action'=>'edit','id'=>$processflow['id']), array('class'=>'button right')); ?>
										</li>
									<?php endforeach; ?>
									</ol>
								</div>
								<div class="panel-footer"></div>
							</div>
							
							<div class="panel panel-default">
								<div class="panel-heading"><h3><?= __('Tags toevoegen'); ?></h3></div>
								<div class="panel-body">
				
									<div class="input-group input-group-lg">
										<span class="input-group-addon" id="sizing-addon1">Naam</span>
										<input type="text" class="form-control" placeholder="Naam" aria-describedby="sizing-addon2" name='to_name'>
									</div>
								</div>
								<div class="panel-footer">
									Een lijst met codes om te implementeren				
									<code>
										$user_name
										$user_email
										$form_email
									</code>
								</div>
							</div>
							
							<div class="panel panel-default">
								<div class="panel-heading"><h3>Email Opties</h3></div>
								<div class="panel-body">
									<div class="input-group input-group-lg">
										<span class="input-group-addon" id="sizing-addon1">@</span>
										<input type="text" class="form-control" placeholder="Email" aria-describedby="sizing-addon1" name='to_email'>
									</div>
									
									<div class="input-group input-group-lg">
										<span class="input-group-addon" id="sizing-addon1">Naam</span>
										<input type="text" class="form-control" placeholder="Naam" aria-describedby="sizing-addon2" name='to_name'>
									</div>
								</div>
								<div class="panel-footer">
									Een lijst met codes om te implementeren				
									<code>
										$user_name
										$user_email
										$form_email
									</code>
								</div>
							</div>
						</div>
					</div><!--//rtm-content-->

					<div class="rtm-content hidden" id="my-aia-content-other">				
						<div class="rtm-option-wrapper">
						</div>
					</div><!--//rtm-content-->
				
			</div>				
		</div>
	</div>
			