<?php defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" class="contentpaneopen">
	<tr>
		<td class="contentheading"><?php echo '&nbsp;' . JText::_('COM_JOOMLEAGUE_TEAMINFO_HISTORY'); ?>
		</td>
	</tr>
</table>


<table class="fixtures">
	<tr class="sectiontableheader">
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_SEASON'); ?></td>
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_LEAGUE'); ?></td>
		<?php if($this->project->project_type=='DIVISIONS_LEAGUE') { ?> 
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_DIVISION'); ?></td>
		<?php } ?> 
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_RANK'); ?></td>
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TOTAL_GAMES'); ?></td>
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TOTAL_POINTS'); ?></td>
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TOTAL_WDL'); ?></td>
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TOTAL_GOALS'); ?></td>
		<td><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TOTAL_PLAYERS'); ?></td>
	</tr>
	<?php
	$k=0;
	foreach ($this->seasons as $season)
	{
		$ranking_link   = JoomleagueHelperRoute::getRankingRoute($season->project_slug, null, null, null, 0, $season->division_slug);
		$results_link   = JoomleagueHelperRoute::getResultsRoute($season->project_slug, null, $season->division_slug);
		$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($season->project_slug, $this->team->slug, $season->division_slug);
		$teamstats_link = JoomleagueHelperRoute::getTeamStatsRoute($season->project_slug, $this->team->slug);
		$players_link   = JoomleagueHelperRoute::getPlayersRoute($season->project_slug, $season->team_slug);
		?>
	<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
		<td><?php echo $season->season; ?></td>
		<td><?php echo $season->league; ?></td>
		<?php if($this->project->project_type=='DIVISIONS_LEAGUE') { ?> 
		<td><?php echo $season->division_name; ?></td>
		<?php } ?> 
		<?php if($this->config['show_teams_ranking_link'] == 1): ?>
		<td><?php echo JHTML::link($ranking_link, $season->rank); ?></td>
		<?php else: ?>
		<td><?php echo $season->rank; ?></td>
		<?php endif; ?>
		<td><?php echo $season->games; ?></td>
		<?php if($this->config['show_teams_results_link'] == 1): ?>
		<td><?php echo JHTML::link($results_link, $season->points);	?></td>
		<?php else: ?>
		<td><?php echo $season->points; ?></td>
		<?php endif; ?>
		<?php if($this->config['show_teams_teamplan_link'] == 1): ?>
		<td><?php echo JHTML::link($teamplan_link, $season->series); ?></td>
		<?php else: ?>
		<td><?php echo $season->series; ?></td>
		<?php endif; ?>
		<?php if($this->config['show_teams_teamstats_link'] == 1): ?>
		<td><?php echo JHTML::link($teamstats_link, $season->goals); ?></td>
		<?php else: ?>
		<td><?php echo $season->goals; ?></td>
		<?php endif; ?>
		<?php if($this->config['show_teams_roster_link'] == 1): ?>
		<td><?php echo JHTML::link($players_link, $season->playercnt); ?></td>
		<?php else: ?>
		<td><?php echo $season->playercnt; ?></td>
		<?php endif; ?>
	</tr>
	<?php
	$k = 1 - $k;
	}
	?>
</table>
