<?php defined('_JEXEC') or die('Restricted access');
?>

		<fieldset class="adminform">
			<legend>
            <?php  
echo JText::sprintf(	'COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_EXT_TITLE',
				  JoomleagueHelper::formatName(null, $this->project_teamstaff->firstname, $this->project_teamstaff->nickname, $this->project_teamstaff->lastname, 0),
				  $this->teamws->name, $this->projectws->name);                                                
                                                ?>
			</legend>
<?php 
foreach ($this->extended->getFieldsets() as $fieldset)
{
	?>
	<fieldset class="adminform">
	<legend><?php echo JText::_($fieldset->name); ?></legend>
	<?php
	$fields = $this->extended->getFieldset($fieldset->name);
	
	if(!count($fields)) {
		echo JText::_('COM_JOOMLEAGUE_GLOBAL_NO_PARAMS');
	}
	
	foreach ($fields as $field)
	{
		echo $field->label;
       	echo $field->input;
	}
	?>
	</fieldset>
	<?php
}
?>
</fieldset>