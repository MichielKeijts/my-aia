<?php
/*
 * Display the shopping cart widget. 
 * 
 * @todo add SESSION / COOKIE data in initialisation instead of an empty list
 */

?>
<div id="shopping-cart">
	<table class="shopping-cart-widget">
		<thead>
			<tr class="hidden">
				<td width="10%"><span class="count">AANTAL</span></td>
				<td width="80%"><span class="name">ART NAAM</span></td>
				<td width="10%"><a href="#" data-id="0" class="button-remove-product">X</a></td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	
	<a href="<?= $payment->links->paymentUrl; ?>" class="button white link_to_order" id="link_to_order">Afrekenen</a>
	<a href="/members/<?php echo wp_get_current_user()->data->user_nicename; ?>/orders/?create" class="button white link_to_order" id="link_to_order">Afrekenen</a>
</div>