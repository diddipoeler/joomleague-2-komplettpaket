<?php
JHTML::_('behavior.tooltip');

// echo 'this->teams<pre>';
// print_r($this->teams);
// echo '</pre><br>';

$current  = &$this->current;
$previous = &$this->previousRanking[$this->division];
$awayRank = ( isset( $this->awayRank[$this->division] ) ) ? $this->awayRank[$this->division] : NULL;
$homeRank = ( isset( $this->homeRank[$this->division] ) ) ? $this->homeRank[$this->division] : NULL;
$type     = &$this->type;
$config   = &$this->tableconfig;

// echo 'config <pre>';
// print_r($config);
// echo '</pre><br>';

// echo 'current <pre>';
// print_r($current);
// echo '</pre><br>';

// echo 'this->current <pre>';
// print_r($this->current);
// echo '</pre><br>';

// echo 'config ordered_columns  <pre>';
// print_r($config['ordered_columns']);
// echo '</pre><br>';

		$counter = 1;
		$k = 0;
		$j = 0;
		$temprank = 0;
		$tempoldrank = 0;
		//$config['ordered_columns'] = 'JL_POINTS, JL_PLAYED, JL_WINS, JL_TIES, JL_LOSSES, JL_RESULTS, JL_DIFF, JL_QUOT';

if ( (int)$this->alltimepoints == 2  )
{
$convert = array (
      'JL_POINTS' => 'JL_OLDNEGPOINTS'
  );

//$suchen_middle_name = $fehlt->middle_name;
$config['ordered_columns'] = str_replace(array_keys($convert), array_values($convert), $config['ordered_columns'] );
}		
    
    $columns = explode( ",", $config['ordered_columns'] );
		
