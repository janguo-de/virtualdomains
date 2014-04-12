   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:virtualdomain.php  1 2014-02-26 11:56:55Z mliebler $
* @package		Virtualdomains
* @subpackage 	Models
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * VirtualdomainsModelVirtualdomain 
 * @author Michael Liebler
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class VirtualdomainsModelVirtualdomain  extends JModelAdmin { 

		
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
		JFormHelper::addRulePath(JPATH_COMPONENT_ADMINISTRATOR.'/models/rules');
		// Get the form.
		$form = $this->loadForm('com_virtualdomains.virtualdomain', 'virtualdomain', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = $app->getUserState('com_virtualdomains.edit.virtualdomain.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_virtualdomains.virtualdomain', $data);
		}
		

		return $data;
	}
	
	public function beforeSave($data)
	{
		$db = JFactory::getDbo();
		if(isset($data['template_style_id'])) {
			$db->setQuery('Select template from #__template_styles where id = '.(int) $data['template_style_id']);
			$data['template'] = $db->loadResult();
		}
				
		$query = "SELECT id FROM #__viewlevels WHERE title = ".$db->Quote($data['domain']). " OR id = ". (int) $data['viewlevel'] ;
		$db->setQuery($query);
		$viewlevel = $db->loadResult();
		
		//Add or update viewlevel
		if($viewlevel) {
			$query = "UPDATE #__viewlevels SET title = ".$db->Quote($data['domain'])." WHERE id = ". (int) $viewlevel ;
			$db->setQuery($query);
			$db->query();
			$data['viewlevel'] = $viewlevel;
		} else {
			$query = "INSERT INTO #__viewlevels SET rules = ". $db->Quote('[]').",  title = ".$db->Quote($data['domain']);
			$db->setQuery($query);
			$db->query();
			$data['viewlevel'] = $db->insertid();
		}
		return $data;
	}
	
/**
 * Method to delete assigned viewlevels
 * @param int/array $cid
 * @return boolean
 */
	public function preDelete($cid) {
		$db = JFactory::getDbo();
		if(is_array($cid)) {
			foreach($cid as $id) {
				$row = $this->getTable();
				$row->load($id);
				var_dump($row);
				if($row->viewlevel) {
					echo 'DELETE FROM #__viewlevels WHERE id = '.(int) $row->viewlevel.'<br />';
					$db->setQuery('DELETE FROM #__viewlevels WHERE id = '.(int) $row->viewlevel);
					$db->query();
				}
			}
		} else {
			$row = $this->getTable();
			$row->load($id);
			if($row->viewlevel) {
				echo 'DELETE FROM #__viewlevels WHERE id = '.(int) $row->viewlevel.'<br />';
				$db->setQuery('DELETE FROM #__viewlevels WHERE id = '.(int) $row->viewlevel);
				$db->query();
			}
		}
		return true;	
	}
	
	/**
	 * Override parent method validate
	 * @param JForm $form
	 * @param array $data
	 * @param string $group
	 * @return array
	 */
	public function validate($form, $data, $group = null) {
	
	
		$origparams = isset($data['params']) ? $data['params'] : array();
		$data = parent::validate($form, $data, $group);
		$data['params'] = isset($data['params']) ? array_merge($data['params'], $origparams) : $origparams;
		return $data;
	}	
	
	/**
	 * Method to set a template style as home.
	 *
	 * @param	int		The primary key ID for the style.
	 *
	 * @return	boolean	True if successful.
	 * @throws	Exception
	 */
	public function setDefault($cids, $value = 1)
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$db		= $this->getDbo();		
		$cids = (array) $cids;
		$id = (int) $cids[0];

		// Reset the home fields for the client_id.
		$db->setQuery(
				'UPDATE #__virtualdomain' .
				' SET home= ' .$db->Quote('0').
				' WHERE home = '.$db->Quote('1')
		);
	
		if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
	
		// Set the new home style.
		$db->setQuery(
				'UPDATE  #__virtualdomain' .
				' SET home ='. (int) $value.
				' WHERE id = '.(int) $id
		);
	
		if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
	
		return true;
	}
	

	/**
	 * @notice Zurück zu Revision 11
	 *
	 * VirtualdomainsModelVirtualdomain::getParamFields()
	 *
	 * @return
	 */
	public function getParamFields()
	{
		$item =$this->getItem();
		$this->_db->setQuery('Select name, "" as value From #__virtualdomain_params Where 1');
		$result = $this->_db->loadObjectList();
		$params = (array)  $item->params;
		if (count($params )) {
			for ($i=0;$i<count($result);$i++) {
				foreach ($params as $key=>$value) {
					if ($result[$i]->name == $key) {
						$result[$i]->value = $value;
					}
				}
			}
		}
		return $result;
	}
	
}
?>