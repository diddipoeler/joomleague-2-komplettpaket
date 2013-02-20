<?php defined('_JEXEC') or die('Restricted access');


JToolBarHelper::addNewX('jlextaddsinglematch',JText::_('JL_ADMIN_MATCHES_ADD_SINGLE_MATCH'));
JToolBarHelper::deleteList(JText::_('JL_ADMIN_MATCHES_MASSADD_WARNING'));
JToolBarHelper::divider();
JToolBarHelper::back('Back','index.php?option=com_joomleague&view=matches&controller=match');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

?>


<form method="post" id="adminForm">

<fieldset>
				<div class="fltrt">
					<button type="button" onclick="Joomla.submitform('jlextindividualsport.savedetails');">
						<?php echo JText::_('JSAVEDETAILS');?></button>
					<button type="button" onclick="Joomla.submitform('jlextindividualsport.jlextaddsinglematch');">
						<?php echo JText::_('JADDSINGLEMATCH');?></button>
					
				</div>
				<div class="configuration" >
					<?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_F_TITLE',$this->match->hometeam,$this->match->awayteam); ?>
				</div>
			</fieldset>
      
<?php 
echo $this->loadTemplate('matches'); 
?>

<div class="clr"></div>
		<input type="hidden" name="option" value="com_joomleague"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="close" id="close" value="0"/>
		<input type="hidden" name="cid[]" value="<?php echo $this->match->id; ?>"/>
		<?php echo JHTML::_('form.token')."\n"; ?>
</form>    
