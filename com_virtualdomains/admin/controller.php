<?php
/**
 * @version		$Id:controller.php 1 2014-02-26Z mliebler $
 * @author	   	Michael Liebler
 * @package    Virtualdomains
 * @subpackage Controllers
 * @copyright  	Copyright (C) 2014, Michael Liebler. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Virtualdomains Standard Controller
 *
 * @package Virtualdomains   
 * @subpackage Controllers
 */
class VirtualdomainsController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since   1.6
	 */
	protected $default_view = 'virtualdomains';
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
	
		if(version_compare(JVERSION,'3','<')){
			$view   = JRequest::getVar('view', 'virtualdomains');
			$layout = JRequest::getVar('layout', 'default');
			$id     = JRequest::getInt('id');
		} else {
			$view   = $this->input->get('view', 'virtualdomains');
			$layout = $this->input->get('layout', 'default');
			$id     = $this->input->getInt('id');
		}
		
		parent::display();
	
		return $this;
	}

}// class
  
?>