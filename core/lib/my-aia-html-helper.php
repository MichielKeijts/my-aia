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
 * Description of my-aia-view-helper
 *
 * @author Michiel
 */
class MY_AIA_HTML_HELPER {
	private $admin_link = "/wp-admin/admin.php?page=my-aia-admin";
	
	/**
	 *
	 * @var \MY_AIA_CONTROLLER 
	 */
	private $controller;
	
	/**
	 * View holder
	 * @var MY_AIA_VIEW
	 */
	private $view;
	
	/**
	 * 
	 * @param \MY_AIA_VIEW 
	 */
	public function __construct(&$view, &$controller) {
		$this->view = &$view;
		$this->controller = &$controller;
	}


	/**
	 * Create a <a></a> link and returns it.
	 * 
	 * @param string $text
	 * @param mixed $link (array keys controller/action or a link
	 * @param array $options (not implemented yet!) array ('class'=>..,'title'=>..)
	 * @return string
	 */
	public function link ($text, $link, $options="") {
		$linktext=$this->admin_link;
		
		if (is_array($link)) {
			$linktext .= "&controller=".(isset($link['controller'])?$link['controller']:$this->controller->classname);
			$linktext .= "&action=".(isset($link['action'])?$link['action']:'index');
			
			// add other params
			foreach ($link as $key=>$value) {
				if ($key=='controller' || $key=='action') 
					continue; // we already added them manually
				
				$linktext = sprintf('%s&%s=%s', $linktext, $key, $value);
			}
			
		} else {
			// assume static link
			$linktext = $link;
		}		
		
		$options['title'] = isset($options['title'])?$options['title']:$text;
		$options['class'] = isset($options['class'])?$options['class']:"";
		
		return sprintf('<a href="%s" title="%s" class="%s">%s</a>', $linktext, $options['title'], $options['class'], $text);
	}
	
	/**
	 * Create a form input element (textarea, input, etc) and returns it.
	 * 
	 * @param string $text
	 * @param mixed $link (array keys controller/action or a link
	 * @param array $extra_options (not implemented yet!) array ('class'=>..,'title'=>..)
	 * @return string
	 */
	public function input ($name, $extra_options=null) {
		$options = array(
			'type'	=> 'text',
			'allowempty' => false,
			'emptytext' => "",
			'label'	=> false,
			'class' => '',
			'placeholder' => '',
			'options' => array()
		);
		
		// merge params
		if (is_array($extra_options)) {
			$options = array_merge($options, $extra_options);
		} 
		
		// get the variabele
		if ($this->view->get_data($name, NULL) != NULL) {
			$value = $this->view->get_data($name);
		} else {
			$value = "";
		}
		
		$element = "";
		switch ($options['type']) {
			case "select":
				$element  = sprintf('<select name="%s" class="%s">',$name, $options['class']);
			case "radio":
				if ($options['allowempty'])
					array_unshift($option['options'], $options['emptytext']);
				
				foreach ($options['options'] as $key=>$val) {
					$checked = $value==$key?"selected":"";
					if ($options['type']=='select') 
						$element  .= sprintf('<option value="%s" %s>%s</option>', $key, $checked, $val);
					else 
						$element  .= sprintf('<input type="radio" name="%s" value="%s" class="%s" %>',$name, $val, $options['class'], $checked);
				}
				
				if ($options['type']=='select') 
					$element .= "</select>";
				break;			
			case "textarea":
				$element  .= sprintf('<textarea name="%s" class="%s" placeholder="">%s</textarea>', $name, $options['class'], $options['placeholder'], $value);
				break;
			case "checkbox":
			case "hidden":
			case "password":
			case "text":
				$element  .= sprintf('<input type="%s" name="%s" value="%s" class="%s" placeholder="">', $options['type'],$name, $value, $options['class'], $options['placeholder']);
				break;
			default:
				$element = $name;
		}
			
		return $element;
	}
	
	/**
	 * Create a form select element (textarea, input, etc) and returns it.
	 * 
	 * @param string $name
	 * @param array $extra_options (not implemented yet!) array ('class'=>..,'title'=>..)
	 * @return string
	 */
	public function select($name, $extra_options=array()) {
		$extra_options['type'] = 'select';
		return $this->input($name, $extra_options);
	}
}
