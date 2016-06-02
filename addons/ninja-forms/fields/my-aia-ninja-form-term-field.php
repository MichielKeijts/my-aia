<?php if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * @copyright (c) 2016, Michiel Keijts
 */

function my_aia_ninja_forms_term_field_register(){
	$args = array(
		'name' => 'Taxonomy Term',
		'edit_function' => 'my_aia_ninja_forms_term_field_edit',
		'edit_options' => array(
			array(
				'type' => 'checkbox',
				'name' => 'my_aia_use_term_keys_as_value',
				'label' => 'Gebruik ID als waarde (normaal: waarde is gelijk aan het label)',
				'class' => 'widefat',
			),
		),
		'display_function' => 'my_aia_ninja_forms_field_term_display',
		'sub_edit_function' => 'my_aia_ninja_forms_field_term_edit',
		'group' => '',
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'action' => array(
				'show' => array(
					'name'        => __( 'Show This', 'ninja-forms' ),
					'js_function' => 'show',
					'output'      => 'hide',
				),
				'hide' => array(
					'name'        => __( 'Hide This', 'ninja-forms' ),
					'js_function' => 'hide',
					'output'      => 'hide',
				),
				'change_value' => array(
					'name'        => __( 'Selected Value', 'ninja-forms' ),
					'js_function' => 'change_value',
					'output'      => 'list',
				),
				'add_value' => array(
					'name'        => __( 'Add Value', 'ninja-forms' ),
					'js_function' => 'add_value',
					'output'      => 'ninja_forms_field_list_add_value',
				),
				'remove_value' => array(
					'name'        => __( 'Remove Value', 'ninja-forms' ),
					'js_function' => 'remove_value',
					'output'      => 'list',
				),
			),
			'value' => array(
				'type' => 'list',
			),
		),
		'pre_process' => 'ninja_forms_field_term_pre_process',
		'process' => 'ninja_forms_field_term_process',
		'req_validation' => 'ninja_forms_field_term_req_validation',
	);

	ninja_forms_register_field('term', $args);
}

/**
 * Placeholder for pre-process function. Validates user input
 * @param int $field_id 
 * @param mixed $user_value
 * @global \Ninja_Forms_Processing $ninja_forms_processing
 */
function ninja_forms_field_term_pre_process($field_id, $user_value) {
	global $ninja_forms_processing;

	if (!ninja_forms_field_term_req_validation($field_id, $user_value)) {
		$ninja_forms_processing->add_error( $field_id, __('Er is geen bestand toegevoegd', 'my-aia') );
	}
}

/**
 * Placeholder for upload_process function
 */
function ninja_forms_field_term_process() {
	
}

/**
 * Validation function
 * @return bool validation successfull
 */
