<?php
defined('_JEXEC') or die('Restricted access');
/**
* @author     	Michael Liebler {@link http://www.janguo.de}
* @copyright	Copyright (C) 2008 - 2013 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Virtualdomains is free software. This version may have been modified pursuant to the
* GNU General Public License, and as distributed it includes or is derivative
* of works licensed under the GNU General Public License or other free or open
* source software licenses. See COPYRIGHT.php for copyright notices and
* details.
*/

$host = $_SERVER['HTTP_HOST'];
$data = json_encode(array('hostname'=>$host));
ob_clean();
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
print json_encode($data);
exit; 
