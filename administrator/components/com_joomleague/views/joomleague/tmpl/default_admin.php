<?php 
defined('_JEXEC') or die('Restricted access'); 
$option = 'com_joomleague';
?>
<!--[if gt IE 5.5]>
<script type="text/javascript">
window.addEvent('domready', function() {
	for(var i=0;i<$$('select').length;i++) {
		$$('select')[i].addEvent('focus', function () {
			$('area').setStyle('width', '140px');
			this.setProperty('rel',this.getStyle('width'));
			this.setStyle('width','auto');
			this.setStyle('min-width','138px');
			$('area').setStyle('border-right', '1px solid silver');
		});
		$$('select')[i].addEvent('click', function () {
			this.setProperty('rel',this.getStyle('width'));
			this.setStyle('width','auto');
			this.setStyle('min-width','138px');
			$('area').setStyle('border-right', '1px solid silver');
			$('area').setStyle('max-width', '150px');
		});
		$$('select')[i].addEvent('blur', function () {
			this.setStyle('width',this.getProperty('rel'));
		});
	}}
)
</script>
<![endif]-->
<table width="100%">
	<tr>
		<td width="140px" valign="top">
		<div id="element-box">
			<div class="m">
			<div id="navbar">
			<a href='#' style='text-decoration: none; color: green; text-align: left; padding: 2px;'>JoomLeague - <?php echo $this->version; ?> (diddipoeler)</a>
			<form action="index.php?option=com_joomleague" method="post" id="adminForm1">
				<div id="area" style="overflow:hidden; width:100%; max-width: 150px;">
				<?php echo $this->lists['sportstypes']; ?>
				<?php if ($this->sports_type_id): ?>
				<?php echo $this->lists['seasons']; ?><br />
				<?php echo $this->lists['projects']; ?><br />
				<?php endif; ?>
				<?php
				// Project objects
				if ($this->project->id && $this->sports_type_id)
				{
					echo $this->lists['projectteams'];
					?><br /><?php echo $this->lists['projectrounds'];
				}
				?>
				</div>
				<input type="hidden" name="option" value="com_joomleague" />
				<input type="hidden" name="act" value="" id="jl_short_act" />
				<input type="hidden" name="task" value="joomleague.selectws" />
				<?php echo JHTML::_('form.token')."\n"; ?>
			</form>
			<?php
				$n		= 0;
				$tabs	=& $this->tabs;
				$link	=& $this->link;
				$label	=& $this->label;
				$limage	=& $this->limage;
				$href	= '';
				$title	= '';
				$image	= '';
				$text	= '';
				
				echo JHTML::_('sliders.start','sliders',array(
												'allowAllClose' => true,
												'startOffset' => $this->active,
												'startTransition' => true,
											true));
				foreach ($tabs as $tab)
				{
					$title=$tab->title;
					echo JHTML::_('sliders.panel',$title,'jfcpanel-panel-'.$tab->name);
					?>
					<div style="float: left;">
						<table><?php
							for ($i=0; $i<count($link[$n]); $i++)
							{
								$href	=& $link[$n][$i];
								$title	=& $label[$n][$i];
								$image	=& $limage[$n][$i];
								$text	=& $label[$n][$i];
								$allowed= true;
								$data 	= JUri::getInstance($href)->getQuery(true);
								$jinput = new JInput($data);
								$task 	= $jinput->getCmd('task');
								//$option = JRequest::getCmd('option');
								if($task != '' && $option == 'com_joomleague')  {
									if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
										//display the task which is not handled by the access.xml
										//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
										$allowed = false;
									}
								}
								if($allowed) {
									echo '<tr><td><b><a href="'.$href.'" title="'.JText::_('JGLOBAL_AUTH_ACCESS_GRANTED').'">'.$image.' '.$text.'</a></b></td></tr>';
								} else {
									echo '<tr><td><span title="'.JText::_('JGLOBAL_AUTH_ACCESS_DENIED').'">'.$image.' '.$text.'</span></td></tr>';
								}
							}
							?></table>
					</div>
					<?php
					$n++;
				}
				echo JHTML::_('sliders.end');
				//Extension
				$extensions=JoomleagueHelper::getExtensions(1);
				foreach ($extensions as $e => $extension) {
					$JLGPATH_EXTENSION= JPATH_COMPONENT_SITE.DS.'extensions'.DS.$extension;
					$menufile = $JLGPATH_EXTENSION . DS . 'admin' .DS .'views'.DS.'joomleague'.DS.'tmpl'.DS.'default_'.$extension.'.php';
					if(JFile::exists($menufile )) {
						echo $this->loadTemplate($extension);
					} else {
					}
				}
				?>
			<div style="text-align: center;"><br />
			<?php
//			echo JHTML::_('image','administrator/components/com_joomleague/assets/images/jl.png',JText::_('JoomLeague'),array("title" => JText::_('JoomLeague')));
        echo JHTML::_('image','administrator/components/com_joomleague/assets/images/jl-logo.png',JText::_('extended JoomLeague'),array("title" => JText::_('extended JoomLeague'),"width" => '150'  ));
			?></div>
			</div>
			</div>
			</div>
		</td>
		<td valign="top">
