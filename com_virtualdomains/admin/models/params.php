  <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:params.php  1 2010-11-14 17:12:07Z  $
* @package		Virtualdomain
* @subpackage 	Models
* @copyright	Copyright (C) 2008 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * VirtualdomainModelParams 
 * @author 
 */
 
 
class VirtualdomainsModelParams  extends VirtualdomainsModel { 

	
	
	protected $_default_filter = 'a.name';   

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
		$row = $this->getTable();
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

	/**
	* Method to build the Order Clause
	*
	* @access private
	* @return string orderby	
	*/
	
	protected function _buildContentOrderBy() 
	{
		$app = JFactory::getApplication('');
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
		
		$app = JFactory::getApplication('');
		$context			= $this->option.'.'.strtolower($this->getName()).'.list.';
		
		$filter_order = $app ->getUserStateFromRequest($context . 'filter_order', 'filter_order', $this->getDefaultFilter(), 'cmd');
		$filter_order_Dir = $app ->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$search = $app ->getUserStateFromRequest($context . 'search', 'search', '', 'string');
					
		if ($search) {
			$this->_query->where('LOWER(a.name) LIKE ' . $this->_db->Quote('%' . $search . '%'));			
		}
		
	}
	
}
?>