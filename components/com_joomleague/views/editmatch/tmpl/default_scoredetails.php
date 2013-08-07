<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<fieldset class="adminform">
	<legend>
		<?php
		echo JText::sprintf(	'COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_TITLE',
								'<i>' . $this->team1->name . '</i>',
								'-',
								'<i>' . $this->team2->name . '</i>' );
		?>
	</legend>
	<?php
	// Create global variable for readabillity of script source
	$matchid = $this->match->id;

	if ( ( !isset( $this->overallconfig['results_below'] ) || $this->overallconfig['results_below'] == 0 ) &&
		 ( !isset( $this->overallconfig['edit_results_below'] ) || $this->overallconfig['edit_results_below'] == 0 ) )
	{
		if( $this->project->allow_add_time )
		{
			$xrounds = array();
			$xrounds[] = JHTML::_( 'select.option', 0, JText::_('COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_REGULAR_TIME'), 'value', 'text' );
			$xrounds[] = JHTML::_( 'select.option', 1, JText::_('COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_OVER_TIME'), 'value', 'text' );
			$xrounds[] = JHTML::_( 'select.option', 2, JText::_('COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_SHOOTOUTS'), 'value', 'text' );
			$mstl = JHTML::_(	'select.genericlist', $xrounds, 'match_result_type',
								'class="inputbox" size="1"', 'value', 'text', $this->match->match_result_type );
			unset( $xrounds );
		}
	}
	?>
	<!-- Score Table START -->
	<!-- Main END -->
	<input type="hidden" class="button" name="save_data" value="1" />
	<table border="0">
		<tr class="sectiontableheader">
			<th>&nbsp;</th>
			<th align="right">
				<?php
				echo $this->team1->name;
				?>
			</th>
			<th>
				&nbsp;
			</th>
			<th>
				<?php
				echo $this->team2->name;
				?>
			</th>
		</tr>
		<?php
		if ( ( !isset( $this->overallconfig['results_below'] ) ||
			   $this->overallconfig['results_below'] == 0 ) &&
			 ( !isset( $this->overallconfig['edit_results_below'] ) ||
			   $this->overallconfig['edit_results_below'] == 0 ) )
		{
			?>
			<tr>
				<td align="left">
					<?php
					echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_PART_RESULT' );
					?>
				</td>
				<td align="right">
					<?php
					$partresults1 = explode( ";", $this->match->team1_result_split );
					$partresults2 = explode( ";", $this->match->team2_result_split );
					for ( $x = 0; $x < ( $this->project->game_parts ); $x++ )
					{
						echo ($x+1) . ':&nbsp;';
						$value = '';
						if ( isset( $partresults1[$x] ) )
						{
							$value = $partresults1[$x];
						}
						$tabindex = 2 * $x + 10;
						echo '<input type="text" name="team1_result_split[]" value="' . $value;
						echo '" size="2" maxlength="4" tabindex="' . $tabindex . '" class="inputbox" />';
						echo '<br />';
					}
					if( $this->project->allow_add_time )
					{
						echo JText::_('COM_JOOMLEAGUE_RESULTS_OVERTIME') . ':';
						$value = '';
						if ( isset( $this->match->team1_result_ot ) )
						{
							$value = $this->match->team1_result_ot;
						}
						$tabindex = 2 * $this->project->game_parts + 10;
						echo '<input type="text" name="team1_result_ot" value="' . $value;
						echo '" size="2" maxlength="4" tabindex="' . $tabindex . '" class="inputbox" />';
						echo '<br />';

						echo JText::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT') . ':';
						$value = '';
						if ( isset( $this->match->team1_result_so ) )
						{
							$value = $this->match->team1_result_so;
						}
						$tabindex = 2 * $this->project->game_parts + 12;
						echo '<input type="text" name="team1_result_so" value="' . $value;
						echo '" size="2" maxlength="4" tabindex="' . $tabindex . '" class="inputbox" />';
						echo '<br />';
					}
					?>
				</td>
				<td align="center" style="line-height:19px; width:4px;">
					<?php
					for ( $x = 0; $x <( $this->project->game_parts ); $x++ )
					{
						echo ":<br />";
					}
					if ( $this->project->allow_add_time )
					{
						echo ":<br />";
					}
					?>
				</td>
				<td>
					<?php
					for ( $x = 0; $x < ( $this->project->game_parts ); $x++ )
					{
						$value = '';
						if ( isset( $partresults2[$x] ) )
						{
							$value = $partresults2[$x];
						}
						$tabindex = 2 * $x + 11;
						echo '<input type="text" name="team2_result_split[]" value="' . $value;
						echo '" size="2" maxlength="4" tabindex="'.$tabindex.'" class="inputbox" />';
						echo '<br />';
					}
					if ( $this->project->allow_add_time )
					{
						$value = '';
						if ( isset( $this->match->team2_result_ot ) )
						{
							$value = $this->match->team2_result_ot;
						}
						$tabindex = 2 * $this->project->game_parts + 11;
						echo '<input type="text" name="team2_result_ot" value="' . $value;
						echo '" size="2" maxlength="4" tabindex="' . $tabindex . '" class="inputbox" />';
						echo '<br />';

						$value = '';
						if ( isset( $this->match->team2_result_so ) )
						{
							$value = $this->match->team2_result_so;
						}
						$tabindex = 2 * $this->project->game_parts + 13;
						echo '<input type="text" name="team2_result_so" value="' . $value;
						echo '" size="2" maxlength="4" tabindex="' . $tabindex . '" class="inputbox" />';
					}
					?>
				</td>
			</tr>
			<?php
			if ( $this->project->use_legs == 1 )
			{
				?>
				<tr>
					<td>
						<?php
						if ( $this->rankingconfig['alternative_legs'] == "" )
						{
							echo JText::_('COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_LEGS');
						}
						else
						{
							echo $this->rankingconfig['alternative_legs'];
						}
						echo $this->overallconfig['seperator'];
						?>
					</td>
					<td align="right">
						<?php
						echo '<input type="text" name="team1_legs" ';
						echo 'value="' . $this->match->team1_legs . '" size="3" tabindex="100" class="inputbox" />';
						?>
					</td>
					<td align="center">:</td>
					<td>
						<?php
						echo '<input type="text" name="team2_legs" ';
						echo 'value="' . $this->match->team2_legs . '" size="3" tabindex="101" class="inputbox" />';
						?>
					</td>
				</tr>
				<?php
			}
		}
		?>
		<tr>
			<td>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_BONUS_POINTS' );
				?>
			</td>
			<td align="right">
				<input	type="text" name="team1_bonus" value="<?php echo $this->match->team1_bonus; ?>"
						size="2" maxlength="4" tabindex="102" class="inputbox" />
			</td>
			<td align="center">:</td>
			<td>
				<input	type="text" name="team2_bonus" value="<?php echo $this->match->team2_bonus; ?>"
						size="2" maxlength="4" tabindex="103" class="inputbox" />
			</td>
		</tr>
		<tr>
			<td>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_SCORE_NOTICE' );
				?>
			</td>
			<td colspan="3">
				<input	type="text" name="match_result_detail"
						value="<?php echo $this->match->match_result_detail; ?>" size="50" maxlength="64" class="inputbox" />
			</td>
		</tr>
		<?php
		if ( ( !isset($this->overallconfig['results_below']) || $this->overallconfig['results_below'] == 0 ) &&
			 ( !isset($this->overallconfig['edit_results_below'] ) || $this->overallconfig['edit_results_below'] == 0 ) )
		{
			if( $this->project->allow_add_time )
			{
				?>
				<tr>
					<td>
						<?php
						echo JText::_('COM_JOOMLEAGUE_EDITMATCH_SCOREDETAILS_RESULT_TYPE');
						?>
					</td>
					<td>
						<?php
						echo $mstl;
						?>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
	<!-- Score Table END -->
	<br/>
</fieldset>
<div style="text-align:right; ">
	<input type="submit" value="<?php echo JText::_( 'JSAVE' ); ?>">
</div><br />