<?php
/**
* @version		$Id$ $Revision$ $Date$ $Author$ $
* @package		Virtualdomains
* @subpackage 	Controllers
* @copyright	Copyright (C) 2014, Michael Liebler.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// 

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
/**
 * Virtualdomain list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Virtualdomains
 */
class VirtualdomainsControllerVirtualdomains extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  VirtualdomainsControllervirtualdomains
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;
		$this->view_list = 'virtualdomains';
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Virtualdomain', $prefix = 'VirtualdomainsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	
	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		$model = $this->getModel();
		$model->preDelete($cid);
		return parent::delete();
	}
	
	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $id     The validated data.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $id = null)	
	{
		
	}

	/**
	 * Method to set the default property for a domain
	 *
	 */
	public function setDefault()
	{
		// Check for request forgeries
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid   = $this->input->get('cid', array(), 'array');
		$data  = array('setDefault' => 1, 'unsetDefault' => 0);
		$task  = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 1, 'int');
		echo var_dump($cid);
		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);
	
			// Publish the items.
			if (!$model->setDefault($cid, $value))
			{
				JError::raiseWarning(500, $model->getError());
			} else {				
				$this->setMessage(JText::_('SUCCESS_DEFAULT_SET'));
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
		
	}
	
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	

}
