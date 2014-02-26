 <?php
/**
* @version		$Id:virtualdomain.php  1 2014-02-26 11:56:55Z mliebler $
* @package		Virtualdomains
* @subpackage 	Tables
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Jimtawl TableVirtualdomain class
*
* @package		Virtualdomains
* @subpackage	Tables
*/
class TableVirtualdomain extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db) 
	{
		parent::__construct('#__virtualdomain', 'id', $db);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	public function bind($array, $ignore = '')
	{
		if ( isset( $array['params'] ) && is_array( $array['params'] ) )
        {
            $registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
        }		
		return parent::bind($array, $ignore);		
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	public function check()
	{
		if ($this->id === 0) {
			//get next ordering

			$this->ordering = $this->getNextOrder();
		}
		
		/** No www */		
		if (strpos($this->domain,'www.') ===0) {
			$this->domain = substr($this->domain,4);			
		}
		
	    /** check for valid name */

		if (trim($this->domain) == '') {
			$this->setError(JText::_('Your Domain must have a name.'));
			return false;
		}

		return true;
	}
		
}
 