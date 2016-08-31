<?php
/*
 * Display the shopping cart widget. 
 * 
 * @todo add SESSION / COOKIE data in initialisation instead of an empty list
 */

?>
<div id="shopping-cart">
	<section class="buddypress-tiles">
		<div class="tile">
		<table class="shopping-cart">
			<thead>
				<tr>
					<td width="20%"><span class="name"></span></td>
					<td width="60%"><span class="name">omschrijving</span></td>
					<td width="7%"><span class="count">aantal</span></td>
					<td width="120"><span class="count">bedrag</span></td>
					<td width="7%"></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach (my_aia_order()->ORDER->order_items as $item): ?>
				<tr class="">
					<td><div class="product-image"><?= get_the_post_thumbnail($item->product_id, array('width'=>150, 'height'=>150)); ?></div></td>
					<td><span class="name"><?= $item->post_title; ?></span></td>
					<td><span class="count"><?= $item->count; ?></span></td>
					<td><span class="count">&euro; <?= number_format($item->get_product()->price * $item->count, 2, ',','.'); ?></span></td>
					<td><a href="#" data-id="0" class="button-remove-product">X</a></td>
				</tr>
				<?php endforeach; //$order_items ?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td colspan=2><span class="total_price"><?= __('TOTAAL','my-aia'); ?></span></td>
					<td><span class="total_price">&euro; <?= number_format(my_aia_order()->ORDER->total_order_price, 2, ',','.'); ?></span></td>
					<td></tD>
				</tr>
			</tfoot>
		</table>
		</div>
	</section>
	<form class='' enctype="multipart/form-data" method='POST' action='?'>
	<section class="buddypress-tiles">
		<div class="column-wrapper">
			<div class="column-2-1 column-sm-1">
				<div class="column-inner padding-left-25 tile">
					<h3><?= __('Verzend Adres','my-aia');?></h3>
					<div class="tab-display">
						<span class="text">
							<?= my_aia_order()->ORDER->shipping_name; ?><br>
							<?= my_aia_order()->ORDER->shipping_address; ?><br>
							<?= my_aia_order()->ORDER->shipping_postcode; ?> <?= my_aia_order()->ORDER->shipping_city; ?><br>
							<?= my_aia_order()->ORDER->shipping_country; ?><br>
						</span>
						<button class="button blauw change-address">Wijzig</button>
					</div>
					<div class="hidden tab-input">
						<?php my_aia_order()->get_attributes_form("shipping"); ?>
						<button class="button blauw display-address">Akkoord</button>
					</div>
				</div>
			</div>
			<div class="column-2-1 column-sm-1">
				<div class="column-inner padding-left-25 tile">
					<h3><?= __('Factuur Adres','my-aia');?></h3>
					<div class="tab-display">
						<span class="text">
							<?= my_aia_order()->ORDER->invoice_name; ?><br>
							<?= my_aia_order()->ORDER->invoice_address; ?><br>
							<?= my_aia_order()->ORDER->invoice_postcode; ?> <?= my_aia_order()->ORDER->invoice_city; ?><br>
							<?= my_aia_order()->ORDER->invoice_country; ?><br>
						</span>
						<button class="button blauw change-address">Wijzig</button>
					</div>
					<div class="hidden tab-input">
						<?php my_aia_order()->get_attributes_form("invoice"); ?>
						<button class="button blauw display-address">Akkoord</button>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section class="buddypress-tiles">
		<div class="column-wrapper">
			<div class="column-inner tile">
				<div class="padding-left-25">
					<h3><?= __('Aanvullende Afspraken','my-aia');?></h3>
					<ul>
						<li>Levering artikelen (indien van toepassing) binnen 10 dagen na bestelling.</li>
						<li>Niet goed, geld terug garantie op artikelen.</li>
						<li>Betalingswijze: iDeal.</li>						
					</ul>
					<input type="hidden" name='akkoord-voorwaarden' value='0'>
					<input type="checkbox" id="akkoord-voorwaarden" name='akkoord-voorwaarden' value='1' required="true">
					<label for="akkoord-voorwaarden">Ik ga akkoord met de <a href='#' title='Opent in nieuw venster'>algemene voorwaarden</a> van Athletes in Action</label>

				</div>
				<div class="buttons center">
		<?php if (gettype(filter_input(INPUT_GET, 'create')) == 'string'): ?>
			<a href="/shop" class="button white link_to_order" id="link_to_order"><?= __('Terug naar de winkel'); ?></a>
			<input type='hidden' name='_method' value='create'>
			<input type='submit' class="button white link_to_order" id="link_to_order" value='<?= __('Plaats bestelling'); ?>'>
		<?php endif;?>
		<?php if (filter_input(INPUT_POST, '_method') == 'create'): ?>
			<a href="?reset" class="button white link_to_order" id="link_to_order"><?= __('Bestelling Annuleren'); ?></a>
			<a href="?create" class="button white link_to_order" id="link_to_order"><?= __('Vorige Stap'); ?></a>
			<input type='hidden' name='_method' value='pay'>

			<a href='?make_payment&order_id=<?= my_aia_order()->ORDER->ID; ?>' class="button white link_to_order" id="link_to_pay"><?= __('Bevestigen & Betalen met iDeal'); ?></a>
		<?php endif;?>
				</div>
			</div>
		</div>
	</section>
		
	</form>
</div>