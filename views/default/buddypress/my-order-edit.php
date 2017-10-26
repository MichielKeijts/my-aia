<?php
/*
 * Display the shopping cart widget. 
 * 
 * @todo add SESSION / COOKIE data in initialisation instead of an empty list
 */

$editable = my_aia_order()->error || (filter_input(INPUT_POST, '_method') != 'create' && !(isset(my_aia_order()->ORDER) && my_aia_order()->ORDER->ID > 0));
$coupon_insertable = isset(my_aia_order()->ORDER) && my_aia_order()->ORDER->ID > 0;

?>
<div id="shopping-cart">
	<section class="buddypress-tiles address-details">
		<div class="tile">
		<table class="shopping-cart">
			<thead>
				<tr>
					<td width="20%"><span class="name"></span></td>
					<td width="60%"><span class="name">omschrijving</span></td>
					<td width="7%"><span class="count">aantal</span></td>
					<td width="140"><span class="count">bedrag</span></td>
					<td width="7%"></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach (my_aia_order()->ORDER->order_items as $item): ?>
				<tr class="">
					<td><div class="product-image"><?= get_the_post_thumbnail($item->product_id, array('width'=>150, 'height'=>150)); ?></div></td>
					<td><span class="name"><?= $item->post_title; ?></span></td>
					<td><span class="count"><input type="number" min="0" max="100" size="2" style="width: 50px;" value="<?= $item->count; ?>" name="count" data-id="<?= $item->get_product()->ID; ?>" <?= $editable?"":"readonly"?>></span></td>
					<td><span class="count">&euro; <?= number_format($item->get_product()->price * $item->count, 2, ',','.'); ?></span></td>
					<td><?php if ($editable): ?><a href="#" data-id="<?= $item->get_product()->ID; ?>" class="button-remove-product">X</a><?php endif; //editable ?></td>
				</tr>
				<?php endforeach; //$order_items ?>
				<?php if ($coupon_insertable): ?>
				<tr class="">
					<form method="POST" action="?order_id=<?= my_aia_order()->ORDER->ID; ?>">
					<td><div class="product-image"></div></td>
					<td colspan="1"><span class="name"><?= __('Als u een coupon code heeft, kunt u deze hier invullen');?>:</td>
					<td colspan="1"><span class="count"><input type="text" maxlength="12" minlength="12" style="width: 120px;" placeholder="EG34AB3.." value="<?= isset($_SESSION['coupon_code'])?$_SESSION['coupon_code']:""; ?>" name="coupon_code"></span></td>
					<td><span class="count"><?php if (!empty(my_aia_order()->ORDER->coupon)): ?>&euro; -<?= number_format(my_aia_order()->ORDER->coupon_value, 2, ',','.'); ?><?php endif; ?></span></td>
					<td><button type="submit" class="button-update-order"><?= __('Update'); ?></button></td>
					</form>
				</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td colspan=2><span class="total_price"><?= __('TOTAAL','my-aia'); ?></span></td>
					<td><span class="total_price">&euro; <?= number_format(my_aia_order()->ORDER->total_amount, 2, ',','.'); ?></span></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
		</div>
	</section>
	
	<form class='' enctype="multipart/form-data" method='POST' action='?'>
	<section class="buddypress-tiles address-details">
		<?php if (my_aia_order()->error): ?>
		<div class="column-wrapper">
			<div class="column-1-1 column-sm-1">
				<div class="column-inner center">
				<span class="flash-message"><?= my_aia_order()->message; ?></span>
			</div>
		</div>
		<?php endif; // error handling ?>
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
						<?php if ($editable): ?><button class="button blauw change-address">Wijzig</button><?php endif; //edit button ?>
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
						<?php if ($editable): ?><button class="button blauw change-address">Wijzig</button><?php endif; ?>
					</div>
					<div class="hidden tab-input">
						<?php my_aia_order()->get_attributes_form("invoice"); ?>
						<button class="button blauw display-address">Akkoord</button>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section class="buddypress-tiles address-details">
		<div class="column-wrapper">
			<div class="column-inner tile">
				<div class="padding-left-25">
					<h3><?= __('Aanvullende Afspraken','my-aia');?></h3>
					<ul>
						<li>Levering artikelen (indien van toepassing) binnen 10 dagen na bestelling.</li>
						<li>Niet goed, geld terug garantie op artikelen.</li>
						<li>Betalingswijze: iDeal.</li>						
						<li>Eventuele coupons kun je in de volgende stap inwisselen</li>
					</ul>
					<input type="hidden" name='akkoord-voorwaarden' value='0'>
					<input type="checkbox" id="akkoord-voorwaarden" name='akkoord-voorwaarden' value='1' required="true" <?php if (!$editable) echo "checked disabled"; ?>>
					<label for="akkoord-voorwaarden">Ik ga akkoord met de <a href='#' title='Opent in nieuw venster'>algemene voorwaarden</a> van Athletes in Action</label>

				</div>
				<div class="buttons center">
					<?php if ($editable): ?>
						<a href="/shop" class="button blauw link_to_order" id="link_to_order"><?= __('Terug naar de winkel'); ?></a>
						<input type='hidden' name='_method' value='create'>
						<input type='submit' class="button blauw link_to_order" id="link_to_order" value='<?= __('Plaats bestelling'); ?>'>
					<?php endif;?>
					<?php if (!$editable): ?>
						<a href="?reset" class="button rood link_to_order" id="link_to_order"><?= __('Bestelling Annuleren'); ?></a>
						<a href="?create" class="button blauw link_to_order" id="link_to_order"><?= __('Vorige Stap'); ?></a>
						<input type='hidden' name='_method' value='pay'>

						<a href='?make_payment&order_id=<?= my_aia_order()->ORDER->ID; ?>' class="button blauw link_to_order" id="link_to_pay"><?= __('Bevestigen & Betalen met iDeal'); ?></a>
					<?php endif;?>
				</div>
			</div>
		</div>
	</section>
		
	</form>
</div>