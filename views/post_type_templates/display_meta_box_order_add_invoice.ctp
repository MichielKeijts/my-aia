<div class='<?php if ($this->ORDER->invoice) echo "hidden"; ?>' id='select_invoice'>
	<p><?= __('Er bestaat nog geen factuur voor deze order. Klik hieronder om de order definitief te maken en de factuur op te maken.', 'my-aia'); ?></p>
	<select name="invoice_template">
		<?php
			foreach ($this->get_invoice_templates() as $post) {
				echo sprintf('<option value="%s" %s>%s</option>', $post->ID, '', $post->post_title);
			}
		?>
	</select>
	<button class='btn btn-info' id='create_invoice'><?= __('Factuur Aanmaken','my-aia'); ?></button>
</div>
<div class='<?php if (!$this->ORDER->invoice) echo "hidden"; ?>' id='show_invoice'>
	<p>Factuur (<span id='invoice_number'><?= $this->ORDER->invoice->post_name; ?></span>)</p>
	<p><a href='<?= $this->ORDER->invoice ? $this->ORDER->invoice->pdf_link():'#'; ?>' class='pdf-open'>PDF</a></p>
	<p><a href='#' id='reminder' data-id='<?= $this->ORDER->invoice->ID; ?>' class='reminder-mail'>Stuur herinnering per mail aan gebruiker.</a></p>
</div>

