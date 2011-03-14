<?php
/**
 * @version sulu-1.0
 * @package    joomla
 * @subpackage Virtualdomains
 * @author	   	
 *  @copyright  	Copyright (C) 2010, . All rights reserved.
 *  @license 
 */

//--No direct access
defined('_JEXEC') or die('Resrtricted Access');
// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

jimport('joomla.application.component.model');
require_once( JPATH_COMPONENT.DS.'models'.DS.'model.php' );
// Component Helper
jimport('joomla.application.component.helper');

//add Helperpath to JHTML
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');


//Use the JForms, even in Joomla 1.5 
$jv = new JVersion();
$GLOBALS['alt_libdir'] = ($jv->RELEASE < 1.6) ? JPATH_COMPONENT_ADMINISTRATOR : null;

//set the default view
$controller = JRequest::getWord('view', 'virtualdomain');

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'virtualdomains.php' );

//add submenu for 1.6
if ($jv->RELEASE > 1.5) {

	VirtualdomainsHelper::addSubmenu($controller);	
}



$ControllerConfig = array();

// Require specific controller if requested
if ( $controller) {   
   $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
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