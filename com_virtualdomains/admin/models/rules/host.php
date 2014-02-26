<?php
/**
 * @version		$Id: email.php 12774 2009-09-18 04:47:09Z eddieajau $
 * @package		Joomla.Framework
 * @subpackage	Form
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formrule');		
/**
 * Form Rule class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormRuleHost extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $regex = '[\w\.\-]+\w+[\w\.\-]*?\.\w{1,4}';

	/**
	 * Method to test if an e-mail address is unique.
	 *
	 * @param	object		$field		A reference to the form field.
	 * @param	mixed		$values		The values to test for validiaty.
	 * @return	mixed		JException on invalid rule, true if the value is valid, false otherwise.
	 * @since	1.6
	 */
	
	public function test(SimpleXMLElement $field, $values, $group = null, JRegistry $input = null, JForm $form = null)	
	{
		
		$return = false;
		$name	= $field->attributes('name');
    		if(stristr($values,'http://')) return false;
			// Test the value against the regular expression.
			if (parent::test($field, $values)) {
				$return = true;
			}

		return $return;
	}
}