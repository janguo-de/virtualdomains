  <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:virtualdomain.php  1 2010-10-23 15:29:07Z  $
* @package		Virtualdomains
* @subpackage 	Models
* @copyright	Copyright (C) 2010, . All rights reserved.
* @license #
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * VirtualdomainsModelVirtualdomain 
 * @author 
 */
 
 
class VirtualdomainsModelVirtualdomain  extends VirtualdomainsModel { 

	
	
	protected $_default_filter = 'a.domain';   

/**
 * Constructor
 */
	
	public function __construct()
	{
		parent::__construct();

	}

	/**
	* Method to build the query
	*
	* @access private
	* @return string query	
	*/

	protected function _buildQuery()
	{
		return parent::_buildQuery();
	}
	
	/**
	 * Method to store the Item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store($data)
	{
		$row =& $this->getTable();
		/**
		 * Example: get text from editor 
		 * $Text  = JRequest::getVar( 'text', '', 'post', 'string', JREQUEST_ALLOWRAW );
		 */
		 
		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the table is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		/**
		 * Clean text for xhtml transitional compliance
		 * $row->text		= str_replace( '<br>', '<br />', $Text );
		 */
	
		// Store the table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$this->setId($row->{$row->getKeyName()});
		return $row->{$row->getKeyName()};
	}	

	
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
	/**
	* Method to build the Order Clause
	*
	* @access private
	* @return string orderby	
	*/
	
	protected function _buildContentOrderBy() 
	{
		$app = &JFactory::getApplication('');
		$context			= $this->option.'.'.strtolower($this->getName()).'.list.';
		$filter_order = $app ->getUserStateFromRequest($context . 'filter_order', 'filter_order', $this->getDefaultFilter(), 'cmd');
		$filter_order_Dir = $app ->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', '', 'word');
		$this->_query->order($filter_order . ' ' . $filter_order_Dir );
	}
	
	/**
	* Method to build the Where Clause 
	*
	* @access private
	* @return string orderby	
	*/
	
	protected function _buildContentWhere() 
	{
		
		$app = &JFactory::getApplication('');
		$context			= $this->option.'.'.strtolower($this->getName()).'.list.';		
		$filter_state = $app ->getUserStateFromRequest($context . 'filter_state', 'filter_state', '', 'word');		
		$filter_order = $app ->getUserStateFromRequest($context . 'filter_order', 'filter_order', $this->getDefaultFilter(), 'cmd');
		$filter_order_Dir = $app ->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$search = $app ->getUserStateFromRequest($context . 'search', 'search', '', 'string');
					
		if ($search) {
			$this->_query->where('LOWER(a.domain) LIKE ' . $this->_db->Quote('%' . $search . '%'));			
		}		
		if ($filter_state) {
			if ($filter_state == 'P') {
				$this->_query->where("a.published = 1");
			} elseif ($filter_state == 'U') {
					$this->_query->where("a.published = 0");
			} else {
				$this->_query->where("a.published > -2");
			}
		}		
	}
	
}
?>