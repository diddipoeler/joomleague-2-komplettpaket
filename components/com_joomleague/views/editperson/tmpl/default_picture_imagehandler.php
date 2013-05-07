<?php 
defined('_JEXEC') or die('Restricted access');

// joomla25.diddipoeler.de/images/com_joomleague/database/persons/25628_186x236_1360240340.jpg
// joomla25.diddipoeler.de/images/com_joomleague/database/persons/25628_186x236_1360240607.jpg

?>

		
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_PERSON_PICTURE' );?>
			</legend>
			<table class="admintable">
					<?php foreach ($this->form->getFieldset('imageselect') as $field): ?>
					<tr>
						<td class="key"><?php echo $field->label; ?></td>
						<td><?php echo $field->input; ?></td>
					</tr>					
					<?php endforeach; ?>
			</table>
		</fieldset>