<?php
/*
 * Display the order of a person. 
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
					<td width="60%"><span class="name">Naam</span></td>
					<td width="7%"><span class="count">Datum</span></td>
					<td width="7%"><span class="count">Bedrag</span></td>
					<td width="120"><span class="count">Status</span></td>
					<td></td>
					<td width="7%"></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach (MY_AIA::get('orders', array()) as $item): ?>
				<tr class="">
					<td><span class="name"><?= $item->post_title; ?></span></td>
					<td><span class="count"><?= $item->date; ?></span></td>
					<td><span class="count">&euro; <?= number_format($item->total_order_price,2,',','.'); ?></span></td>
					<td><span class="count"><?= $item->post_status; ?></span></td>
					<td><span class="count"><a href="<?= $item->invoice->pdf_link(); ?>">Factuur</a></span></td>
					
				</tr>
				<?php endforeach; //$order_items ?>
			</tbody>
			<tfoot>
				<tr>
					<td width="60%"><span class="name">Naam</span></td>
					<td width="7%"><span class="count">Datum</span></td>
					<td width="7%"><span class="count">Bedrag</span></td>
					<td width="120"><span class="count">Status</span></td>
					<td></td>
					<td width="7%"></td>
				</tr>
			</tfoot>
		</table>
		</div>
	</section>
</div>