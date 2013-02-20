<?php defined('_JEXEC') or die('Restricted access');
?>
<div id="editcell">
	<fieldset class="adminform">


		<!-- Start games list -->
		<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id='adminForm'>
			<?php
			$colspan = 6;
			?>
			<table class='adminlist' border='0'>
				<thead>
					<tr>
						<th width="5" ><?php echo count($this->matches).'/'.$this->pagination->total; ?></th>
						<th width="20" >
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->matches); ?>);" />
						</th>

						<th class="title" nowrap="nowrap" ><?php echo JTEXT::_('JL_ADMIN_MATCHES_HOME_TEAM_PLAYER'); ?></th>
						<th class="title" nowrap="nowrap" ><?php echo JTEXT::_('JL_ADMIN_MATCHES_AWAY_TEAM_PLAYER'); ?></th>
						<th style="  "><?php echo JTEXT::_('JL_ADMIN_MATCHES_RESULT'); ?></th>
						
            
					</tr>
				</thead>
				<tfoot><tr><td colspan="<?php echo $colspan; ?>"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
				<tbody>
				
					<?php
					$k=0;
					for ($i=0,$n=count($this->matches); $i < $n; $i++)
					{
						$row		=& $this->matches[$i];
						$checked	= JHTML::_('grid.checkedout',$row,$i,'id');
						$published	= JHTML::_('grid.published',$row,$i);

						list($date,$time)=explode(" ",$row->match_date);
						$time=strftime("%H:%M",strtotime($time));
						?>
						<tr class="<?php echo "row$k"; ?>">
						<?php if(($row->cancel)>0)
								{
									$style="text-align:center;  background-color: #FF9999;";
								}
								else
								{
									$style="text-align:center; ";
								}
								?>
							<td style="<?php echo $style;?>">
								<?php
								echo $this->pagination->getRowOffset($i);
								?>
							</td>
							<td style="text-align:center; ">
								<?php
								echo $checked;
								?>
							</td>


							<td style="text-align: right; " nowrap="nowrap">
								
								<?php
								$append='';
								$append.=' onchange="document.getElementById(\'cb'.$i.'\').checked=true" ';
								echo JHTML::_(	'select.genericlist',$this->homeplayer,'projectteam1_id'.$row->id,
												'class="inputbox select-hometeam" size="1"'.$append,'value','text',$row->projectteam1_id);
								?>
							</td>
							
							
							<td style="text-align: left; " nowrap="nowrap">
								<?php
								$append='';
								$append.=' onchange="document.getElementById(\'cb'.$i.'\').checked=true" ';
								echo JHTML::_(	'select.genericlist',$this->awayplayer,'projectteam2_id'.$row->id,
												'class="inputbox select-awayteam" size="1"'.$append,'value','text',$row->projectteam2_id);
								?>
								
							</td>
							
							<td nowrap="nowrap" style="text-align: right; ">
								<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" <?php if($row->alt_decision==1) echo "class=\"subsequentdecision\" title=\"".JText::_('JL_ADMIN_MATCHES_SUB_DECISION')."\"" ?> type="text" name="team1_result<?php echo $row->id; ?>"
										value="<?php echo $row->team1_result; ?>" size="2" tabindex="4" class="inputbox" /> : 
								<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" <?php if($row->alt_decision==1) echo "class=\"subsequentdecision\" title=\"".JText::_('JL_ADMIN_MATCHES_SUB_DECISION')."\"" ?> type="text" name="team2_result<?php echo $row->id; ?>"
										value="<?php echo $row->team2_result; ?>" size="2" tabindex="4" class="inputbox" />
								<a	href="javascript:void(0)"
									onclick="switchMenu('part<?php echo $row->id; ?>')">&nbsp;
									<?php echo JHTML::_(	'image','administrator/components/com_joomleague/assets/images/arrow_open.png',
															JText::_('JL_ADMIN_MATCHES_PERIOD_SCORES'),
															'title= "'.JText::_('JL_ADMIN_MATCHES_PERIOD_SCORES').'"');
									?>
								</a>&nbsp;
								<span id="part<?php echo $row->id; ?>" style="display: none">
									<br />
									<?php
									$partresults1=explode(";",$row->team1_result_split);
									$partresults2=explode(";",$row->team2_result_split);
									for ($x=0; $x < ($this->projectws->game_parts); $x++)
									{
										echo ($x+1).".: "; ?>

										<input	onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" onchange="document.getElementById(\'cb'<?php echo $i; ?>'\').checked=true" type="text" style="font-size: 9px;"
												name="team1_result_split<?php echo $row->id;?>[]"
												value="<?php echo (isset($partresults1[$x])) ? $partresults1[$x] : ''; ?>"
												size="3" tabindex="1" class="inputbox" /> :&nbsp;
										<input	onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" onchange="document.getElementById(\'cb'<?php echo $i; ?>'\').checked=true" type="text" style="font-size: 9px;"
												name="team2_result_split<?php echo $row->id; ?>[]"
												value="<?php echo (isset($partresults2[$x])) ? $partresults2[$x] : ''; ?>"
												size="3" tabindex="1" class="inputbox" /><br />
										<?php
									}
									
									?>
								</span>
							</td>
							
							
							
							
							
							<td style="text-align:center; ">
								<?php
								echo $row->id;
								?>
							</td>
						</tr>
						<?php
						$k=1 - $k;
					}
					?>
				</tbody>
			</table>
				
			
			
			
			<input type='hidden' name='act' value='' id='short_act' />
			<input type='hidden' name='controller' value='jlextindividualsport' />
			<input type='hidden' name='boxchecked' value='0' />
			<input type='hidden' name='search_mode' value='<?php echo $this->lists['search_mode']; ?>' />
			<input type='hidden' name='filter_order' value='<?php echo $this->lists['order']; ?>' />
			<input type='hidden' name='filter_order_Dir' value='' />
			<input type='hidden' name='rid[]' value='<?php echo $this->roundws->id; ?>' />
			<input type='hidden' name='project_id' value='<?php echo $this->roundws->project_id; ?>' />
			<input type='hidden' name='act' value='' />
			<input type='hidden' name='task' value='' id='task' />
			<?php echo JHTML::_('form.token')."\n"; ?>
		</form>
	</fieldset>
</div>