// echo 'columns<pre>';
// print_r($columns);
// echo '</pre><br>';
		
		$stars = 1;

		foreach( $current as $ptid => $team )
		{
		
			$team->team = $this->teams[$ptid];
			$class = ($k == 0)? $config['style_class1'] : $config['style_class2'];
			
// 			echo 'team->team <pre> ';
//       echo print_r($team->team);
//       echo '</pre><br>';
      
//       echo 'this->teams <pre> ';
//       echo print_r($this->teams[$ptid]);
//       echo '</pre><br>';
 
// 			echo 'ptid = '.$ptid.'<br>';
			

			$color = "";

			if ( isset( $this->colors[$j]["from"] ) &&
				 $counter == $this->colors[$j]["from"] )
			{
				$color = $this->colors[$j]["color"];
			}

			if ( isset( $this->colors[$j]["from"] ) &&
				 isset( $this->colors[$j]["to"] ) &&
				 (	$counter > $this->colors[$j]["from"] &&
					 $counter <= $this->colors[$j]["to"] ) )
			{
				$color = $this->colors[$j]["color"];
			}

			if ( isset( $this->colors[$j]["to"] ) &&
				 $counter == $this->colors[$j]["to"] )
			{
				$j++;
			}

			$format = "%s";

			if ( in_array( $team->team->id, array( $this->project->fav_team ) ) &&
				 $config['fav_highlight_type'] == 1 )
			{
				if( trim( $this->project->fav_team_color ) != "" )
				{
					$color = $this->project->fav_team_color;
				}
				$format = "%s";
			}

			echo "\n\n";
			echo '<tr class="' . $class . '">';
			echo "\n";
				echo '<td class="rankingrow_rank" ';
					if( $color != '' )
					{
						echo ' style="background-color: ' . $color . '"';
					}
					echo ' align="right" nowrap="nowrap">';

					if ( $team->rank != $temprank )
					{
						printf( $format, $team->rank );
					}
					else
					{
						echo "-";
					}
					//printf($format, $counter);
				echo '</td>';
				echo "\n";

				echo '<td class="rankingrow_rank" ';
					if ( $color != '' )
					{
						echo " style='background-color: " . $color . "'";
					}
					echo ">";

					if ( isset( $previous[$ptid]->rank ) )
					{
						$imgsrc = JURI::root() . 'media/com_joomleague/jl_images/';
						if ( ( $team->rank == $previous[$ptid]->rank ) || ( $previous[$ptid]->rank == "" ) )
						{
							$imgsrc .= "same.png";
							$alt	 = "same";
							$title	 = "JL_RANKING_SAME";
						}
						elseif ( $team->rank < $previous[$ptid]->rank )
						{
							$imgsrc .= "up.png";
							$alt	 = "up";
							$title	 = "JL_RANKING_UP";
						}
						elseif ( $team->rank > $previous[$ptid]->rank )
						{
							$imgsrc .= "down.png";
							$alt	 = "down";
							$title	 = "JL_RANKING_DOWN";
						}
						echo JHTML::image(	$imgsrc,
											$alt,
											array( "title" => JText::_($title), "align" => "middle" ) );
					}
				echo '</td>';
				echo "\n";

				echo '<td class="rankingrow_rank" nowrap="nowrap" style="background-color:' . $color . '" align="right">';
					echo '<span class="small">';
					if ( ( $this->tableconfig['last_ranking'] == 1 ) &&
						 ( isset( $previous[$ptid]->rank ) ) )
					{
						echo "(" . $previous[$ptid]->rank . ")";
					}
					echo '</span>';
				echo '</td>';
				echo "\n";

				if ( $config['show_logo_small_table'] > 0 )
				{
					echo '<td valign="top"';
					if ( $color != '' )
					{
						echo ' style="background-color: ' . $color . '"';
					}
					echo ' align="center">';
						JoomleagueHelper::showClubIcon($team->team, $config['show_logo_small_table']);
					echo '</td>';
					echo "\n";
				}

				echo '<td class="rankingrow_team" nowrap="nowrap"';
					if ( $color != '' )
					{
						echo ' style="background-color: ' . $color . '"';
					}
					echo ' align="left">';
					$config['highlight_fav'] = in_array( $team->team->id, array( $this->project->fav_team ) ) ? 1 : 0;
					//echo JoomleagueHTML::formatTeamname( $team->team, 't' . $team->team->id, $config, $config['highlight_fav'] );
					echo JoomleagueHelper::formatTeamname( $team->team, 'tr' . $team->team->id, $config, $config['highlight_fav'] );
				
//         echo 'team->team -> '.$team->team.'<br>';
        
        echo '</td>';
				echo "\n";

				if( $type == 3 )
				{
					$tabcols = 3;
				}
				else
				{
					$tabcols = 1;
				}

				for ( $tabs = 0; $tabs < $tabcols; $tabs++ )
				{
					if ( $tabs == 0 )
					{
						$showRow = $team;
					}
					else
					{
						// search correct row
						$otherrow = 0;
						if ( $tabs == 1 )
						{
							foreach ( $homeRank AS $key => $value )
							//while ($homeRank[$otherrow]->team->projectteamid != $team->team->projectteamid)
							{
								//if ($key == $team->team->projectteamid) $otherrow++;
								if ( isset( $homeRank[$key]->team->projectteamid ) &&
									 $homeRank[$key]->team->projectteamid == $team->team->projectteamid )
								{
									$showRow = $homeRank[$key];
								}
							}
							  //$showRow = $homeRank[$otherrow];
						}
						else
						{
							foreach ( $awayRank AS $key => $value )
							  //while ($awayRank[$otherrow]->team->projectteamid != $team->team->projectteamid)
							{
								//$otherrow++;
								if ( isset( $awayRank[$key]->team->projectteamid ) &&
									 $awayRank[$key]->team->projectteamid == $team->team->projectteamid )
								{
									$showRow = $awayRank[$key];
								}
							}
							  //$showRow = $awayRank[$otherrow];
						}
					}
					  // table total
			// START OPTIONAL COLUMN DISPLAY
			foreach ( $columns AS $c )
			{
// 			if ( $c == 'PLAYED' )
// 			{
//       $c = 'JL_PLAYED';
//       }
				  switch ( trim( strtoupper( $c ) ) )
				  {
					  case 'JL_PLAYED':
						echo '<td class="rankingrow_played" ';
							if ( $color != '' && $type == 3 )
							{
								echo ' style="text-align:center;border-left: 1px solid; background-color:' . $color . '"';
							}
							else if ( $color != '' )
								{
									echo ' style="text-align:center;background-color:' . $color . '"';
								}
								else if ( $type == 3 )
									{
										echo ' style="text-align:center;border-left: 1px solid"';
									}
									else
									{
										echo ' style="text-align:center;"';
									}
							echo '>';
							printf( $format, $showRow->cnt_matches );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_WINS':
						echo '<td class="rankingrow" ';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							if (( $config['show_wdl_teamplan_link'])==1)
							{
							$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($showRow->_pid, $showRow->_teamid, 0, 1);
							echo JHTML::link($teamplan_link, $showRow->cnt_won);
							}
							else
							{
							printf( $format, $showRow->cnt_won );
							}
						echo '</td>';
						echo "\n";
					break;

					case 'JL_TIES':
						echo '<td class="rankingrow" ';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							if (( $config['show_wdl_teamplan_link'])==1)
							{							
							$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($showRow->_pid, $showRow->_teamid, 0, 2);
							echo JHTML::link($teamplan_link, $showRow->cnt_draw); 
							}
							else
							{							
							printf( $format, $showRow->cnt_draw );
							}
						echo '</td>';
						echo "\n";
					break;

					case 'JL_LOSSES':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							if (( $config['show_wdl_teamplan_link'])==1)
							{							
							$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($showRow->_pid, $showRow->_teamid, 0, 3);
							echo JHTML::link($teamplan_link, $showRow->cnt_lost); 	
							}
							else
							{							
							printf( $format, $showRow->cnt_lost );
							}
						echo '</td>';
						echo "\n";
					break;

					case 'JL_WINPCT':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, sprintf( "%.3F", ($showRow->winpct() ) ) );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_GB':
						//GB calculation, store wins and loss count of the team in first place
						if ( $team->rank == 1 )
						{
							$ref_won = $team->cnt_won;
							$ref_lost = $team->cnt_lost;
						}
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, round( ( ( $ref_won - $showRow->cnt_won ) - ( $ref_lost - $showRow->cnt_lost ) ) / 2, 1 ) );
						echo '</td>';
						echo "\n";

					break;

					case 'JL_LEGS':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, sprintf( "%s:%s", $team->sum_team1_legs, $team->sum_team2_legs ) );
						echo '</td>';
						echo "\n";
					break;
					
					case 'JL_LEGS_DIFF':   
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, $team->diff_team_legs );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_LEGS_RATIO':   
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							$legsratio=round(($showRow->legsRatio()),2);
							printf( $format, $legsratio );
						echo '</td>';
						echo "\n";
					break;					
					
					case 'JL_SCOREFOR':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, sprintf( "%s" , $showRow->sum_team1_result ) );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_SCOREAGAINST':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, sprintf( "%s", $showRow->sum_team2_result ) );
						echo '</td>';
						echo "\n";
					break;
					
					case 'JL_SCOREPCT':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							$scorepct=round(($showRow->goalPct()),2);
							printf( $format, $scorepct );
							
						echo '</td>';
						echo "\n";
					break;					
					
					case 'JL_RESULTS':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, sprintf( "%s" . ":" . "%s", $showRow->sum_team1_result, $showRow->sum_team2_result ) );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_DIFF':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, $showRow->diff_team_results );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_POINTS':
						echo '<td class="rankingrow_points"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, $showRow->getPoints() );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_NEGPOINTS':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, $showRow->neg_points );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_OLDNEGPOINTS':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, sprintf( "%s" . ":" . "%s", $showRow->getPoints(), $showRow->neg_points ) );
						echo '</td>';
						echo "\n";
					break;
					
					case 'JL_POINTS_RATIO':   
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';					
							$pointsratio=round(($showRow->pointsRatio()),2);
							printf( $format, $pointsratio );
						echo '</td>';
						echo "\n";
					break;	
					
					case 'JL_BONUS':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, $showRow->bonus_points );
						echo '</td>';
						echo "\n";
					break;

					case 'JL_START':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							if ((($team->team->start_points)!=0) AND (( $config['show_manipulations'])==1))
							{
								$toolTipTitle	= JText::_('JL_START');
								$toolTipText	= $team->team->reason;
								?>
								<span class="hasTip" title="<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>">
								<?php
								printf( $format, $team->team->start_points );
								?>
								</span>
							
							<?php
							}
							else
							{
								printf( $format, $team->team->start_points );
							}
						echo '</td>';
						echo "\n";
					break;

					case 'JL_QUOT':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';							
							$pointsquot = number_format( $showRow->pointsQuot(), 3, ",", "." );
							printf($format, $pointsquot);
						echo '</td>';
						echo "\n";
					break;
					
					case 'JL_TADMIN':
						echo '<td class="rankingrow"';
							if ( $color != '' )
							{
								echo 'style="background-color:' . $color . '"';
							}
							echo '>';
							printf( $format, $team->team->username );
						echo '</td>';
						echo "\n";
					break;					
				}
			}
		}

		echo '</tr>';
		echo "\n";
		$k = 1 - $k;
		$counter++;
		$temprank = $team->rank;
	}