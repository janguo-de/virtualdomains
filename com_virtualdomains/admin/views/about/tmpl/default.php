<?php
defined('_JEXEC') or die( 'Restricted access' );
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
	JToolBarHelper::title(   JText::_( 'About' ), 'generic.png' );
	 VirtualdomainsHelper::helpIcon('About');
	 $lang = JFactory::getLanguage()->getTag();
	 if($lang != 'de-DE') {
	 	$lang = 'en-GB';
	 }
?>
<fieldset class="adminform">
<legend>About</legend>
<table class="admintable" width="100%">
  <tbody>
  <tr class="row0">
    <td class="key">Authors</td>
    <td><a target="_blank" href="http://janguo.de">Michael Liebler</a>, <a target="_blank" href="http://romacron.de">romacron</a></td>
  </tr>
  <tr class="row1">
    <td class="key">Project Website</td>
    <td><a target="_blank" href="http://vd.janguo.de/redmine/projects/vd-main">vd.janguo.de</a></td>
  </tr>
  
<tr>
    <td class="key">Documentation</td>
    <td><a target="_blank" href="http://help.janguo.de/vd-mccoy/<?php echo $lang; ?>/"><?php echo JText::_('Documentation')?></a></td>
  </tr>      
  </tbody>
</table>
</fieldset>