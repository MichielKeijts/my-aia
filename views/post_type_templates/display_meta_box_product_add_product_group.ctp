	<label for="my-aia-product-select" class="screen-reader-text"><?= __( 'Versies van dit product', 'my-aia' ); ?></label>
	<label style='display:inline-block;'><?= __(strtoupper($post->group_by_name) . ' opties','my-aia'); ?> (<?= count($versions); ?>)</label>
	<hr>
	<div class="product_example" style='display:inline-block; position:relative;'>
		<ul>
			<?php foreach ($versions as $version): ?>
			<li><b><?= $version->group_by_option; ?></b> post ID <a href='<?= get_edit_post_link( $version->ID); ?>'><?= $version->ID; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<hr>
	<br>
	De producten hierboven worden gegroepeerd op: <?= $post->group_by_name; ?>. 
