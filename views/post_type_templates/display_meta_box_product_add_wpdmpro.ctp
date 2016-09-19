	<label for="my-aia-product-select" class="screen-reader-text"><?= __( 'Voeg download toe indien betaald moet worden voor deze resource', 'my-aia' ); ?></label>
	<input name="my_aia_product_add" id="my-aia-product-select" class="my-aia-find-order" placeholder="<?= __( 'Type download titel om te zoeken', 'my-aia' ) ?>" style="border:0px; width:100%;display:inline-block;"/>
	<hr>
	<label style='display:inline-block;'><?= __('Selected product','my-aia'); ?></label>
	<div class="product_example" style='display:inline-block; position:relative;'>
		<label for="my_aia_product_add_id"  style='display:inline-block;'><?= __('ID:','my-aia'); ?></label> <div id="my_aia_product_add_id" style='display:inline-block; width: 30px'><?= (($this->download && $this->download->ID)?$this->download->ID:'');?></div>
		<div id="my_aia_product_add_name" style='display:inline-block; width: 200px'><i><?= (($this->download && $this->download->ID)?$this->download->post_title:__('empty','my-aia')); ?></i></div>
	</div>
	<input type="hidden" name="download_id" value="<?= (($this->download && $this->download->ID)?$this->download->ID:'');?>">
	<hr>
	<br>
	Koppel een product uit de webshop zodat voor deze download betaald moet worden.
