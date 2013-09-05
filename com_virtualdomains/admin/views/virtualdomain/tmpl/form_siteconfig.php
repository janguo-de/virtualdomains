<?php 
/**
 * @package     Joomla.Administrator
 * @subpackage  com_virtualdomains
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<fieldset class="panelform">
	<legend>
		<?php echo JText::_( 'Site_Config' ); ?>
	</legend>

	<?php $fieldSets = $this->form->getFieldsets( 'params' );

	foreach ( $fieldSets as $name => $fieldset ):
	if(!in_array($name ,array('menus', 'accesslevels','translation'))) :
	?>
	<?php foreach ( $this->form->getFieldset( $name ) as $field ): ?>
	<?php if ( $field->hidden ): ?>
	<?php echo $field->input; ?>
	<?php else: ?>
	<div class="control-group">
		<?php echo $field->label; ?>
		<div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	<?php endforeach; ?>

</fieldset>
