<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<fieldset class="adminform">
	<legend>
		<?php
		echo JText::sprintf( 'COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_LINEUP', $this->hometeam->name );
		?>
	</legend>
	<table border='0'>
		<tr>
			<td style='text-align:center; ' colspan='4'>
				<input type="submit" class="inputbox"	value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_SAVE_LINEUP' ); ?>" />
			</td>
		</tr>
		<tr>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_AVAILABLE_PLAYERS'); ?></b></p></td>
			<td>&nbsp;</td>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_PLAYERS_ASSIGNED'); ?></b></p></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan='<?php echo ( count( $this->playerprojectpositions )+1 ); ?>' style='text-align:center; '>
				<?php
				// echo select list of non assigned staff from team staffs
				echo $this->lists['team_players']['home'];
				?>
			</td>
		</tr>
		<?php
		foreach ($this->playerprojectpositions AS $project_position_id => $pos )
		{
			?>
			<tr>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- left / right buttons -->
					<br />
					<input	type="button" id="hp-move-right-<?php echo $project_position_id;?>" class="inputbox hp-move-right"
							value="&gt;&gt;" /><br />
					<input	type="button" id="hp-move-left-<?php echo $project_position_id;?>" class="inputbox hp-move-left"
							value="&lt;&lt;" />
				</td>
				<td style='text-align:center; '>
					<!-- player affected to this position -->
					<b><?php echo JText::_( $pos->text ); ?></b><br />
					<?php
					echo $this->lists['team_players' . $project_position_id]['home'];
					?>
				</td>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- up/down buttons -->
					<br />
					<input	type="button" id="hp-move-up-<?php echo $project_position_id;?>" class="inputbox hp-move-up"
							value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_UP' ); ?>" /><br />
					<input	type="button" id="hp-move-down-<?php echo $project_position_id;?>" class="inputbox hp-move-down"
							value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_DOWN' ); ?>" />
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</fieldset>
<br />