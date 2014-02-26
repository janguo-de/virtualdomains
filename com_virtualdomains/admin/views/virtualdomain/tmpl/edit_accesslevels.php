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
		<?php echo JText::_('Access_Level_Inheritance'); ?>
	</legend>
	<?php foreach ( $this->form->getFieldset( 'accesslevels') as $field ): ?>
	<div class="control-group">
		<?php echo $field->label; ?>
		<div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
	<?php endforeach; ?>
</fieldset>
