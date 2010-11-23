<?php
/**
* @version		$Id$
* @package		Virtualdomain
* @subpackage 	Controllers
* @copyright	Copyright (C) 2010, . All rights reserved.
* @license #
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * VirtualdomainParams Controller
 *
 * @package    Virtualdomain
 * @subpackage Controllers
 */
class VirtualdomainsControllerParams extends VirtualdomainsController
{
	/**
	 * Constructor
	 */
	protected $_viewname = 'params'; 
	 
	public function __construct($config = array ()) 
	{
		parent :: __construct($config);
		JRequest :: setVar('view', $this->_viewname);

	}
	

	
	
}// class
?>