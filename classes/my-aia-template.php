<?php
/**
 * @copyright (c) 2016, (c) Normit, Michiel Keijts
 * @package my-aia
 * @license GPL
 */

/**
 * Definition of the MY_AIA_ORDER post_type and including the custom fields
 * It also enables to create relationships
 */
class MY_AIA_TEMPLATE extends MY_AIA_BASE {
	/**
	 * Definition of the associate WP POST TYPE
	 * @var string
	 */
	var $post_type = MY_AIA_POST_TYPE_TEMPLATE;
	
	/**
	 * If a attribute is present
	 * @var bool 
	 */
	public $has_attribute_form	=	TRUE;
	
	/**
	 * Type of the parent Post, where it belongs to
	 * @var string 
	 */
	public $parent_type	=	NULL;		
	
	/**
	 * Set while parsing, the parent_id where the template gets it data from
	 * @var int
	 */
	public $parent_id	=	0;
	
	/**
	 * Document Type
	 * @var string 
	 */
	public $document_type	=	'PDF';	// or EMAIL
	
	/**
	 * Attribute which is read only
	 * @var string 
	 */
	public $filename		=	NULL;	// used by PDF template / template
	
	public $subject			=	"";
	public $author			=	"Athletes in Action";
	
	/**
	 * Order 
	 * @var \MY_AIA_INVOICE
	 */
	public $parent;

	/**
	 * Holder of the key->value pairs for replacing in the template
	 * @var array 
	 */
	private $_template_fields = array();
	
	/**
	 * @var array List of Fields saved into database. Same list as class variables
	 */
	public $fields = array(
		'ID'				=> array('name'=>'ID','type'=>'%d'),
		'name'				=> array('name'=>'name','type'=>'%s'),
		'description'		=> array('name'=>'description','type'=>'%s'),
		'parent_type'		=> array('name'=>'parent_type','type'=>'%d'),
		'document_type'		=> array('name'=>'document_type','type'=>'%s'),
		'subject'			=> array('name'=>'subject', 'type'=>'%s'),
		'from'				=> array('name'=>'from', 'type'=>'%s'),
	);
	
	
	
	public function __construct($post = NULL) {
		parent::__construct($post);
	}
	
	/**
	 * Create an attribute form, which holds the custom post fields
	 * @global type $post
	 * @return boolean
	 */
	public function get_attributes_form() {
		global $post;
		
		if (!$this->ID && $post && $post->ID)	parent::get($post);		
		
		$displayed_fields = array('ID','name', 'description','assigned_user_id','order_items'); // hide
		//
		// return data
		$data = array();
		foreach ($this->fields as $field):
			if (in_array($field['name'], $displayed_fields)) continue; // step over already displayed fields..
			$field['label'] = __($field['name'],'my-aia'); //my_aia_get_default_field_type($_field);

			// get value (as usually is an array)
			$value = isset($this->$field['name']) ? esc_attr($this->$field['name'], ENT_QUOTES):'';
			//$value = is_array($values[	$field['id'] ]) ?  reset($values[ $field['id'] ]) : $values[$field['id']];
			if (!$value) $value="";

			$field['value'] = $value;
			$data[] = $field;
		endforeach; // loop over $fields
		
		return my_aia_add_attributes_form(MY_AIA_POST_TYPE_TEMPLATE, MY_AIA_POST_TYPE_TEMPLATE, $data);
	}

