<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>

<?php
	// Set toolbar items for the page
	$edit		= JRequest::getVar('edit',true);
	$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
	JToolBarHelper::title(   JText::_( 'Virtual Domains' ).': <small><small>[ ' . $text.' ]</small></small>' );
	JToolBarHelper::save();
	if (!$edit)  {
		JToolBarHelper::cancel();
	} else {
		// for existing items the button is renamed `close`
		JToolBarHelper::cancel( 'cancel', 'Close' );
	}
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		var pattern = /htt[p|ps]:/g;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.domain.value == ""){
			alert( "<?php echo JText::_( 'Item must have a domain', true ); ?>" );
		} else if (pattern.test(form.domain.value) == true){
			alert( "<?php echo JText::_( 'Domain don\'t start width http:', true ); ?>" );
		} else if (form.template.value == ""){
			alert( "<?php echo JText::_( 'You must have a Template.', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col width-50">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="domain" class="hasTip" title="Enter your virtual domain (e.g.: www.mydomain.de). ">
					Domain <?php echo JText::_( 'Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="domain" id="domain" size="32" maxlength="250" value="<?php echo $this->virtualdomain->domain;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias" class="hasTip" title="Select the template, you have set up for your virtual domain .">
					<?php echo JText::_( 'Template' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['template']; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="menuid" class="hasTip" title="Select the menu, that acts as homepage/starting point for your virtual domain .">
					<?php echo JText::_( 'Menu' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['menu']; ?>
			</td>
		</tr>

	</table>
	</fieldset>
</div>
<div class="col width-50">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Parameters' ); ?></legend>

		<table class="admintable">
		<tr>
			<td colspan="2">
				<?php echo $this->params->render();?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>


<div class="clr"></div>

	<input type="hidden" name="option" value="com_virtualdomains" />
	<input type="hidden" name="cid[]" value="<?php echo $this->virtualdomain->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>