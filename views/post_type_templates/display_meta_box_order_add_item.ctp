	<label for="my-aia-order-items-add" class="screen-reader-text"><?= __( 'Add new products', 'my-aia' ); ?></label>
	<input name="my_aia_order_item_add" id="my-aia-order-items-add" class="my-aia-find-order" placeholder="<?= esc_attr_e( 'Enter a product name to search', 'my-aia' ) ?>" style="border:0px; width:100%;display:inline-block;"/>
	
	<fieldset style='width:100%; display:inline-block; border: 1px solid #eee;'>
		<label style='display:inline-block;'><?= __('Selected product','my-aia'); ?></label>
		<div class="order_item_example" style='display:inline-block; position:relative;'>
			<label for="my_aia_order_item_add_id"  style='display:inline-block;'><?= __('ID:','my-aia'); ?></label> <div id="my_aia_order_item_add_id" style='display:inline-block; width: 30px'></div>
			<label for="my_aia_order_item_add_name" style='display:inline-block;'><?= __('Description:','my-aia'); ?></label> <div id="my_aia_order_item_add_name" style='display:inline-block; width: 200px'><i><?= __('empty','my-aia'); ?></i></div>
			<label for="my_aia_order_item_add_price" style='display:inline-block;'><?= __('Price:','my-aia'); ?></label> <input id="my_aia_order_item_add_price" style='display:inline-block; width: 70px' type='text'>	
			<label for="my_aia_order_item_add_count" style='display:inline-block;'><?= __('Count:','my-aia'); ?></label> <input id="my_aia_order_item_add_count" style='display:inline-block; width: 70px' type='text'>
		</div>
		<button class="btn btn-default" id="button_order_item_add"><i class="glyphicon glyphicon-plus-sign"></i><?= __('Add Product to order', 'my-aia'); ?></button>
	</fieldset>