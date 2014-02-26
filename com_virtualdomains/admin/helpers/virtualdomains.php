<?php
// no direct access
defined('_JEXEC') or die;

/**
 * @version		$Id$
 * @package		Virtualdomain
 * @subpackage 	Helpers
 * @copyright	Copyright (C) 2010, . All rights reserved.
 * @author     	Michael Liebler {@link http://www.janguo.de}
 * @copyright	Copyright (C) 2008 - 2013 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Virtualdomains is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
class VirtualdomainsHelper
{
	

    /*
     * Submenu for Joomla 3.x
    */
    public static function addSubmenu($vName = 'virtualdomains')
    {
    	if(version_compare(JVERSION,'3','<')){
    		JSubMenuHelper::addEntry(
    		JText::_('Virtualdomains'),
    		'index.php?option=com_virtualdomains&view=virtualdomains',
    		($vName == 'virtualdomains')
    		);
    	} else {
    		JHtmlSidebar::addEntry(
    		JText::_('Virtualdomains'),
    		'index.php?option=com_virtualdomains&view=virtualdomains',
    		($vName == 'virtualdomains')
    		);
    	}
    	if(version_compare(JVERSION,'3','<')){
    		JSubMenuHelper::addEntry(
    		JText::_('Params'),
    		'index.php?option=com_virtualdomains&view=params',
    		($vName == 'params')
    		);
    	} else {
    		JHtmlSidebar::addEntry(
    		JText::_('Params'),
    		'index.php?option=com_virtualdomains&view=params',
    		($vName == 'params')
    		);
    	}
    	if(version_compare(JVERSION,'3','<')){
    		JSubMenuHelper::addEntry(
    		JText::_('About'),
    		'index.php?option=com_virtualdomains&view=about',
    		($vName == 'about')
    		);
    	} else {
    		JHtmlSidebar::addEntry(
    		JText::_('About'),
    		'index.php?option=com_virtualdomains&view=about',
    		($vName == 'about')
    		);
    	}
    
    }
    
    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   integer  The category ID.
     *
     * @return  JObject
     * @since   1.6
     */
    public static function getActions($categoryId = 0)
    {
    	$user	= JFactory::getUser();
    	$result	= new JObject;
    
    	if (empty($categoryId))
    	{
    		$assetName = 'com_virtualdomains';
    		$level = 'component';
    	}
    	else
    	{
    		$assetName = 'com_virtualdomains.category.'.(int) $categoryId;
    		$level = 'category';
    	}
    
    	$actions = JAccess::getActions('com_virtualdomains', $level);
    
    	foreach ($actions as $action)
    	{
    		$result->set($action->name,	$user->authorise($action->name, $assetName));
    	}
    
    	return $result;
    }
    	
	/**
	 * 
	 * Show the Helpicon
	 * @param string $chapter - anchor within the manual
	 */
	public static function helpIcon($chapter = '') {
		
		$jv = new JVersion();
		
		$chapter = ($chapter) ? "#".$chapter : "";
		
		$lang = JFactory::getLanguage();
		
		if ($jv->RELEASE > 1.5) {
			$text	= JText::_('JTOOLBAR_HELP');
		} else {
			$text = JText::_('Help');
		}
		$bar = JToolBar::getInstance('toolbar');
	
		//strip extension
		$icon	= 'icon-32-help';

		// Add a standard button
		
		$html = "<a href='#' onclick=\"popupWindow('http://help.janguo.de/vd/".$lang->getTag()."/index.html".$chapter."', '".$text	."', 900, 600, 1)\" class='toolbar'>";
        $html .= "<span class='icon-32-help' title='".$text	."'></span>".$text.'</a>';
        $bar->appendButton( 'Custom', $html);
		
	}

}

abstract class JHtmlVirtualdomains {

        
	
	 public static function domains ($domain, $control = 'jform[domain]', $attribs = array('selecttext'=>null, 'class'=>"inputbox", 'onchange'=>'', 'multiple'=>'', 'size'=>''))
        { 
                
                $db             = JFactory::getDbo();
                
                $query = "SELECT domain as value, domain as text
                                                FROM #__virtualdomain
                                                WHERE published =1";
                $db->setQuery($query );
                
                $options = $db->loadObjectList();
                
                $selecttext  = (isset($attribs['selecttext']) && $attribs['selecttext']) ? $attribs['selecttext'] :  JText::_('JALL');

                array_unshift($options, JHtml::_('select.option', '',$selecttext));
                
                $attr = "";
                $attr .= (isset($attribs['class']) && $attribs['class']) ? ' class="'.(string) $attribs['class'].'"' : '';
                $attr .= (isset($attribs['onchange']) && $attribs['onchange']) ? ' onchange="'.(string) $attribs['onchange'].'"' : '';
                $attr .= (isset($attribs['multiple']) && $attribs['multiple']) ? ' multiple="multiple"' : '';
                $attr .= (isset($attribs['size']) && $attribs['size']) ? ' size="'.(int) $attribs['size'].'"' : '';
                //return JHtml::_('access.level', $this->name, $this->value, $attr, $options, $this->id);

                return JHtml::_('select.genericlist', $options, $control,
                        array(
                                'list.attr' => $attr,
                                'list.select' => $domain,
                                'id' => 'form_vd_domain'
                        ));     
        }
        
        public static function languages($lang,  $control = 'jform[language]', $attribs='class="inputbox"') {
                        
        				$options = JHtml::_('contentlanguage.existing', true, true);
        				array_unshift($options, JHtml::_('select.option', '', JText::_('JALL')));
   
   						return JHtml::_('select.genericlist', $options, $control,
                        array(
                                'list.attr' => $attribs,
                                'list.select' => $lang,
                                'id' => 'form_vd_lang'
                        ));                             
   
        }
}
	
