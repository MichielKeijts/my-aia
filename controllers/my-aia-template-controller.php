<?php

/*
 * Copyright (C) 2016 Michiel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


/**
 * Description of my-aia-template-controller
 *
 * @author Michiel
 */
class MY_AIA_TEMPLATE_CONTROLLER extends MY_AIA_CONTROLLER {

	/**
	 * Name of the class
	 */
	public $classname = 'template';
	
	
	/**
	 * Template model
	 * @var MY_AIA_TEMPLATE
	 */
	public $TEMPLATE;
	
	/**
	 * Has a attribute form
	 * @var bool
	 */
	public $has_attribute_form = TRUE;


	/**
	 * Before Filter function
	 * called before most of the wordpress logic happens.
	 * -- add ajax functions over here
	 */
	public function before_filter() {
		add_action( 'wp_ajax_my_aia_admin_get_template_fields', array($this, 'ajax_get_template_fields'), 1);	
		parent::before_filter();
	}

	/**
	 * Get the template fields for the post type defined in GET['post_type']
	 * on the admin interface.
	 * Sends JSON output
	 * @retun void
	 */
	public function ajax_get_template_fields() {
		if ($term = filter_input(INPUT_GET, 'post_type')) {
			// get the option data
			$fields = $this->get_parent_type_fields($term);
						
			wp_send_json_success(array('fields'=>$fields));		
		}
		wp_send_json_error();
	}
	
	
		
	/**
	 * Intilialize & Parse the template .
	 * @param int $id
	 * @param int $parent_id
	 * @param string $path to save template, without trailing slash
	 * @return string
	 */
	public function parse($id, $parent_id, $path = WP_CONTENT_DIR) {
		if (!is_numeric($this->TEMPLATE->ID)) {
			$this->TEMPLATE->get($id);
		}
		
		// if not a valid template...
		if (!is_numeric($this->TEMPLATE->ID))
			return FALSE;
		
		$this->parent_id = $parent_id;
		
		// parse the content first, put into content_filtered
		$this->parse_content();
		
		// auto parse
		switch (strtoupper($this->TEMPLATE->document_type)) {
			case "EMAIL":
				return $this->TEMPLATE->post_content_filtered;
				break;
			case 'PDF':
			default:
				include_once MY_AIA_PLUGIN_DIR .'vendor/tcpdf/tcpdf.php';
				
				// set filename to (ex.) order_1092.pdf
				$this->filename = sprintf('%s/%s_%s.pdf', $path, strtolower($this->TEMPLATE->parent_type), $this->parent_id); 
				
				// return empty string on success
				if (empty($this->parse_pdf("F")))
					return $this->filename;
				break;
		}
	}
	
	/**
	 * Get all the various related elements and parse
	 */
	private function parse_content() {
		// first get info from the parent type
		$className = 'MY_AIA_' . strtoupper($this->TEMPLATE->parent_type);
		if (class_exists($className)) {
			$parent = new $className;
			if ($parent->get($this->parent_id)!==FALSE) {
				$this->parse_parent_type($parent);
			}
		} 
		
		// next get the user info if possible
		$user_id = NULL;
		if (isset($parent->assigned_user_id))	$user_id = $parent->assigned_user_id;
		elseif (isset($parent->person_id))		$user_id = $parent->person_id;
		
		if ($user_id) {
			$user = get_user_by('id', $user_id);
			$this->parse_parent_type($user->data);
		}
		
		$this->TEMPLATE->post_content_filtered = apply_filters('the_content', $this->TEMPLATE->post_content);
		
		// parse the content
		$this->TEMPLATE->post_content_filtered = str_replace(
			array_keys($this->_template_fields),
			$this->_template_fields,
			$this->TEMPLATE->post_content_filtered
		);
	}
	
	/**
	 * Sets the _template_fields 
	 * @param array $parent
	 */
	private function parse_parent_type($parent = array()) {
		foreach ($parent as $key=>$value) {
			if ($key=='btw') {
				echo "";
			}
			// if is a property/method of the parent
			if (method_exists($parent, sprintf('template_%s', strtolower($key)))) {
				$value =  $parent->{sprintf('template_%s', strtolower($key))}($value);	// set
				if (is_numeric($value)) {
					$val = $value * 1.0;
					$value = number_format($val,2,",",".");
				}
				$this->_template_fields[sprintf('%%%s%%',  strtoupper($key))] = $value;
			} elseif (property_exists($parent, $key) && !is_object($parent->{$key}) && !is_array($parent->{$key})) {
				if (is_numeric($value) && strpos($key,'id') ===FALSE) {
					$val = $value * 1.0;
					$value = number_format($val,2,",",".");
				}
				$this->_template_fields[sprintf('%%%s%%',  strtoupper($key))] = $value;	// set 
			}
		}
	}
	
	/**
	 * Parse and create a document. Uses object variables:
	 * <li>$this->author</li>
	 * <li>$this->subject as subject</li>
	 * <li>$this->title as title</li>
	 * @param string $destination
	 */
	public function parse_pdf($destination = 'D') {
		$internal_encoding = mb_internal_encoding();
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		mb_internal_encoding($internal_encoding);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Athletes in Action');
		$pdf->SetTitle($this->post_title);
		$pdf->SetSubject($this->subject);
		$pdf->SetKeywords('PDF');

		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
		$pdf->SetHeaderData('', '', 'Athletes in Action', '');

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 0);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 10);

		// add a page
		$pdf->AddPage();

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		
		
		$html = "<html><body>{$this->TEMPLATE->post_content_filtered}</body></html>";
		
		
		//$html=utf8_encode($html);
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
		
		// save as a FILE
		return $pdf->Output($this->filename, $destination);
	}
	
	/**
	 * Add Meta Box to display
	 */
	public function set_meta_boxes() {
		add_meta_box('my-aia-'.$this->TEMPLATE->post_type.'-post_type', __('Applies to Post Type','my-aia'), array($this,'display_meta_box_post_type'), $this->TEMPLATE->post_type, 'side', 'high');
	}
	
	
	/** Meta Box Display Functions */
	public function display_meta_box_post_type() {
		global $post;
		$this->TEMPLATE->get($post);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-template-ui', MY_AIA_PLUGIN_URL . 'assets/js/my-aia-custom-post-template-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/views/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	
	/**
	 * Returns a list of fields, which can be used in template functions to 
	 * choose from the available fields
	 */
	public function get_parent_type_fields($post_type = NULL) {
		if (!$post_type) {
			$post_type = $this->TEMPLATE->parent_type;
		}
		
		$return_fields = array(
			'user_nice_name',
			'user_id',
			'user_email',
			'post_type',
			'post_content',
			'post_date'
		);
		
		// use array_keys, as we are not interested in database fields, but in OBJECT properties
		// call the user function to get the controller
		$fields = MY_AIA::$controllers[$post_type]->get_model()->fields;
		$return_fields = array_merge($return_fields, array_keys($fields));
		
		// send JSON
		return $return_fields;		
	}
}