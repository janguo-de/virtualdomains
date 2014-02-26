<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
 * @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
  defined('_JEXEC') or die('Restricted access');
  
  $user		= JFactory::getUser();
  $userId		= $user->get('id');
  $listOrder	= $this->escape($this->state->get('list.ordering'));
  $listDirn	= $this->escape($this->state->get('list.direction'));    
?>

<form action="index.php?option=com_virtualdomains&amp;view=virtualdomain" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<label class="element-invisible" for="filter_search"><?php echo JText::_( 'Filter' ); ?>:</label>
						<input type="text" name="search" id="search" value="<?php  echo $this->escape($this->state->get('filter.search'));?>" class="text_area" onchange="document.adminForm.submit();" />
					</div>
					<div class="btn-group pull-left">
						<button class="btn" onclick="this.form.submit();"><?php if(version_compare(JVERSION,'3.0','lt')): echo JText::_( 'Go' ); else: ?><i class="icon-search"></i><?php endif; ?></button>
						<button type="button" class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php if(version_compare(JVERSION,'3.0','lt')): echo JText::_( 'Reset' ); else: ?><i class="icon-remove"></i><?php endif; ?></button>
					</div>
				</div>					
			</td>
			<td nowrap="nowrap">
				<?php
 				  	echo JHTML::_('grid.state', $this->state->get('filter.state'));
  				?>
		
			</td>
		</tr>		
	</table>
  <div id="editcell">
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th width="10"> <?php echo JText::_( 'NUM' ); ?> </th>
          <th width="10"> <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
          </th>
          <th width="29%" class="title"><?php echo JHTML::_( 'grid.sort', 'Domain', 'a.domain', $listDirn, $listOrder ); ?></th>
          <th  width="29%" class="title"><?php echo JHTML::_( 'grid.sort', 'Template', 'a.template', $listDirn, $listOrder ); ?></th>
          <th width="13%" class="title"><?php echo JText::_('HOST_CHECK');?></th>
		  <th width="13%">
					<?php echo JHtml::_('grid.sort', 'Default_Domain', 'a.home', $listDirn, $listOrder ); ?>
		 </th>          
          <th width="13%" class="title"><?php echo JHTML::_( 'grid.sort', 'Published', 'a.published', $listDirn, $listOrder ); ?></th>
          <th width="13%" class="title"><?php echo JText::_('Preview');?> </th>
          <th width="1%" class="title"><?php echo JHTML::_( 'grid.sort', 'Id', 'a.id', $listDirn, $listOrder ); ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
      <tbody>
        <?php 

$k = 0;
if ( count( $this->items ) > 0 ):

    for ( $i = 0, $n = count( $this->items ); $i < $n; $i++ ):

        $row = &$this->items[$i];
     
        $link = JRoute::_( 'index.php?option=com_virtualdomains&view=virtualdomain&task=edit&cid[]=' . $row->id );
        $row->id = $row->id;
        $checked = JHTML::_( 'grid.checkedout', $row, $i );

        $published = JHTML::_( 'grid.published', $row, $i ); ?>
        <tr class="<?php echo "row$k"; ?>">
          <td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?>.</td>
          <td><?php echo $checked ?></td>
          <td>        
            <a href="<?php echo $link; ?>"><?php echo $row->domain; ?></a>
           </td>
          <td><?php echo $row->template ?></td>
          <td style="text-align:center"><span data-host="<?php echo $row->domain; ?>" class="hostcheck"></span></td>
          <td class="center">
				<?php echo JHtml::_('jgrid.isdefault', $row->home != '0' , $i, 'virtualdomains.', $row->home!='1');?>
			</td>            
          <td style="text-align:center"><?php echo $published ?></td>
          <?php $preViewModalHandlerLink= "http://". $this->escape( $row->domain );?>
          <td style="text-align:center"><a class="modal" title="<?php JText::_('TEST OUT DOMAIN')?>"  href="<?php echo $preViewModalHandlerLink;?>" rel="{classWindow:'testingFrame',handler: 'iframe', size:{x: <?php echo $this->params->get('framewidth',400) ?>, y:<?php echo $this->params->get('frameheight',400) ?>}}"><?php echo JText::_('Preview')?></a></td>
          <td><?php echo $row->id ?></td>
        </tr>
        <?php $k = 1 - $k;
        endfor;
        else: ?>
        <tr>
          <td colspan="12"><?php echo JText::_( 'There_are_no_items_present' ); ?></td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <input type="hidden" name="option" value="com_virtualdomains" />
  <input type="hidden" name="task" value="virtualdomain" />
  <input type="hidden" name="view" value="virtualdomains" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
  <input type="hidden" name="filter_order_Dir" value="" />
  <?php echo JHTML::_( 'form.token' ); ?>  
</form>
	  	