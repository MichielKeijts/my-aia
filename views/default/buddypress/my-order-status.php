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
					<td width="20%"><span class="count">bedrag</span></td>
					
				</tr>
			</thead>
			<tbody>
				<?php foreach (
						$this->get_order_items() as $item): ?>
				<tr class="">
					<td width="10%"><div class="product-image"><?= get_the_post_thumbnail($item->product_id, array('width'=>150, 'height'=>150)); ?></div></td>
					<td width="50%"><span class="name"><?= $item->post_title; ?></span></td>
					<td width="10%"><span class="count"><?= $item->count; ?></span></td>
					<td width="20%"><span class="count">&euro; <?= number_format($item->get_product()->price * $item->count, 2, ',', '.');; ?></span></td>
				</tr>
				<?php endforeach; //$order_items ?>
				<?php 
				if ($this->ORDER->coupon_value >0): ?>
				<tr class="">
					<td><div class="product-image"></div></td>
					<td colspan="1"><span class="name"><?= __('Gebruikte coupon waarde');?>:</td>
					<td colspan="1"></td>
					<td><span class="count">&euro; -<?= number_format($this->ORDER->coupon_value, 2, ',','.'); ?></span></td>
				</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td colspan=2><span class="total_price"><?= __('TOTAAL','my-aia'); ?></span></td>
					<td><span class="total_price">&euro; <?= $this->ORDER->total_amount; ?></span></td>
				</tr>
			</tfoot>
		</table>
	</section>
	<form class='' enctype="multipart/form-data" method='POST' action='?'>
	<section class="buddypress-tiles">
		<div class="column-wrapper">
			<div class="column-2-1 column-sm-1">
				<div class="column-inner">
					<h3><?= __('Verzend Adres','my-aia');?></h3>
					<div class="tab-display">
						<span class="text">
							<?= $this->ORDER->shipping_name; ?><br>
							<?= $this->ORDER->shipping_address; ?><br>
							<?= $this->ORDER->shipping_postcode; ?> <?= $this->ORDER->shipping_city; ?><br>
							<?= $this->ORDER->shipping_country; ?><br>
						</span>
					</div>
				</div>
			</div>
			<div class="column-2-1 column-sm-1">
				<div class="column-inner">
					<h3><?= __('Factuur Adres','my-aia');?></h3>
					<div class="tab-display">
						<span class="text">
							<?= $this->ORDER->invoice_name; ?><br>
							<?= $this->ORDER->invoice_address; ?><br>
							<?= $this->ORDER->invoice_postcode; ?> <?= $this->ORDER->invoice_city; ?><br>
							<?= $this->ORDER->invoice_country; ?><br>
						</span>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section class="buddypress-tiles">
		<div class="column-wrapper">
			<div class="column-sm-1">
				<div class="column-inner">
					<h3><?= __('Overige gegevens','my-aia');?></h3>
					<ul>
						<li>Status: <?= $this->ORDER->order_status ?> 
							<?php if ($this->ORDER->order_status == MY_AIA_ORDER_STATUS_AWAITING_PAYMENT) echo '<br>klik <a href="../my-order-edit/?make_payment&order_id=', $this->ORDER->ID, '">hier</a> om je order te betalen';
						?></li>
						<li>Download <a href='<?= $this->ORDER->invoice->pdf_link() ?>'>hier</a> je proforma factuur</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
		
	<script>
		reset_shopping_cart(); 
	</script>
		
</div>