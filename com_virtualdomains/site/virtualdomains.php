<?php
defined('_JEXEC') or die('Restricted access');
$host = $_SERVER['HTTP_HOST'];
$data = json_encode(array('hostname'=>$host));
ob_clean();
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
print json_encode($data);
exit; 
