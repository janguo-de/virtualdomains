<?php
/**
* @version		$Id: virtualdomain.php 136 2013-09-24 14:49:14Z michel $ $Revision$ $DAte$ $Author$ $
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
 * VirtualdomainsVirtualdomain Controller
 *
 * @package    Virtualdomains
 * @subpackage Controllers
 */
class VirtualdomainsControllerVirtualdomain extends JControllerForm
{
	public function __construct($config = array())
	{		
		$this->view_item = 'virtualdomain';
		$this->view_list = 'virtualdomains';
		parent::__construct($config);
	}	
	
	public function save($key = null, $urlVar = null) {
		$data  = $this->input->post->get('jform', array(), 'array');
		
		$model = $this->getModel();
		$data = $model->beforeSave($data);
		$this->input->post->set('jform', $data);				
		return parent::save($key, $urlVar);
 
	}
}// class
?>