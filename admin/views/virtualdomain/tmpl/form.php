<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Set toolbar items for the page
$edit		= JRequest::getVar('edit', true);
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Virtualdomain' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply();
JToolBarHelper::save();
if (!$edit) {
	JToolBarHelper::cancel();
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
}

?>

<script language="javascript" type="text/javascript">


	
function submitbutton(task)
{
    var form = document.adminForm;
    if (task == 'cancel' || document.formvalidator.isValid(form)) {
		submitform(task);
	}
}
</script>

	 	<form method="post" action="index.php" id="adminForm" name="adminForm">
	 	<div class="col width-70 fltlft">
		  <fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>
							
				<?php echo $this->form->getLabel('domain'); ?>
				
				<?php echo $this->form->getInput('domain');  ?>
					
				<?php echo $this->form->getLabel('menuid'); ?>
				
				<?php echo $this->form->getInput('menuid');  ?>
					
				<?php echo $this->form->getLabel('template'); ?>
				
				<?php echo $this->form->getInput('template');  ?>										
							
				<?php echo $this->form->getLabel('published'); ?>
				
				<?php echo $this->form->getInput('published');  ?>
			
						
          </fieldset>                      
        </div>
        <div class="col width-30 fltrt">
			        
     		
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Advanced Parameters' ); ?></legend>
				<table>				
				<?php 
					$fieldSets = $this->form->getFieldsets('params');
					
					foreach($fieldSets  as $name =>$fieldset):  
					
					?>				
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<?php if ($field->hidden):  ?>
						<?php echo $field->input;  ?>
					<?php else:  ?>
					<tr>
						<td class="paramlist_key" width="40%">
							<?php echo $field->label;  ?>
						</td>
						<td class="paramlist_value">
							<?php echo $field->input;  ?>
						</td>
					</tr>
				<?php endif;  ?>
				<?php endforeach;  ?>
			<?php endforeach;  ?>
			</table>			
			</fieldset>									
		
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Custom Parameters' ); ?></legend>
				<table>				
				<?php 
					foreach($this->paramFields  as $field):  ?>
					<tr>
						<td class="paramlist_key" width="40%">
							<label id="jform_params_<?php echo $field->name; ?>-lbl" for="jform_params_<?php echo $field->name; ?>"><?php echo $field->name; ?></label>							
						</td>
						<td class="paramlist_value">
							<input type="text" name="jform[params][<?php echo $field->name; ?>]" id="jform_<?php echo $field->name; ?>" value="<?php echo $field->value ?>" class="inputbox" size="20"/>
						</td>
					</tr>
				<?php endforeach;  ?>

			</table>			
			</fieldset>									

        </div>                   
		<input type="hidden" name="option" value="com_virtualdomains" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="virtualdomain" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>