	/**
	 * Add Meta Box to display
	 */
	public function set_meta_boxes() {
		add_meta_box('my-aia-'.$this->post_type.'-post_type', __('Applies to Post Type','my-aia'), array($this,'display_meta_box_post_type'), $this->post_type, 'side', 'high');
	}
	
	
	/** Meta Box Display Functions */
	public function display_meta_box_post_type() {
		global $post;
		$this->get($post);
		
		// enque script
		wp_enqueue_script( 'my-aia-admin-custom-post-template-ui', MY_AIA_PLUGIN_URL . 'admin/assets/js/my-aia-custom-post-template-ui.js', '', MY_AIA_VERSION );
		
		include(MY_AIA_PLUGIN_DIR . "/admin/view/post_type_templates/" . __FUNCTION__ . '.ctp');
	}
	
	
	/**
	 * Returns a list of fields, which can be used in template functions to 
	 * choose from the available fields
	 */
	public function get_parent_type_fields($post_type = NULL) {
		if (!$post_type) {
			$post_type = $this->parent_type;
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
		$return_fields = array_merge($return_fields, array_keys(MY_AIA::$post_types[$post_type]->fields));
		
		// send JSON
		return $return_fields;		
	}
	
	
	/**
	 * Intilialize & Parse the template .
	 * @param int $id
	 * @param int $parent_id
	 * @param string $path to save template, without trailing slash
	 * @return boolean
	 */
	public function parse($id, $parent_id, $path = WP_CONTENT_DIR) {
		if ($this->get($id) === FALSE) return FALSE;
		
		$this->parent_id = $parent_id;
		
		// parse the content first, put into content_filtered
		$this->parse_content();
		
		// auto parse
		switch (strtoupper($this->document_type)) {
			case "EMAIL":
				$this->parse_pdf();
				break;
			case 'PDF':
			default:
				include_once MY_AIA_PLUGIN_DIR .'vendor/tcpdf/tcpdf.php';
				
				// set filename to (ex.) order_1092.pdf
				$this->filename = sprintf('%s/%s_%s.pdf', $path, strtolower($this->parent_type), $this->parent_id); 
				
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
		$className = 'MY_AIA_' . strtoupper($this->parent_type);
		if (class_exists($className)) {
			$parent = new $className;
			if ($parent->get($this->parent_id)!==FALSE) {
				foreach ($parent as $key=>$value) {
					// if is a property
					if (property_exists($parent, $key)) {
						$this->_template_fields[sprintf('%%%s%%',$key)] = $value;	// set 
					}
				}
			}
		} 
		
		// next get the user info if possible
		$user_id = NULL;
		if (isset($parent->assigned_user_id))	$user_id = $parent->assigned_user_id;
		elseif (isset($parent->person_id))		$user_id = $parent->person_id;
		
		if ($user_id) {
			$user = get_user_by('id', $user_id);
			foreach ($parent as $key=>$value) {
				// if is a property
				if (property_exists($parent, $key)) {
					$this->_template_fields[sprintf('%%%s%%',$key)] = $value;	// set 
				}
			}
		}
		
		// parse the content
		$this->post_content_filtered = str_replace(
				array_keys($this->_template_fields),
				$this->_template_fields,
				$this->post_content
			);
	}
	
	/**
	 * Parse and create a document. Uses object variables:
	 * <li>$this->author</li>
	 * <li>$this->subject as subject</li>
	 * <li>$this->title as title</li>
	 * @param string $destination
	 */
	public function parse_pdf($destination = 'D') {
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Athletes in Action');
		$pdf->SetTitle($this->post_title);
		$pdf->SetSubject($this->subject);
		$pdf->SetKeywords('PDF');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

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
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		/*if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}*/

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 12);

		// add a page
		$pdf->AddPage();

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		// create some HTML content
		$html = '<h1>HTML Example</h1>
		Some special characters: &lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash
		<h2>List</h2>
		List example:
		<ol>
			<li><img src="http://mijn.athletesinaction.local/wp-content/uploads/2016/03/aia_facebook1-150x150.png" alt="test alt attribute" width="30" height="30" border="0" /> test image</li>
			<li><b>bold text</b></li>
			<li><i>italic text</i></li>
			<li><u>underlined text</u></li>
			<li><b>b<i>bi<u>biu</u>bi</i>b</b></li>
			<li><a href="http://www.tecnick.com" dir="ltr">link to http://www.tecnick.com</a></li>
			<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.<br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li>
			<li>SUBLIST
				<ol>
					<li>row one
						<ul>
							<li>sublist</li>
						</ul>
					</li>
					<li>row two</li>
				</ol>
			</li>
			<li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li>
			<li><font size="+3">font + 3</font></li>
			<li><small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal</li>
		</ol>
		<dl>
			<dt>Coffee</dt>
			<dd>Black hot drink</dd>
			<dt>Milk</dt>
			<dd>White cold drink</dd>
		</dl>
		<div style="text-align:center">IMAGES<br />
		<img src="http://mijn.athletesinaction.local/wp-content/uploads/2016/03/aia_facebook1-150x150.png" alt="test alt attribute" width="100" height="100" border="0" />
		</div>';

		
		
		$html = $this->post_content_filtered;
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		
		// save as a FILE
		$pdf->Output($this->filename, $destination);
	}
}
