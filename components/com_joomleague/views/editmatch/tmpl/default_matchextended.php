<?php defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldSets = $this->extended->getFieldsets();
if(count($fieldSets) > 0)
{
	// fieldset->name is set in the backend and is localized, so we need the backend language file here
	JFactory::getLanguage()->load('com_joomleague', JPATH_ADMINISTRATOR);
	
	foreach ($this->extended->getFieldsets() as $fieldset)
	{
		$fields = $this->extended->getFieldset($fieldset->name);
		if (count($fields) > 0)
		{
			?>
			<fieldset class="adminform">
			<legend><?php echo JText::_($fieldset->name); ?></legend>
			<table class='adminForm' border='0'>
			<?php
			foreach ($fields as $field)
			{
				?>
				<tr>
					<td class="td_r"><?php echo $field->label;?></td>
					<td><?php echo $field->input;?></td>
		       	</tr>
		       	<?php
			}
			?>
			</table>
			</fieldset>
			<?php
		}
	}
	?>
<div style="text-align:right; ">
	<input type="submit" value="<?php echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_SAVE' ); ?>">
</div><br />
	<?php
}
else
{
	echo JText::_('COM_JOOMLEAGUE_EDITMATCH_NO_EXTENDED_PARAMS');
}
?>
