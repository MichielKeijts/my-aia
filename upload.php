<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
define( 'MY_AIA_PLUGIN_DIR', __DIR__ . '/');
include_once ("vendor/jQuery-File-Upload/server/php/UploadHandler.php");

class MY_AIA_UploadHandler extends UploadHandler {
	protected function get_unique_filename($file_path, $name, $size, $type, $error,
            $index, $content_range) {
		if (isset($_REQUEST['_new_name'])) { 
			list($name, $ext) = explode('.',$name);
			$new_file_path = str_replace ('/', '', $_REQUEST['_new_name']). '.' .$ext;
			return $new_file_path;
		}
		
		return parent::get_unique_filename($file_path, $name, $size, $type, $error,
            $index, $content_range);
	}
}

$upload_handler = new MY_AIA_UploadHandler(array(
	"upload_dir"		=>		MY_AIA_PLUGIN_DIR . "../../uploads/my_aia/form_uploads/",
	"upload_url"		=>		"/wp-content/uploads/forbidden/"
));
