<?php
/**
* @version		$Id:params.php  1 2010-11-14 17:12:07Z  $
* @package		Virtualdomain
* @subpackage 	Tables
* @author     	Michael Liebler {@link http://www.janguo.de}
* @copyright	Copyright (C) 2008 - 2013 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Virtualdomains is free software. This version may have been modified pursuant to the
* GNU General Public License, and as distributed it includes or is derivative
* of works licensed under the GNU General Public License or other free or open
* source software licenses. See COPYRIGHT.php for copyright notices and
* details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Jimtawl TableParams class
*
* @package		Virtualdomain
* @subpackage	Tables
*/
class TableParams extends JTable
{
	
   /** @var  id- Primary Key  **/
   public $id = null;

   /** @var  name  **/
   public $name = null;

   /** @var  action  **/
   public $action = null;

   /** @var  home **/
   public $home = null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db) 
	{
		parent::__construct('#__virtualdomain_params', 'id', $db);
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

		
		
		/** check for valid name */
		/**
		if (trim($this->name) == '') {
			$this->setError(JText::_('Your Params must contain a name.')); 
			return false;
		}
		**/		

		return true;
	}
}
