<?php defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');JHTML::_('behavior.modal');
jimport('joomla.html.pane');

// Set toolbar items for the page
$edit=JRequest::getVar('edit',true);
$text=!$edit ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_GLOBAL_EDIT');

JToolBarHelper::save('jlextassociation.save');

if (!$edit)
{
	JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ASSOCIATION_ADD_NEW'));
	JToolBarHelper::divider();
	JToolBarHelper::cancel('jlextassociation.cancel');
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ASSOCIATION_EDIT'));
	JToolBarHelper::apply('jlextassociation.apply');
	JToolBarHelper::divider();
	JToolBarHelper::cancel('jlextassociation.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
}
JToolBarHelper::divider();
JLToolBarHelper::onlinehelp();
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form=document.adminForm;
		if (pressbutton == 'cancel')
		{
			submitform(pressbutton);
			return;
		}

		// do field validation
		if (form.name.value == "")
		{
			alert("<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_ASSOCIATION_NO_NAME',true); ?>");
		}
		else
		{
			submitform(pressbutton);
		}
		// do field validation
		if (form.short_name.value == "")
		{
			alert("<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_ASSOCIATION_NO_SHORT_NAME',true); ?>");
		}
		else
		{
			submitform(pressbutton);
		}		
	}
</script>
<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="index.php" method="post" id="adminForm">
	<div class="col50">
		<?php
		echo JHTML::_('tabs.start','tabs', array('useCookie'=>1));
    echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_DETAILS'), 'panel1');
    echo $this->loadTemplate('details');
    

echo JHTML::_('tabs.panel',JText::_('COM_JOOMLEAGUE_TABS_PICTURE'), 'panel2');
echo $this->loadTemplate('picture');
    echo JHTML::_('tabs.end');
// 		$pane =& JPane::getInstance('tabs',array('startOffset'=>0));
// 		echo $pane->startPane('pane');
// 		echo $pane->startPanel(JText::_('JL_TABS_DETAILS'),'panel1');
// 		echo $this->loadTemplate('details');
// 		echo $pane->endPanel();
// 		echo $pane->endPane();
		?>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->object->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token')."\n"; ?>
</form>