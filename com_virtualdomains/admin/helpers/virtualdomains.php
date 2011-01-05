<?php


// no direct access
defined('_JEXEC') or die;


class VirtualdomainsHelper
{
	
	/*
	 * Submenu for Joomla 1.6
	 */
	public static function addSubmenu($vName = 'coach')
	{
			$db		= &JFactory::getDbo();
			
			jimport('joomla.database.query');
			$query	= new JQuery;
			$query->from('#__menu');
			$query->select('*');
			
			$query->where('menutype = "main"');
			$query->where('client_id = "1"');
			$query->where('link LIKE "%option=com_virtualdomains%"');
			$query->where('parent_id > 1');					
			$query->order('lft');

			$db->setQuery($query);
			$items = $db->loadObjectList();
			foreach ($items as $item) {
				JSubMenuHelper::addEntry(
					JText::_($item->title),
					$item->link,
					$vName == $item->alias
				);
			}			
	}
}