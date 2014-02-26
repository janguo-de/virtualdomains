<?php
/**
 * @version 1.3.0
 * @package    joomla
 * @subpackage Virtualdomains
 * @author	   	Michael Liebler
 *  @copyright  	Copyright (C) 2014, Michael Liebler. All rights reserved.
 *  @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

//--No direct access
defined('_JEXEC') or die('Resrtricted Access');

require_once(JPATH_COMPONENT.'/helpers/virtualdomains.php');
$controller = JControllerLegacy::getInstance('virtualdomains');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();