


<div class="rtm-content hide" id="my-aia-webshop">				
	<div class="rtm-option-wrapper">
		<h3 class="rtm-option-title">Webshop instellingen</h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th>Verzendkosten</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-0" name="my_aia_options[webshop_verzendkosten]" class="rtm-form-number rtmedia-setting-text-box" type="text" value="<?= $my_aia_options['webshop_verzendkosten']; ?>"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Standaard verzendkosten bij een bestelling
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	
	
	<div class="rtm-option-wrapper">
		<h3 class="rtm-option-title">Mollie iDeal instellingen</h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th>iDeal Moll server</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-0" name="my_aia_options[mollie_key]" class="rtm-form-number rtmedia-setting-text-box" type="text" value="<?= $my_aia_options['mollie_key']; ?>"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								mollie_key op te vragen via mollie account. Gebruik test key of live key. De key die hier staat wordt gebruikt! 
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
			<tr>
				<th>Test Mode (niet wijzigen als je niet weet wat dit doet)</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><span class="rtm-form-checkbox"><label for="rtm-form-checkbox-123" class="switch"><input data-toggle="switch" id="rtm-form-checkbox-123" name="my_aia_options[mollie_test_mode]" value="1" type="checkbox" <?= $my_aia_options['mollie_test_mode']>0 ? 'checked' :''; ?>><span class="switch-label" data-on="On" data-off="Off"></span><span class="switch-handle"></span> </label></span></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								Activeer de test parameter. Dit betekent niet dat de shop in test mode staat
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
		
	</div>
	
	<div class="rtm-option-wrapper">
		<h3 class="rtm-option-title">Email Adressen</h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th>Orderbevestiging (inclusief factuur) sturen naar</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-0" name="my_aia_options[email_order_confirmation]" class="rtm-form-number rtmedia-setting-text-box" type="text" value="<?= $my_aia_options['email_order_confirmation']; ?>"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								De orderbevestiging met factuur wordt altijd naar het account emailadres gestuurd en ook met BCC naar dit adres
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
			<tr>
				<th>Kopie betaalbevestiging sturen naar</th>
				<td>
					<fieldset>
						<span class="rtm-field-wrap"><input id="rtm-form-number-0" name="my_aia_options[email_payment_confirmation]" class="rtm-form-number rtmedia-setting-text-box" type="text" value="<?= $my_aia_options['email_payment_confirmation']; ?>"></span>
						<span class="rtm-tooltip">
							<i class="dashicons dashicons-info rtmicon"></i>
							<span class="rtm-tip">
								De betaalbevestiging wordt altijd naar het account emailadres gestuurd en ook met BCC naar dit adres
							</span>
						</span>
					</fieldset>
				</td>
			</tr>
			</tbody>
		</table>
		
	</div>
</div>