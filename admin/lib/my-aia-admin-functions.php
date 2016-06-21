<?php
/* 
 * @copyright (c) 2016, Michiel Keijts
 * @licence			restricted
 * 
 * Contains a list of helper functions without class interface
 */



/**
 * Returns a list of all buddypress xprofile fields
 * Form:
 *	non-grouped =>	array(<id> => <name>);
 *  grouped		=>	array(<group_id> => array (<name> => <group_name>, <id>=><name>, <id2>=><name2>);
 * @param bool $return_grouped
 * @return array $grouped_list_of_fields|$list_of_fields
 */
function my_aia_get_buddy_press_xprofile_fields($return_grouped = FALSE) {
	$group_data = BP_XProfile_Group::get(array('fetch_fields'=>true));
	
	$grouped_list_of_fields = array();
	$list_of_fields = array();
	
	// loop over groups
	foreach ($group_data as $group=>$field_data) {
		$grouped_list_of_fields[$field_data->id] = array('name'=>$field_data->name);
		if (!empty($field_data->fields)){
			foreach ($field_data->fields as $field=>$data) {
				$list_of_fields[$data->id] = $data->name;
				$grouped_list_of_fields[$field_data->id][$data->id] = $data->name;
			}
		}
	}
	
	if ($return_grouped) return $grouped_list_of_fields;
	
	return $list_of_fields;	
}


/**
 * Get User by Meta Key / Value 
 * @param type $meta_key
 * @param type $meta_value
 * @return type
 */
function get_user_by_meta_data($meta_key, $meta_value, $return_all = FALSE) {
	wp_reset_query();	
	// get al the users by key/value	
	$user_query = new WP_User_Query(
		array(
			'meta_key'	  =>	$meta_key,
			'meta_value'	=>	$meta_value
		)
	);

	// Get the results from the query
	$users = $user_query->get_results();

	if (!is_array($users)) 
		return false; // no users found
	
	return $return_all ? $users:$users[0];
}


/**
 * Get a Google Geocode Result 
 * Maps API implementation See https://console.developers.google.com/apis/api/geocoding_backend/usage?project=mijn-athletesinaction&authuser=1&duration=PT1H
 * @param mixed $data
 * @return mixed Array(result) | FALSE
 */
function get_google_geocode_result($data) {
	include_once MY_AIA_PLUGIN_DIR . 'classes/crmsync/class_google_geocode.php';
	
	$geocoder = new class_google_geocode();
	return $geocoder->get_result($data);
}


/**
 * Adds the Attribute widget to the custom post type page
 */
function my_aia_post_type_partner_add_metaboxes() {
	add_meta_box('my-aia-partner-buddy-press-box', __('Groep','my-aia'), 'my_aia_post_type_partner_display_buddy_press_groups_metabox', MY_AIA_POST_TYPE_PARTNER, 'side', 'high');
	add_meta_box('my-aia-partner-assigned-user-box', __('Contactpersoon','my-aia'), 'my_aia_post_type_partner_display_assigned_user_id_metabox', MY_AIA_POST_TYPE_PARTNER, 'side', 'high');
}

function my_aia_post_type_partner_display_buddy_press_groups_metabox() {
	global $post;
	
	$partner = new MY_AIA_PARTNER($post);
	
	$groups = BP_Groups_Group::get(array('order_by'=>'name'));
	
	if ($groups['total'] > 0) {
		?><select name="bp_group_id"><option value="0"><i>geen group</i></option><?php
		foreach ($groups['groups'] as $group) {
			echo sprintf('<option value="%d" %s>%s</option>', $group->id, ($group->id == $partner->bp_group_id)?'selected':'', $group->name);
		}
		?></select><p><?= __('Selecteer de group waar de partner bij hoort of maak een nieuwe aan'); ?></p><?php
	}
	//@todo: add link to group
}

/**
 * Display a list of users
 */
function my_aia_post_type_partner_display_assigned_user_id_metabox() {
	global $post;
	
	$partner = new MY_AIA_PARTNER($post);
	
	wp_dropdown_users(array(
		'option_none_value' => 0,
		'show_option_none'	=> '<i> - leeg - </li>',
		'name'				=> 'assigned_user_id',
		'selected'			=> $partner->assigned_user_id
	));
	?><p>Selecteer de contactpersoon (gebruiker) voor deze partner</p><?php
	//@todo: add link to user
}


/**
 * Display an order form with items 
 * @param type $order_id
 * @param type $order_items
 */
