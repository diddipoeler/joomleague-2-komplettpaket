<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
//this is an example menu for an extension
/*
	$imagePath='administrator/components/com_joomleague/assets/images/';
	// active pane selector
	switch (JRequest::getVar('view'))
	{
		case 'yourview':	$active=count($this->tabs);
		break;
		default: $active=count($this->tabs);
	}
	
	$pane=new stdClass();
	$pane->id = 'Extension';
	$pane->title=JText::_('COM_JOOMLEAGUE_T_MENU_Extension');
	$pane->name='ExtMenuExtension';
	$pane->alert=false;
	$tabs[]=$pane;
	
	$link5=array();
	$label5=array();
	$limage5=array();
	$link5[]=JRoute::_('index.php?option=com_joomleague&view=yourview&active='.$active);
	$label5[]=JText::_('COM_JOOMLEAGUE_T_MENU_yourview');
	$limage5[]=JHTML::_('image',$imagePath.'icon-16-FrontendSettings.png',JText::_('COM_JOOMLEAGUE_T_MENU_yourview'));
	
	$link[]=$link5;
	$label[]=$label5;
	$limage[]=$limage5;
	
	
	$n=0;
	
	echo JHTML::_('sliders.start','sliders',array('allowAllClose' => true,
												'startOffset' => $this->active,
												'startTransition' => true, true));
												
	foreach ($tabs as $tab)
	{
		$title=JText::_($tab->title);
		echo JHTML::_('sliders.panel',$title, $tab->id);
		?>
		<div style="float: left;">
			<table><?php
				for ($i=0;$i < count($link[$n]); $i++)
				{
					?><tr><td><b><a href="<?php echo $link[$n][$i]; ?>" title="<?php echo JText::_($label[$n][$i]); ?>">
							<?php echo $limage[$n][$i].' '.JText::_($label[$n][$i]); ?>
					</a></b></td></tr><?php
				}
				?></table>
		</div>
		<?php
		$n++;
	}
	echo JHTML::_('sliders.end');
*/
?>
