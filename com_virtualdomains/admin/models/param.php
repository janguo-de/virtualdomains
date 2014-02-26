   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:param.php  1 2014-02-26 11:56:55Z mliebler $
* @package		Virtualdomains
* @subpackage 	Models
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * VirtualdomainsModelParam 
 * @author Michael Liebler
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class VirtualdomainsModelParam  extends JModelAdmin { 

		
/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form. [optional]
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure

	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_virtualdomains.param', 'param', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_virtualdomains.edit.param.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_virtualdomains.param', $data);
		}
		

		return $data;
	}
	
	
}
?>