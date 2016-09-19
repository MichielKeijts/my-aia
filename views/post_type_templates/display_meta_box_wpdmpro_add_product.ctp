	<label for="my-aia-product-select" class="screen-reader-text"><?= __( 'Voeg product toe indien betaald moet worden voor deze resource', 'my-aia' ); ?></label>
	<input name="my_aia_product_add" id="my-aia-product-select" class="my-aia-find-order" placeholder="<?= __( 'Type product naam om te zoeken', 'my-aia' ) ?>" style="border:0px; width:100%;display:inline-block;"/>
	<hr>
	<label style='display:inline-block;'><?= __('Selected product','my-aia'); ?></label>
	<div class="product_example" style='display:inline-block; position:relative;'>
		<label for="my_aia_product_add_id"  style='display:inline-block;'><?= __('ID:','my-aia'); ?></label> <div id="my_aia_product_add_id" style='display:inline-block; width: 30px'><?= (($this->get_model()->product && $this->get_model()->product->ID)?$this->get_model()->product->ID:'');?></div>
		<div id="my_aia_product_add_name" style='display:inline-block; width: 200px'><i><?= (($this->get_model()->product && $this->get_model()->product->ID)?$this->get_model()->product->post_title:__('empty','my-aia')); ?></i></div>
	</div>
	<input type="hidden" name="product_id" value="<?= (($this->get_model()->product && $this->get_model()->product->ID)?$this->get_model()->product->ID:'');?>">
	<hr>
	<br>
	Koppel een product uit de webshop zodat voor deze download betaald moet worden.
