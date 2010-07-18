<?php
/**
* @version		$Id: view.html.php 10381 2009-08-01 12:35:53Z mliebler $
 * @package		Virtualdomains
 * @subpackage	Virtualdomains
 * @author     	Michael Liebler {@link http://www.janguo.de}
 * @copyright	Copyright (C) 2008 - 2009 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Virtualdomains is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Virtualdomains component
 *
 * @static
 * @package		Virtualdomains
 * @subpackage	Virtualdomains
 * @since 1.0
 */
class VirtualdomainsViewVirtualdomain extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}

		//get the virtualdomain
		$virtualdomain =& $this->get('data');


		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		global $mainframe, $option;

		require_once( JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helper.php' );
		require_once( JPATH_BASE.DS.'components'.DS.'com_templates'.DS.'helpers'.DS.'template.php' );
		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();


		$lists = array();

		//get the virtualdomain
		$virtualdomain	=& $this->get('data');
		$isNew		= ($virtualdomain->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The Virtual Domain' ), $virtualdomain->domain );
			$mainframe->redirect( 'index.php?option='. $option, $msg );
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout( $user->get('id') );
		}
		else
		{
			// initialise new record
			$virtualdomain->published = 1;
			$virtualdomain->approved 	= 1;
			$virtualdomain->order 	= 0;
		}

		// build the html select list for ordering
	
		$tBaseDir = JPATH_SITE.DS.'templates';

		//get template xml file info
		$tBaseDir = JPATH_SITE.DS.'templates';
		$trows = array();
		$trows = TemplatesHelper::parseXMLTemplateFiles($tBaseDir);
  

		$lists['template'] 		= JHTML::_('select.genericlist', $trows, 'template', 'class="inputbox"','directory','directory', $virtualdomain->template );


		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $virtualdomain->published );



		// build the html select list
		$options	= JHTML::_('menu.linkoptions');
		$lists['menu']	= JHTML::_('select.genericlist',   $options, 'menuid', 'class="inputbox" size="15" ', 'value', 'text', $virtualdomain->menuid, 'menuid' );		
		//clean virtualdomain data
		JFilterOutput::objectHTMLSafe( $virtualdomain, ENT_QUOTES, 'description' );

		$file 	= JPATH_COMPONENT.DS.'models'.DS.'virtualdomain.xml';
		$params = new JParameter( $virtualdomain->params, $file );

		$this->assignRef('lists',		$lists);
		$this->assignRef('virtualdomain',		$virtualdomain);
		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
}
