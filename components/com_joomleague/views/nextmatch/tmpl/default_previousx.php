<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if ($this->previousx[$this->currentteam]) :	?>
<!-- Start of last 5 matches -->

<h2><?php echo JText::sprintf('COM_JOOMLEAGUE_NEXTMATCH_PREVIOUS', $this->allteams[$this->currentteam]->name); ?></h2>
<table width="100%">
	<tr>
		<td>
		<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
			<?php
			$pr_id = 0;
			$k=0;
			
			foreach ( $this->previousx[$this->currentteam] as $game )
			{
				$class = ($k == 0)? 'sectiontableentry1' : 'sectiontableentry2';
				$result_link = JoomleagueHelperRoute::getResultsRoute( $game->project_id,$game->roundid);
				$report_link = JoomleagueHelperRoute::getMatchReportRoute( $game->project_id,$game->id);
				$home = $this->allteams[$game->projectteam1_id];
				$away = $this->allteams[$game->projectteam2_id];
				?>
			<tr class="<?php echo $class; ?>">
				<td><?php
				echo JHTML::link( $result_link, $game->roundcode );
				?></td>
				<td nowrap="nowrap"><?php
				echo JHTML::date( $game->match_date, JText::_( 'COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE' ) );
				?></td>
				<td><?php
				echo substr( $game->match_date, 11, 5 );
				?></td>
				<td nowrap="nowrap"><?php
				echo $home->name;
				?></td>
				<td nowrap="nowrap">-</td>
				<td nowrap="nowrap"><?php
				echo $away->name;
				?></td>
				<td nowrap="nowrap"><?php
				echo $game->team1_result;
				?></td>
				<td nowrap="nowrap"><?php echo $this->overallconfig['seperator']; ?></td>
				<td nowrap="nowrap"><?php
				echo $game->team2_result;
				?></td>
				<td nowrap="nowrap"><?php
				if ($game->show_report==1)
				{
					$desc = JHTML::image( "media/com_joomleague/jl_images/zoom.png",
					JText::_( 'Match Report' ),
					array( "title" => JText::_( 'Match Report' ) ) );
					echo JHTML::link( $report_link, $desc);
				}
				$k = 1 - $k;
				?></td>
				<?php	if (($this->config['show_thumbs_picture'])): ?>
				<td><?php echo JoomleagueHelperHtml::getThumbUpDownImg($game, $this->currentteam); ?></td>
				<?php endif; ?>
			</tr>
			<?php
			}
			?>
		</table>
		</td>
	</tr>
</table>
<!-- End of  show matches -->
<?php endif; ?>