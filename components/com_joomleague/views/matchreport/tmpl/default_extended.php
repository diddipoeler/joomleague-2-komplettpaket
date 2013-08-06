<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- EXTENDED DATA-->
<?php
if(count($this->extended->getFieldsets()) > 0)
{
	// fieldset->name is set in the backend and is localized, so we need the backend language file here
	JFactory::getLanguage()->load('com_joomleague', JPATH_ADMINISTRATOR);
	
	foreach ($this->extended->getFieldsets() as $fieldset)
	{
		$fields = $this->extended->getFieldset($fieldset->name);
		if (count($fields) > 0)
		{
			// Check if the extended data contains information 
			$hasData = false;
			foreach ($fields as $field)
			{
				// TODO: backendonly was a feature of JLGExtraParams, and is not yet available.
				//       (this functionality probably has to be added later)
				$value = $field->value;	// Remark: empty($field->value) does not work, using an extra local var does
				if (!empty($value)) // && !$field->backendonly
				{
					$hasData = true;
					break;
				}
			}
			// And if so, display this information
			if ($hasData)
			{
				?>
				<h2><?php echo '&nbsp;' . JText::_($fieldset->name); ?></h2>
				<table>
					<tbody>
				<?php
				foreach ($fields as $field)
				{
					$value = $field->value;
					if (!empty($value)) // && !$field->backendonly)
					{
						?>
						<tr>
							<td class="label"><?php echo $field->label; ?></td>
							<td class="data">
              <?php if ($field->value =="foggy") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_FOGGY');?>
              <?php if ($field->value =="rainy") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_RAINY');?>
              <?php if ($field->value =="sunny") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_SUNNY');?>
              <?php if ($field->value =="windy") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_WINDY');?>
              <?php if ($field->value =="dry") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_DRY');?>
              <?php if ($field->value =="snowing") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_SNOWING');?>
              <?php if ($field->value =="normal") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_NORMAL');?>
              <?php if ($field->value =="wet") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_WET');?>
              <?php if ($field->value =="dry") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_DRY');?>
              <?php if ($field->value =="snow") echo JText::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_SNOW');?>
              </td>
						<tr>
						<?php
					}
				}
				?>
					</tbody>
				</table>
				<br/>
				<?php
			}
		}
	}
}
?>	
