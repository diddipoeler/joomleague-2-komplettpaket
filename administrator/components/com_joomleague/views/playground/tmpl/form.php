<?php defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" id="adminForm">
<fieldset class="adminform">
			<legend>
      <?php 
      echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_LEGEND_DESC','<i>'.$this->venue->name.'</i>'); 
      ?>
      </legend>
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

echo JHTML::_('tabs.end');
?>
<div class="clr"></div>
<input type="hidden" name="option" value="com_joomleague" />
<input type="hidden" name="cid[]" value="<?php echo $this->venue->id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token')."\n"; ?>
</div>
</fieldset>	
</form>