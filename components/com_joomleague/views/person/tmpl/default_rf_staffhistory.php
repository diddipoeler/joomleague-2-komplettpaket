<?php defined( '_JEXEC' ) or die( 'Restricted access' );
if ( count( $this->historyStaff ) > 0 )
{
	?>
	<!-- Staff history START -->
	<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				echo '&nbsp;' . JText::_('Career as Staff Member');
				?>
			</td>
		</tr>
	</table>

	<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<br/>
				<table id="pl_staff_history" width="96%" align="center" cellspacing="0" cellpadding="0" border="0">
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
						<th class="td_l">
							<?php
							echo JText::_('Team of Staffmember');
							?>
						</th>
						<th class="td_l">
							<?php
							echo JText::_( 'Function in Team' );
							?>
						</th>
					</tr>
					<?php
					$k = 0;
					foreach ( $this->historyStaff AS $station )
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
							<td class="td_l">
								<?php
								echo JHTML::link( $link2, $station->teamname );
								?>
							</td>
							<td class="td_l">
								<?php
								echo JText::_( $station->position );
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
	<!-- Staff history END -->
	<?php
}
?>