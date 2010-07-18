<?php
/**
 * @version 	$Id: virtualdomains.php 10381 2009-14-08 12:35:53Z mliebler $
 * @package		Virtualdomains
 * @subpackage	Virtualdomains
 * @author     	Michael Liebler {@link http://www.janguo.de}
 * @copyright	Copyright (C) 2008 - 2009 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Virtualdomains is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );



class TableVirtualdomain extends JTable
{
	/** @var int */
	var $id					= null;

	/** @var string */
	var $domain				= null;

	/** @var int */
	var $menuid				= null;
	
	/** @var string */
	var $template			= '';

	/** @var int */
	var $published 			= null;

	/** @var boolean */
	var $checked_out 		= 0;

	/** @var time*/
	var $checked_out_time 	= 0;

	/** @var int */
	var $ordering 			= null;
		
	/** @var string */
	var $params				= '';

	/** Constructor **/

	function __construct( &$_db )
	{
		parent::__construct( '#__virtualdomain', 'id', $_db );

	}
	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	*/
	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] ))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	function check()
	{


		/** check for valid name */
		if (trim($this->domain) == '') {
			$this->setError(JText::_('Your Virtual Domain must contain a domain').'.');
			return false;
		}

		/** check for existing name */
		$query = 'SELECT id FROM #__virtualdomain WHERE domain = '.$this->_db->Quote($this->domain);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::sprintf('WARNNAMETRYAGAIN', JText::_('Virtual Domain Link')));
			return false;
		}

		return true;
	}
}
?>

