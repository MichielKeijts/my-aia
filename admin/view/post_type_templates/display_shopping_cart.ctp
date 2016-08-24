<?php
/*
 * Display the shopping cart widget. 
 * 
 * @todo add SESSION / COOKIE data in initialisation instead of an empty list
 */

?>
<div id="shopping-cart">
	<table class="shopping-cart">
		<thead>
			<tr>
				<td width="20%"><span class="product_imaage">aantal</span></td>
				<td width="50%"><span class="name">omschrijving</span></td>
				<td width="10%"><span class="count">aantal</span></td>
				<td width="10%"><span class="count">bedrag</span></td>
				<td width="10%" class="hidden"><a href="#" data-id="0" class="button-remove-product"><i class="dashicon dashicons-trash"></i> X</a></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($order_items as $item): ?>
			<tr class="hidden">
				<td width="10%"><span class="count"><?= get_the_post_thumbnail($item->product_id); ?></span></td>
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
	
	<a href="/shopping-cart" class="button white link_to_order" id="link_to_order">Afrekenen</a>
</div>