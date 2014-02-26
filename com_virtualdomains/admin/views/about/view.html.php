 <?php
/**
* @version		$Id:view.html.php 1 2014-02-26 11:56:55Z mliebler $
* @package		Virtualdomains
* @subpackage 	Views
* @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Virtual Domains component
 *
 * @static
 * @package		Joomla
 * @subpackage	Virtual Domains
 * @since 1.0
 */
class virtualdomainsViewAbout extends JViewLegacy
{
	function display($tpl = null)
	{
		if(!version_compare(JVERSION,'3','<')){
			VirtualdomainsHelper::addSubmenu('about');
			$this->sidebar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
}
?>