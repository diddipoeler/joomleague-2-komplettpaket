<?php defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm" id="match-form" class="form-validate">
<fieldset class="adminform">
			<legend>
      <?php 
      echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_STAT_LEGEND_DESC','<i>'.$this->item->name.'</i>'); 
      ?>
      </legend>
	<div class="col50">
		<?php
echo JHTML::_('tabs.start','tabs', array('useCookie'=>1));
echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_DETAILS'), 'panel1');
echo $this->loadTemplate('details');

echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_PICTURE'), 'panel2');
echo $this->loadTemplate('picture');
		
if ($this->edit):
echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_PARAMETERS'), 'panel3');
echo $this->loadTemplate('param');

echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_GENERAL_PARAMETERS'), 'panel4');
echo $this->loadTemplate('gparam');		
endif;		
		
echo JHTML::_('tabs.end');
		?>	

	</div>

	<div class="clr"></div>
	<?php if ($this->edit): ?>
		<input type="hidden" name="calculated" value="<?php echo $this->calculated; ?>" />
	<?php endif; ?>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</fieldset>			
</form>