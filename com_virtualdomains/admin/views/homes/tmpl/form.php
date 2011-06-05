<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_virtualdomains
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Initiasile related data.
require_once JPATH_ADMINISTRATOR.'/components/com_virtualdomains/helpers/homes.php';
$menuTypes = VDHomesHelper::getMenuLinks();


JToolBarHelper::title( JText::_( 'Add Home Items' ), 'generic.png' );
JToolBarHelper::apply('home');
    JToolBarHelper::cancel( 'cancel', 'Close' );    
?>
<form action="<?php echo JRoute::_('index.php?option=com_virtualdomains&view=homes');?>" method="post" name="adminForm" id="adminForm">

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MENUS_SUBMENU_ITEMS'); ?></legend>
			
			<div id="menu-assignment">
			
			<?php echo JHtml::_('tabs.start','module-menu-assignment-tabs', array('useCookie'=>1));?> 
			
			<?php foreach ($menuTypes as &$type) : 
				echo JHtml::_('tabs.panel', $type->title ? $type->title : $type->menutype, $type->menutype.'-details');
				
				$count 	= count($type->links);
				$i		= 0;
				if ($count) :
				?>					
				<ul class="menu-links">
					<?php
					foreach ($type->links as $link) :
					?>
					<li class="menu-link">
						<input type="checkbox" class="chk-menulink" name="jform[assigned][]" value="<?php echo (int) $link->value;?>" id="link<?php echo (int) $link->value;?>"/>
						<label for="link<?php echo (int) $link->value;?>">
							<?php echo $link->text; ?>
						</label>
					</li>
					<?php if ($count > 20 && ++$i == ceil($count/2)) :?>
					</ul><ul class="menu-links">
					<?php endif; ?>						
					<?php endforeach; ?>
				</ul>
				<div class="clr"></div>
				<?php endif; ?>					
			<?php endforeach; ?>
			
			<?php echo JHtml::_('tabs.end');?>
			
			</div>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="homes" />
		<?php echo JHTML::_( 'form.token' ); ?>