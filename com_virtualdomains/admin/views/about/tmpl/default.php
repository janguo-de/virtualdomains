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
<fieldset class="adminform">
	<legend>Make a Donation</legend>
	<table>
		<tr>
			<td align="center">If <strong>Virtual Domains</strong> was usefull
				for you, please make a little donation, to bring the developement
				forward. Thank you.
			</td>
		</tr>
		<tr>
			<td align="center">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post"
					target="_top">
					<input type="hidden" name="cmd" value="_donations"> <input
						type="hidden" name="business" value="michael-liebler@janguo.de"> <input
						type="hidden" name="lc" value="GB"> <input type="hidden"
						name="no_note" value="0"> <input type="hidden"
						name="currency_code" value="EUR"> <input type="hidden" name="bn"
						value="PP-DonationsBF:btn_donateCC_LG_global.gif:NonHostedGuest">
					<input type="image"
						src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG_global.gif"
						border="0" name="submit"
						alt="PayPal – The safer, easier way to pay online."> <img alt=""
						border="0"
						src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif"
						width="1" height="1">
				</form>

			</td>
		</tr>
	</table>
</fieldset>