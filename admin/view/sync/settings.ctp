			<div class="clearfix rtm-vertical-tabs rtm-admin-tab-container rtm-settings-tab-container">
				<ul class="rtm-tabs my-aia-tabs">
					<li class="active">
						<a id="tab-rtmedia-display" title="Algemeen Settings" href="#my-aia-content-common" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>SugarCRM instellingen</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-display" title="SugarCRM Settings" href="#my-aia-content-sugarcrm" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>SugarCRM instellingen</span>
						</a>
					</li>
					<li class="">
						<a id="tab-rtmedia-display" title="Manyware Settings" href="#my-aia-content-manyware" class="rtmedia-tab-title display">
							<i class="dashicons-desktop dashicons rtmicon"></i><span>Manyware instellingen</span>
						</a>
					</li>
				</ul>

				<div class="tabs-content rtm-tabs-content my-aia-tabs-content">
					<div class="rtm-content active" id="my-aia-content-sugarcrm">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("Algemeen","my-aia"); ?></h3>
							<p>
								In het linker menu kunnen de verschillende instellingen voor de SugarCRM en Manyware (+evt overige) koppelingen worden ingesteld.
								<br>In principe is het uitgangspunt dat alles vanuit AIA Office naar Mijn AIA automatisch wordt geupload en de andere kant op alleen na goedkeuring
								van een medewerker, zodat niet de belangrijke gegevens van deelnemers verdwijnen.								
							</p>
						</div>
					</div>
					
					<div class="rtm-content active" id="my-aia-content-sugarcrm">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("SugarCRM Settings","my-aia"); ?></h3>
							<table class="form-table" id="conditions">
							<tbody>
							<tr>
								<th><p>SugarCRM url</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('sugar_url',array('placeholder'=>__('https://sugar.ath..'),'size'=>120)); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												De URL van de SugarCRM installatie. Er wordt van de soap interface (&lt;sugarurl&gt;/soap.php) gebruik gemaakt.
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><p>Sugar User (Admin).</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('sugar_user',array('placeholder'=>__('admin'))); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												Login username van de SugarCRM cron user (waarschijnlijk user:sugar). Zorg dat deze gebruiker genoeg privileges heeft.
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><p>Sugar Password (Admin).</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('sugar_user_password',array('type'=>'password')); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												Login password van de SugarCRM cron user.
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							</tbody>
							</table>
						</div>
					</div>
					
					<div class="rtm-content" id="my-aia-content-manyware">				
						<div class="rtm-option-wrapper">
							<h3 class="rtm-option-title"><?= __("Manyware Settings","my-aia"); ?></h3>
							<table class="form-table" id="conditions">
							<tbody>
							<tr>
								<th><p>Manyware url (Soap Endpoint)</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('manyware_url',array('placeholder'=>__('https://soap.man...'),'size'=>120)); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												De URL (SOAP endpoint) van de Manyware installatie. Er wordt van de soap interface gebruik gemaakt.
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><p>Manyware cClientLogin.</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('manyware_c_client_login',array('placeholder'=>__('AGAP'),'size'=>120)); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												cClientLogin (Manyware Client)
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><p>Manyware cClientPass.</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('manyware_c_client_password',array('placeholder'=>__(''),'size'=>120)); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												cClientPassword (Manyware Client)
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><p>Manyware User (Admin).</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('manyware_user',array('placeholder'=>__('Admin'))); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												Login user van de Manyware user. Zorg dat deze gebruiker genoeg privileges heeft.
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><p>Manyware ser password.</th>
								<td>
									<fieldset>
										<span class="rtm-field-wrap">
											<?= $this->Html->input('manyware_password',array('type'=>'password')); ?>
										</span>

										<span class="rtm-tooltip">
											<i class="dashicons dashicons-info rtmicon"></i>
											<span class="rtm-tip">
												Login password van de Manyware user. Zorg dat deze gebruiker genoeg privileges heeft.
											</span>
										</span>
									</fieldset>
								</td>
							</tr>
							</tbody>
							</table>
						</div>
					</div>

					<div class="rtm-content hidden" id="my-aia-content-other">				
						<div class="rtm-option-wrapper">
						</div>
					</div><!--//rtm-content-->
				
			</div>				
		</div>
			