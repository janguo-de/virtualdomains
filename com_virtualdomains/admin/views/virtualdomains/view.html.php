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


class VirtualdomainsViewvirtualdomains  extends JViewLegacy {


	protected $items;

	protected $pagination;

	protected $state;


	/**
	 *  Displays the list view
	 * @param string $tpl
	 */
	public function display($tpl = null)
	{

		$doc = JFactory::getDocument();
		if(version_compare(JVERSION, '3.0', 'lt')) {
			$doc->addScript('components/com_virtualdomains/assets/js/jquery.min.js');
		} else {
			JHtml::_('jquery.framework');
		}
		$doc->addScript('components/com_virtualdomains/assets/js/hostcheck.js');
		JHTML::_('behavior.modal', 'a.modal');
				
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		$this->params = JComponentHelper::getParams( 'com_virtualdomains' );
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		VirtualdomainsHelper::addSubmenu('virtualdomains');

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

		$canDo = VirtualdomainsHelper::getActions();
		$user = JFactory::getUser();

		$lang = JFactory::getLanguage()->getTag();
		if($lang != 'de-DE') {
			$lang = 'en-GB';
		}
		$help_url = 'http://help.janguo.de/vd-mccoy/'.$lang.'/#Virtualdomains-Manager';
		JToolBarHelper::title( JText::_( 'Virtual Domains' ), 'generic.png' );

		JToolBarHelper::help('#', false, $help_url);
		VirtualdomainsHelper::helpIcon('Virtualdomains-Manager');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('virtualdomain.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('virtualdomain.edit');
		}


		if ($this->state->get('filter.state') != 2)
		{
			JToolbarHelper::publish('virtualdomains.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('virtualdomains.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		JToolbarHelper::deleteList('', 'virtualdomains.delete');


		JToolBarHelper::preferences('com_virtualdomains', '550');
		if(!version_compare(JVERSION,'3','<')){
			JHtmlSidebar::setAction('index.php?option=com_virtualdomains&view=virtualdomains');
		}
		
		$excludeOptions = array('archived' => false, 'trash' => false);
		
		if(!version_compare(JVERSION,'3','<')){
			JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $excludeOptions ), 'value', 'text', $this->state->get('filter.state'), true)
			);
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
				'a.domain' => JText::_('Domain'),
				'a.template' => JText::_('Template'),
				'a.home' => JText::_('Default_Domain'),
				'a.published' => JText::_('JSTATUS'),
				'a.id' => JText::_('JGRID_HEADING_ID'),
		);
	}
}
?>
