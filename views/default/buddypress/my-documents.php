<?php
/*
 * Display the documents of a person. 
 * 
 * @todo add SESSION / COOKIE data in initialisation instead of an empty list
 */

?>
<div id="shopping-cart">
	<section class="buddypress-tiles">
		<div class="-tile">
		<table class="shopping-cart">
			<thead>
				<tr>
					<td width="60%"><span class="name">Naam</span></td>
					<td width="14%"><span class="count">Omschrijving</span></td>
					<td width="7%"><span class="count">Tags</span></td>
					<td width="120"><span class="count">Download</span></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach (MY_AIA::get('documents', array()) as $item): ?>
				<tr class="">
					<td><a href="<?= get_permalink($item); ?>"><span class="name"><?= $item->post_title; ?></span></a></td>
					<td><span class="count"><?= $item->post_date; ?></span></td>
					<td><span class="count"></span></td>
					<td><span class="count"><a href="<?= wpdm_download_url($item->ID); ?>">Link</a></span></td>
				</tr>
				<?php endforeach; //$order_items ?>
			</tbody>
			<tfoot>
				<tr>
					<td width="60%"><span class="name">Naam</span></td>
					<td width="14%"><span class="count">Omschrijving</span></td>
					<td width="7%"><span class="count">Tags</span></td>
					<td width="120"><span class="count">Download</span></td>

				</tr>
			</tfoot>
		</table>
		</div>
	</section>
</div>