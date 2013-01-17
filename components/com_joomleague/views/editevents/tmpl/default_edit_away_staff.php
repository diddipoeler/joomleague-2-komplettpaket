<?php defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_STAFF',$this->awayteam->name); ?></legend>
	<table border='0'>
		<tr>
			<td style='text-align:center; ' colspan='4'>
				<input type="submit" class="inputbox"	value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_SAVE_STAFF'); ?>" />
			</td>
		</tr>
		<tr>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_AVAILABLE_STAFF'); ?></b></p></td>
			<td>&nbsp;</td>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_STAFF_ASSIGNED'); ?></b></p></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan='<?php echo (count($this->staffprojectpositions)+1); ?>' style='text-align:center; '>
				<?php
				// echo select list of non assigned staff from team staffs
				echo $this->lists['team_staffs']['away'];
				?>
			</td>
		</tr>
		<?php
		foreach ($this->staffprojectpositions AS $project_position_id => $pos)
		{
			?>
			<tr>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- left / right buttons -->
					<br />
					<?php
					/*
					$url		= 'javascript:void(0)';
					$imgTitle	= JText::_('Assign Staff to Starting Line-Up');
					$urlText	= JHTML::image(	JURI::root().'media/com_joomleague/jl_images/arrow_right.png',
												$imgTitle,array(' title' => $imgTitle,' border' => 0));
					$urlAttribs['onclick']="$('changes_check').value=1;$('asqad$key').focus();move($('asqad'),$('asqad$key'));selectAll($('asqad$key')); ";
					echo JHtml::link($url,$urlText,$urlAttribs);
					*/
					?>
					<input	type="button" id="as-move-right-<?php echo $project_position_id;?>" class="inputbox as-move-right"
							value="&gt;&gt;" /><br />
					<input	type="button" id="as-move-left-<?php echo $project_position_id;?>" class="inputbox as-move-left"
							value="&lt;&lt;" />
				</td>
				<td style='text-align:center; '>
					<!-- staff affected to this position -->
					<b><?php echo JText::_($pos->text); ?></b><br /><?php echo $this->lists['team_staffs'.$project_position_id]['away']; ?>
				</td>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- up/down buttons -->
					<br />
					<input	type="button" id="as-move-up-<?php echo $project_position_id;?>" class="inputbox as-move-up"
							value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_UP'); ?>" /><br />
					<input	type="button" id="as-move-down-<?php echo $project_position_id;?>" class="inputbox as-move-down"
							value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_DOWN'); ?>" />
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</fieldset><br />