<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');JHTML::_('behavior.modal');

// Set toolbar items for the page
$edit=JRequest::getVar('edit',true);
$text=!$edit ? JText::_('Add new settings') : JText::_('Edit Prediction-Game settings');
JToolBarHelper::title(JText::_($text));
JToolBarHelper::save('predictiongame.save');

if (!$edit)
{
	JToolBarHelper::divider();
	JToolBarHelper::cancel('predictiongame.cancel');
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	JToolBarHelper::apply('predictiongame.apply');
	JToolBarHelper::divider();

	JToolBarHelper::cancel('predictiongame.cancel',JText::_('JL_GLOBAL_CLOSE'));
}
JToolBarHelper::divider();
JLToolBarHelper::onlinehelp();

?>
<form method="post" name="adminForm" id="adminForm">
	<div class="col50">
		<?php
		$pane =& JPane::getInstance('tabs',array('startOffset'=>0));
		echo $pane->startPane('pane');
		echo $pane->startPanel(JText::_('JL_TABS_DETAILS'),'panel1');
		echo $this->loadTemplate('details');
		echo $pane->endPanel();
		echo $pane->endPane();
		?>
		<div class="clr"></div>
		<input type="hidden" name="option" value="com_joomleague" />
		
		<input type="hidden" name="user_id" value="0" />
		<input type="hidden" name="project_id" value="0" />
		<input type="hidden" name="prediction_id" value="<?php echo $this->prediction->id; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $this->prediction->id; ?>" />
		<input type="hidden" name="task" value="" />
	</div>
<?php echo JHTML::_('form.token')."\n"; ?>
</form>