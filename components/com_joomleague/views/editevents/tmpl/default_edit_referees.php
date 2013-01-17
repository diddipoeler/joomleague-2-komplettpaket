<?php defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend>
		<?php
		echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_STARTING_REFEREES');
		if ($this->project->teams_as_referees)
		{
			echo '&nbsp;-&nbsp;<i>' . JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_ONLY_TEAMS_AS_REFEREES') . '</i>';
		}
		?>
	</legend>
	<table border='0'>
		<tr>
			<td style='text-align:center; ' colspan='4'>
				<input type="submit" class="inputbox" value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_SAVE_STARTING_REFEREES'); ?>" />
			</td>
		</tr>
		<tr>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_AVAILABLE_REFEREES'); ?></b></p></td>
			<td>&nbsp;</td>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_ASSIGNED_REFEREES'); ?></b></p></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan='<?php echo (count($this->refereeprojectpositions)+1); ?>' style='text-align:center; '>
				<?php
				// echo select list of non assigned referees from project referees
				echo $this->lists['project_referees'];
				?>
			</td>
		</tr>
		<?php
		foreach ($this->refereeprojectpositions AS $project_position_id => $pos)
		{
			?>
			<tr>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- left / right buttons -->
					<br />
					<input	type="button" id="mr-move-right-<?php echo $project_position_id;?>" class="inputbox mr-move-right"
							value="&gt;&gt;" /><br />
					<input	type="button" id="mr-move-left-<?php echo $project_position_id;?>" class="inputbox mr-move-left"
							value="&lt;&lt;" />
				</td>
				<td style='text-align:center; '>
					<!-- referee affected to this position -->
					<b><?php echo JText::_($pos->text); ?></b><br />
					<?php echo $this->lists['match_referees' . $project_position_id]; ?>
				</td>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- up/down buttons -->
					<br />
					<input	type="button" id="mr-move-up-<?php echo $project_position_id;?>" class="inputbox mr-move-up"
							value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_UP'); ?>" /><br />
					<input	type="button" id="mr-move-down-<?php echo $project_position_id;?>" class="inputbox mr-move-down"
							value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_DOWN'); ?>" />
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</fieldset>
<br />