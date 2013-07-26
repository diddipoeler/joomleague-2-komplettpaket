<?php defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');JHtml::_('behavior.modal');
?>
<form action="index.php" method="post" id="adminForm">
<fieldset class="adminform">
			<legend>
      <?php 
      echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_CLUB_LEGEND_DESC','<i>'.$this->club->name.'</i>'); 
      ?>
      </legend>
<div class="col50">
<?php
echo JHtml::_('tabs.start','tabs', array('useCookie'=>1));
echo JHtml::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_DETAILS'), 'panel1');
echo $this->loadTemplate('details');

echo JHtml::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_PICTURE'), 'panel2');
echo $this->loadTemplate('picture');

echo JHtml::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_EXTENDED'), 'panel3');
echo $this->loadTemplate('extended');

echo JHtml::_('tabs.end');
?>
	<div class="clr"></div>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->club->id; ?>" />
    <input type="hidden" name="task" value="" />	
</div>
	<?php echo JHtml::_('form.token'); ?>
</fieldset>	
</form>
