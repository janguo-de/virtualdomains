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
	
	/**
	 * 
	 * Show the Helpicon
	 * @param string $chapter - anchor within the manual
	 */
	public static function helpIcon($chapter = '') {
		
		$jv = new JVersion();
		
		$chapter = ($chapter) ? "#".$chapter : "";
		
		$lang = &JFactory::getLanguage();
		
		if ($jv->RELEASE > 1.5) {
			$text	= JText::_('JTOOLBAR_HELP');
		} else {
			$text = JText::_('Help');
		}
		$bar = & JToolBar::getInstance('toolbar');
	
		//strip extension
		$icon	= 'icon-32-help';

		// Add a standard button
		
		$html = "<a href='#' onclick=\"popupWindow('http://help.janguo.de/vd/".$lang->getTag()."/virtualdomain.html".$chapter."', '".$text	."', 900, 600, 1)\" class='toolbar'>";
        $html .= "<span class='icon-32-help' title='".$text	."'></span>".$text;
        $bar->appendButton( 'Custom', $html);
		
	}	
}