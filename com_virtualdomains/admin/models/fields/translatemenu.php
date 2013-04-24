<?php /**
 * @date		$Date$
 * @revision    $Rev$
 * @id 			$Id$
 * @version		!j 1.6 Series com_virtualdomains $
 * @package		com_virtualdomains Webmaster
 * @copyright	Copyright © 2010 - All rights reserved.
 * @author		michael liebler
 * @authorMail	michael-liebler@janguo.de
 * @website		http://www.janguo.de
 *
 * @description Shows The Form with Costom Parameter KEYs
 * This is the KEY Pattern
 * The settet Keys can be used at every VD form.
 * Note: only possible Keys will be set
 * 
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldTranslateMenu extends JFormField
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    public $type = 'TranslateMenu';

    protected function getInput()
    {
		$lang = JFactory::getLanguage();
		$langs = $lang->getKnownLanguages();
        $class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$html ="<table class='table table-striped'>";

					
		foreach($langs  as $lang) {
			   $value = (isset($this->value[$lang['tag']])) ? $this->value[$lang['tag']] : ''; 
			   $html .= '<tr><td class="paramlist_key" width="40%">'.$lang['tag'].'</td>';
			   $html .= '<td class="paramlist_key" width="40%"><input type="text" name="jform[params][translatemenu]['.$lang['tag'].']" id="jform_'.$lang['tag'].'" value="'.$value.'" class="'.$class.'" size="20"/></td></tr>';	
		}
		
        $html .="</table>";
        return $html;
    }

} ?>
