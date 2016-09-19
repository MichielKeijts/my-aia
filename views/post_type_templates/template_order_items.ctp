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
				<td width="20%"><span class="count">bedrag</span></td>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($order->order_items as $item): ?>
			<tr class="hidden">
				<td width="20%"><span class="count"><?= $item->count; ?></span></td>
				<td width="50%"><span class="name"><?= $item->post_title; ?></span></td>
				<td width="10%"><span class="count"><?= $item->count; ?></span></td>
				<td width="20%"><span class="count">&euro; <?= number_format($item->get_product()->price * $item->count);; ?></span></td>
			</tr>
			<?php endforeach; //$order_items ?>
		</tbody>
	</table>
</div>