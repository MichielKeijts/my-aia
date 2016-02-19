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
	 * @var \MY_AIA_APP_CONTROLLER 
	 */
	private $controller;
	
	/**
	 * 
	 * @param \MY_AIA_APP_CONTROLLER $controller
	 */
	public function __construct($controller) {
		$this->controller=$controller;
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
		} else {
			$linktext = $link;
		}		
		
		$options['title'] = isset($options['title'])?$options['title']:$text;
		$options['class'] = isset($options['class'])?$options['class']:"";
		
		return sprintf('<a href="%s" title="%s" class="%s">%s</a>', $linktext, $options['title'], $options['class'], $text);
	}
}
