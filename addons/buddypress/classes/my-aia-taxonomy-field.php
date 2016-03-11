<?php
/**
 * MY_AIA addon to BuddyPress
 * 
 * BuddyPress XProfile Classes.
 *
 * @package MY_AIA
 * @subpackage XProfileClasses
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;




/**
 * Multi-selectbox xprofile field type.
 *
 * @since 2.0.0
 */
class MY_AIA_BUDDYPRESS_TAXONOMY_FIELD extends BP_XProfile_Field_Type {
	/**
	 * Constructor for the multi-selectbox field type.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Taxonomy', 'xprofile field type taxonomy', 'my-aia' );
		$this->name     = _x( 'Taxonomy Select Box', 'xprofile field type', 'my-aia' );

		$this->supports_multiple_defaults = false;
		$this->accepts_null_value         = true;
		$this->supports_options           = false;

		$this->set_format( '/^.+$/', 'replace' );

		/**
		 * Fires inside __construct() method for MY_AIA_BUDDYPRESS_TAXONOMY_FIELD class.
		 *
		 * @since 2.0.0
		 *
		 * @param MY_AIA_BUDDYPRESS_TAXONOMY_FIELD $this Current instance of
		 *                                                    the field type multiple select box.
		 */
		do_action( 'MY_AIA_BUDDYPRESS_TAXONOMY_FIELD', $this );
	}

	/**
	 * Output the edit field HTML for this field type.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_properties Optional key/value array of
	 *                              {@link http://dev.w3.org/html5/markup/select.html permitted attributes}
	 *                              that you want to add.
	 */
	public function edit_field_html( array $raw_properties = array() ) {
		global $field;
		// User_id is a special optional parameter that we pass to
		// {@link bp_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = bp_displayed_user_id();
		}

		$r = bp_parse_args( $raw_properties, array(
			//'multiple' => 'false',
			'id'       => bp_get_the_profile_field_input_name() . '[]',
			'name'     => bp_get_the_profile_field_input_name() . '[]',
		) ); 
		
		$taxonomy_name = bp_xprofile_get_meta(bp_get_the_profile_field_id(), 'field', 'taxonomy_name');
		$taxonomy_display_select = bp_xprofile_get_meta( bp_get_the_profile_field_id(), 'field', 'taxonomy_display_select');
		$taxonomy_options = get_terms($taxonomy_name, array( 'hide_empty' => false ));
		
		// @TODO show multiple select, in future ajax_search box
		if ($taxonomy_display_select!=1) {
			$r = bp_parse_args( $raw_properties, array('multiple'=>'true'));
		}
		
		$values = explode(', ',bp_unserialize_profile_field($field->data->value));
		?>

		<label for="<?php bp_the_profile_field_input_name(); ?>[]">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</label>

		<?php	
			/** This action is documented in bp-xprofile/bp-xprofile-classes */
			do_action( bp_get_the_profile_field_errors_action() );
			
		?>
		<select <?php echo $this->get_edit_field_html_elements( $r ); ?> >
			<?php
			foreach ( $taxonomy_options as $taxonomy ) {
				$selected = in_array($taxonomy->term_id,$values)?"selected":"";
				echo "<option value='{$taxonomy->term_id}' {$selected}>" . $taxonomy->name . '</option>';
			}
			?>
		</select>
		<?php if ( ! bp_get_the_profile_field_is_required() ) : ?>
			<a class="clear-value" href="javascript:clear( '<?php echo esc_js( bp_get_the_profile_field_input_name() ); ?>[]' );">
				<?php esc_html_e( 'Clear', 'buddypress' ); ?>
			</a>
		<?php endif; ?>

		<?php
	}

	/**
	 * Output the edit field options HTML for this field type.
	 *
	 * BuddyPress considers a field's "options" to be, for example, the items in a selectbox.
	 * These are stored separately in the database, and their templating is handled separately.
	 *
	 * This templating is separate from {@link BP_XProfile_Field_Type::edit_field_html()} because
	 * it's also used in the wp-admin screens when creating new fields, and for backwards compatibility.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. The arguments passed to {@link bp_the_profile_field_options()}.
	 */
	public function edit_field_options_html( array $args = array() ) {
		$original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		$options = $this->field_obj->get_children();
		$html    = '';

		if ( empty( $original_option_values ) && ! empty( $_POST['field_' . $this->field_obj->id] ) ) {
			$original_option_values = sanitize_text_field( $_POST['field_' . $this->field_obj->id] );
		}

		$option_values = ( $original_option_values ) ? (array) $original_option_values : array();
		for ( $k = 0, $count = count( $options ); $k < $count; ++$k ) {
			$selected = '';

			// Check for updated posted values, but errors preventing them from
			// being saved first time.
			foreach( $option_values as $i => $option_value ) {
				if ( isset( $_POST['field_' . $this->field_obj->id] ) && $_POST['field_' . $this->field_obj->id][$i] != $option_value ) {
					if ( ! empty( $_POST['field_' . $this->field_obj->id][$i] ) ) {
						$option_values[] = sanitize_text_field( $_POST['field_' . $this->field_obj->id][$i] );
					}
				}
			}

			// Run the allowed option name through the before_save filter, so
			// we'll be sure to get a match.
			$allowed_options = xprofile_sanitize_data_value_before_save( $options[$k]->name, false, false );

			// First, check to see whether the user-entered value matches.
			if ( in_array( $allowed_options, $option_values ) ) {
				$selected = ' selected="selected"';
			}

			// Then, if the user has not provided a value, check for defaults.
			if ( ! is_array( $original_option_values ) && empty( $option_values ) && ! empty( $options[$k]->is_default_option ) ) {
				$selected = ' selected="selected"';
			}

			/**
			 * Filters the HTML output for options in a multiselect input.
			 *
			 * @since 1.5.0
			 *
			 * @param string $value    Option tag for current value being rendered.
			 * @param object $value    Current option being rendered for.
			 * @param int    $id       ID of the field object being rendered.
			 * @param string $selected Current selected value.
			 * @param string $k        Current index in the foreach loop.
			 */
			$html .= apply_filters( 'bp_get_the_profile_field_options_multiselect', '<option' . $selected . ' value="' . esc_attr( stripslashes( $options[$k]->name ) ) . '">' . esc_html( stripslashes( $options[$k]->name ) ) . '</option>', $options[$k], $this->field_obj->id, $selected, $k );
		}

		echo $html;
	}

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		$r = bp_parse_args( $raw_properties, array(
			'multiple' => 'multiple'
		) );
		
		//$taxonomy_name = bp_xprofile_get_meta( bp_the_profile_field_id(), 'field', 'taxonomy_name');
		//$taxonomy_display_select = bp_xprofile_get_meta( $current_field->id, 'field', 'taxonomy_display_select');
		//$taxonomy_options = get_taxonomy($taxonomy_name);
		?>

		<label for="<?php bp_the_profile_field_input_name(); ?>" class="screen-reader-text"></label>
			<select <?php echo $this->get_edit_field_html_elements( $r ); ?> >
			<?php

			/*foreach ( $taxonomy_options as $key=>$taxonomy ) {
				$selected = $taxonomy_name==$key?"selected":"";
				echo "<option name='{$key}' {$selected}>" . $taxonomy . '</option>';
			}*/

			?>
		</select>
		<?php
	}

	/**
	 * Output HTML for this field type's children options on the wp-admin Profile Fields,
	 * "Add Field" and "Edit Field" screens.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the current
	 *                                         field's child options.
	 */
	public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
		if ( false === $type ) {
			return;
		}

		$class            = $current_field->type != $type ? 'display: none;' : '';
		$current_type_obj = bp_xprofile_create_field_type( $type );
		
		
		$taxonomy_name = bp_xprofile_get_meta( $current_field->id, 'field', 'taxonomy_name');
		$taxonomy_display_select = bp_xprofile_get_meta( $current_field->id, 'field', 'taxonomy_display_select');
		?>

		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Please enter options for this Field:', 'buddypress' ); ?></h3>
			<div class="inside">
				<p>
					<label for="taxonomy_name"><?php esc_html_e( 'Taxnonomy'); ?>:</label>
					<select name="taxonomy_name" id="taxonomy_name_<?php echo esc_attr( $type ); ?>" >
						<?php

						$taxonomies = get_taxonomies(); 
						foreach ( $taxonomies as $key=>$taxonomy ) {
							$selected = $taxonomy_name==$key?"selected":"";
							echo "<option name='{$key}' {$selected}>" . $taxonomy . '</option>';
						}

						?>
					</select>
				</p>
				
				<p>
					<label for="taxonomy_display_select"><?php esc_html_e( 'Toon als SELECT box? (anders normale taxonomy box)'); ?>:</label>
					<input type="checkbox" name="taxonomy_display_select" id="taxonomy_display_select_<?php echo esc_attr( $type ); ?>" value="1" 
					<?= $taxonomy_display_select==1?"checked":"" ?>
						   />
				</p>

				<?php
				/**
				 * Fires at the end of the new field additional settings area.
				 *
				 * @since 2.3.0
				 *
				 * @param BP_XProfile_Field $current_field Current field being rendered.
				 */
				do_action( 'bp_xprofile_admin_new_field_additional_settings', $current_field ) ?>
			</div>
		</div>

		<?php
	}
	
	/**
	 * Allow field types to modify the appearance of their values.
	 *
	 * By default, this is a pass-through method that does nothing. Only
	 * override in your own field type if you need to provide custom
	 * filtering for output values.
	 *
	 * @since 2.1.0
	 * @since 2.4.0 Added `$field_id` parameter.
	 *
	 * @param mixed $field_value Field value.
	 * @param int   $field_id    ID of the field.
	 *
	 * @return mixed
	 */
	public static function display_filter( $field_value, $field_id = '' ) {
		$newValue="";
		$values = explode(', ', $field_value);
		
		// we have taxonomy ID, convert to slug, 
		foreach ($values as $value)	 {
			$term = get_term($value);
			$newValue = sprintf('%s <a href="/members/?members_search=%s">%s</a>', $newValue, $value, $term->name);
		}
		
		return $newValue;
	}
}
