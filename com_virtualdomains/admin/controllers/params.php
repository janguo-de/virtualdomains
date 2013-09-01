<?php
/**
* @version		$Id$
* @package		Virtualdomain
* @subpackage 	Controllers
* @copyright	Copyright (C) 2010, . All rights reserved.
* @author     	Michael Liebler {@link http://www.janguo.de}
* @copyright	Copyright (C) 2008 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Virtualdomains is free software. This version may have been modified pursuant to the
* GNU General Public License, and as distributed it includes or is derivative
* of works licensed under the GNU General Public License or other free or open
* source software licenses. See COPYRIGHT.php for copyright notices and
* details.
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
		VirtualdomainsHelper::addSubmenu($this->_viewname );
		parent :: __construct($config);
		JRequest :: setVar('view', $this->_viewname);

	}
	

	
	
}// class
?>