function my_aia_order_form($order_id, $order_items) {
	?>
		<div class="bp-groups-member-type" id="my-aia-order-items">

			<table class="widefat bp-group-members">
				<thead>
					<tr>
						<th scope="col" class="uid-column"><?= __( 'ID', 'my-aia' ); ?></th>
						<th scope="col" class="uname-column"><?= __( 'Name', 'Group member name in group admin', 'my-aia' ); ?></th>
						<th scope="col" class="ucount-column"><?= __( 'Aantal', 'my-aia' ); ?></th>
						<th scope="col" class="uprice-column"><?= __( 'Prijs', 'my-aia' ); ?></th>
						<th scope="col" class="usubtotal-column"><?= __( 'Totaal', 'my-aia' ); ?></th>
						<th scope="col" class="umodify-column"></th>
					</tr>
					<tr class="hidden">
						<th scope="row" class="uid-column"><label class="text"></label><input type="hidden" name="order_item_product_id" value="-1" class="order-item-id"></th>
						<td class="uname-column"><a style="float: left;" href="<?php echo get_permalink($order_item->product_id); ?>"></a></td>
						<td class="ucount-column"><input type="number" name="count" value="1" min="0" max="999" step="1" class="order-item-count"></td>
						<td class="uprice-column"><input type="text" name="price" value="0.00" class="order-item-price"></td>
						<td class="usubtotal-column">&euro;</td>
						<td class="umodify-column"><span class="dashicons dashicons-dismiss"></span></td>
					</tr>
				</thead>
				<tbody>
				<?php 
					$totalPrice = $totalVat = 0.00;
					foreach($order_items as $order_item): 
						if (empty($order_item->product_id)) continue;
						
						$totalPrice +=  $order_item->price * $order_item->count;
						$totalVat += $order_item->price * $order_item->vat;
				?>
					<tr>
						<th scope="row" class="uid-column"><?php echo esc_html($order_item->product_id); ?><input type="hidden" name="order_items[<?= $order_item->product_id; ?>][id]" value="<?= $order_item->product_id; ?>" class="order-id"></th>

						<td class="uname-column"><a style="float: left;" href="<?php echo get_permalink($order_item->product_id); ?>"><?= $order_item->post_title; ?></a></td>
						<td class="ucount-column"><input type="number" name="order_items[<?= $order_item->product_id; ?>][count]" value="<?= $order_item->count; ?>" min="0" max="999" step="1" class="order-item-count"></td>
						<td class="uprice-column"><input type="text" name="order_items[<?= $order_item->product_id; ?>][price]" value="<?= $order_item->price; ?>" class="order-item-price"></td>
						<td class="usubtotal-column">&euro;<?= round((float)$order_item->count * $order_item->price, 2); ?></td>
						<td class="umodify-column"><span class="dashicons dashicons-dismiss"></span></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="row" class="uid-column" colspan="3">Totaal:</th>
						<td class="uprice-column"></td>
						<td class="usubtotal-column">&euro; <span class="text"><?= round($totalPrice,2); ?></span></td>
						<td class="umodify-column"></td>
					</tr>
				</tfoot>
			</table>
		</div><!-- .bp-groups-member-type -->

	<?php 
}


/**
 * Output a add_product_item to 
 */
function my_aia_order_form_add_item( ) {
	?>

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
	
	<?php
}



/**
 * Function which calls custom post types to save in case a custom post class
 * exists.
 */
function my_aia_post_save_action($post_id, $post, $update) {
	switch ($post->post_type) {
		case MY_AIA_POST_TYPE_PARTNER:
			// update partner meta
			$partner = new MY_AIA_PARTNER($post);
			$partner->save_post($post_id, $post, $update);
			//$partner->update_post_meta();
			break;
		case MY_AIA_POST_TYPE_ORDER:
			// update partner meta
			$partner = new MY_AIA_ORDER($post);
			$partner->save_post($post_id, $post, $update);
			//$partner->update_post_meta();
			break;
		case MY_AIA_POST_TYPE_INVOICE:
			// update partner meta
			$partner = new MY_AIA_INVOICE($post);
			$partner->save_post($post_id, $post, $update);
			//$partner->update_post_meta();
			break;
		case MY_AIA_POST_TYPE_PRODUCT:
			// update partner meta
			$partner = new MY_AIA_PRODUCT($post);
			$partner->save_post($post_id, $post, $update);
			//$partner->update_post_meta();
			break;
		default:
			return true;
	}
}


/**
 * Try and find a method of a function and call it. 
 * @param class $obj
 * @param string $method
 * @return mixed FALSE if function not exists, function output otherwise 
 */
function call_method_if_exists($obj, $method) {
	if (method_exists($obj, $method))
		return $obj->$method();
	
	return FALSE;
}