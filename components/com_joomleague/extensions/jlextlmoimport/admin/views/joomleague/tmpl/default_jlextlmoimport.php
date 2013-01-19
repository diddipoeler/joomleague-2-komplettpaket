<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
//this is an example menu for an extension

	$imagePath='administrator/components/com_joomleague/assets/images/';
	// active pane selector
	  //$active=count($this->tabs);	
    //$active=$this->active;
	switch (JRequest::getVar('view'))
	{
  	case 'jlextlmoimport':	$active=count($this->tabs);
		break;
		default: $active=count($this->tabs);
	}
	
	$pane=new stdClass();
	$pane->id = 'ExtensionLMOIMPORT';
	$pane->title=JText::_('JL_T_MENU_LMO_IMPORT');
	$pane->name='ExtMenuExtensionLMOIMPORT';
	$pane->alert=false;
	$tabs[]=$pane;
	
	$link5=array();
	$label5=array();
	$limage5=array();
	$link5[]=JRoute::_('index.php?option=com_joomleague&view=jlextlmoimport&active='.$active);
	$label5[]=JText::_('JL_T_MENU_LMO_IMPORT');
	$limage5[]=JHTML::_('image',$imagePath.'icon-16-FrontendSettings.png',JText::_('JL_T_MENU_LMO_IMPORT'));
	
	$link[]=$link5;
	$label[]=$label5;
	$limage[]=$limage5;
	
	
	$n=0;
  echo JHTML::_('sliders.start','sliders',array('allowAllClose' => true,
												'startOffset' => $this->active,
												'startTransition' => true, true));
												
/*	
	$pane =& JPane::getInstance('sliders', array('allowAllClose' => true, 
								'startOffset' => $this->active, 
								'startTransition' => true,true));
*/
								
	//echo $pane->startPane("extpaneExtensionLMOIMPORT");
	foreach ($tabs as $tab)
	{
		$title=JText::_($tab->title);
		//echo $pane->startPanel($title, $tab->id);
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
		//echo $pane->endPanel();
		$n++;
	}
	//echo $pane->endPane();
	echo JHTML::_('sliders.end');

?>
