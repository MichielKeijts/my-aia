<?php if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * @copyright (c) 2016, Michiel Keijts
 */

function my_aia_ninja_forms_upload_field_enque_scripts() {
	my_aia_ninja_forms_upload_field_enque_styles();
	wp_enqueue_script('jquery-ui-widget', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/vendor/jquery.ui.widget.js', array('jquery','jquery-ui-core'));
	
	wp_enqueue_script('blueimp-load-image', 'http://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js',array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery-iframe-transport', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.iframe-transport.js', array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery.fileupload', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.fileupload.js', array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery-fileupload-process', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.fileupload-process.js', array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery-fileupload-image', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.fileupload-image.js', array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery-fileupload-audio', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.fileupload-audio.js', array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery-fileupload-video', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.fileupload-video.js', array('jquery','jquery-ui-core'));
	//wp_enqueue_script('jquery-fileupload-validate', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/jquery.iframe-validate.js', array('jquery','jquery-ui-core'));
	
	// wp_enqueue_script('jquery-file-upload', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/js/main.js', array('jquery','jquery-ui-core'));
}

function my_aia_ninja_forms_upload_field_enque_styles() {
	//wp_enqueue_style('bootstrap', 'http://netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css', '', MY_AIA_VERSION );
	wp_enqueue_style('jquery-fileupload-style', MY_AIA_PLUGIN_URL . 'vendor/jQuery-File-Upload/css/style.css', '', MY_AIA_VERSION );
	wp_enqueue_style('jquery-fileupload', MY_AIA_PLUGIN_URL .		'vendor/jQuery-File-Upload/css/jquery.fileupload.css', '', MY_AIA_VERSION );
}

function my_aia_ninja_forms_upload_field_register(){
	$args = array(
		'name' => 'File Upload',
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'my_aia_upload_field',
				'label' => 'Upload Field',
				'class' => 'widefat',
			),
		),
		'display_function' => 'my_aia_ninja_forms_field_upload_display',
		'sub_edit_function' => 'my_aia_ninja_forms_field_upload_sub_edit',
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

	ninja_forms_register_field('upload', $args);
}

/**
 * Placeholder for pre-process function. Validates user input
 * @param int $field_id 
 * @param mixed $user_value
 * @global \Ninja_Forms_Processing $ninja_forms_processing
 */
function ninja_forms_field_upload_pre_process($field_id, $user_value) {
	global $ninja_forms_processing;

	if (!ninja_forms_field_upload_req_validation($field_id, $user_value)) {
		$ninja_forms_processing->add_error( $field_id, __('Er is geen bestand toegevoegd', 'my-aia') );
	}
}

/**
 * Placeholder for upload_process function
 */
function ninja_forms_field_upload_process() {
	
}

/**
 * Validation function
 * @return bool validation successfull
 */
function ninja_forms_field_upload_req_validation($field_id, $user_value) {
	if( strpos($user_value, '.') !== FALSE && file_exists(MY_AIA_PLUGIN_DIR . '../../uploads/my_aia/form_uploads/'.$user_value)) {
		return true;
	} else {
		return false;
	}
}


/**
 * Display function for the upload field
 * @param int $field_id
 * @param array $data
 * @param int $form_id
 */
