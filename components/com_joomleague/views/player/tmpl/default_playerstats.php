<?php defined('_JEXEC') or die('Restricted access'); ?>

<!-- Player stats History START -->
<h2><?php	echo JText::_('COM_JOOMLEAGUE_PERSON_PERSONAL_STATISTICS');	?></h2>
<table width="96%" align="center" border="0" cellpadding="0"
	cellspacing="0">
	<tr>
		<td>
		<table id="playercareer">
			<thead>
			<tr class="sectiontableheader">
				<th class="td_l" class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_PERSON_COMPETITION'); ?></th>
				<th class="td_l" class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_PERSON_TEAM'); ?></th>
				<th class="td_c"><?php
				$imageTitle=JText::_('COM_JOOMLEAGUE_PERSON_PLAYED');
				echo JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/played.png',
				$imageTitle,array(' title' => $imageTitle,' width' => 20,' height' => 20));
				?></th>
				<?php
				if ($this->config['show_substitution_stats'])
				{
					if ((isset($this->overallconfig['use_jl_substitution'])) && ($this->overallconfig['use_jl_substitution']==1))
					{
						?>
				<th class="td_c"><?php
				$imageTitle=JText::_('COM_JOOMLEAGUE_PERSON_STARTROSTER');
				echo JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/startroster.png',
				$imageTitle,array(' title' => $imageTitle));
				?></th>
				<th class="td_c"><?php
				$imageTitle=JText::_('COM_JOOMLEAGUE_PERSON_IN');
				echo JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/in.png',
				$imageTitle,array(' title' => $imageTitle));
				?></th>
				<th class="td_c"><?php
				$imageTitle=JText::_('COM_JOOMLEAGUE_PERSON_OUT');
				echo JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/out.png',
				$imageTitle,array(' title' => $imageTitle));
				?></th>
				<?php
					}
				}
				if ($this->config['show_career_events_stats'])
				{
					if (count($this->AllEvents))
					{
						foreach($this->AllEvents as $eventtype)
						{
				?>
				<th class="td_c"><?php
				$iconPath=$eventtype->icon;
				if (!strpos(" ".$iconPath,"/")){$iconPath="images/com_joomleague/database/events/".$iconPath;}
				echo JHTML::image($iconPath,
					JText::_($eventtype->name),
					array(	"title" => JText::_($eventtype->name),
						"align" => "top",
						"hspace" => "2"));
				?>&nbsp;</th>
				<?php
						}
					}
				}
				if ($this->config['show_career_stats'])
				{
					foreach ($this->stats as $stat)
					{
						//do not show statheader when there are no stats
						if (!empty($stat)) {
							if ($stat->showInPlayer()) {
						
				?>
				<th class="td_c"><?php echo !empty($stat) ? $stat->getImage() : ""; ?>&nbsp;</th>
				<?php 			}
						}
					}
				}
				?>
			</tr>
			</thead>
			<tbody>
			<?php
			$k=0;
			$career=array();
			$career['played']=0;
			$career['started']=0;
			$career['in']=0;
			$career['out']=0;
			$player =& JLGModel::getInstance("Person","JoomleagueModel");

			if (count($this->historyPlayer) > 0)
			{
				foreach ($this->historyPlayer as $player_hist)
				{
					$model = $this->getModel();
					$this->assignRef('inoutstat',$model->getInOutStats($player_hist->project_id, $player_hist->ptid, $player_hist->tpid));
					$link1=JoomleagueHelperRoute::getPlayerRoute($player_hist->project_slug,$player_hist->team_slug,$this->person->slug);
					$link2=JoomleagueHelperRoute::getTeamInfoRoute($player_hist->project_slug,$player_hist->team_slug);
					?>
			<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
				<td class="td_l" nowrap="nowrap"><?php echo JHTML::link($link1,$player_hist->project_name); ?>
				</td>
				<td class="td_l" class="nowrap">
				<?php
					if ($this->config['show_playerstats_teamlink'] == 1) {
						echo JHTML::link($link2,$player_hist->team_name);
					} else {
						echo $player_hist->team_name;
					} 
				?>
				</td>
				<!-- Player stats History - played start -->
				<td class="td_c"><?php
				echo ($this->inoutstat->played > 0) ? $this->inoutstat->played : '0';
				$career['played'] += $this->inoutstat->played;
				?></td>
				<?php
				if ($this->config['show_substitution_stats'])
				{
					//substitution system
					if ((isset($this->overallconfig['use_jl_substitution']) && ($this->overallconfig['use_jl_substitution']==1)))
					{
						?>
						<!-- Player stats History - startroster start -->
						<td class="td_c"><?php
						$career['started'] += $this->inoutstat->started;
						echo ($this->inoutstat->started);
						?></td>
						<!-- Player stats History - substitution in start -->
						<td class="td_c"><?php
						$career['in'] += $this->inoutstat->sub_in;
						echo ($this->inoutstat->sub_in );
						?></td>
						<!-- Player stats History - substitution out start -->
						<td class="td_c"><?php
						$career['out'] += $this->inoutstat->sub_out;
						echo ($this->inoutstat->sub_out) ;
						?></td>
						<?php
					}
				}
				?>
				<!-- Player stats History - allevents start -->
				<?php
				if ($this->config['show_career_events_stats'])
				{
					// stats per project
					if (count($this->AllEvents))
					{
						foreach($this->AllEvents as $eventtype)
						{
							$stat=$player->getPlayerEvents($eventtype->id, $player_hist->project_id, $player_hist->ptid);
							?>
				<td class="td_c"><?php echo ($stat > 0) ? $stat : 0; ?></td>
				<?php
						}
					}
				}
				if ($this->config['show_career_stats'])
				{
					foreach ($this->stats as $stat)
					{
						//do not show when there are no stats
						if (!empty($stat)) {
						    if ($stat->showInPlayer()) {    
				?>
				<td class="td_c hasTip" title="<?php echo JText::_($stat->name); ?>">
				<?php
							if(isset($this->projectstats[$stat->id][$player_hist->project_id][$player_hist->ptid])) {
								echo $this->projectstats[$stat->id][$player_hist->project_id][$player_hist->ptid];
							} else {
								echo 0;
							}
						    }
						}
				?></td>
				<?php
					}
				}
				?>
				<!-- Player stats History - allevents end -->
			</tr>
			<?php
			$k=(1-$k);
				}
			}
			?>
			<tr class="career_stats_total">
				<td class="td_r" colspan="2"><b><?php echo JText::_('COM_JOOMLEAGUE_PERSON_CAREER_TOTAL'); ?></b></td>
				<td class="td_c"><?php echo $career['played']; ?></td>
				<?php //substitution system
				if	($this->config['show_substitution_stats'] && isset($this->overallconfig['use_jl_substitution']) &&
				($this->overallconfig['use_jl_substitution']==1))
				{
					?>
				<td class="td_c"><?php echo ($career['started'] ); ?></td>
				<td class="td_c"><?php echo ($career['in'] ); ?></td>
				<td class="td_c"><?php echo ($career['out'] ); ?></td>
				<?php
				}
				?>
				<?php // stats per project
				if ($this->config['show_career_events_stats'])
				{
					if (count($this->AllEvents))
					{
						foreach($this->AllEvents as $eventtype)
						{
							if (isset($player_hist))
							{
								$total=$player->getPlayerEvents($eventtype->id);
							}
							else
							{
								$total='';
							}
							?>
				<td class="td_c"><?php echo (($total) ? $total : 0); ?></td>
				<?php
						}
					}
				}
				if ($this->config['show_career_stats'])
				{
					foreach ($this->stats as $stat)
					{
						if(!empty($stat)) {
						    if ($stat->showInPlayer()) {
						?>
							<td class="td_c" title="<?php echo JText::_($stat->name); ?>">
							<?php
								if (isset($this->projectstats) &&
								    array_key_exists($stat->id, $this->projectstats))
								{
									echo $this->projectstats[$stat->id]['totals'];
								}
								else	// In case there are no stats for the player
								{
									echo 0;
								}
							?>
							</td>
						<?php
						    }
						}
					}
				}
				?>
			</tr>
			</tbody>
		</table>
		</td>
	</tr>
</table>

<!-- Player stats History END -->
