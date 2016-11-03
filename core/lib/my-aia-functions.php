<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 * 
 * 
 * List of Functions, which are not object oriented
 */


/**
 * Set Capabitilities for roles
 * @global \WP_Roles $wp_roles
 * @param array $roles
 * @param array $caps
 */
function my_aia_set_mass_capabilities( $roles, $caps ){
	global $wp_roles;
	foreach( $roles as $user_role ){
		foreach($caps as $cap){
			$wp_roles->add_cap($user_role, $cap);
		}
	}
}

/*
 * Set Roles 
 * @param array $roles roles to declare
 * @param array $caps caps to initiate (default: empty array)
 */
function my_aia_set_mass_roles( $roles, $capabilities = array()) {
	foreach ($roles as $role) {
		remove_role( $role, __($role), $capabilities );
		add_role( $role, __($role), $capabilities );
	}
}

//Register the Upload field
/*add_action('init', 'my_custom_field_register');
function my_custom_field_register(){
	$args = array(
		'name' => 'Mijn Custom Field',
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'my_text',
				'label' => 'My Text Label',
				'class' => 'widefat',
			),
			array(
				'type' => 'select',
				'name' => 'my_select',
				'label' => 'My Select Label',
			),
		),
		'display_function' => 'ninja_forms_field_upload_display',
		'sub_edit_function' => 'ninja_forms_field_upload_sub_edit',
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
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_upload_pre_process',
		'process' => 'ninja_forms_field_upload_process',
		'req_validation' => 'ninja_forms_field_upload_req_validation',
	);

	if (function_exists('ninja_forms_register_field')) return ninja_forms_register_field('_upload', $args);
	return false;
}*/

function my_aia_nf_add_notifications ($nf_notification_types) {
	$nf_notification_types['custom_post'] = require_once (MY_AIA_PLUGIN_DIR . 'addons/ninja-forms/custom-notifications/custom-post-notification.php');
	return $nf_notification_types;
}

/**
 * Returns the lower_case_and_underscore style of $name. 
 *  - to _
 *  spaces to _
 * @param string $name
 * @return string
 */
function lowercase_underscore ($name) {
	return str_replace(array('-',' '), array('_','_'), strtolower($name));
}

/**
 * Returns the wp-style-style of $name. 
 *  underscores to -
 *  spaces to -
 * @param string $name
 * @return string
 */
function lowercase_wordpressize ($name) {
	return str_replace(array('_',' '), array('-','-'), strtolower($name));
}


/*
 * Saves and autocreates options
 * @param array $fields 
 * @param int	$method (default INPUT_POST)
 * @return bool 
 */
function my_aia_save_options ($fields, $method=INPUT_POST) {
	foreach ($fields as $var=>$params) {
		if (!$param['autoload']) $autoload = FALSE;
		else $autoload = TRUE;
		
		if ($value = filter_input($method, $var)) {
			update_option('my_aia_'.$var, $value, $autoload);
		}
	}
	return true;
}


/*
 * Saves and autocreates options
 * @param array $fields 
 * @param int	$method (default INPUT_POST)
 * @return array $data 
 */
function my_aia_get_options_data ($fields) {
	$data = array();
	foreach ($fields as $var=>$params) {
		if (!isset($param['default'])) {
			$data[$var] = get_option('my_aia_'.$var);
		} else {
			$data[$var] = get_option('my_aia_'.$var, $param['default']);
		}
	}
	return $data;
}


/**
 * Get the default (init) set of the input field.
 * 
 * @param string $field Fieldname
 * @return array (type: .. ,name:..
 */
function my_aia_get_default_field_type($field) {
	return array('type'=>'%s','id'=>$field,'name'=>$field,'label'=>$field);
}


/**
 * Add Attributes form to the post edit page. This is a same idea as custom post types,
 * but now in the MY_AIA setup
 * @global array $wp_meta_boxes
 * @param string $name Name of the (replaced) form
 * @param string $post_type
 * @param array $data array('field' => 'value') pairs
 * @return boolean
 */
