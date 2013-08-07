<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_ALTDECISION' ); ?></legend>
	<table>
		<tr>
			<td align="left">
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_ALTDECISION_RATE_MATCH' );
				?>
			</td>
			<td>
				<?php
				echo JHTML::_(	'select.booleanlist',
								'count_result' . $this->match->id,
								'class="inputbox"',
								$this->match->count_result );
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_ALTDECISION_RESULT_DECISION' );
				?>
			</td>
			<td>
				<select	name="alt_decision" id="alt_decision"
						   onchange="if (this.selectedIndex==0) {$('alt_decision_enter').style.display='none';} else {$('alt_decision_enter').style.display='block';}FormSwitch('', this.options[this.selectedIndex].value)">
					<option	value="0"<?php if ($this->match->alt_decision == 0){echo ' selected="selected"';} ?>>
						<?php echo JText::_( 'JNO' ); ?>
					</option>
					<option	value="1"<?php if ($this->match->alt_decision == 1){echo ' selected="selected"';} ?>>
						<?php echo JText::_( 'JYES' ); ?>
					</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div	id="alt_decision_enter"
						style="display:<?php echo ($this->match->alt_decision == 0) ? 'none' : 'block'; ?>">
					<table>
						<tr>
							<td align="left">
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_ALTDECISION_NEW_SCORE_HOMETEAM' );
								?>
							</td>
							<td>
								<input	type="text" class="inputbox" id="team1_result_decision"
										name="team1_result_decision" size="3"
										value="<?php
												if ($this->match->alt_decision == 1)
												{
													if (isset($this->match->team1_result_decision))
													{
														echo $this->match->team1_result_decision;
													}
													else
													{
														echo 'X';
													}
												}?>" <?php if ($this->match->alt_decision == 0){echo 'DISABLED ';} ?>/>
							</td>
						</tr>
						<tr>
							<td align="left">
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_ALTDECISION_NEW_SCORE_GUESTTEAM' );
								?>
							</td>
							<td>
								<input	type="text" class="inputbox" id="team2_result_decision"
										name="team2_result_decision" size="3"
										value="<?php if ($this->match->alt_decision == 1){if (isset($this->match->team2_result_decision)){echo $this->match->team2_result_decision;}else{echo 'X';}} ?>" <?php if ($this->match->alt_decision == 0){echo 'DISABLED ';} ?>/>
							</td>
						</tr>
						<tr>
							<td align="left">
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_ALTDECISION_NOTICE' );
								?>
							</td>
							<?php
							if ( ( is_null($this->match->team1_result) ) or
								 ( $this->match->alt_decision == 0 ) )
							{
								$disinfo='DISABLED ';
							}
							?>
							<td>
								<input	type="text" class="inputbox" id="decision_info"
										name="decision_info" size="50" maxlength='128'
										value="<?php if ($this->match->alt_decision == 1){echo $this->match->decision_info;} ?>" <?php if ($this->match->alt_decision == 0){echo 'DISABLED ';} ?>/>
							</td>
						</tr>

					</table>
				</div>
			</td>
	</tr>
	</table>
</fieldset>
<div style="text-align:right; ">
	<input type="submit" value="<?php echo JText::_( 'JSAVE' ); ?>">
</div><br />