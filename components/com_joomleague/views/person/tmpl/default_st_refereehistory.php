<?php defined( '_JEXEC' ) or die( 'Restricted access' );
if ( count( $this->historyReferee ) > 0 )
{
	?>
	<!-- Referee history START -->
	<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				echo '&nbsp;' . JText::_('Career as Referee');
				?>
			</td>
		</tr>
	</table>

	<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<br/>
				<table id="pl_referee_history" width="96%" align="center" cellspacing="0" cellpadding="0" border="0">
					<tr class="sectiontableheader">
						<th class="td_l">
							<?php
							echo JText::_( 'Competition' );
							?>
						</th>
						<th class="td_l">
							<?php
							echo JText::_( 'Season' );
							?>
						</th>
						<th class="td_c">
							<?php
							echo JText::_('Count of matches');
							?>
						</th>
						<th class="td_l">
							<?php
							echo JText::_( 'Referee function' );
							?>
						</th>
					</tr>
					<?php
					$k = 0;
					foreach ( $this->historyReferee AS $station )
					{
						#$link1 = JoomleagueHelperRoute::getTeamStaffRoute( $station->project_id, $station->pid, $station->ttid );
						$link1 = JoomleagueHelperRoute::getPlayerRoute( $station->project_id, $station->ptid , '2' );
						$link2 = JoomleagueHelperRoute::getPlayersRoute( $station->project_id, $station->ttid );
						?>
						<tr class="<?php echo ($k == 0)? 'sectiontableentry1' : 'sectiontableentry2'; ?>">
							<td class="td_l">
								<?php
								echo JHTML::link( $link1, $station->pname );
								?>
							</td>
							<td class="td_l">
								<?php
								echo $station->sname;
								?>
							</td>
							<td class="td_c">
								<?php
								echo $station->matchesCount;
								?>
							</td>
							<td class="td_l">
								<?php
								echo $station->position;
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
				</table>
			</td>
		</tr>
	</table>
	<br /><br />
	<!-- Referee history END-->
	<?php
}
?>