function my_aia_add_attributes_form($name='em-event-attributes', $post_type = EM_POST_TYPE_EVENT, $data, $hide_header = FALSE) {
	global $wp_meta_boxes;

	// remove the old box, as we are called before!
	// remove_meta_box('em-event-attributes', EM_POST_TYPE_EVENT, 'normal');
	// above not working, is in sorted part, not being removed by remove_meta_box(!)
	if (is_array($wp_meta_boxes[$post_type]['normal']['sorted']) && array_key_exists($name, $wp_meta_boxes[$post_type]['normal']['sorted'])) {
		unset($wp_meta_boxes[$post_type]['normal']['sorted']['em-event-attributes']);
	}
	?>
		<table class="form-data">
			<?php if (!$hide_header): ?>
			<thead>
				<tr>
					<td valign="top"><?= __('Attribute Name','my-aia'); ?></td>
					<td valign="top"><?= __('Attribute Value','my-aia'); ?></td>
				</tr>
			</thead>
			<?php endif; // $hide_header ?>
			<tbody>
	<?php
		foreach ($data as $field):
			switch ($field['type']) {
				case "%b":
					?>
					<tr>
						<td><label for="<?= $field['name']; ?>"><?= $field['label']; ?>:</label></td>
						<td><input type='checkbox' id="<?= $field['name']; ?>" name="<?= $field['name']; ?>" <?= $field['value']>0?'checked':''; ?>  /></td>
					</tr>
					<?php
					break;
				case "%s":
				case "%d":
				default:
					?>
					<tr>
						<td><label for="<?= $field['name']; ?>"><?= $field['label']; ?>:</label></td>
						<td><input type='text' name="<?= $field['name']; ?>" value="<?= $field['value']; ?>" /></td>
					</tr>
					<?php
			}
		endforeach; // loop over $fields
	?>
			</tbody>
		</table>

	<?php 
	return true;
}


/**
 * Works much like <a href="http://codex.wordpress.org/Function_Reference/locate_template" target="_blank">locate_template</a>, except it takes a string instead of an array of templates, we only need to load one.
 * Copied and modified from Events-Manager
 * @param string $template_name
 * @param boolean $load
 * @uses locate_template()
 * @return string
 */
function my_aia_locate_template( $template_name, $load=false, $args = array() ) {
	//First we check if there are overriding tempates in the child or parent theme
	$located = locate_template(array(MY_AIA_PLUGIN_DIR.$template_name, $template_name));
	if( !$located ){
		if ( file_exists(MY_AIA_PLUGIN_DIR.'/views/default/'.$template_name) ) {
			$located = MY_AIA_PLUGIN_DIR.'/views/default/'.$template_name;
		}
	}
	$located = apply_filters('my_aia_locate_template', $located, $template_name, $load, $args);
	if( $located && $load ){
		if( is_array($args) ) extract($args);
		include($located);
	}
	return $located;
}


/**
 * Shorthand to return the controller of MY_AIA_ORDER declared in 
 * MY_AIA::$post_types[MY_AIA_ORDER]
 * @return MY_AIA_ORDER_CONTROLLER Order Instace
 */
function my_aia_order() {
	return MY_AIA::$controllers[MY_AIA_POST_TYPE_ORDER];
}

/**
 * Shorthand to return the controller of MY_AIA_INVOICE declared in 
 * MY_AIA::$post_types[MY_AIA_INVOICE]
 * @return MY_AIA_INVOICE_CONTROLLER Order Instace
 */
function my_aia_invoice() {
	return MY_AIA::$controllers[MY_AIA_POST_TYPE_INVOICE];
}

/**
 * Shorthand to return the controller of MY_AIA_INVOICE declared in 
 * MY_AIA::$post_types[MY_AIA_POST_TYPE_PAYMENT]
 * @return MY_AIA_POST_TYPE_PAYMENT Payment Instace
 */
function my_aia_payment() {
	return MY_AIA::$controllers[MY_AIA_POST_TYPE_PAYMENT];
}

/**
 * Shorthand to return the controller of MY_AIA_INVOICE declared in 
 * MY_AIA::$post_types[MY_AIA_POST_TYPE_PAYMENT]
 * @return MY_AIA_WPDMPRO_CONTROLLER Payment Instace
 */
function my_aia_wpdmpro() {
	return MY_AIA::$controllers[MY_AIA_POST_TYPE_DOCUMENT];
}




/**
 * Get Terms.
 *
 * This function adds a custom property (image_id) to each
 * object returned by WordPress core function get_terms().
 * This property will be set for all term objects. In cases
 * where a term has an associated image, "image_id" will
 * contain the value of the image object's ID property. If
 * no image has been associated, this property will contain
 * integer with the value of zero.
 *
 * @see http://codex.wordpress.org/Function_Reference/get_terms
 *
 * Recognized Arguments:
 *
 * cache_images (bool) If true, all images will be added to
 * WordPress object cache. If false, caching will not occur.
 * Defaults to true. Optional.
 *
 * having_images (bool) If true, the returned array will contain
 * only terms that have associated images. If false, all terms
 * of the taxonomy will be returned. Defaults to true. Optional.
 *
 * taxonomy (string) Name of a registered taxonomy to
 * return terms from. Defaults to "category". Optional.
 *
 * term_args (array) Arguments to pass as the second
 * parameter of get_terms(). Defaults to an empty array.
 * Optional.
 *
 * @param     mixed     Default value for apply_filters() to return. Unused.
 * @param     array     Named arguments. Please see above for explantion.
 * @return    array     List of term objects.
 *
 * @access    private   Use the 'taxonomy-images-get-terms' filter.
 * @since     0.7
 */
