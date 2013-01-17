<?php defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_JOOMLEAGUE_ADMIN_DIVISION' );?>
	</legend>
	<table class="admintable">
		<tr>
			<td class="key"><?php echo $this->form->getLabel('name'); ?></td>
			<td><?php echo $this->form->getInput('name'); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo $this->form->getLabel('alias'); ?></td>
			<td><?php echo $this->form->getInput('alias'); ?></td>
		</tr>		
		<tr>
			<td class="key"><?php echo $this->form->getLabel('middle_name'); ?></td>
			<td><?php echo $this->form->getInput('middle_name'); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo $this->form->getLabel('shortname'); ?></td>
			<td><?php echo $this->form->getInput('shortname'); ?></td>
		</tr>		
		<tr>
			<td class="key"><?php echo JText::_( 'COM_JOOMLEAGUE_ADMIN_DIVISION_PARENT' );?></td>
			<td><?php echo $this->lists['parents'];?></td>
		</tr>
		<tr>
			<td class="key"><?php echo $this->form->getLabel('notes'); ?></td>
			<td><?php echo $this->form->getInput('notes'); ?></td>
		</tr>
	</table>
</fieldset>