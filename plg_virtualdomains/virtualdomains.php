<?php


/**
* @version		$Id: virtualdomains.php 10381 2008-06-01 03:35:53Z mliebler $
* @package		Virtualdomains
* @subpackage	plug_virtualdomains
* @copyright	Copyright (C) 2008 - 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Virtualdomains is free software. This version may have been modified pursuant
* @author     	Michael Liebler {@link http://www.janguo.de}
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.menu');
jimport('joomla.plugin.plugin');
/*
 * test
 */
class plgSystemVirtualdomains extends JPlugin {

	var $_db = null;

	/**
	 * Constructor
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemVirtualdomains(& $subject, $config) {
		$this->_db = JFactory :: getDBO();
		parent :: __construct($subject, $config);
	}

	function onAfterInitialise() {
		global $mainframe;

		$db = & JFactory :: getDBO();

		if ($mainframe->isAdmin()) {
			return; // Dont run in backend
		}

		$uri = JURI :: getInstance();
		$jos_host = str_replace('www.', '', $uri->getHost());
		
		//let joomla do its work, if its the main domain 
		if ($jos_host == $this->params->get('std_domain'))
			return;
	
		//is there an entry for the domain returned by $uri->getHost() ?
		$query = "SELECT  * FROM #__virtualdomain WHERE domain = " . $db->Quote($jos_host) . " AND published > 0";
		$db->setQuery($query);
		$row = $db->loadObject();
		
		//return, if no result
		if (!$row)
			return;
		
		//set the template
		if ($row->template)
			$mainframe->setTemplate($row->template);
		
		//Set the route, if necessary 
		if(!$this->params->get('noreroute'))
			$this->_reRoute($row, $uri);
	}

	function _reRoute($row, & $uri) {
		$db = & JFactory :: getDBO();
	
		//get the domains frontpage menu item 
		$query = "SELECT link" .
		"\n FROM #__menu" .
		"\n WHERE id = " . (int) $row->menuid . "\n AND published = '1'";
		$db->setQuery($query);
		$menulink = $db->loadResult();
		if (!$menulink) {
			//item is lost
			return;
		}
		//check the Item ID, that joomla will return, if we are on frontpage   
		$query = "SELECT id " .
		"\n FROM #__menu " .
		"\n WHERE home = 1 ";
		$db->setQuery($query);
		$home = $db->loadResult();

		$router = JSite :: getRouter();

		$query_link = $router->parse(clone ($uri));
		//do nothing, if we are not on frontpage
	             
		if (isset ($query_link['Itemid']) and ($query_link['Itemid'] <> $home))
			return;

		//rewrite the uri
		$link = $menulink . "&Itemid=" . $row->menuid;

		//set some global variables
		$uri->setQuery($link);
		
		$parse = parse_url($this->_getBase() . $link);
		
		//rewrite some server environment vars to fool joomla 
		$_SERVER['QUERY_STRING'] = $parse['query']; 
		$_SERVER['REQUEST_URI'] = $this->_getBase() . $link;
		$_SERVER['PHP_SELF'] = $this->_getBase() . $parse['path'];
		$_SERVER['SCRIPT_NAME'] = $this->_getBase() . $parse['path'];		
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT']. DS. preg_replace('#[/\\\\]+#', DS, $parse['path']);		
	
		$output = array ();

		parse_str($parse['query'] . '&Itemid=' . $home, $output);

		JRequest :: set($output, 'get', false);
		JRequest :: set($output, 'post', false);
	}

	function _getBase() {
		$path = '';
		if (strpos(php_sapi_name(), 'cgi') !== false && !empty ($_SERVER['REQUEST_URI'])) {
			//Apache CGI
			$path = rtrim(dirname(str_replace(array (
				'"',
				'<',
				'>',
				"'"
			), '', $_SERVER["PHP_SELF"])), '/\\');
		} else {
			//Others
			$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
		}
		$path .= '/';
		return $path;
	}

}