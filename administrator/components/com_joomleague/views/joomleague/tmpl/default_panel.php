<?php defined('_JEXEC') or die('Restricted access');

$path='/administrator/components/com_joomleague/assets/images/';
$user = JFactory::getUser();
JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_CONTROL_PANEL_TITLE'));
$this->addTemplatePath(JPATH_COMPONENT.DS.'views'.DS.'joomleague');
?>
			<div id="element-box">
				<div class="t"><div class="t"><div class="t">&nbsp;</div></div></div>
				<div class="m">
					<fieldset class="adminform">
						<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTS_CONTROL_PANEL_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
						<table border='0'>
							<tr>
								<td>
									<div id="cpanel">
										<?php
				 						$link=JRoute::_('index.php?option=com_joomleague&task=project.edit&cid[]='.$this->project->id);
										$text=JText::_('COM_JOOMLEAGUE_P_MENU_PSETTINGS');
										$imageFile='icon-48-ProjectSettings.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>		
										<?php
				 						$link=JRoute::_('index.php?option=com_joomleague&view=templates');
										$text=JText::_('COM_JOOMLEAGUE_P_MENU_FES');
										$imageFile='icon-48-FrontendSettings.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
										if ((isset($this->project->project_type)) &&
											($this->project->project_type == 'DIVISIONS_LEAGUE'))
										{
											$link=JRoute::_('index.php?option=com_joomleague&view=divisions');
											$text=JText::_('COM_JOOMLEAGUE_P_MENU_DIVISIONS');
											$imageFile='icon-48-Divisions.png';
											$linkParams="<span>$text</span>&nbsp;";
											$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
											<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
										}
										if ((isset($this->project->project_type)) &&
											(($this->project->project_type == 'TOURNAMENT_MODE') ||
											 ($this->project->project_type == 'DIVISIONS_LEAGUE')))
										{
											$link=JRoute::_('index.php?option=com_joomleague&view=treetos');
											$text=JText::_('COM_JOOMLEAGUE_P_MENU_TREE');
											$imageFile='icon-48-Tree.png';
											$linkParams="<span>$text</span>&nbsp;";
											$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
											?>
											<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
										}
										$link=JRoute::_('index.php?option=com_joomleague&view=projectposition');
										$text=JText::_('COM_JOOMLEAGUE_P_MENU_POSITIONS');
										$imageFile='icon-48-Positions.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
										$link=JRoute::_('index.php?option=com_joomleague&view=projectreferees');
										$text=JText::_('COM_JOOMLEAGUE_P_MENU_REFEREES');
										$imageFile='icon-48-Referees.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
				 						$link=JRoute::_('index.php?option=com_joomleague&view=projectteams');
										$text=JText::_('COM_JOOMLEAGUE_P_MENU_TEAMS');
										$imageFile='icon-48-Teams.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
				 						$link=JRoute::_('index.php?option=com_joomleague&view=rounds');
										$text="10 " . JText::_('COM_JOOMLEAGUE_P_MENU_MATCHDAYS');
										$imageFile='icon-48-Matchdays.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
										<?php
				 						$link=JRoute::_('index.php?option=com_joomleague&view=jlxmlexports');
										$text=JText::_('COM_JOOMLEAGUE_P_MENU_XML_EXPORT');
										$imageFile='icon-48-XMLExportData.png';
										$linkParams="<span>$text</span>&nbsp;";
										$image=JHTML::_('image.administrator',$imageFile,$path,NULL,NULL,$text).$linkParams;
										?>
										<div class="icon-wrapper"><div class="icon"><?php echo JHTML::link($link,$image); ?></div></div>
									</div>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
				<div class="m">
					<fieldset class="adminform">
						<table><tr><td><div id="cpanel"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_CONTROL_PANEL_HINT'); ?></div></td></tr></table>
					</fieldset>
				</div>
				<div class="b"><div class="b"><div class="b"></div></div></div>
			</div>
			<!-- bottom close main table opened in default_admin -->
		</td>
	</tr>
</table>