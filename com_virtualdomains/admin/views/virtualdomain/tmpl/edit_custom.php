<?php 
/**
 * @package     Joomla.Administrator
 * @subpackage  com_virtualdomains
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<fieldset class="adminform">
	<legend>
		<?php echo JText::_( 'Custom Parameters' ); ?>
	</legend>
	<?php if ( count( $this->paramFields ) > 0 )
	{
		foreach ( $this->paramFields as $field ): ?>
			<div class="control-group">
				<label class="control-label" id="jform_params_<?php echo $field->name; ?>-lbl"
					for="jform_params_<?php echo $field->name; ?>"><?php echo $field->name; ?>
				</label>
		 		<div class="controls">
					<input type="text"
						name="jform[params][<?php echo $field->name; ?>]"
						id="jform_<?php echo $field->name; ?>"
						value="<?php echo $field->value ?>" class="inputbox" size="20" />
				</div>
			</div>			

	<?php endforeach;
	} else { ?>
		<?php JText::_( 'YOU CAN DEFINE PARAMETERS' ); ?>
	<?php } ?>

</fieldset>

