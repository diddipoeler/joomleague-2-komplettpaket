<?php
$current  = &$this->current;
$previous = &$this->previousRanking[$this->division];
$awayRank = ( isset( $this->awayRank[$this->division] ) ) ? $this->awayRank[$this->division] : NULL;
$homeRank = ( isset( $this->homeRank[$this->division] ) ) ? $this->homeRank[$this->division] : NULL;
$type     = &$this->type;
$config   = &$this->tableconfig;

/*
echo 'default_rankingrows <br>';
echo '<pre>';
print_r($current);
echo '</pre>';
*/

$config['ordered_columns'] = 'POINTS, PLAYED, WINS, TIES, LOSSES, RESULTS, DIFF, QUOT';
$config['show_logo_small_table'] = 1;


		$counter = 1;
		$k = 0;
		$j = 0;
		$temprank = 0;
		$tempoldrank = 0;
		$columns = explode( ",", $config['ordered_columns'] );
		$stars = 1;

/*
echo 'columns <br>';
echo '<pre>';
print_r($columns);
echo '</pre>';
*/
		
		foreach( $current as $ptid => $team )
		{
			$class = ($k == 0)? $config['style_class1'] : $config['style_class2'];

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
				echo '<td valign="top"';
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

				echo '<td valign="top"';
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
						}
						elseif ( $team->rank < $previous[$ptid]->rank )
						{
							$imgsrc .= "up.png";
							$alt	 = "up";
						}
						elseif ( $team->rank > $previous[$ptid]->rank )
						{
							$imgsrc .= "down.png";
							$alt	 = "down";
						}
						echo JHTML::image(	$imgsrc,
											$alt,
											array( "title" => $alt, "align" => "middle" ) );
					}
				echo '</td>';
				echo "\n";

				echo '<td valign="top" nowrap="nowrap" style="background-color:' . $color . '" align="right">';
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
						JoomleagueHTML::showClubIcon($team->team, $config['show_logo_small_table']);
					echo '</td>';
					echo "\n";
				}

				echo '<td valign="top" nowrap="nowrap"';
					if ( $color != '' )
					{
						echo ' style="background-color: ' . $color . '"';
					}
					echo ' align="left">';
					$config['highlight_fav'] = in_array( $team->team->id, array( $this->project->fav_team ) ) ? 1 : 0;
					echo JoomleagueHTML::formatTeamname( $team->team, 't' . $team->team->id, $config, $config['highlight_fav'] );
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
				  switch ( trim( strtoupper( $c ) ) )
				  {
					  case 'PLAYED':
						echo '<td valign="top"';
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

					case 'WINS':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->cnt_won );
						echo '</td>';
						echo "\n";
					break;

					case 'TIES':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->cnt_draw );
						echo '</td>';
						echo "\n";
					break;

					case 'LOSSES':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->cnt_lost );
						echo '</td>';
						echo "\n";
					break;

					case 'WINPCT':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';

							if ( ( $showRow->cnt_won+$showRow->cnt_lost+$showRow->cnt_draw) > 0 )
							{
								printf( $format, sprintf( "%.3F", ($showRow->cnt_won/($showRow->cnt_won+$showRow->cnt_lost+$showRow->cnt_draw ) ) *100));
							}
							else
							{
								printf( $format, sprintf( "%.3F", $showRow->cnt_won ) );
							}
						echo '</td>';
						echo "\n";
					break;

					case 'GB':
						//GB calculation, store wins and loss count of the team in first place
						if ( $team->rank == 1 )
						{
							$ref_won = $team->cnt_won;
							$ref_lost = $team->cnt_lost;
						}
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, round( ( ( $ref_won - $showRow->cnt_won ) - ( $ref_lost - $showRow->cnt_lost ) ) / 2, 1 ) );
						echo '</td>';
						echo "\n";
						
					break;

					case 'LEGS':
					if ( isset( $this->tableconfig['use_legs'] ) &&
						 $this->tableconfig['use_legs'] )
					{
						echo '<td valign="top" align="center" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, sprintf( "%s:%s", $team->sum_team1_legs, $team->sum_team2_legs ) );
						echo '</td>';
						echo "\n";

						//case 'LEGS_DIFF':   fixme
						if ( $this->tableconfig['show_legs_diff'] == 1 )
						{
							echo '<td valign="top" align="center" style="text-align:center;';
								if ( $color != '' )
								{
									echo 'background-color:' . $color;
								}
								echo '">';
								printf( $format, $team->diff_team_legs );
							echo '</td>';
							echo "\n";
						}
					}
					break;

					case 'RESULTS':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, sprintf( "%s" . ":" . "%s", $showRow->sum_team1_result, $showRow->sum_team2_result ) );
						echo '</td>';
						echo "\n";
					break;

					case 'DIFF':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->diff_team_results );
						echo '</td>';
						echo "\n";
					break;

					case 'POINTS':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->sum_points );
						echo '</td>';
						echo "\n";
					break;

					case 'NEGPOINTS':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->neg_points );
						echo '</td>';
						echo "\n";
					break;

					case 'BONUS':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $showRow->bonus_points );
						echo '</td>';
						echo "\n";
					break;

					case 'START':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';
							printf( $format, $team->team->start_points );
						echo '</td>';
						echo "\n";
					break;

					case 'QUOT':
						echo '<td valign="top" style="text-align:center;';
							if ( $color != '' )
							{
								echo 'background-color:' . $color;
							}
							echo '">';

							if ( $showRow->cnt_matches > 0 )
							{
								$Dummy = number_format( $showRow->sum_points / $showRow->cnt_matches, 3, ",", "." );
							}
							else
							{
								$Dummy = number_format( 0, 3, ",", "." );
							}
							printf($format, $Dummy);
							#printf( $format, sprintf( "%.3F", $showRow->cnt_won ) );
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