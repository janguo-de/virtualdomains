<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
 * @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$params		= (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_virtualdomains&task=virtualdomains.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<style>.romacron {height:100%; width:100%;left:10%!important; top:10%!important;}</style>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_virtualdomains&view=virtualdomains');?>"
	method="post" name="adminForm" id="adminForm">

	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif;?>
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" name="filter_search" id="filter_search"
						value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip"
						title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip"
						title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
						onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?>
					</label> <select name="directionTable" id="directionTable"
						class="input-medium" onchange="Joomla.orderTable()">
						<option value="">
							<?php echo JText::_('JFIELD_ORDERING_DESC');?>
						</option>
						<option value="asc"
						<?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?>
						</option>
						<option value="desc"
						<?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?>
						</option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?>
					</label> <select name="sortTable" id="sortTable"
						class="input-medium" onchange="Joomla.orderTable()">
						<option value="">
							<?php echo JText::_('JGLOBAL_SORT_BY');?>
						</option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>


			<div id="editcell">
				<table class="adminlist table table-striped" id="articleList">
					<thead>
						<tr>
							<th width="1%" class="nowrap center hidden-phone"><?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
							</th>

							<th width="20"><input type="checkbox" name="checkall-toggle"
								value="" title="(<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
								onclick="Joomla.checkAll(this)" />
							</th>
							<th class="title"><?php echo JHTML::_('grid.sort', 'Domain', 'a.domain', $listDirn, $listOrder ); ?>
							</th>
							<th class="title"><?php echo JHTML::_('grid.sort', 'Template', 'a.template', $listDirn, $listOrder ); ?>
							</th>
							<th class="title"><?php echo JText::_('HOST_CHECK');?></th>							
							<th class="title"><?php echo JHTML::_('grid.sort', 'Default_Domain', 'a.home', $listDirn, $listOrder ); ?>
							</th>
							<th class="title"><?php echo JHTML::_('grid.sort', 'Published', 'a.published', $listDirn, $listOrder ); ?>
							</th>
							<th width="13%" class="title"><?php echo JText::_('Preview');?> </th>
							<th class="title"><?php echo JHTML::_('grid.sort', 'Id', 'a.id', $listDirn, $listOrder ); ?>
							</th>
						</tr>         				
					</thead>
					<tfoot>
						<tr>
							<td colspan="7"><?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if (count($this->items)) :
						foreach ($this->items as $i => $item) :
						$ordering  = ($listOrder == 'ordering');
						$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canCreate  = $user->authorise('core.create');
						$canEdit    = $user->authorise('core.edit');
						$canChange  = $user->authorise('core.edit.state');
							
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) {
					$disabledLabel    = JText::_('JORDERINGDISABLED');
					$disableClassName = 'inactive tip-top';
				}

				$onclick = "";
					
				if (JRequest::getVar('function', null)) {
    				$onclick= "onclick=\"window.parent.jSelectVirtualdomain_id('".$item->id."', '".$this->escape($item->domain)."', '','id')\" ";
    			}

    			$link = JRoute::_( 'index.php?option=com_virtualdomains&view=virtualdomain&task=virtualdomain.edit&id='. $item->id );
    			$checked = JHTML::_('grid.checkedout', $item, $i );
    			$preViewModalHandlerLink= "http://". $this->escape( $item->domain );
				

    			?>
						<tr class="row<?php echo $i % 2; ?>"">
							<td class="order nowrap center hidden-phone"><?php if ($canChange) : ?>
								<span
								class="sortable-handler hasTooltip <?php echo $disableClassName; ?>"
								title="<?php echo $disabledLabel; ?>"> <i class="icon-menu"></i>
							</span> <input type="text" style="display: none" name="order[]"
								size="5" value="<?php echo $item->ordering;?>"
								class="width-20 text-area-order " /> <?php else : ?> <span
								class="sortable-handler inactive"> <i class="icon-menu"></i>
							</span> <?php endif; ?>
							</td>

							<td><?php echo $checked;  ?></td>

							<td class="nowrap has-context">
								<div class="pull-left">

									<?php if ($canEdit) : ?>
									<a href="<?php  echo $link; ?>"> <?php  echo $this->escape($item->domain); ?>
									</a>
									<?php  else : ?>
									<?php  echo $this->escape($item->domain); ?>
									<?php  endif; ?>

								</div>
								<div class="pull-left">
									<?php
									// Create dropdown items
									JHtml::_('dropdown.edit', $item->id, 'virtualdomain.');
									JHtml::_('dropdown.divider');
									if ($item->published) :
									JHtml::_('dropdown.unpublish', 'cb' . $i, 'virtualdomains.');
									else :
									JHtml::_('dropdown.publish', 'cb' . $i, 'virtualdomains.');
									endif;
									JHtml::_('dropdown.divider');
									JHtml::_('dropdown.trash', 'cb' . $i, 'virtualdomains.');									
									JHtml::_('dropdown.divider');
									// render dropdown list
									echo JHtml::_('dropdown.render');
									?>
								</div>
							</td>														
							<td><?php echo $item->template; ?></td>
							<td ><span data-host="<?php echo $item->domain; ?>" class="hostcheck"></span></td>														
							<td><?php echo JHtml::_('jgrid.isdefault', $item->home != '0' , $i, 'virtualdomains.', $item->home!='1');?></td>
							<td><?php echo JHtml::_('jgrid.published', $item->published, $i, 'virtualdomains.', $canChange, 'cb'); ?>
							</td>
						<td style="text-align:center"><a class="modal" title="<?php JText::_('TEST OUT DOMAIN')?>"  href="<?php echo $preViewModalHandlerLink;?>" rel="{classWindow:'testingFrame',handler: 'iframe', size:{x: <?php echo $this->params->get('framewidth',400) ?>, y:<?php echo $this->params->get('frameheight',400) ?>}}"><?php echo JText::_('Preview')?></a></td>
							<td><?php echo $item->id; ?></td>
						</tr>
						<?php

						endforeach;
						else:
						?>
						<tr>
							<td colspan="12"><?php echo JText::_( 'There are no items present' ); ?>
							</td>
						</tr>
						<?php
						endif;
						?>
					</tbody>
				</table>
			</div>
			<input type="hidden" name="option" value="com_virtualdomains" /> <input
				type="hidden" name="task" value="virtualdomain" /> <input
				type="hidden" name="view" value="virtualdomains" /> 
				<input type="hidden" name="boxchecked" value="0" /> 
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> <input
				type="hidden" name="filter_order_Dir" value="" />
			<?php echo JHTML::_( 'form.token' ); ?>

</form>
