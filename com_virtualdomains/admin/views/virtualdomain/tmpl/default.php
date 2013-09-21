<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$lang = JFactory::getLanguage()->getTag();
if($lang != 'de-DE') {
	$lang = 'en-GB';
}
$help_url = 'http://help.janguo.de/vd-mccoy/'.$lang.'/#Virtualdomains-Manager';
JToolBarHelper::title( JText::_( 'Virtual Domains' ), 'generic.png' );
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::deleteList();
JToolbarHelper::preferences( 'com_virtualdomains',500,500,'CONFIG_PARAMETER' );

JToolBarHelper::help('#', false, $help_url);
VirtualdomainsHelper::helpIcon('Virtualdomains-Manager');


JHTML::_('behavior.modal', 'a.modal');
 ?>
<style>.romacron {height:100%; width:100%;left:10%!important; top:10%!important;}</style>
<form action="index.php?option=com_virtualdomains&amp;view=virtualdomain" method="post" name="adminForm" id="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<table class="table">
			<tr>
				<td align="left" width="100%">
					<div class="filter-search btn-group pull-left">
						<label for="filter_search" class="element-invisible"><?php echo JText::_( 'Filter' ); ?>:</label>
						<input type="text" name="search" id="search"
							value="<?php echo $this->lists['search']; ?>" class="text_area"
							onchange="document.adminForm.submit();" />
					</div>	
					<div class="btn-group pull-left">								
						<button class="btn" onclick="this.form.submit();">
							<?php echo JText::_( 'Go' ); ?>
							<i class="icon-search"></i>
						</button>

						<button class="btn"
							onclick="document.getElementById('search').value='';this.form.submit();">
							<?php echo JText::_( 'Reset' ); ?>
							<i class="icon-remove"></i>
						</button>
					</div>
				</td>

				<td nowrap="nowrap"><?php echo $this->lists['state']; ?></td>
			</tr>
		</table>
	</div>
  <div id="editcell">
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th width="10"> <?php echo JText::_( 'NUM' ); ?> </th>
          <th width="10"> <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
          </th>
          <th width="29%" class="title"><?php echo JHTML::_( 'grid.sort', 'Domain', 'a.domain', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th  width="29%" class="title"><?php echo JHTML::_( 'grid.sort', 'Template', 'a.template', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th width="13%" class="title"><?php echo JText::_('HOST_CHECK');?></th>
		  <th width="13%">
					<?php echo JHtml::_('grid.sort', 'Default_Domain', 'a.default', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		 </th>          
          <th width="13%" class="title"><?php echo JHTML::_( 'grid.sort', 'Published', 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th width="13%" class="title"><?php echo JText::_('Preview');?> </th>
          <th width="1%" class="title"><?php echo JHTML::_( 'grid.sort', 'Id', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
      <tbody>
        <?php 
$cParam=$this->cParams;

$k = 0;
if ( count( $this->items ) > 0 ):

    for ( $i = 0, $n = count( $this->items ); $i < $n; $i++ ):

        $row = &$this->items[$i];
        $onclick = "";

        if ( JRequest::getVar( 'function', null ) )
        {
            $onclick = "onclick=\"window.parent.jSelectVirtualdomain_id('" . $row->id . "', '" . $this->escape( $row->domain ) . "', '','id')\" ";
        }

        $link = JRoute::_( 'index.php?option=com_virtualdomains&view=virtualdomain&task=edit&cid[]=' . $row->id );
        $row->id = $row->id;
        $checked = JHTML::_( 'grid.checkedout', $row, $i );

        $published = JHTML::_( 'grid.published', $row, $i ); ?>
        <tr class="<?php echo "row$k"; ?>">
          <td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?>.</td>
          <td><?php echo $checked ?></td>
          <td><?php if ( JTable::isCheckedOut( $this->user->get( 'id' ), $row->checked_out ) ):
            echo $row->domain;
        else: ?>
            <a <?php echo $onclick; ?>href="<?php echo $link; ?>"><?php echo $row->domain; ?></a>
            <?php endif; ?></td>
          <td><?php echo $row->template ?></td>
          <td style="text-align:center"><span data-host="<?php echo $row->domain; ?>" class="hostcheck"></span></td>
          <td class="center">
					<?php echo JHtml::_('jgrid.isdefault', $row->home!='0', $i, '', $row->home!='1');?>
			</td>            
          <td style="text-align:center"><?php echo $published ?></td>
          <?php $preViewModalHandlerLink= "http://". $this->escape( $row->domain );?>
          <td style="text-align:center"><a class="modal" title="<?php JText::_('TEST OUT DOMAIN')?>"  href="<?php echo $preViewModalHandlerLink;?>" rel="{classWindow:'testingFrame',handler: 'iframe', size:{x: <?php echo $cParam->get('framewidth',400) ?>, y:<?php echo $cParam->get('frameheight',400) ?>}}"><?php echo JText::_('Preview')?></a></td>
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
  <input type="hidden" name="view" value="virtualdomain" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
