 <?php
/**
* @version		$Id:virtualdomain.php 1 2014-02-26 11:56:55Z mliebler $
* @package		Virtualdomains
* @subpackage 	Views
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class VirtualdomainsViewVirtualdomain  extends JViewLegacy {

	
	protected $form;
	
	protected $item;
	
	protected $state;
	
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null) 
	{
		
		JFactory::getApplication()->input->set('hidemainmenu', true);
		
		$doc = JFactory::getDocument();
		
		JHTML::stylesheet( 'fields.css', 'administrator/components/com_virtualdomains/assets/' );
		if(version_compare(JVERSION, '3', 'lt')) {
			JHTML::stylesheet( 'bootstrap-forms.css', 'administrator/components/com_virtualdomains/assets/' );
		}
		
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->paramFields = $this->get('ParamFields');
				
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$code = $this->_getJs();
		
		$doc->addScriptDeclaration($code);
		$this->_tabs();
		parent::display($tpl);	
	}	
	

	private function _tabs() {
		$this->tabs = array();
		if(version_compare(JVERSION,'3.0','lt')) {
			$this->tabsstart = JHtml::_('tabs.start','vd-sliders-'.$this->item->id, array('useCookie'=>1));
			$this->tabsend = JHtml::_('tabs.end');
			$this->endtab = "";
			$this->tabs['details'] = JHtml::_('tabs.panel',JText::_('Details'), 'details');
			$this->tabs['siteconfig'] = JHtml::_('tabs.panel',JText::_('Main Config'), 'advanced-config');
			$this->tabs['menufilter'] = JHtml::_('tabs.panel',JText::_('Menu_Filter'), 'advanced-menus');
			$this->tabs['accesslevels'] = JHtml::_('tabs.panel',JText::_('Access_Level_Inheritance'), 'advanced-accesslevel');
			$this->tabs['components'] = JHtml::_('tabs.panel',JText::_( 'COMPONENTS_FILTER' ), 'components');
			$this->tabs['translation'] = JHtml::_('tabs.panel',JText::_('Translation'), 'advanced-translation');
			$this->tabs['custom-params'] = JHtml::_('tabs.panel',JText::_( 'Custom Parameters' ), 'custom-params');
	
		} else {
			$this->tabsstart = JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));
			$this->tabsend = JHtml::_('bootstrap.endTabSet');
			$this->endtab = JHtml::_('bootstrap.endTab');
			$this->tabs['details'] = JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('Details'));
			$this->tabs['siteconfig'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-config', JText::_('Site_Config'));
			$this->tabs['menufilter'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-menus', JText::_('Menu_Filter'));
			$this->tabs['accesslevels'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-accesslevel', JText::_('Access_Level_Inheritance'));
			$this->tabs['components'] = JHtml::_('bootstrap.addTab', 'myTab', 'components', JText::_( 'COMPONENTS_FILTER' ));
			$this->tabs['translation'] = JHtml::_('bootstrap.addTab', 'myTab', 'advanced-translation', JText::_('Translation'));
			$this->tabs['custom-params'] = JHtml::_('bootstrap.addTab', 'myTab', 'custom-params', JText::_( 'Custom Parameters' ));
	
		}
		 
	}
	
	private function _getJs() {
		$js = "
    		function switchMenuMode () {
     				var form = $('jform_params_menumode');
     				if(form.value == 'show' || form.value == 'hide') {
     					$('jform_params_menufilter').disabled=false;
     				} else {
     					$('jform_params_menufilter').disabled=true;
    				}
    		}
	
    		window.addEvent('domready', function() {
    			switchMenuMode ();
    			$('jform_params_menumode').addEvent('change',function(){
					switchMenuMode ();
				});
    		});
    	";
		return $js;
	}
	
}
?>