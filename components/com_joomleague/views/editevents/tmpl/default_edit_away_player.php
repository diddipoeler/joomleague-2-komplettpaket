<?php defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_LINEUP',$this->awayteam->name); ?></legend>
	<table border='0'>
		<tr>
			<td style='text-align:center; ' colspan='4'>
				<input type="submit" class="inputbox" value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_SAVE_LINEUP'); ?>" />
			</td>
		</tr>
		<tr>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_AVAILABLE_PLAYERS'); ?></b></p></td>
			<td>&nbsp;</td>
			<td style='text-align:center; '><p><b><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_PLAYERS_ASSIGNED'); ?></b></p></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan='<?php echo (count($this->playerprojectpositions)+1); ?>' style='text-align:center; '>
				<?php
				// echo select list of non assigned staff from team staffs
				echo $this->lists['team_players']['away'];
				?>
			</td>
		</tr>
		<?php
		foreach ($this->playerprojectpositions AS $project_position_id => $pos)
		{
			?>
			<tr>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- left / right buttons -->
					<?php
					//$link='http://www.joomla.org';
					//$text='Joomla!';
					//$attribs['target']='_blank';
					//$attribs['class']='test';
					//$attribs['style']='font-size:20px;';
					//link($url,$text,$attribs)
					//$url 	string 	Die URL,auf die verlinkt werden soll.
					//$text 	string 	Der Linktext,der zwischen den <a></a> angezeigt werden soll.
					//$attribs 	array() 	Attribute des Links,m�ssen als Array in der Form array('name' => 'wert') �bergeben werden
					//JHtml::link($url,$text,$attribs);
					//$url		= 'javascript:void(0)';
					//$imgTitle	= JText::_('Assign Player to Starting Line-Up');
					//$urlText	= JHTML::image(	JURI::root().'media/com_joomleague/jl_images/arrow_right.png',
					//							$imgTitle,array(' title' => $imgTitle,' border' => 0));
					//$urlAttribs['onclick']="$('changes_check').value=1;$('asqad$key').focus();move($('asqad'),$('asqad$key'));selectAll($('asqad$key')); ";
					//echo JHtml::link($url,$urlText,$urlAttribs);
					?>
					<br />
					<input	type="button" id="ap-move-right-<?php echo $project_position_id;?>" class="inputbox ap-move-right"
							value="&gt;&gt;" /><br />
					<input	type="button" id="ap-move-left-<?php echo $project_position_id;?>" class="inputbox ap-move-left"
							value="&lt;&lt;" />
				</td>
				<td style='text-align:center; '>
					<!-- player affected to this position -->
					<b><?php echo JText::_($pos->text); ?></b><br /><?php echo $this->lists['team_players'.$project_position_id]['away']; ?>
				</td>
				<td style='text-align:center; vertical-align:middle; '>
					<!-- up/down buttons -->
					<br />
					<input	type="button" id="ap-move-up-<?php echo $project_position_id;?>" class="inputbox ap-move-up"
							value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_UP'); ?>" /><br />
					<input	type="button" id="ap-move-down-<?php echo $project_position_id;?>" class="inputbox ap-move-down"
							value="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_DOWN'); ?>" />
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</fieldset><br />