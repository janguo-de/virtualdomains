<?php
defined('_JEXEC') or die( 'Restricted access' );
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