<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<!-- Player stats History START -->
<table width="100%" class="contentpaneopen">
	<tr>
		<td class="contentheading">
			<?php
			echo '&nbsp;' . JText::_('Personal Statistics');
			?>
		</td>
	</tr>
</table>
<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<br/>
			<table id="stats_history" width="96%" align="center" cellspacing="0" cellpadding="0" border="0">
				<tr class="sectiontableheader">
					<th class="td_l" class="nowrap">
						<?php
						echo JText::_( 'Competition' );
						?>
					</th>
					<th class="td_l" class="nowrap">
						<?php
						echo JText::_( "Team of Player" );
						?>
					</th>
					<th class="td_c">
						<?php
						echo JHTML::image( 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/played.png',
											 JText::_( 'COM_JOOMLEAGUE_PLAYER_PLAYED' ),
											 array( ' title' => JText::_( 'COM_JOOMLEAGUE_PLAYER_PLAYED' ),
													' width' => 20,
													' height' => 20 ) );
						?>
					</th>
					<?php
					if ( ( isset( $this->overallconfig['use_jl_substitution'] ) ) &&
						 ( $this->overallconfig['use_jl_substitution'] == 1 ) )
					{
						?>
						<th class="td_c">
							<?php
							echo JHTML::image( 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/startroster.png',
												 JText::_( 'COM_JOOMLEAGUE_PLAYER_STARTROSTER' ),
												 array( ' title' => JText::_( 'COM_JOOMLEAGUE_PLAYER_STARTROSTER' ) ) );
							?>
						</th>
						<th class="td_c">
							<?php
							echo JHTML::image( 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/in.png',
												 JText::_( 'COM_JOOMLEAGUE_PLAYER_IN' ),
												 array( ' title' => JText::_( 'COM_JOOMLEAGUE_PLAYER_IN' ) ) );
							?>
						</th>
						<th class="td_c">
							<?php
							echo JHTML::image( 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/out.png',
												 JText::_( 'COM_JOOMLEAGUE_PLAYER_OUT' ),
												 array( ' title' => JText::_( 'COM_JOOMLEAGUE_PLAYER_OUT' ) ) );
							?>
						</th>
						<?php
					}
					if ( count( $this->AllEvents ) )
					{
						foreach( $this->AllEvents as $eventtype )
						{
							?>
							<th class="td_c">
							<?php
								$iconPath = $eventtype->icon;
								if ( !strpos( " " . $iconPath, "/"	) )
								{
									$iconPath = "images/com_joomleague/database/events/" . $iconPath;
								}
								echo JHTML::image(	$iconPath,
													 $eventtype->name,
													 array( "title" => $eventtype->name . " (" . $eventtype->id . ")",
															"align" => "top",
															"hspace" => "2" ) );
							?>
							</th>
							<?php
						}
					}
					?>
				</tr>
				<?php
				$k = 0;
				$carerr = array();
				$career['played'] = 0;
				$career['in'] = 0;
				$career['out'] = 0;
				$player =& JLGModel::getInstance( "Person", "JoomleagueModel" );
				if ( count( $this->historyPlayer ) > 0 )
				{
					foreach ( $this->historyPlayer as $player_hist )
					{
						$model = $this->getModel();
						$this->assignRef( 'inoutstat', $model->getInOutStats( $player_hist->project_id, $player_hist->pid ) );
						?>
						<tr class="<?php echo ($k == 0)? 'sectiontableentry1' : 'sectiontableentry2'; ?>">
							<td class="td_l" class="nowrap">
								<?php
								echo $player_hist->project_name;
								# echo " (" . $player_hist->project_id . ")";
								?>
							</td>
							<td class="td_l" class="nowrap">
								<?php
								echo $player_hist->team_name;
								?>
							</td>
							<!-- Player stats History - played start -->
							<td class="td_c">
								<?php
								echo ($this->inoutstat[0]->played > 0) ? $this->inoutstat[0]->played : '-';
								$career['played'] += $this->inoutstat[0]->played;
								?>
							</td>
							<?php
							//substitution system
							if ( ( isset( $this->overallconfig['use_jl_substitution'] ) &&
								 ( $this->overallconfig['use_jl_substitution'] == 1 ) ) )
							{
								?>
								<!-- Player stats History - startroster start -->
								<td class="td_c">
									<?php
									echo ($this->inoutstat[0]->played - $this->inoutstat[0]->sub_in > 0) ? $this->inoutstat[0]->played - $this->inoutstat[0]->sub_in : '-';
									?>
								</td>
								<!-- Player stats History - substituion in start -->
								<td class="td_c">
									<?php
									$career['in'] += $this->inoutstat[0]->sub_in;
									echo ($this->inoutstat[0]->sub_in > 0) ? $this->inoutstat[0]->sub_in : '-';
									?>
								</td>
								<!-- Player stats History - substitution out start -->
								<td class="td_c">
									<?php
									$career['out'] += $this->inoutstat[0]->sub_out;
									echo ($this->inoutstat[0]->sub_out > 0) ? $this->inoutstat[0]->sub_out : '-';
									?>
								</td>
							<?php
							}
							?>
							<!-- Player stats History - allevents start -->
							<?php
							// stats per project
							if ( count( $this->AllEvents ) )
							{
								foreach( $this->AllEvents as $eventtype )
								{
									$stat = $player->getPlayerEvents($eventtype->id, $player_hist->project_id);
									?>
									<td class="td_c">
										<?php
										echo ( $stat > 0 ) ? $stat : '-';
										?>
									</td>
									<?php
								}
							}
							?>
							<!-- Player stats History - allevents end -->
						</tr>
						<?php
						$k = 1 - $k;
					}
				}
				$cp = 3;
				if ( ( isset( $this->overallconfig['use_jl_substitution'] ) ) &&
					 ( $this->overallconfig['use_jl_substitution'] == 1 ) )
				{
					$cp += 3;
				}
				$cp += count( $this->AllEvents );
				?>
				<tr class="sectiontableheader">
					<td colspan="<?php echo $cp ?>" height="2">

					</td>
				</tr>
				<tr class="career_stats_total">
					<td class="td_r" colspan="2">
						<b>
							<?php
							echo JText::_( 'Totals of Career as a Player' );
							?>
						</b>
					</td>
					<td class="td_c">
						<?php
						echo ($career['played'] > 0) ? $career['played'] : '-';
						?>
					</td>
					<?php //substitution system
					if ( ( isset( $this->overallconfig['use_jl_substitution'] ) ) &&
						 ( $this->overallconfig['use_jl_substitution'] == 1 ) )
					{
						?>
						<td class="td_c">
							<?php
							echo ($career['played'] - $career['in'] > 0) ? $career['played'] - $career['in'] : '-';
							?>
						</td>
						<td class="td_c">
							<?php
							echo ($career['in'] > 0) ? $career['in'] : '-';
							?>
						</td>
						<td class="td_c">
							<?php
							echo ($career['out'] > 0) ? $career['out'] : '-';
							?>
						</td>
						<?php
					}
					?>

					<?php // stats per project
					if ( count( $this->AllEvents ) )
					{
						foreach( $this->AllEvents as $eventtype )
						{
							#$total = $player->getPlayerEvents( $eventtype->id );
							if (isset($player_hist))
							{
								$total = $player->getPlayerEvents($eventtype->id);
							}
							else
							{
								$total = '';
							}
							?>
							<td class="td_c">
								<?php
								echo (($total > 0) ? $total : '-');
								?>
							</td>
							<?php
						}
					}
					?>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br/>
<!-- Player stats History END -->
