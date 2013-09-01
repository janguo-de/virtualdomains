<?php
/**
 * @version sulu-1.0
 * @package    joomla
 * @subpackage Virtualdomains
 * @author	   	Michael Liebler
 * @copyright	Copyright (C) 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

//--No direct access
defined('_JEXEC') or die('Resrtricted Access');
// Require the base controller
require_once( JPATH_COMPONENT.'/controller.php' );

jimport('joomla.application.component.model');
require_once( JPATH_COMPONENT.'/models/model.php' );
// Component Helper
jimport('joomla.application.component.helper');

//add Helperpath to JHTML
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');


//Use the JForms, even in Joomla 1.5 
$jv = new JVersion();
$GLOBALS['alt_libdir'] = ($jv->RELEASE < 1.6) ? JPATH_COMPONENT_ADMINISTRATOR : null;

//set the default view
$controller = JRequest::getWord('view', 'virtualdomain');

require_once( JPATH_COMPONENT.'/helpers/virtualdomains.php' );


$ControllerConfig = array();

// Require specific controller if requested
if ( $controller) {   
   $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
   $ControllerConfig = array('viewname'=>strtolower($controller),'mainmodel'=>strtolower($controller),'itemname'=>ucfirst(strtolower($controller)));
   if ( file_exists($path)) {
       require_once $path;
   } else {       
	   $controller = '';	   
   }
}

// Create the controller
$classname    = 'VirtualdomainsController'.$controller;
$controller   = new $classname($ControllerConfig );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();