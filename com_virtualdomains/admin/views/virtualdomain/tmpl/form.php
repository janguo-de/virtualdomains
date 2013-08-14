<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.formvalidation' );

// Set toolbar items for the page
$edit = JRequest::getVar( 'edit', true );
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title( JText::_( 'Virtualdomain' ) . ': <small><small>[ ' . $text . ' ]</small></small>' );
JToolBarHelper::apply();
JToolBarHelper::save();
$leftcolClass = (version_compare(JVERSION, '3.0', 'lt')) ? 'width-60' : 'span10';
$rightcolClass = (version_compare(JVERSION, '3.0', 'lt')) ? 'width-40' : 'span2';
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
VirtualdomainsHelper::helpIcon('Details-Page');

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
		<div class="span10 form-horizontal">
			<div class="col <?php echo $leftcolClass; ?> fltlft pull-left">

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

					<div class="clr clearfix"></div>
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
					<div class="clr clearfix"></div>
					<div class="control-group">
						<?php
						$formname = version_compare(JVERSION,'1.5','gt') ? 'template_style_id' : 'template';
						?>
						<?php echo $this->form->getLabel( $formname ); ?>
						<div class="controls">
							<?php if($this->item->home != 1):?>
							<?php echo $this->form->getInput( $formname ); ?>
							<?php else: ?>
							<?php echo JText::_('JOOMLA_SETTINGS')?>
							<div class="clr"></div>
							<?php endif; ?>
						</div>
						<div class="clr clearfix"></div>
						<div class="control-group">
							<?php echo $this->form->getLabel( 'published' ); ?>
							<div class="controls">
								<?php echo $this->form->getInput( 'published' ); ?>
							</div>
						</div>
					</div>

				</fieldset>
				<?php echo JHtml::_('sliders.start','vd-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
				
				<?php echo JHtml::_('sliders.panel',JText::_('Menu_Filter'), 'advanced-menus'); ?>
				<fieldset class="panelform">
					<?php foreach ( $this->form->getFieldset( 'menus') as $field ): ?>
					<?php echo $field->label; ?>
					<br />
					<?php echo $field->input; ?>
					<br />
					<?php endforeach; ?>
				</fieldset>
				<?php echo JHtml::_('sliders.panel',JText::_('Access_Level_Inheritance'), 'advanced-accesslevel'); ?>
				<fieldset class="panelform">
					<?php foreach ( $this->form->getFieldset( 'accesslevels') as $field ): ?>
					<?php echo $field->label; ?>
					<br />
					<?php echo $field->input; ?>
					<br />
					<?php endforeach; ?>
				</fieldset>
				<?php echo JHtml::_('sliders.panel',JText::_('Translation'), 'advanced-translation'); ?>
				<fieldset class="panelform">
				 <?php foreach ( $this->form->getFieldset( 'translation') as $field ): ?>         		
         			<?php echo $field->label; ?><br /> 	
         			<?php echo $field->input; ?>         		
         		<?php endforeach; ?>
         		</fieldset>
         		<?php echo JHtml::_('sliders.end'); ?>
			</div>
			<div class="<?php echo $rightcolClass; ?>  fltrt">


				<fieldset class="panelform">
					<legend>
						<?php echo JText::_( 'Advanced Parameters' ); ?>
					</legend>

					<?php $fieldSets = $this->form->getFieldsets( 'params' );

					foreach ( $fieldSets as $name => $fieldset ):
					if(!in_array($name ,array('menus', 'accesslevels','translation'))) :
					?>
					<?php foreach ( $this->form->getFieldset( $name ) as $field ): ?>
					<?php if ( $field->hidden ): ?>
					<?php echo $field->input; ?>
					<?php else: ?>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
					<br />
					<?php endif; ?>
					<?php endforeach; ?>
					<?php endif; ?>
					<?php endforeach; ?>

				</fieldset>

				<fieldset class="adminform">
					<legend>
						<?php echo JText::_( 'Custom Parameters' ); ?>
					</legend>
					<?php if ( count( $this->paramFields ) > 0 )
					{
                foreach ( $this->paramFields as $field ): ?>

					<label id="jform_params_<?php echo $field->name; ?>-lbl"
						for="jform_params_<?php echo $field->name; ?>"><?php echo $field->name; ?>
					</label> <input type="text"
						name="jform[params][<?php echo $field->name; ?>]"
						id="jform_<?php echo $field->name; ?>"
						value="<?php echo $field->value ?>" class="inputbox" size="20" /><br />

					</tr>
					<?php endforeach;
					} else
					{ ?>
					<tr>
						<td><?php JText::_( 'YOU CAN DEFINE PARAMETERS' ); ?></td>
					</tr>
					<?php } ?>

					</table>
				</fieldset>

			</div>
		</div>
	</div>

	<?php echo $this->form->getInput( 'viewlevel' ); ?>
	<input type="hidden" name="option" value="com_virtualdomains" /> <input
		type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" /> <input
		type="hidden" name="task" value="" /> <input type="hidden" name="view"
		value="virtualdomain" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
