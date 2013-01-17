<?php defined('_JEXEC') or die('Restricted access');
?>
<!-- SUBSTITUTIONS START -->
<div id="io">
	<?php // Don't remove following <div id='ajaxresponse-awaysubst'></div> as it is neede for ajax changings ?>
	<?php // <div id='ajaxresponse-awaysubst'></div> ?>
	<fieldset class="adminform">
		<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_SUBSTITUTIONS',$this->awayteam->name); ?></legend>
		<table border='0'>
			<thead>
				<tr>
					<th>
						<?php
						echo JHTML::_('image','administrator/components/com_joomleague/assets/images/out.png',JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_OUT'));
						echo ' '.JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_WENT_OUT');
						?>
					</th>
					<th>
						<?php
						echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_CAME_IN').' ';
						echo JHTML::_('image','administrator/components/com_joomleague/assets/images/in.png',JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_IN'));
						?>
					</th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_POSITION'); ?></th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_TIME'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k=0;
				for ($i=0; $i < count($this->awaysubstitutions); $i++)
				{
					$substitution=$this->awaysubstitutions[$i];
					?>
					<tr id="sub-<?php echo $substitution->id; ?>">
						<td style="background-color:<?php echo ($k == 0) ? '#efefef' : '#ffffff'; ?>; ">
							<?php echo JoomleagueHelper::formatName(null,$substitution->out_firstname, $substitution->out_nickname, $substitution->out_lastname, $this->config["name_format"]) ?>
						</td>
						<td style="background-color:<?php echo ($k == 0) ? '#efefef' : '#ffffff'; ?>; ">
							<?php echo JoomleagueHelper::formatName(null,$substitution->firstname, $substitution->nickname, $substitution->lastname, $this->config["name_format"]) ?>
						</td>
						<td style="background-color:<?php echo ($k == 0) ? '#efefef' : '#ffffff'; ?>; ">
							<?php
							echo $substitution->in_position;
							?>
						</td>
						<td style="background-color:<?php echo ($k == 0) ? '#efefef' : '#ffffff'; ?>; ">
							<?php
							$time=(!is_null($substitution->in_out_time) && $substitution->in_out_time > 0) ? $substitution->in_out_time : '--';
							echo $time;
							?>
						</td>
						<td>
							<input	id="delete-<?php echo $substitution->id; ?>" type="button" class="inputbox button-delete"
									value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_DELETE'); ?>" />
						</td>
					</tr>
					<?php
					$k=(1-$k);
				}
				?>
				<tr id="row-new-away">
					<td><?php echo JHTML::_('select.genericlist',$this->awayplayersoptions,'away_out','class="inputbox player-out"'); ?></td>
					<td><?php echo JHTML::_('select.genericlist',$this->awayplayersoptions,'away_in','class="inputbox aplayer-in"'); ?></td>
					<td><?php echo $this->lists['away_projectpositions']; ?></td>
					<td><input type="text" size="3" id="away_in_out_time" name="in_out_time" class="inputbox" /></td>
					<td><input id="save-new-away" type="button" class="inputbox button-save-awaysubst" value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_SAVE'); ?>" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>
<!-- SUBSTITUTIONS END -->
<br />