	<label for="my-aia-product-role-add" class="screen-reader-text"><?= __( 'Give group or user permission to document', 'my-aia' ); ?></label>
	<input name="my_aia_product_role_add" id="my-aia-product-role-add" class="my-aia-find-order" placeholder="<?= esc_attr_e( 'Enter a user or group name to search', 'my-aia' ) ?>" style="border:0px; width:100%;display:inline-block;"/>

	<br>
	<fieldset style='width:100%; display:inline-block; border: 1px solid #eee;'>
		<h2><span><label style='display:inline-block;'><?= __('Current Roles have access to this document','my-aia'); ?></label></span></h2>
		<div class="tagchecklist" id="tagchecklist">
		<?php foreach ($this->roles->get_post_roles(get_the_ID()) as $role): ?>		
		<span><a id="post_tag-check-nu" class="ntdelbutton" tabindex="0" data-id="<?= $role->id; ?>" data-type="<?= $role->type; ?>">X</a>&nbsp;<?= $this->roles->get_role_name($role); ?></span>			
		<?php endforeach; ?>	
		</div>
	</fieldset>