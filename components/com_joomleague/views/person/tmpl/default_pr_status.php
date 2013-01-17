<?php defined( '_JEXEC' ) or die( 'Restricted access' );

if (	( isset($this->inprojectinfo->injury) && $this->inprojectinfo->injury > 0 ) ||
		( isset($this->inprojectinfo->suspension) && $this->inprojectinfo->suspension > 0 ) ||
		( isset($this->inprojectinfo->away) && $this->inprojectinfo->away > 0 ) )
{
	?>
	<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				echo '&nbsp;' . JText::_( 'Status of Person' );
				?>
			</td>
		</tr>
	</table>
	<br/>
	<table width="96%" align="center" border="0" cellpadding="0" cellspacing="5">
		<?php
		if ($this->inprojectinfo->injury > 0)
		{
			$injury_date = "";
			$injury_end  = "";

			if (is_array($this->roundsdata))
			{
				//injury start
				if (array_key_exists($this->inprojectinfo->injury_date, $this->roundsdata))
				{
					$injury_date = JHTML::date($this->roundsdata[$this->inprojectinfo->injury_date]['date_first'], JText::_('COM_JOOMLEAGUE_BASIC_CALENDAR_DATE'));
					$injury_date .= " - ".$this->inprojectinfo->injury_date.". ".JText::_('COM_JOOMLEAGUE_MATCHDAY');
				}

				//injury end
				if (array_key_exists($this->inprojectinfo->injury_end, $this->roundsdata))
				{
					$injury_end = JHTML::date($this->roundsdata[$this->inprojectinfo->injury_end]['date_last'], JText::_('COM_JOOMLEAGUE_BASIC_CALENDAR_DATE'));
					$injury_end .= " - ".$this->inprojectinfo->injury_end.". ".JText::_('COM_JOOMLEAGUE_MATCHDAY');
				}
			}

			if ($this->inprojectinfo->injury_date == $this->inprojectinfo->injury_end)
			{
				?>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							$imageTitle = JText::_( 'Injured' );
							echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_INJURED' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $injury_end;
						?>
					</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							$imageTitle = JText::_( 'Injured' );
							echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							?>
						</b>
					</td>
				</tr>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_INJURY_DATE' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $injury_date;
						?>
					</td>
				</tr>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_INJURY_END' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $injury_end;
						?>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="td_l" class="nowrap">
					<b>
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_PLAYER_INJURY_TYPE' );
						?>
					</b>
				</td>
				<td class="td_l">
					<?php
					printf( "%s", htmlspecialchars( $this->inprojectinfo->injury_detail ) );
					?>
				</td>
			</tr>
			<?php
		}

		if ($this->inprojectinfo->suspension > 0)
		{
			$suspension_date = "";
			$suspension_end  = "";

			if (is_array($this->roundsdata))
			{
				//suspension start
				if (array_key_exists($this->inprojectinfo->suspension_date, $this->roundsdata))
				{
					$suspension_date = JHTML::date($this->roundsdata[$this->inprojectinfo->suspension_date]['date_first'], JText::_('COM_JOOMLEAGUE_BASIC_CALENDAR_DATE'));
					$suspension_date .= " - ".$this->inprojectinfo->suspension_date.". ".JText::_('COM_JOOMLEAGUE_MATCHDAY');
				}

				//suspension end
				if (array_key_exists($this->inprojectinfo->suspension_end, $this->roundsdata))
				{
					$suspension_end = JHTML::date($this->roundsdata[$this->inprojectinfo->suspension_end]['date_last'], JText::_('COM_JOOMLEAGUE_BASIC_CALENDAR_DATE'));
					$suspension_end .= " - ".$this->inprojectinfo->suspension_end.". ".JText::_('COM_JOOMLEAGUE_MATCHDAY');
				}
			}

			if ($this->inprojectinfo->suspension_date == $this->inprojectinfo->suspension_end)
			{
				?>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							$imageTitle = JText::_( 'Suspended' );
							echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_SUSPENDED' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $suspension_end;
						?>
					</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							$imageTitle = JText::_( 'Suspended' );
							echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							?>
						</b>
					</td>
				</tr>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_SUSPENSION_DATE' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $suspension_date;
						?>
					</td>
				</tr>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_SUSPENSION_END' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $suspension_end;
						?>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="td_l" class="nowrap">
					<b>
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_PLAYER_SUSPENSION_REASON' );
						?>
					</b>
				</td>
				<td class="td_l">
					<?php
					printf( "%s", htmlspecialchars( $this->inprojectinfo->suspension_detail ) );
					?>
				</td>
			</tr>
			<?php
		}

		if ($this->inprojectinfo->away > 0)
		{
			$away_date = "";
			$away_end  = "";

			if (is_array($this->roundsdata))
			{
				//suspension start
				if (array_key_exists($this->inprojectinfo->away_date, $this->roundsdata))
				{
					$away_date = JHTML::date($this->roundsdata[$this->inprojectinfo->away_date]['date_first'], JText::_('COM_JOOMLEAGUE_BASIC_CALENDAR_DATE'));
					$away_date .= " - ".$this->inprojectinfo->away_date.". ".JText::_('COM_JOOMLEAGUE_MATCHDAY');
				}

				//suspension end
				if (array_key_exists($this->inprojectinfo->away_end, $this->roundsdata))
				{
					$away_end = JHTML::date($this->roundsdata[$this->inprojectinfo->away_end]['date_last'], JText::_('COM_JOOMLEAGUE_BASIC_CALENDAR_DATE'));
					$away_end .= " - ".$this->inprojectinfo->away_end.". ".JText::_('COM_JOOMLEAGUE_MATCHDAY');
				}
			}

			if ($this->inprojectinfo->away_date == $this->inprojectinfo->away_end)
			{
				?>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							$imageTitle = JText::_( 'Away' );
							echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_AWAY' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $away_end;
						?>
					</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							$imageTitle = JText::_( 'Away' );
							echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							?>
						</b>
					</td>
				</tr>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_AWAY_DATE' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $away_date;
						?>
					</td>
				</tr>
				<tr>
					<td class="td_l" style="color: red">
						<b>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PLAYER_AWAY_END' );
							?>
						</b>
					</td>
					<td class="td_l">
						<?php
						echo $away_end;
						?>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="td_l" class="nowrap">
					<b>
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_PLAYER_AWAY_REASON' );
						?>
					</b>
				</td>
				<td class="td_l">
					<?php
					printf( "%s", htmlspecialchars( $this->inprojectinfo->away_detail ) );
					?>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<br/>
	<?php
}
?>