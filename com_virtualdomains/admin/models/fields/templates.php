<?php
/**
 * @version		$Id: templates.php 12633 2009-08-13 14:28:31Z erdsiger $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldTemplates extends JFormField {

	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Templates';
		/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function getInput()
	{
		$jv = new JVersion();
		$tBaseDir = JPATH_SITE.'/templates';
		$trows = array();
		
		if ($jv->RELEASE > 1.5) {
			
			require_once( JPATH_BASE.'/components'.'/com_templates/helpers/templates.php' );
  			$trows =TemplatesHelper::getTemplateOptions("0");
			$html  = JHTML::_('select.genericlist', $trows, $this->name, 'class="inputbox"', 'value', 'text', $this->value );
						
		} else {
			
			require_once( JPATH_BASE.'/components'.'/com_templates/helpers/template.php' );					
			$trows = TemplatesHelper::parseXMLTemplateFiles($tBaseDir);
			$html  = JHTML::_('select.genericlist', $trows, $this->name, 'class="inputbox"', 'directory', 'directory', $this->value );
			
		}
		return $html;
	}
}
