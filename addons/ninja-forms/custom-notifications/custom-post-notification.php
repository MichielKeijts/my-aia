<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */


// DO NOT auto include, as the library is included as include -> return 

class MY_AIA_NF_SAVE_TO_POST extends NF_Notification_Base_Type {
	/**
	 * Loaded in initialize
	 * @var MY_AIA_BASE post class 
	 */
	var $post;
	
	var $notification_id = 0;
	
	/**
	 * Get things rolling
	 */
	function __construct($post_type = 'post') {
		$this->name = __( 'Save to Custom Post', 'ninja-forms' );
		$this->post_type = $post;
	}
	
	public function initialize($post_type = NULL) {
		if ($post_type) {
			$this->post_type = $post_type;
		} else {
			// get post type to save to
			$this->post_type = Ninja_Forms()->notification( $this->notification_id )->get_setting( 'post_type' );
		}
		
		$class = $this->get_post_class(); 
		if ($class) $this->post = new $class();		
	}
	
	/**
	 * Get the post class associated with post_type
	 */
	private function get_post_class() {
		//form: MY_AIA_<POST_TYPE>
		$className = 'MY_AIA_' . strtoupper(str_replace('-', '_', $this->post_type));
		
		if (class_exists($className)) {
			return $className;
		}
		
		// no valid class found
		return FALSE;
	}

	/**
	 * Output our edit screen
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function edit_screen( $id = '' ) {
		$loc_opts = apply_filters( 'nf_success_message_locations',
			array(
				array( 'action' => 'ninja_forms_display_before_fields', 'name' => __( 'Before Form', 'ninja-forms' ) ),
				array( 'action' => 'ninja_forms_display_after_fields', 'name' => __( 'After Form', 'ninja-forms' ) ),
			)
		);
		?>
		<!-- <tr>
			<th scope="row"><label for="success_message_loc"><?php _e( 'Location', 'ninja-forms' ); ?></label></th>
			<td>
				<select name="settings[success_message_loc]">
					<?php
					foreach ( $loc_opts as $opt ) {
						?>
						<option value="<?php echo $opt['action'];?>" <?php selected( nf_get_object_meta_value( $id, 'success_message_loc' ), $opt['action'] ); ?>><?php echo $opt['name'];?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr> -->
		<r>
			<th scope="row"><label for="settings[post_type]"><?php _e( 'Post type to save', 'ninja-forms' ); ?></label></th>
			<td>
				<input type="text" name="settings[post_type]" value="<?php  echo nf_get_object_meta_value( $id, 'post_type' ); ?>" placeholder="e.g.: partner">
			</td>
		</tr>

		<?php
	}

	/**
	 * Process our Success Message notification
	 * 
	 * @global Ninja_Forms_Processing $ninja_forms_processing
	 * @return void
	 */
	public function process( $id ) {
		global $ninja_forms_processing;
		
		$this->notification_id = $id; // set to global
		$this->initialize();

		// get submission data
		$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
		$submission = Ninja_Forms()->sub( $sub_id );
				
		// set all fields
		foreach ($submission->field as $field=>$value) {
			$field = ninja_forms_get_field_by_id($field);
			// in the adminlabel the post key is saved.
			if (!empty($field['data']['admin_label']))
				$this->post->set($field['data']['admin_label'], $value);
		}
				
		$this->post->save();
	}
	
	
	public function process_setting($id, $setting, $html = 1) {
		parent::process_setting($id, $setting, $html);
	}
}

return new MY_AIA_NF_SAVE_TO_POST();