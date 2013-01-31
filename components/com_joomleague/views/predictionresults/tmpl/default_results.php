<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die(JText::_('Restricted access'));
JHTML::_('behavior.tooltip');


//echo '<br /><pre>~' . print_r($this->config,true) . '~</pre><br />';
//echo '<br /><pre>~' . print_r($this->config['limit'],true) . '~</pre><br />';
?>
<a name='jl_top' id='jl_top'></a>
<?php
foreach ($this->model->_predictionProjectS AS $predictionProject)
{
	$gotSettings = $predictionProjectSettings = $this->model->getPredictionProject($predictionProject->project_id);
	if ((($this->model->pjID==$predictionProject->project_id) && ($gotSettings)) || ($this->model->pjID==0))
	{
		$this->model->pjID = $predictionProject->project_id;
		$this->model->predictionProject = $predictionProject;
		$actualProjectCurrentRound = $this->model->getProjectSettings($predictionProject->project_id);
		if (!isset($this->roundID) || ($this->roundID < 1)){$this->roundID=$actualProjectCurrentRound;}
		if ($this->roundID < 1){$this->roundID=1;}
		if ($this->roundID > $this->model->getProjectRounds($predictionProject->project_id)){$this->roundID=$this->model->_projectRoundsCount;}
		?>
		<form name='resultsRoundSelector' method='post'>
			<input type='hidden' name='option' value='com_joomleague' />
			<input type='hidden' name='view' value='predictionresults' />
			<input type='hidden' name='prediction_id' value='<?php echo (int)$this->predictionGame->id; ?>' />
			<input type='hidden' name='project_id' value='<?php echo (int)$predictionProject->project_id; ?>' />
			
      <input type='hidden' name='pj' value='<?php echo (int)$predictionProject->project_id; ?>' />
      <input type='hidden' name='p' value='<?php echo (int)$predictionProject->project_id; ?>' />
			<input type='hidden' name='r' value='<?php echo (int)$this->roundID; ?>' />
			<input type='hidden' name='pjID' value='<?php echo (int)$this->model->pjID; ?>' />
			<input type='hidden' name='task' value='predictionresults.selectProjectRound' />
			
			<?php echo JHTML::_('form.token'); ?>

			<table class='blog' cellpadding='0' cellspacing='0' >
				<tr>
					<td class='sectiontableheader'>
						<?php
						echo '<b>'.JText::sprintf($this->optiontext.'JL_PRED_RESULTS_SUBTITLE_01').'</b>';
						?>
					</td>
					<td class='sectiontableheader' style='text-align:right; ' width='20%' nowrap='nowrap' ><?php
						$rounds = JoomleagueHelper::getRoundsOptions($predictionProject->project_id);
						//$htmlRoundsOptions = JHTML::_('select.genericlist',$rounds,'current_round','class="inputbox" size="1" onchange="document.forms[\'resultsRoundSelector\'].r.value=this.value;submit()"','value','text',$this->roundID);
						$htmlRoundsOptions = JHTML::_('select.genericList',$rounds,'r','class="inputbox" onchange="this.form.submit(); "','value','text',$this->roundID);
            echo JText::sprintf(	$this->optiontext.'JL_PRED_RESULTS_SUBTITLE_02',
						$htmlRoundsOptions,
						$this->model->createProjectSelector($this->model->_predictionProjectS,$predictionProject->project_id));
						
            echo '&nbsp;&nbsp;';
            
						$link = JoomleagueHelperRoute::getResultsRoute($predictionProject->project_id,$this->roundID);
						$imgTitle=JText::_($this->optiontext.'JL_PRED_ROUND_RESULTS_TITLE');
						$desc = JHTML::image('media/com_joomleague/jl_images/icon-16-Matchdays.png',$imgTitle,array('border' => 0,'title' => $imgTitle));
						echo JHTML::link($link,$desc,array('target' => ''));
						?></td>
				</tr>
			</table><br />
		</form>
		<table width='100%' cellpadding='0' cellspacing='0'>
			<tr>
				<?php $tdClassStr="class='sectiontableheader' style='text-align:center; vertical-align:middle; '"; ?>
				<td <?php echo $tdClassStr; ?> ><?php echo JText::_($this->optiontext.'JL_PRED_RANK'); ?></td>
				<?php
				
        if ($this->config['show_user_icon'])
				{
					?><td <?php echo $tdClassStr; ?> ><?php echo JText::_($this->optiontext.'JL_PRED_AVATAR'); ?></td><?php
				}
				
				?>
				<td <?php echo $tdClassStr; ?> ><?php echo JText::_($this->optiontext.'JL_PRED_MEMBER'); ?></td>
				<?php
				// holen wir uns die spiele
				$match_ids = '';
				if ( $this->config['use_pred_select_matches'] )
        {
        $match_ids = $this->config['predictionmatchid'];
        //echo '<br />predictionmatchid<pre>~' . print_r($this->config['predictionmatchid'],true) . '~</pre><br />';
        }
        
				$roundMatchesList = $this->model->getMatches($this->roundID,$predictionProject->project_id,$match_ids);
				
				//echo '<br />roundMatchesList<pre>~' . print_r($roundMatchesList,true) . '~</pre><br />';
				
				foreach ($roundMatchesList AS $match)
				{
					?>
					<td <?php echo $tdClassStr; ?> >
          <?php
          // clublogo oder vereinsflagge
						if ( $this->config['show_logo_small_overview'] == 1 ){echo JoomleagueModelPredictionResults::showClubLogo($match->homeLogo,$match->homeName).'<br />';}
						if ( $this->config['show_logo_small_overview'] == 2 ){echo Countries::getCountryFlag($match->homeCountry).'<br />';}
            $outputStr = (isset($match->homeResult)) ? $match->homeResult : '-';
						$outputStr .= '&nbsp;'.$this->config['seperator'].'&nbsp;';
						$outputStr .= (isset($match->awayResult)) ? $match->awayResult : '-';
						?><span class='hasTip' title="<?php echo JText::sprintf($this->optiontext.'JL_PRED_RESULTS_RESULT_HINT',$match->homeName,$match->awayName,$outputStr); ?>"><?php echo $outputStr; ?></span><?php
						if ( $this->config['show_logo_small_overview'] == 1 ){echo '<br />'.JoomleagueModelPredictionResults::showClubLogo($match->awayLogo,$match->awayName);}
						if ( $this->config['show_logo_small_overview'] == 2 ){echo '<br />'.Countries::getCountryFlag($match->awayCountry);}
						
            ?>
            </td>
					<?php
				}
				?>
				<?php
				if ($this->config['show_points'])
				{
					?><td <?php echo $tdClassStr; ?> ><?php echo JText::_($this->optiontext.'JL_PRED_POINTS'); ?></td><?php
				}
				?>
				<?php
				if ($this->config['show_average_points'])
				{
					?><td <?php echo $tdClassStr; ?> ><?php echo JText::_($this->optiontext.'JL_PRED_AVERAGE'); ?></td><?php
				}
				?>
			</tr>
			<?php
			
			if ( $this->show_debug_info )
      {
			echo '<br />predictionMember<pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';
			echo '<br />predictionProject<pre>~' . print_r($predictionProject,true) . '~</pre><br />';
			}
			
			$k = 0;
			$tdStyleStr = " style='text-align:center; vertical-align:middle; ' ";

			$memberList = $this->model->getPredictionMembersList($this->config,$this->configavatar);
			//$memberResultsList = $this->model->getPredictionMembersResultsList($predictionProject->project_id,$this->roundID);

			$membersResultsArray = array();
			$membersDataArray = array();
			$membersMatchesArray = array();

      if ( $this->show_debug_info )
        {
				echo '<br />memberList<pre>~' . print_r($memberList,true) . '~</pre><br />';
				}
				
			foreach ($memberList AS $member)
			{
			
			  if ( $this->show_debug_info )
        {
				echo '<br />member<pre>~' . print_r($member,true) . '~</pre><br />';
				}
				
				$memberPredictionPoints = $this->model->getPredictionMembersResultsList(	$predictionProject->project_id,
																							$this->roundID,
																							$this->roundID,
																							$member->user_id);
        if ( $this->show_debug_info )
        {																							
				echo '<br />memberPredictionPoints<pre>~' . print_r($memberPredictionPoints,true) . '~</pre><br />';
				}

				$memberPredictionPointsCount=0;
				$predictionsCount=0;
				$totalPoints=0;
				$totalTop=0;
				$totalDiff=0;
				$totalTend=0;
				$totalJoker=0;

				//$membersMatchesArray[$member->pmID]='';
				if (!empty($memberPredictionPoints))
				{
					foreach ($memberPredictionPoints AS $memberPredictionPoint)
					{
						if ((!is_null($memberPredictionPoint->homeResult)) ||
							(!is_null($memberPredictionPoint->awayResult)) ||
							(!is_null($memberPredictionPoint->homeDecision)) ||
							(!is_null($memberPredictionPoint->awayDecision)))
						{
						
						if ( $this->show_debug_info )
            {
				    echo '<br />memberPredictionPoint<pre>~' . print_r($memberPredictionPoint,true) . '~</pre><br />';
				    }
				    
							$predictionsCount++;
							$result = $this->model->createResultsObject(	$memberPredictionPoint->homeResult,
																			$memberPredictionPoint->awayResult,
																			$memberPredictionPoint->prTipp,
																			$memberPredictionPoint->prHomeTipp,
																			$memberPredictionPoint->prAwayTipp,
																			$memberPredictionPoint->prJoker,
																			$memberPredictionPoint->homeDecision,
																			$memberPredictionPoint->awayDecision);
							$newPoints = $this->model->getMemberPredictionPointsForSelectedMatch($predictionProject,$result);
							//if (!is_null($memberPredictionPoint->prPoints))
							{
								$points=$memberPredictionPoint->prPoints;
								if ($newPoints!=$points)
								{
									// this check also should be done if the result is not displayed
									$memberPredictionPoint=$this->model->savePredictionPoints(	$memberPredictionPoint,
																								$predictionProject,
																								true);
									//$points=$newPoints;
								}
								//$totalPoints=$totalPoints+$points;
							}
							if (!is_null($memberPredictionPoint->prJoker)){$totalJoker=$totalJoker+$memberPredictionPoint->prJoker;}
							if (!is_null($memberPredictionPoint->prTop)){$totalTop=$totalTop+$memberPredictionPoint->prTop;}
							if (!is_null($memberPredictionPoint->prDiff)){$totalDiff=$totalDiff+$memberPredictionPoint->prDiff;}
							if (!is_null($memberPredictionPoint->prTend)){$totalTend=$totalTend+$memberPredictionPoint->prTend;}
						}

            if ( $this->show_debug_info )
            {
            echo '<br />memberPredictionPoint<pre>~' . print_r($memberPredictionPoint,true) . '~</pre><br />';
            }
						
						$memberPredictionOutput = JText::_($this->optiontext.'JL_PRED_RESULTS_NOT_AVAILABLE');
						$matchTimeDate = JoomleagueHelper::getTimestamp($memberPredictionPoint->match_date,1,$predictionProjectSettings->serveroffset);
						$thisTimeDate = JoomleagueHelper::getTimestamp('',1,$predictionProjectSettings->serveroffset);
						$showAllowed = (($thisTimeDate >= $matchTimeDate) ||
										(!is_null($memberPredictionPoint->homeResult)) ||
										(!is_null($memberPredictionPoint->awayResult)) ||
										(!is_null($memberPredictionPoint->homeDecision)) ||
										(!is_null($memberPredictionPoint->awayDecision)) ||
										($this->predictionMember->pmID==$member->pmID));

						if ($showAllowed)
						{
							
              // anzeige ändern bei normaler tipeingabe und toto-tip
              if ( $predictionProject->mode == 0 )
              {
              $memberPredictionOutput = $memberPredictionPoint->prHomeTipp.$this->config['seperator'].$memberPredictionPoint->prAwayTipp;
              }
              elseif ( $predictionProject->mode == 1 )
              {
              $memberPredictionOutput = $memberPredictionPoint->prTipp;
              }



							if ((!is_null($memberPredictionPoint->homeResult)) ||
								(!is_null($memberPredictionPoint->awayResult)) ||
								(!is_null($memberPredictionPoint->homeDecision)) ||
								(!is_null($memberPredictionPoint->awayDecision)))
							{
								$points=$memberPredictionPoint->prPoints;
								$totalPoints = $totalPoints+$points;
								$memberPredictionPointsCount++;
								$memberPredictionOutput .= '<sub style="color: red;">'.$points.'</sub>';
							}
							else	// needed for Windows Internet Explorer
							{
								$memberPredictionOutput .= '<sub>&nbsp;</sub>';
							}
						}
						else
						{
							$memberPredictionOutput = '- '.$this->config['seperator'].' -';
						}
						
            if ($memberPredictionPoint->prJoker)
						{
							$memberPredictionOutput .= '<sub style="color: red;">*</sub>';
						}

						//$membersMatchesArray[$member->pmID].='<td'.$tdStyleStr.'>'.$memberPredictionOutput.'</td>';
						$membersMatchesArray[$member->pmID][$memberPredictionPoint->matchID] = $memberPredictionOutput;
					}
				}

				$membersResultsArray[$member->pmID]['rank']				= 0;
				$membersResultsArray[$member->pmID]['predictionsCount']	= $predictionsCount;
				$membersResultsArray[$member->pmID]['totalPoints']		= $totalPoints;
				$membersResultsArray[$member->pmID]['totalTop']			= $totalTop;
				$membersResultsArray[$member->pmID]['totalDiff']		= $totalDiff;
				$membersResultsArray[$member->pmID]['totalTend']		= $totalTend;
				$membersResultsArray[$member->pmID]['totalJoker']		= $totalJoker;

				// check all needed output for later
				{
					$picture = $member->avatar;
					
					if ( $member->aliasName )
          {
          $member->name = $member->aliasName;
          }
          
					$playerName = $member->name;
					
					if (((!isset($member->avatar)) ||
						($member->avatar=='') ||
						(!file_exists($member->avatar)) ||
						((!$member->show_profile) && ($this->predictionMember->pmID!=$member->pmID))))
					{
						$picture = JoomleagueHelper::getDefaultPlaceholder("player");
					}
				//tobe removed
				//$imgTitle = JText::sprintf('JL_PRED_AVATAR_OF',$member->name);
				//$output = JHTML::image($member->avatar,$imgTitle,array(' width' => 20, ' title' => $imgTitle));
				
					$output = JoomleagueHelper::getPictureThumb($picture, $playerName,0,25);
					$membersDataArray[$member->pmID]['show_user_icon'] = $output;
				
					if (($this->config['link_name_to'])&&(($member->show_profile)||($this->predictionMember->pmID==$member->pmID)))
					{
						$link = PredictionHelperRoute::getPredictionMemberRoute($this->predictionGame->id,$member->pmID);
						$output = JHTML::link($link,$member->name);
					}
					else
					{
						$output = $member->name;
					}
					$membersDataArray[$member->pmID]['name'] = $output;
				}
			}

      if ( $this->show_debug_info )
      {
			echo '<br />membersResultsArray<pre>~' . print_r($membersResultsArray,true) . '~</pre><br />';
			echo '<br />membersDataArray<pre>~' . print_r($membersDataArray,true) . '~</pre><br />';
			}
			
			$computedMembersRanking = $this->model->computeMembersRanking($membersResultsArray,$this->config);
			$recordCount = count($computedMembersRanking);
			
			if ( $this->show_debug_info )
      {
			echo '<br />computedMembersRanking<pre>~' . print_r($computedMembersRanking,true) . '~</pre><br />';
      echo '<br />membersMatchesArray<pre>~' . print_r($membersMatchesArray,true) . '~</pre><br />';
      }
      
			$i=1;
			if ((int)$this->config['limit'] < 1){$this->config['limit']=1;}
			$rlimit=ceil($recordCount / $this->config['limit']);
			$this->model->page=($this->model->page > $rlimit) ? $rlimit : $this->model->page;
			$skipMemberCount=($this->model->page > 0) ? (($this->model->page-1)*$this->config['limit']) : 0;

			foreach ($computedMembersRanking AS $key => $value)
			{
				if ($i <= $skipMemberCount) { $i++; continue; }

				$class = ($k==0) ? 'sectiontableentry1' : 'sectiontableentry2';
				$styleStr = ($this->predictionMember->pmID==$key) ? ' style="background-color:yellow; color:black; " ' : '';
				$class = ($this->predictionMember->pmID==$key) ? 'sectiontableentry1' : $class;
				$tdStyleStr = " style='text-align:center; vertical-align:middle; ' ";
				?>
				<?php
	
    			//$this->config['show_all_user']=1;
				//if (((!$this->config['show_all_user']) && ($value['predictionsCount'] > 0)) ||
//					( $this->config['show_all_user'] ) ||
//					( $this->predictionMember->pmID == $key ) )
//				{
    
					?>
					<tr class='<?php echo $class; ?>' <?php echo $styleStr; ?> >
						<td<?php echo $tdStyleStr; ?>><?php echo $value['rank']; ?></td>
						<?php
						if ($this->config['show_user_icon'])
						{
							?><td<?php echo $tdStyleStr; ?>><?php echo $membersDataArray[$key]['show_user_icon']; ?></td><?php
						}
						?>
						<td<?php echo $tdStyleStr; ?>><?php echo $membersDataArray[$key]['name']; ?></td>

						<?php
						foreach ($roundMatchesList AS $roundMatch)
						{
							echo '<td'.$tdStyleStr.'>';
							if (isset($membersMatchesArray[$key][$roundMatch->mID]))
							{
								echo $membersMatchesArray[$key][$roundMatch->mID];
							}
							else
							{
								echo JText::_($this->optiontext.'JL_PRED_RESULTS_NOT_AVAILABLE');
							}
							echo '</td>';
						}
						?>

						<?php
						if ($this->config['show_points'])
						{
							?><td<?php echo $tdStyleStr; ?>><?php echo $membersResultsArray[$key]['totalPoints']; ?></td><?php
						}
						?>
						<?php
						if ($this->config['show_average_points'])
						{
							?><td<?php echo $tdStyleStr; ?>><?php
							if ($membersResultsArray[$key]['predictionsCount'] > 0)
							{
								echo number_format(round($membersResultsArray[$key]['totalPoints']/$membersResultsArray[$key]['predictionsCount'],2),2);
							}
							else
							{
								echo number_format(0,2);
							}
							?></td><?php
						}
						?>
					</tr>
					<?php
					$k = (1-$k);
					$i++;
					//if ($i > $skipMemberCount+$this->config['limit']){break;}
				//}
			}
			?>
		</table>
	<?php
	}
}
?>