function my_aia_taxonomy_images_plugin_get_terms( $args = array() ) {
	$filter = 'taxonomy-images-get-terms';
	if ( current_filter() !== $filter ) {
		taxonomy_image_plugin_please_use_filter( __FUNCTION__, $filter );
	}

	$args = wp_parse_args( $args, array(
		'cache_images'  => true,
		'having_images' => false,
		'taxonomy'      => 'category',
		'term_args'     => array(),
	) );

	$args['taxonomy'] = explode( ',', $args['taxonomy'] );
	$args['taxonomy'] = array_map( 'trim', $args['taxonomy'] );

	foreach ( $args['taxonomy'] as $taxonomy ) {
		if ( ! taxonomy_image_plugin_check_taxonomy( $taxonomy, $filter ) ) {
			return array();
		}
	}

	$assoc = taxonomy_image_plugin_get_associations();
	if ( ! empty( $args['having_images'] ) && empty( $assoc ) ) {
		return array();
	}

	//$terms = get_terms( $args['taxonomy'], $args['term_args'] );
	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) ) {
		return array();
	}

	$image_ids = array();
	$terms_with_images = array();
	foreach ( (array) $terms as $key => $term ) {
		$terms[ $key ]->image_id = 0;
		if ( array_key_exists( $term->term_taxonomy_id, $assoc ) ) {
			$terms[ $key ]->image_id = $assoc[ $term->term_taxonomy_id ];
			$image_ids[] = $assoc[ $term->term_taxonomy_id ];
			if ( ! empty( $args['having_images'] ) ) {
				$terms_with_images[] = $terms[ $key ];
			}
		}
	}
	$image_ids = array_unique( $image_ids );

	if ( ! empty( $args['cache_images'] ) ) {
		$images = array();
		if ( ! empty( $image_ids ) ) {
			$images = get_children( array( 'include' => implode( ',', $image_ids ) ) );
		}
	}

	if ( ! empty( $terms_with_images ) ) {
		return $terms_with_images;
	}
	return $terms;
}

/**
 * 
 * @global BuddyPress $bp
 * @global WP_User $current_user
 * @global wpdb $wpdb
 * @param array $atts
 * @param string $content
 * @return string
 */
function my_aia_shortcode_show_for_group($atts, $content=NULL) {
	global $bp, $current_user, $wpdb;
	$a = shortcode_atts(array('name'=>NULL,'id'=>0), $atts );
	extract($a);
	
	if ($name == NULL && $id == 0) return $content;
	
	// remove @.. @voetbal, @..
	// convert to Array
	$name = explode(',',str_replace('@', '', $name)); 
	
	if ($id==0) {
		// get group ID
		$groups = new BP_Groups_Group();
		$results = $groups->get(array('name'=>$name)); 
		if (isset($results['groups'][0]->id)) {
			$id = array();
			foreach ($results['groups'] as $group) {
				$id[] = $group->id; // append id's
			}
		} else {
			// no ID found, return all
			return $content;
		}		
	} else {
		$id = explode(',',$id); // create array of id's 
	}
	// we have a name and an id, check if user has access
	$n = $wpdb->query(sprintf('SELECT user_id FROM %s WHERE user_id=%d AND group_id IN(%s)', $bp->groups->table_name_members, $current_user->ID, implode(',',$id)));
	if ($n && $n>0) {
		return $content;
	}
		
	// return nothing if no access
	return "";	
}
add_shortcode('show_for_groups', 'my_aia_shortcode_show_for_group');


/**
 * Die and throw 404
 * @param type $die
 */
function throw_404 ($die = TRUE) {
	status_header( 404 );
	nocache_headers();
	include( get_query_template( '404' ) );
	if ($die) die();
}

/**
 * Auto class loader
 * @param type $classname
 */
function my_aia_autoloader($classname) {
	$classname = str_replace(array('MY_AIA_','_'),array('','-'), $classname);
	if (strpos(strtolower($classname),'controller')) {
		$filename = sprintf('%s/controllers/my-aia-%s.php', MY_AIA_PLUGIN_DIR, strtolower($classname));
	} elseif (strpos(strtolower($classname),'processflow')) {
		$filename = sprintf('%s/core/processflow/my-aia-%s.php', MY_AIA_PLUGIN_DIR, strtolower($classname));
	} else {
	
		// find in ./models/
		$filename = sprintf('%s/models/my-aia-%s.php', MY_AIA_PLUGIN_DIR, strtolower($classname));
		
		// find in ./core/lib
		if (!file_exists($filename))				
			$filename = sprintf('%s/core/lib/my-aia-%s.php', MY_AIA_PLUGIN_DIR, strtolower($classname));
	
		// find in ./core/crmsync
		if (!file_exists($filename))				
			$filename = sprintf('%s/core/lib/my-aia-%s.php', MY_AIA_PLUGIN_DIR, strtolower($classname));
	}

	// try and load classname if fileexists
	if (file_exists($filename))
		include_once($filename);
}
spl_autoload_register('my_aia_autoloader');