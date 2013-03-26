<?php defined('_JEXEC') or die('Restricted access');
?>

		<fieldset class="adminform">
			<legend>
            <?php echo JText::sprintf(	'COM_JOOMLEAGUE_ADMIN_P_TEAM_TITLE_PIC',
												'<i>' . $this->project_team_name . '</i>',
												'<i>' . $this->project_name . '</i>'); ?>
			</legend>
			<table class="admintable">
					<?php foreach ($this->form->getFieldset($this->cfg_which_media_tool) as $field): ?>
					<tr>
						<td class="key"><?php echo $field->label; ?></td>
						<td><?php echo $field->input; ?></td>
					</tr>					
					<?php endforeach; ?>
			</table>
		</fieldset>