function my_aia_ninja_forms_field_upload_display( $field_id, $data, $form_id = '' ) {
	add_action('wp_enqueue_scripts', 'my_aia_ninja_forms_upload_field_enque_scripts');
	add_action('wp_enqueue_styles', 'my_aia_ninja_forms_upload_field_enque_styles');
	
	if ( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	} elseif( isset( $data['number_min'] ) ) {
		$default_value = $data['number_min'];
	}

	if ( isset( $data['number_min'] ) ) {
		$min = ' min="' . esc_attr( $data['number_min'] ) . '"';
	} else {
		$min = '';
	}

	if ( isset( $data['number_max'] ) ) {
		$max = ' max="' . esc_attr( $data['number_max'] ) . '"';
	} else {
		$max = '';
	}

	if ( isset( $data['number_step'] ) ) {
		$step = ' step="' . esc_attr( $data['number_step'] ) . '"';
	} else {
		$step = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );

	// get a new name
	$new_name = sprintf('%s_%s_%s_%s', date('Ymd'), !empty($data['admin_label'])?$data['admin_label']:$data['label'], $field_id, md5(microtime()));
	
	if (!empty ($default_value)) {
		$deleteUrl = "/wp-content/plugins/my-aia/vendor/jQuery-File-Upload/server/php/index.php?file=".$default_value;
	} else {
		$deleteUrl = "";
	}
?>

<script>
/*jslint unparam: true, regexp: true */
/*global window, $ */
jQuery(function ($) {
    'use strict';

	var url = '/wp-content/plugins/my-aia/vendor/jQuery-File-Upload/server/php/';
		
	
    jQuery('#fileupload_<?= $field_id; ?>').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000,
		maxNumberOfFiles : 1,
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: true,
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true,
		dropZone: jQuery('#ninja_forms_field_<?= $field_id; ?>_div_wrap'),
    }).on('fileuploadadd', function (e, data) {
		if (data.files.length > 1) return false;
        data.context = $('<div/>').appendTo('#files_<?= $field_id; ?>');
        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                    .append($('<span/>').text(file.name));
            if (!index) {
                node
                   // .append('<br>')
                   // .append(uploadButton.clone(true).data(data));
            }
            node.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
		if (data.files.length > 1) return false;
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                .prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Delete')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress_<?= $field_id; ?> .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
        $.each(data.result.files, function (index, file) {
            if (file.url) {
                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
                $(data.context.children()[index])
                    .wrap(link);
				jQuery('#file_delete_<?= $field_id; ?>').data('deleteUrl',file.deleteUrl).show();
				jQuery('#fileupload_<?= $field_id; ?>').parent().hide();
				$('#ninja_forms_field_<?= $field_id?>').val(file.name); // set name
				$('#fileupload_<?= $field_id; ?>').fileupload('disable');
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }
        });
    }).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	
	if (jQuery('#ninja_forms_field_<?= $field_id?>').val().length > 0) {
		jQuery('#fileupload_<?= $field_id; ?>').parent().hide();
		jQuery('#fileupload_<?= $field_id; ?>').fileupload('disable');
	} else {
		jQuery('#file_delete_<?= $field_id; ?>').hide();
	}
	
	jQuery('#file_delete_<?= $field_id; ?>').on('click', function(e, data) {
		e.preventDefault();
		jQuery.ajax({	
				url: jQuery(this).data('deleteUrl'),
				data: {},
				type: 'DELETE',
				success: function(){
					jQuery('#fileupload_<?= $field_id; ?>').fileupload('enable');
					jQuery('#files_<?= $field_id; ?>').empty();
					jQuery('#ninja_forms_field_<?= $field_id?>').val("");
					jQuery('#file_delete_<?= $field_id; ?>').hide();
					jQuery('#fileupload_<?= $field_id; ?>').parent().show();
				}
		});
	})
});
</script>


		<!-- The fileinput-button span is used to style the file input field as button -->
		<span class="btn btn-success fileinput-button">
			<i class="glyphicon glyphicon-plus"></i>
			<span>Bestanden Toevoegen..</span>
			<!-- The file input field used as target for the file upload widget -->
			<input id="fileupload_<?= $field_id; ?>" type="file" name="files[]" value="<?php echo esc_attr( $default_value ); ?>">
		</span>
		<button class="btn btn-danger" id="file_delete_<?= $field_id; ?>" data-deleteUrl='<?= $deleteUrl; ?>'>Delete</button>
		<br>
		<br>
		<!-- The global progress bar -->
		<div id="progress_<?= $field_id; ?>" class="progress">
			<div class="progress-bar progress-bar-success"></div>
		</div>
		<!-- The container for the uploaded files -->
		<div id="files_<?= $field_id; ?>" class="files">
			<?php if (!empty($default_value)):  // set default value ?>
				<div>
					<a href="/wp-content/uploads/my_aia/form_uploads/<?= $default_value; ?>" target="_blank">
						<p>
							<?php if (preg_match("/\.(jp?eg|png|bmp|gif|svg)/", strtolower($default_value)) >= 1): ?>
								<canvas height="100" width="100"><img src="/wp-content/uploads/my_aia/form_uploads/<?= $default_value; ?>" height='100'></canvas><br>
							<?php endif; // if is image ?>
							<span><?php echo esc_attr( $default_value ); ?></span>
						</p>
					</a>
				</div>	
			<?php endif;?>
		</div>
		
		<input type="hidden" name="_new_name" value="<?= $new_name; ?>">
		<input type="hidden"<?php echo $min . $max . $step; ?> name="ninja_forms_field_<?php echo esc_attr( $field_id ); ?>" id="ninja_forms_field_<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" rel="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $default_value ); ?>"/>
<?php
}
