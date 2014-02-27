<?php
/**
* @version		$Id:view.html.php 1 2014-02-26 11:56:55Z mliebler $
* @package		Virtualdomains
* @subpackage 	Views
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class VirtualdomainsViewparams  extends JViewLegacy {


	protected $items;

	protected $pagination;

	protected $state;
	
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null)
	{
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		VirtualdomainsHelper::addSubmenu('params');

		$this->addToolbar();
		if(!version_compare(JVERSION,'3','<')){
			$this->sidebar = JHtmlSidebar::render();
		}
		
		if(version_compare(JVERSION,'3','<')){
			$tpl = "25";
		}
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		$lang = JFactory::getLanguage()->getTag();
		if($lang != 'de-DE') {
			$lang = 'en-GB';
		}
		
		$help_url = 'http://help.janguo.de/vd-mccoy/'.$lang.'/#Parameters-Manager';
		JToolBarHelper::help('#', false, $help_url);
		
		$canDo = VirtualdomainsHelper::getActions();
		$user = JFactory::getUser();
		JToolBarHelper::title( JText::_( 'Params' ), 'generic.png' );
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('param.add');
		}	
		
		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('param.edit');
		}
		
				
				

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'params.delete');
		}
				
		
		JToolBarHelper::preferences('com_virtualdomains', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_virtualdomains&view=params');
		}
							
	}	
	

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
		 	          'a.name' => JText::_('Name'),
	     	          'a.id' => JText::_('JGRID_HEADING_ID'),
	     		);
	}	
}
?>
