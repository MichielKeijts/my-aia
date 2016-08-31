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
class MY_AIA_TEMPLATE extends MY_AIA_MODEL {
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
		'parent_type'		=> array('name'=>'parent_type','type'=>'%s'),
		'document_type'		=> array('name'=>'document_type','type'=>'%s'),
		'subject'			=> array('name'=>'subject', 'type'=>'%s'),
		'from'				=> array('name'=>'from', 'type'=>'%s'),
	);
	
	
	
	public function __construct($post = NULL) {
		parent::__construct($post);
	}


}
