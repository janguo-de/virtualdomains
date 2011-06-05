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
	
