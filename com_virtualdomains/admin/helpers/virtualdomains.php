<?php


// no direct access
defined('_JEXEC') or die;


class VirtualdomainsHelper
{
	
	/*
	 * Submenu for Joomla 1.6
	 */
 public static function addSubmenu($vName = 'virtualdomain')

    {

           JSubMenuHelper::addEntry(
                JText::_('virtualdomains'),
                'index.php?option=com_virtualdomains&view=virtualdomain',
                $vName == 'virtualdomain'
            );

          JSubMenuHelper::addEntry(
                JText::_('Params'),
                'index.php?option=com_virtualdomains&view=params',
                $vName == 'params'
            );

          JSubMenuHelper::addEntry(
                JText::_('about'),
                'index.php?option=com_virtualdomains&view=about',
                $vName == 'about'
            );          
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
		
		$html = "<a href='#' onclick=\"popupWindow('http://help.janguo.de/vd/".$lang->getTag()."/index.html".$chapter."', '".$text	."', 900, 600, 1)\" class='toolbar'>";
        $html .= "<span class='icon-32-help' title='".$text	."'></span>".$text.'</a>';
        $bar->appendButton( 'Custom', $html);
		
	}	
}
