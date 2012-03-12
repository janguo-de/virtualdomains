<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js',false,true);

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$canOrder	= $user->authorise('core.edit.state',	'com_menus');
$saveOrder 	= ($listOrder == 'a.lft' && $listDirn == 'asc');

JToolBarHelper::title( JText::_( 'Home Items' ), 'generic.png' );
JToolBarHelper::apply();
JToolBarHelper::addNewX();
JToolBarHelper::deleteList(JText::_('VD_UNHOME_MESSAGE'),'unhome','remove from homes');

?>

<form action="<?php echo JRoute::_('index.php?option=com_virtualdomains&view=homes');?>" method="post" name="adminForm" id="adminForm">
		<div class="filter-select fltrt">
			
			<?php echo JHtml::_('virtualdomains.domains',   $this->state->get('filter.domain'),'filter_domain',array('selecttext'=>'- '.JText::_('Select Domain').' -','class'=>'inputbox','onchange'=>'this.form.submit()')); ?>
			
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>

		</div>
	</fieldset>
	<div class="clr"> </div>
<?php //Set up the grid heading. ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_VD_HEADING_MENUTITLE', 'menutype', $listDirn, $listOrder); ?>
				</th>				
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>				
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MENUS_HEADING_HOME', 'a.home', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_VD_DOMAIN', 'vd.domain', $listDirn, $listOrder); ?>
				</th>				
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php // Grid layout ?>
		<tbody>
		<?php
		$originalOrders = array();
		foreach ($this->items as $i => $item) :
			
			$canCreate	= $user->authorise('core.create',		'com_menus');
			$canEdit	= $user->authorise('core.edit',			'com_menus');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id')|| $item->checked_out==0;
			$canChange	= $user->authorise('core.edit.state',	'com_menus') && $canCheckin;
			$domainLevel = ($item->domain) ? $item->domain.' +<br/>' : ''; 
			if($item->home== -1) {
				$combination = 'unconf';
			} elseif ($item->home == 1 && $item->language == '*' && !$item->domain ) {
				$combination = 'home';
			} elseif ($item->home == 1 && !$item->domain ) {
				$combination = 'langhome';
			} elseif ($item->language != '*'  && $item->domain ) {
				$combination = 'domainlanghome';
			} else {
				$combination = 'domainhome';
			}
			
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_menus&task=item.edit&id='.(int) $item->id);?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub" title="<?php echo $this->escape($item->path);?>">
						<?php echo str_repeat('<span class="gtr">|&mdash;</span>', $item->level-1) ?>
						<?php if ($item->type !='url') : ?>
							<?php if (empty($item->note)) : ?>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							<?php else : ?>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
							<?php endif; ?>
						<?php elseif($item->type =='url' && $item->note) : ?>
							<?php echo JText::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note));?>
						<?php endif; ?></p>
				</td>
				<td class="center">
					<?php echo $this->escape($item->menu_title); ?>
				</td>				
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">			
                         <?php switch ($combination) { 
                         		  case 'unconf': ?>
                         		    <span class="jgrid"><a class="hasTip" title="<?php echo JText::_('Item must be configured')?>"><span class="state icon-16-notice"></span></a></span>
                         		    <?php 
											break;
                         		  case 'home': 
                         		    		echo JHtml::_('jgrid.isdefault', $item->home, $i, 'items.', true);                         		    
                         		  	       break;
                         		  case 'domainhome':
                         		  		  echo $item->domain;
                         		  		  break;	
                         		  case 'domainlanghome':
                         		  	      echo $item->domain.' +<br/>';
                         		 case 'langhome':
											echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>$item->language_title), true);                         		  	
                         		  	      break;
  	       	       			
                         } ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('virtualdomains.domains',  $item->domain,'jform[domain]['.$item->id.']'); ?>
					 <input type="hidden" name="jform[domainhome][<?php echo $item->id ?>]" value="<?php echo $item->domain ?>" />
				</td>				
				<td class="center">
				<?php echo JHtml::_('virtualdomains.languages', $item->language,'jform[language]['.$item->id.']'); ?>
				</td>
				<td class="center">
					<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
						<?php echo (int) $item->id; ?></span>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php //Load the batch processing form. ?>
	

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<form action="<?php echo JRoute::_('index.php?option=com_virtualdomains&view=homes');?>" method="post" name="ajaxForm" id="adminForm">
	
</form> 
