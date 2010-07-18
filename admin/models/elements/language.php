<?php
/**
* @version		$Id: language.php 14401 2010-07-17 14:10:00Z liebler $
* @package		Virtual Domains
* @subpackage	Elements
* @author     	Michael Liebler {@link http://www.janguo.de}
 * @copyright	Copyright (C) 2008 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Virtualdomains is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a category element
 *
 * @package Virtual Domains
 * @subpackage	Elements
 */

class JElementLanguage extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Language';

	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;
		
		$prefix = $mainframe->getCfg('dbprefix');

		$db = &JFactory::getDBO();

		$class = "inputbox";
		if(!in_array($prefix.'languages', $db->getTableList())) {
			return JText::_("Joomfish is not installed");
		}
		$query = 'SELECT id, name' .
				' FROM #__languages' .
				' WHERE active = 1' .
				' ORDER BY ordering';
		
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', JText::_('User defined'), 'id', 'name'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="'.$class.'"', 'id', 'name', $value, $control_name.$name );
	}
}