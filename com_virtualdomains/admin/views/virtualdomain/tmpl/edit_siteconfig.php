<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2014, Michael Liebler. All rights reserved.
 * @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="panelform">
	<legend>
		<?php echo JText::_( 'Site_Config' ); ?>
	</legend>

	<?php $fieldSets = $this->form->getFieldsets( 'params' );

	foreach ( $fieldSets as $name => $fieldset ):
	if(!in_array($name ,array('menus', 'components', 'accesslevels','translation'))) :
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
