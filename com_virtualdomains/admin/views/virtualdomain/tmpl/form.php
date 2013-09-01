<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.formvalidation' );

// Set toolbar items for the page
$edit = JRequest::getVar( 'edit', true );
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
$langTag = JFactory::getLanguage()->getTag();
$langTag = ($langTag == 'de-DE') ? 'de-DE' : 'en-GB';  
JToolBarHelper::title( JText::_( 'Virtualdomain' ) . ': <small><small>[ ' . $text . ' ]</small></small>' );
JToolBarHelper::apply();
JToolBarHelper::save();

// If an existing item, can save to new.
if ($edit)
{

	JToolbarHelper::save2new('saveandnew');
}

if ( !$edit )
{
	JToolBarHelper::cancel();
} else
{
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
}
JToolBarHelper::help('#', '', "http://help.janguo.de/vd/".$langTag."/index.html#Details-Page");

?>

<script language="javascript" type="text/javascript">

<?php  if(version_compare(JVERSION, '2.5', 'lt')) : ?>
	
function submitbutton(task)
{
    var form = document.adminForm;
    if (task == 'cancel' || document.formvalidator.isValid(form)) {
		submitform(task);
	}
}
<?php else: ?>
Joomla.submitbutton = function(task) {
	if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
<?php endif; ?>
</script>

<form method="post" action="index.php" id="adminForm" name="adminForm">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span12 form-horizontal">
		 <?php echo $this->tabsstart; ?>
			<?php echo $this->tabs['details']; ?>
				
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_( 'Details' ); ?>
					</legend>
					<div class="control-group">
						<?php echo $this->form->getLabel( 'domain' ); ?>
						<div class="controls">
							<?php echo $this->form->getInput( 'domain' ); ?>
						</div>
					</div>

					<div class="control-group">						
							<?php echo $this->form->getLabel( 'menuid' ); ?>
							<div class="controls">
							<?php if($this->item->home != 1):?>
							<?php echo $this->form->getInput( 'menuid' ); ?>
							<?php else: ?>
							<?php echo JText::_('JOOMLA_SETTINGS')?>
							<br />

							<?php endif;?>
						</div>
					</div>

					<div class="control-group">						
						<?php echo $this->form->getLabel( 'template_style_id' ); ?>
						<div class="controls">
							<?php if($this->item->home != 1):?>
							<?php echo $this->form->getInput( 'template_style_id' ); ?>
							<?php else: ?>
							<?php echo JText::_('JOOMLA_SETTINGS')?>
							<?php endif; ?>
							<br />
						</div>
						<div class="control-group">
							<?php echo $this->form->getLabel( 'published' ); ?>
							<div class="controls">
								<?php echo $this->form->getInput( 'published' ); ?>
							</div>
						</div>
					</div>

				</fieldset>
				
				<?php echo $this->endtab ?>
				<?php echo $this->tabs['siteconfig']; ?>
					<?php echo $this->loadTemplate('siteconfig');?>
				<?php echo $this->endtab ?>				
				
				<?php echo $this->tabs['menufilter'] ?>					
					<?php echo $this->loadTemplate('menu');?>
				<?php echo $this->endtab ?>
				
				<?php echo $this->tabs['accesslevels'] ?>
					<?php echo $this->loadTemplate('accesslevels');?>
				<?php echo $this->endtab ?>
				
				<?php echo $this->tabs['translation'] ?>
					<?php echo $this->loadTemplate('translation');?>         		
         		<?php echo $this->endtab ?>
         		
				<?php echo $this->tabs['custom-params']; ?> 
					<?php echo $this->loadTemplate('custom');?>
					<?php echo $this->endtab; ?>
				<?php echo $this->tabsend; ?>
			</div>
	</div>

	<?php echo $this->form->getInput( 'viewlevel' ); ?>
	<input type="hidden" name="option" value="com_virtualdomains" /> <input
		type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" /> <input
		type="hidden" name="task" value="" /> <input type="hidden" name="view"
		value="virtualdomain" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
