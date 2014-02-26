<?php
/**
* @version		$Id$ $Revision$ $DAte$ $Author$ $
* @package		Virtualdomains
* @subpackage 	Controllers
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');

/**
 * VirtualdomainsParam Controller
 *
 * @package    Virtualdomains
 * @subpackage Controllers
 */
class VirtualdomainsControllerParam extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'param';
		$this->view_list = 'params';
		parent::__construct($config);
	}	
}// class
?>