<?php
/**
 * * @version		$Id:virtualdomain.php  1 2010-09-24 23:14:34Z  $
 * @package		Virtualdomain
 * @subpackage 	Tables
 * @copyright	Copyright (C) 2010, . All rights reserved.
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
 * Jimtawl TableVirtualdomain class
 * 
 * @package		Virtualdomain
 * @subpackage	Tables
 */

class TableVirtualdomain extends JTable
{

	public $id = null;

	/** @var  domain  **/
	public $domain = null;

	/** @var  published  **/
	public $published = null;

	/** @var  checked_out  **/
	public $checked_out = null;

	/** @var  checked_out_time  **/
	public $checked_out_time = "0000-00-00 00:00:00";

	/** @var  params  **/
	public $params = null;

	/** @var  template_style_id  **/
	public $template_style_id = null;		
	
	/** @var  ordering  **/
	public $ordering = null;

	/** @var  menuid  **/
	public $menuid = null;

	/** @var  template  **/
	public $template = null;


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
		
		$array['params'] = json_encode($array['params']);
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