function ninja_forms_field_term_req_validation($field_id, $user_value) {
	if( strpos($user_value, '.') !== FALSE && file_exists(MY_AIA_PLUGIN_DIR . '../../uploads/my_aia/form_uploads/'.$user_value)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Get an key=>value pair array with the term data. 
 * @param int $field_id ninja_forms field id
 * @param array $data	ninja_forms field data
 * @return array
 */
function my_aia_ninja_forms_field_term_get_terms($field_id, $data) {
	if (isset($data['my_aia_term'])) {
		if ($data['my_aia_use_term_keys_as_value'] || $data['my_aia_use_term_keys_as_value']>0) {
			$data['my_aia_use_term_keys_as_value'] = TRUE;
		}
		
		$terms = get_terms($data['my_aia_term'], array('hide_empty'=>FALSE));
		$options = array();
		if ($terms && count($terms) > 0 ){
			foreach ($terms as $key=>$term) {
				array_push($options,array(
					'value' => $data['my_aia_use_term_keys_as_value']?$key:$term->name,
					'label' => $term->name
				));
			}
			return $options;
		}
	}
	return array();
}


function my_aia_ninja_forms_term_field_edit( $field_id, $data ) {
	global $wpdb;

	$list_type = isset( $data['list_type'] ) ? $data['list_type'] : '';
	$my_aia_term = isset( $data['my_aia_term'] ) ? $data['my_aia_term'] : NULL;
	$multi_size = isset( $data['multi_size'] ) ? $data['multi_size'] : 5;
	$default_options = array(
		array( 'label' => 'Option 1', 'value' => '', 'calc' => '', 'selected' => 0 ),
		/*array( 'label' => 'Option 2', 'value' => '', 'calc' => '', 'selected' => 0 ),
		array( 'label' => 'Option 3', 'value' => '', 'calc' => '', 'selected' => 0 ),*/
	);

	$list_options = isset ( $data['list']['options'] ) ? $data['list']['options'] : $default_options;

	$list_type_options = array(
		array('name' => __( 'Dropdown', 'ninja-forms' ), 'value' => 'dropdown'),
		array('name' => __( 'Radio', 'ninja-forms' ), 'value' => 'radio'),
		array('name' => __( 'Checkboxes', 'ninja-forms' ), 'value' => 'checkbox'),
		array('name' => __( 'Multi-Select', 'ninja-forms' ), 'value' => 'multi'),
	);
	
	ninja_forms_edit_field_el_output( $field_id, 'select', __( 'List Type', 'ninja-forms' ), 'list_type', $list_type, 'wide', $list_type_options, 'widefat' );
	
	?>
	
	<p id="ninja_forms_field_<?php echo $field_id;?>_multi_size_p" class="description description-wide" style="<?php if($list_type != 'multi'){ echo 'display:none;';}?>">
		<?php _e( 'Multi-Select Box Size', 'ninja-forms' );?>: <input type="text" id="" name="ninja_forms_field_<?php echo $field_id;?>[multi_size]" value="<?php echo $multi_size;?>">
	</p>
	<span id="ninja_forms_field_<?php echo $field_id;?>_list_span" class="ninja-forms-list-span">
		<div id="ninja_forms_field_<?php echo $field_id;?>_list_options" class="ninja-forms-field-list-options description description-wide">
			<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[list][options]" value="">
			<label for="ninja_forms_field_<?php echo $field_id;?>_my_aia_term"><?php _e( 'Taxonomy waaruit gekozen kan worden', 'my-aia' );?>:</label>
			<select name="ninja_forms_field_<?php echo $field_id;?>[my_aia_term]" id="ninja_forms_field_<?php echo $field_id;?>_my_aia_term">
				<?php 
					$categories = get_taxonomies();
					foreach ($categories as $category) {
						$taxonomy = get_taxonomy($category);
						printf(
								"<option value='%s' %s>%s</option>",
								$category,
								($category == $my_aia_term ? 'selected':''),
								$taxonomy->label
						);
					}
				?>
			</select>
		</div>
	</span>
	<?php
}

/**
 * Display function for the upload field
 * @param int $field_id
 * @param array $data
 * @param int $form_id
 */
function my_aia_ninja_forms_field_term_display( $field_id, $data, $form_id = '' ) {
	global $wpdb, $ninja_forms_fields;

	if(isset($data['show_field'])){
		$show_field = $data['show_field'];
	}else{
		$show_field = true;
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	$field_row = ninja_forms_get_field_by_id($field_id);

	$type = $field_row['type'];
	$type_name = $ninja_forms_fields[$type]['name'];

	if ( isset( $data['list_type'] ) ) {
		$list_type = $data['list_type'];
	} else {
		$list_type = '';
	}

	if(isset($data['list_show_value'])){
		$list_show_value = $data['list_show_value'];
	}else{
		$list_show_value = 0;
	}

	$options = my_aia_ninja_forms_field_term_get_terms($field_id, $data);
	if (count($options) <= 0) {
		if( isset( $data['list']['options'] ) AND $data['list']['options'] != '' ){
			$options = $data['list']['options'];
		}else{
			$options = array();
		}
	}

	if(isset($data['label_pos'])){
		$label_pos = $data['label_pos'];
	}else{
		$label_pos = 'left';
	}

	if(isset($data['label'])){
		$label = $data['label'];
	}else{
		$label = $type_name;
	}

	if( isset( $data['multi_size'] ) ){
		$multi_size = $data['multi_size'];
	}else{
		$multi_size = 5;
	}

	if( isset( $data['default_value'] ) AND !empty( $data['default_value'] ) ){
		$selected_value = $data['default_value'];
	}else{
		$selected_value = '';
	}

	$list_options_span_class = apply_filters( 'ninja_forms_display_list_options_span_class', '', $field_id );

	switch($list_type){
		case 'dropdown':
			?>
			<select name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>" rel="<?php echo $field_id;?>">
				<?php
				if($label_pos == 'inside'){
					?>
					<option value=""><?php echo $label;?></option>
					<?php
				}
				foreach($options as $option){

					if(isset($option['value'])){
						$value = $option['value'];
					}else{
						$value = $option['label'];
					}

					$value = htmlspecialchars( $value, ENT_QUOTES );

					if(isset($option['label'])){
						$label = $option['label'];
					}else{
						$label = '';
					}

					if(isset($option['display_style'])){
						$display_style = $option['display_style'];
					}else{
						$display_style = '';
					}

					if ( isset( $option['disabled'] ) AND $option['disabled'] ){
						$disabled = 'disabled';
					}else{
						$disabled = '';
					}

					$label = htmlspecialchars( $label, ENT_QUOTES );

					$label = stripslashes( $label );

					$label = str_replace( '&amp;', '&', $label );

					$field_label = $data['label'];

					if($list_show_value == 0){
						$value = $label;
					}


					if ( $selected_value == $value OR ( is_array( $selected_value ) AND in_array( $value, $selected_value ) ) ) {
						$selected = 'selected';
					}else if( ( $selected_value == '' OR $selected_value == $field_label ) AND isset( $option['selected'] ) AND $option['selected'] == 1 ){
						$selected = 'selected';
					}else{
						$selected = '';
					}

					?>
					<option value="<?php echo $value;?>" <?php echo $selected;?> style="<?php echo $display_style;?>" <?php echo $disabled;?>><?php echo $label;?></option>
				<?php
				}
				?>
			</select>
			<?php
			break;
		case 'radio':
			$x = 0;
			if( $label_pos == 'left' OR $label_pos == 'above' ){
				?><?php

			}
			?><input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>" value=""><span id="ninja_forms_field_<?php echo $field_id;?>_options_span" class="<?php echo $list_options_span_class;?>" rel="<?php echo $field_id;?>"><ul><?php
			foreach($options as $option){

				if(isset($option['value'])){
					$value = $option['value'];
				}else{
					$value = $option['label'];
				}

				$value = htmlspecialchars( $value, ENT_QUOTES );

				if(isset($option['label'])){
					$label = $option['label'];
				}else{
					$label = '';
				}

				if(isset($option['display_style'])){
					$display_style = $option['display_style'];
				}else{
					$display_style = '';
				}

				//$label = htmlspecialchars( $label, ENT_QUOTES );

				$label = stripslashes($label);

				if($list_show_value == 0){
					$value = $label;
				}

				if ( $selected_value == $value OR ( is_array( $selected_value ) AND in_array( $value, $selected_value ) ) ) {
					$selected = 'checked';
				}else if( $selected_value == '' AND isset( $option['selected'] ) AND $option['selected'] == 1 ){
					$selected = 'checked';
				}else{
					$selected = '';
				}
				?><li><label id="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>_label" class="ninja-forms-field-<?php echo $field_id;?>-options" style="<?php echo $display_style;?>" for="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>"><input id="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="radio" class="<?php echo $field_class;?>" value="<?php echo $value;?>" <?php echo $selected;?> rel="<?php echo $field_id;?>" /><?php echo $label;?></label></li><?php
				$x++;
			}
			?></ul></span><li style="display:none;" id="ninja_forms_field_<?php echo $field_id;?>_template"><label><input id="ninja_forms_field_<?php echo $field_id;?>_" name="" type="radio" class="<?php echo $field_class;?>" value="" rel="<?php echo $field_id;?>" /></label></li>
			<?php
			break;
		case 'checkbox':
			$x = 0;
			?><input type="hidden" id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" value=""><span id="ninja_forms_field_<?php echo $field_id;?>_options_span" class="<?php echo $list_options_span_class;?>" rel="<?php echo $field_id;?>"><ul><?php
			foreach($options as $option){

				if(isset($option['value'])){
					$value = $option['value'];
				}else{
					$value = $option['label'];
				}

				$value = htmlspecialchars( $value, ENT_QUOTES );

				if(isset($option['label'])){
					$label = $option['label'];
				}else{
					$label = '';
				}

				if(isset($option['display_style'])){
					$display_style = $option['display_style'];
				}else{
					$display_style = '';
				}

				//$label = htmlspecialchars( $label, ENT_QUOTES );

				$label = stripslashes( $label) ;

				if($list_show_value == 0){
					$value = $label;
				}

				if( isset( $option['selected'] ) AND $option['selected'] == 1 ){
					$checked = 'checked';
				}

				if( is_array( $selected_value ) AND in_array($value, $selected_value) ){
					$checked = 'checked';
				}else if($selected_value == $value){
					$checked = 'checked';
				}else if( $selected_value == '' AND isset( $option['selected'] ) AND $option['selected'] == 1 ){
					$checked = 'checked';
				}else{
					$checked = '';
				}

				?><li><label id="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>_label" class="ninja-forms-field-<?php echo $field_id;?>-options" style="<?php echo $display_style;?>"><input id="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>" name="ninja_forms_field_<?php echo $field_id;?>[]" type="checkbox" class="<?php echo $field_class;?> ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $value;?>" <?php echo $checked;?> rel="<?php echo $field_id;?>"/><?php echo $label;?></label></li><?php
				$x++;
			}
			?></ul></span><li style="display:none;" id="ninja_forms_field_<?php echo $field_id;?>_template"><label><input id="ninja_forms_field_<?php echo $field_id;?>_" name="" type="checkbox" class="<?php echo $field_class;?>" value="" rel="<?php echo $field_id;?>" /></label></li>
			<?php
			break;
		case 'multi':
			?>
			<select name="ninja_forms_field_<?php echo $field_id;?>[]" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>" multiple size="<?php echo $multi_size;?>" rel="<?php echo $field_id;?>" >
				<?php
				if($label_pos == 'inside'){
					?>
					<option value=""><?php echo $label;?></option>
					<?php
				}
				foreach($options as $option){

					if(isset($option['value'])){
						$value = $option['value'];
					}else{
						$value = $option['label'];
					}

					$value = htmlspecialchars( $value, ENT_QUOTES );

					if(isset($option['label'])){
						$label = $option['label'];
					}else{
						$label = '';
					}

					if(isset($option['display_style'])){
						$display_style = $option['display_style'];
					}else{
						$display_style = '';
					}

					$label = htmlspecialchars( $label, ENT_QUOTES );

					$label = stripslashes($label);

					if($list_show_value == 0){
						$value = $label;
					}

					if(is_array($selected_value) AND in_array($value, $selected_value)){
						$selected = 'selected';
					}else if( $selected_value == '' AND isset( $option['selected'] ) AND $option['selected'] == 1 ){
						$selected = 'selected';
					}else{
						$selected = '';
					}

					if( $display_style == '' ){
					?>
					<option value="<?php echo $value;?>" <?php echo $selected;?>><?php echo $label;?></option>
					<?php
					}
				}
				?>
			</select>
			<select id="ninja_forms_field_<?php echo $field_id;?>_clone" style="display:none;" rel="<?php echo $field_id;?>" >
				<?php
				$x = 0;
				foreach($options as $option){

					if(isset($option['value'])){
						$value = $option['value'];
					}else{
						$value = $option['label'];
					}

					$value = htmlspecialchars( $value, ENT_QUOTES );

					if(isset($option['label'])){
						$label = $option['label'];
					}else{
						$label = '';
					}

					if(isset($option['display_style'])){
						$display_style = $option['display_style'];
					}else{
						$display_style = '';
					}

					$label = htmlspecialchars( $label, ENT_QUOTES );

					$label = stripslashes( $label );

					if($list_show_value == 0){
						$value = $label;
					}

					if(is_array($selected_value) AND in_array($value, $selected_value)){
						$selected = 'selected';
					}else{
						$selected = '';
					}

					if( $display_style != '' ){
					?>
					<option value="<?php echo $value;?>" title="<?php echo $x;?>" <?php echo $selected;?>><?php echo $label;?></option>
					<?php
					}
					$x++;
				}
				?>
			</select>
			<?php
			break;
	}
}
