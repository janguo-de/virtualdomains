<?php
/**
 * @copyright	Copyright (C) 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class plgSystemVirtualdomainsInstallerScript

{
	
	public function postflight($route, $adapter)
	{
		
		if (stripos($route, 'install') !== false)
		{			
			$db = JFactory::getDBo();
			$db->setQuery('UPDATE #__extensions set enabled = 1 WHERE `type` = "plugin" AND element = "virtualdomains"');
			$db->execute();
		}
	}
	 

	
}