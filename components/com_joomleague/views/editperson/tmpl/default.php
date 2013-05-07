<?php defined('_JEXEC') or die('Restricted access');

?>
<form name="adminForm" id="adminForm" method="post" action="index.php">
<?php
		//save and close 
		$close = JRequest::getInt('close',0);
		if($close == 1) {
			?><script>
			window.addEvent('domready', function() {
				$('cancel').onclick();	
			});
			</script>
			<?php 
		}
		?>
	<fieldset class="adminform">
	<div class="fltrt">
					<button type="button" onclick="Joomla.submitform('editperson.save');">
						<?php echo JText::_('JAPPLY');?></button>
					<button type="button" onclick="$('close').value=1; Joomla.submitform('editperson.save');">
						<?php echo JText::_('JSAVE');?></button>
					<button id="cancel" type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
						<?php echo JText::_('JCANCEL');?></button>
				</div>
	<legend>
  <?php 
  echo JText::sprintf('COM_JOOMLEAGUE_PERSON_LEGEND_DESC','<i>'.$this->person->firstname.'</i>','<i>'.$this->person->lastname.'</i>');
  ?>
  </legend>
  </fieldset>
    
		<?php
		echo JHTML::_('tabs.start','tabs', array('useCookie'=>1));
		
		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_PLAYER_TAB_LABEL_INFO'), 'panel1');
		echo $this->loadTemplate('details');

		echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_PICTURE'), 'panel2');
		echo $this->loadTemplate('picture');
    
    /*
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
    */
    
		echo JHTML::_('tabs.end');
		?>

    
	<input type="hidden" name="assignperson" value="0" id="assignperson" />
	<input type="hidden" name="option" value="com_joomleague" /> 
	<input type="hidden" name="cid" value="<?php echo $this->person->id; ?>" /> 
	<input type="hidden" name="task" value="editperson.save" />
	<?php echo JHTML::_('form.token')."\n"; ?>
	
</form>
