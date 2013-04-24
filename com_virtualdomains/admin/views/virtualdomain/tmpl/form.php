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
$leftcolClass = (version_compare(JVERSION, '3.0', 'lt')) ? 'width-60' : 'span8';
$rightcolClass = (version_compare(JVERSION, '3.0', 'lt')) ? 'width-40' : 'span3'; 
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
			<legend><?php echo JText::_( 'Details' ); ?></legend>
				<div class="control-group">			
					<?php echo $this->form->getLabel( 'domain' ); ?>
				   <?php if (version_compare(JVERSION, '3.0', 'ge')): ?> 
					<div class="controls">
					<?php endif; ?>						
						<?php echo $this->form->getInput( 'domain' ); ?>
					<?php if (version_compare(JVERSION, '3.0', 'ge')): ?>	
				    </div>
				    <?php endif; ?>
				</div>
				<div class="clearfix">
				</div>
				<div class="width-45 span6 fltrt"> 			   
         		<?php foreach ( $this->form->getFieldset( 'translation') as $field ): ?>
         		<div class="control-group">	
         			<?php echo $field->label; ?> 
					<div class="controls">					
         			<?php echo $field->input; ?>
         			</div>
         		</div>	
         		<?php endforeach; ?>
         		</div>


				<div class="<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>width-45 <?php endif;?>span8 fltlft"> 				
					<div class="control-group">	
					<?php echo $this->form->getLabel( 'menuid' ); ?>
				<?php if($this->item->home != 1):?>	
					<?php echo $this->form->getInput( 'menuid' ); ?>
					<?php else: ?>
					<?php echo JText::_('JOOMLA_SETTINGS')?><br />
					</div>
					<div class="clr clearfix"></div>
				<?php endif;?>					
				</div>
				<div class="width-60 span6 pull-left fltlft">
					<?php
					  $formname = version_compare(JVERSION,'1.5','gt') ? 'template_style_id' : 'template';
					?>						
					<?php echo $this->form->getLabel( $formname ); ?>
					<?php if($this->item->home != 1):?>				
					<?php echo $this->form->getInput( $formname ); ?>
					<?php else: ?>
					<?php echo JText::_('JOOMLA_SETTINGS')?>
					<div class="clr"></div>					
					<?php endif; ?>																
					<div class="control-group">			
					<?php echo $this->form->getLabel( 'published' ); ?>
				   <?php if (version_compare(JVERSION, '3.0', 'ge')): ?> 
					<div class="controls">
					<?php endif; ?>					
					<?php echo $this->form->getInput( 'published' ); ?>
					<?php if (version_compare(JVERSION, '3.0', 'ge')): ?>	
				    </div>
				    <?php endif; ?>					
					</div>
				</div>				
	
						
          </fieldset>               
		<?php echo JHtml::_('sliders.start','vd-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel',JText::_('Menu Filter'), 'advanced-menus'); ?>          
         <fieldset class="panelform">        
         <?php foreach ( $this->form->getFieldset( 'menus') as $field ): ?>
         		<?php echo $field->label; ?><br />
         		<?php echo $field->input; ?><br />
         <?php endforeach; ?>
         </fieldset>
		<?php echo JHtml::_('sliders.panel',JText::_('Access Level Inheritance'), 'advanced-accesslevel'); ?>          
         <fieldset class="panelform">        
         <?php foreach ( $this->form->getFieldset( 'accesslevels') as $field ): ?>
         		<?php echo $field->label; ?><br />
         		<?php echo $field->input; ?><br />
         <?php endforeach; ?>
         </fieldset>         
		<?php echo JHtml::_('sliders.end'); ?>
        </div>
        	<div class="<?php echo $rightcolClass; ?>  fltrt">
			        
     		
			<fieldset class="panelform">
				<legend><?php echo JText::_( 'Advanced Parameters' ); ?></legend>
				<table>				
				<?php $fieldSets = $this->form->getFieldsets( 'params' );

		foreach ( $fieldSets as $name => $fieldset ): 
		    if(!in_array($name ,array('menus', 'accesslevels','translation'))) : 
		     ?>				
				<?php foreach ( $this->form->getFieldset( $name ) as $field ): ?>
					<?php if ( $field->hidden ): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
					<tr>
						<td class="paramlist_key" width="40%">
							<?php echo $field->label; ?>
						</td>
						<td class="paramlist_value">
							<?php echo $field->input; ?>
						</td>
					</tr>
				<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>				
			<?php endforeach; ?>
			</table>			
			</fieldset>									
		
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Custom Parameters' ); ?></legend>
				<table>				
			<?php if ( count( $this->paramFields ) > 0 )
            {
                foreach ( $this->paramFields as $field ): ?>
					<tr>
						<td class="paramlist_key" width="40%">
							<label id="jform_params_<?php echo $field->name; ?>-lbl" for="jform_params_<?php echo $field->name; ?>"><?php echo $field->name; ?></label>							
						</td>
						<td class="paramlist_value">
							<input type="text" name="jform[params][<?php echo $field->name; ?>]" id="jform_<?php echo $field->name; ?>" value="<?php echo $field->value ?>" class="inputbox" size="20"/>
						</td>
					</tr>
				<?php endforeach;
            } else
            { ?>	<tr>
						<td><?php JText::_( 'YOU CAN DEFINE PARAMETERS' ); ?></td></tr><?php } ?>

			</table>			
			</fieldset>									

        </div>
        </div>
        </div>
        <?php echo $this->form->getInput( 'viewlevel' ); ?>		        		
		<input type="hidden" name="option" value="com_virtualdomains" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="virtualdomain" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>