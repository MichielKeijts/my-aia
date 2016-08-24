<?php
/*
 * Display the shopping cart widget. 
 * 
 * @todo add SESSION / COOKIE data in initialisation instead of an empty list
 */

?>
<div id="shopping-cart">
	<section class="">
		<table class="shopping-cart">
			<thead>
				<tr>
					<td width="20%"><span class="name"></span></td>
					<td width="50%"><span class="name">omschrijving</span></td>
					<td width="10%"><span class="count">aantal</span></td>
					<td width="10%"><span class="count">bedrag</span></td>
					<td width="10%"></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($order_items as $item): ?>
				<tr class="">
					<td width="10%"><div class="product-image"><?= get_the_post_thumbnail($item->product_id, array('width'=>150, 'height'=>150)); ?></div></td>
					<td width="80%"><span class="name"><?= $item->post_title; ?></span></td>
					<td width="10%"><span class="count"><?= $item->count; ?></span></td>
					<td width="10%"><span class="count">&euro; <?= number_format($item->get_product()->price * $item->count);; ?></span></td>
					<td width="10%"><a href="#" data-id="0" class="button-remove-product">X</a></td>
				</tr>
				<?php endforeach; //$order_items ?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td colspan=2><span class="total_price"><?= __('TOTAAL','my-aia'); ?></span></td>
					<td><span class="total_price">&euro; <?= $total_price; ?></span></td>
				</tr>
			</tfoot>
		</table>
	</section>
	
	<section class="buddypress-tiles">
		<div class="column-wrapper">
			<div class="column-2-1 column-sm-1">
				<div class="column-inner">
					<h3><?= __('Verzend Adres','my-aia');?></h3>
					<div class="tab-display">
						<span class="text">
							Michiel Keijts<br>
							Orionstraat 22<br>
							4356 BR Oostkapelle<br>
							Nederland<br>	
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
				<div class="column-inner">
					<h3><?= __('Factuur Adres','my-aia');?></h3>
					<div class="tab-display">
						<span class="text">
							Michiel Keijts<br>
							Orionstraat 22<br>
							4356 BR Oostkapelle<br>
							Nederland<br>	
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
	
	
	<section class="buttons">
		<div class="column-wrapper">
	<?php if (gettype(filter_input(INPUT_GET, 'create')) == 'string'): ?>
		<a href="/shop" class="button white link_to_order" id="link_to_order"><?= __('Terug naar de winkel'); ?></a>
		<a href="?place_order" class="button white link_to_order" id="link_to_order"><?= __('Plaats bestelling'); ?></a>
	<?php endif;?>
	<?php if (gettype(filter_input(INPUT_GET, 'place_order')) == 'string'): ?>
		<a href="?reset" class="button white link_to_order" id="link_to_order"><?= __('Bestelling Annuleren'); ?></a>
		<a href="?pay" class="button white link_to_order" id="link_to_order"><?= __('Bevestigen & Betalen'); ?></a>
	<?php endif;?>
		</div>
	</section>
</div>