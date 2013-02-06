<?php defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_($this->person->name); ?></legend>
    <div class="col50">
		<?php
		echo JHTML::_('tabs.start','tabs', array('useCookie'=>1));
		
		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_DETAILS'), 'panel1');
		echo $this->loadTemplate('details');

		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_PICTURE'), 'panel2');
		echo $this->loadTemplate('picture');

		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_DESCRIPTION'), 'panel3');
		echo $this->loadTemplate('description');

		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_EXTENDED'), 'panel4');
		echo $this->loadTemplate('extended');

		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_FRONTEND'), 'panel5');
		echo $this->loadTemplate('frontend');

		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_ASSIGN'), 'panel6');
		echo $this->loadTemplate('assign');

		if (!$this->edit):// add a selection to assign a person directly to a project and team
			echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_ASSIGN'), 'panel6');
			echo $this->loadTemplate('assign');
		endif;

		echo JHTML::_('tabs.end');
		?>
	</div>
    </fieldset>
	<input type="hidden" name="assignperson" value="0" id="assignperson" />
	<input type="hidden" name="option" value="com_joomleague" /> 
	<input type="hidden" name="cid" value="<?php echo $this->person->id; ?>" /> 
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token')."\n"; ?>
</form>
