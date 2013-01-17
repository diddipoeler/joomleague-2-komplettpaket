<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model');

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelTeamStats extends JoomleagueModelProject
{
	var $projectid = 0;
	var $teamid = 0;
	var $highest_home = null;
	var $highest_away = null;
	var $highestdef_home = null;
	var $highestdef_away = null;
	var $highestdraw_home = null;
	var $highestdraw_away = null;
	var $totalshome = null;
	var $totalsaway = null;
	var $matchdaytotals = null;
	var $totalrounds = null;
	var $attendanceranking = null;

	function __construct( )
	{
		parent::__construct();

		$this->projectid = JRequest::getInt( "p", 0 );
		$this->teamid = JRequest::getInt( "tid", 0 );
		//preload the team;
		$this->getTeam();
	}

	function getTeam( )
	{
		# it should be checked if any tid is given in the params of the url
		# if ( is_null( $this->team ) )
		if ( !isset( $this->team ) )
		{
			if ( $this->teamid > 0 )
			{
				$this->team = & $this->getTable( 'Team', 'Table' );
				$this->team->load( $this->teamid );
			}
		}
		return $this->team;
	}

	function getHighestHome( )
	{
		if ( is_null( $this->highest_home ) )
		{
			$query = ' SELECT matches.id AS matchid, t1.name AS hometeam, '
			       . ' t2.name AS guestteam, '
			       . ' team1_result AS homegoals, '
			       . ' team2_result AS guestgoals, '
			       . ' t1.id AS team1_id, '
			       . ' t2.id AS team2_id '
			       . ' FROM #__joomleague_match as matches '
			       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
			       . ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
			       . ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
			       . ' WHERE pt1.project_id = '.$this->projectid
			       . ' AND published=1 '
			       . ' AND alt_decision=0 '
			       . ' AND t1.id =  '. $this->team->id
			       . ' AND team1_result > team2_result '
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       . ' ORDER BY (team1_result-team2_result) DESC '
           ;

            $this->_db->setQuery($query, 0, 1);
            $this->highest_home = $this->_db->loadObject( );
        }
        return $this->highest_home;
    }

    function getHighestAway( )
    {
    	if ( is_null( $this->highest_away ) )
    	{
				$query = ' SELECT matches.id AS matchid, t1.name AS hometeam, '
			       . ' t2.name AS guestteam, '
			       . ' team1_result AS homegoals, '
			       . ' team2_result AS guestgoals, '
			       . ' t1.id AS team1_id, '
			       . ' t2.id AS team2_id '
			       . ' FROM #__joomleague_match as matches '
			       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
			       . ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
			       . ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
			       . ' WHERE pt1.project_id = '.$this->projectid
			       . ' AND published=1 '
			       . ' AND alt_decision=0 '
			       . ' AND t2.id =  '. $this->team->id
			       . ' AND team2_result > team1_result '
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       . ' ORDER BY (team2_result-team1_result) DESC '
           ;

    		$this->_db->setQuery($query, 0, 1);
    		$this->highest_away = $this->_db->loadObject( );
    	}
    	return $this->highest_away;
    }

    function getHighestDefHome( )
    {
    	if ( is_null( $this->highestdef_home ) )
    	{
				$query = ' SELECT matches.id AS matchid, t1.name AS hometeam, '
			       . ' t2.name AS guestteam, '
			       . ' team1_result AS homegoals, '
			       . ' team2_result AS guestgoals, '
			       . ' t1.id AS team1_id, '
			       . ' t2.id AS team2_id '
			       . ' FROM #__joomleague_match as matches '
			       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
			       . ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
			       . ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
			       . ' WHERE pt1.project_id = '.$this->projectid
			       . ' AND published=1 '
			       . ' AND alt_decision=0 '
			       . ' AND t1.id =  '. $this->team->id
			       . ' AND team2_result > team1_result '
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       . ' ORDER BY (team2_result-team1_result)  DESC '
           ;

    		$this->_db->setQuery($query, 0, 1);
    		$this->highestdef_home = $this->_db->loadObject( );
    	}
    	return $this->highestdef_home;
    }

    function getHighestDefAway( )
    {
    	if ( is_null( $this->highestdef_away ) )
    	{
				$query = ' SELECT matches.id AS matchid, t1.name AS hometeam, '
			       . ' t2.name AS guestteam, '
			       . ' team1_result AS homegoals, '
			       . ' team2_result AS guestgoals, '
			       . ' t1.id AS team1_id, '
			       . ' t2.id AS team2_id '
			       . ' FROM #__joomleague_match as matches '
			       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
			       . ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
			       . ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
			       . ' WHERE pt1.project_id = '.$this->projectid
			       . ' AND published=1 '
			       . ' AND alt_decision=0 '
			       . ' AND t2.id =  '. $this->team->id
			       . ' AND team1_result > team2_result '
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       . ' ORDER BY (team1_result-team2_result)  DESC '
           ;
    		$this->_db->setQuery($query, 0, 1);
    		$this->highestdef_away = $this->_db->loadObject( );
    	}
    	return $this->highestdef_away;
    }

    function getHighestDrawAway( )
    {
    	if ( is_null( $this->highestdraw_away ) )
    	{
    		$query = ' SELECT t1.name AS hometeam, '
    		. ' t2.name AS guestteam, '
    		. ' team1_result AS homegoals, '
    		. ' team2_result AS guestgoals '
    		. ' FROM #__joomleague_match as matches '
    		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
    		. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
    		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
    		. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
    		. ' WHERE pt1.project_id = '.$this->projectid
    		. ' AND published=1 '
    		. ' AND alt_decision=0 '
    		. ' AND t2.id =  '. $this->team->id
    		. ' AND team1_result = team2_result '
    		. ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
    		. ' ORDER BY team2_result DESC '
    		;
    		$this->_db->setQuery($query, 0, 1);
    		$this->highestdraw_away = $this->_db->loadObject( );
    	}
    	return $this->highestdraw_away;
    }
    
    function getHighestDrawHome( )
    {
    	if ( is_null( $this->highestdraw_home ) )
    	{
    		$query = ' SELECT t1.name AS hometeam, '
    		. ' t2.name AS guestteam, '
    		. ' team1_result AS homegoals, '
    		. ' team2_result AS guestgoals '
    		. ' FROM #__joomleague_match as matches '
    		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
    		. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
    		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
    		. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
    		. ' WHERE pt1.project_id = '.$this->projectid
    		. ' AND published=1 '
    		. ' AND alt_decision=0 '
    		. ' AND t1.id =  '. $this->team->id
    		. ' AND team2_result = team1_result '
    		. ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
    		. ' ORDER BY team1_result DESC '
    		;
    
    		$this->_db->setQuery($query, 0, 1);
    		$this->highestdraw_home = $this->_db->loadObject( );
    	}
    	return $this->highestdraw_home;
    }
    
    function getNoGoalsAgainst( )
    {
    	if ( (!isset( $this->nogoals_against )) || is_null( $this->nogoals_against ) )
    	{
    		$query = ' SELECT '
			       . ' COUNT( round_id ) AS totalzero, '
			       . ' SUM( t1.id = '.$this->team->id.' AND team2_result=0 ) AS homezero, '
			       . ' SUM( t2.id = '.$this->team->id.' AND team1_result=0 ) AS awayzero '
			       . ' FROM #__joomleague_match as matches '
			       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
			       . ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
			       . ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
			       . ' WHERE pt1.project_id = '.$this->projectid.' '
			       . ' AND published=1 '
			       . ' AND alt_decision=0 '
			       . ' AND ((t1.id = '.$this->team->id.' AND team2_result=0 ) '
			       . ' OR  (t2.id = '.$this->team->id.' AND team1_result=0 ))'
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       ;
    		$this->_db->setQuery($query);
    		$this->nogoals_against = $this->_db->loadObject( );
    	}
    	return $this->nogoals_against;
    }

    function getSeasonTotalsHome( )
    {
    	if ( is_null( $this->totalshome ) )
    	{
    		$query = ' SELECT '
			       . ' COUNT(matches.id) AS totalmatches, '
			       . ' COUNT(team1_result) AS playedmatches, '
			       . ' IFNULL(SUM(team1_result),0) AS goalsfor, '
			       . ' IFNULL(SUM(team2_result),0) AS goalsagainst, '
			       . ' IFNULL(SUM(team1_result + team2_result),0) AS totalgoals, '
			       . ' IFNULL(SUM(IF(team1_result=team2_result,1,0)),0) AS totaldraw, '
			       . ' IFNULL(SUM(IF(team1_result<team2_result,1,0)),0) AS totalloss, '
			       . ' IFNULL(SUM(IF(team1_result>team2_result,1,0)),0) AS totalwin, '
			       . ' COUNT(crowd) AS attendedmatches, '
			       . ' SUM(crowd) AS sumspectators '
			       . ' FROM #__joomleague_match AS matches'
			       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
			       . ' WHERE pt1.project_id = '.$this->projectid
			       . ' AND published=1 '
			       . ' AND pt1.team_id = '.$this->team->id
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       ;
    		$this->_db->setQuery($query, 0, 1);
    		$this->totalshome = $this->_db->loadObject();
    	}
    	return $this->totalshome;
    }

    function getSeasonTotalsAway( )
    {
    	if ( is_null( $this->totalsaway ) )
    	{
    		$query = ' SELECT '
			       . ' COUNT(matches.id) AS totalmatches, '
			       . ' COUNT(team1_result) AS playedmatches, '
			       . ' IFNULL(SUM(team2_result),0) AS goalsfor, '
			       . ' IFNULL(SUM(team1_result),0) AS goalsagainst, '
			       . ' IFNULL(SUM(team1_result + team2_result),0) AS totalgoals, '
			       . ' IFNULL(SUM(IF(team2_result=team1_result,1,0)),0) AS totaldraw, '
			       . ' IFNULL(SUM(IF(team2_result<team1_result,1,0)),0) AS totalloss, '
			       . ' IFNULL(SUM(IF(team2_result>team1_result,1,0)),0) AS totalwin, '
    				. ' COUNT(crowd) AS attendedmatches, '
			       . ' SUM(crowd) AS sumspectators '
			       . ' FROM #__joomleague_match AS matches'
			       . ' INNER JOIN #__joomleague_project_team pt ON pt.id = matches.projectteam2_id '
			       . ' WHERE pt.project_id = '.$this->projectid
			       . ' AND published=1 '
			       . ' AND pt.team_id = '.$this->team->id
				   . ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       ;
    		$this->_db->setQuery($query, 0, 1);
    		$this->totalsaway = $this->_db->loadObject();
    	}
    	return $this->totalsaway;
    }

    /**
     * get data for chart
     * @return  
     */
		function getChartData( )
		{
			$query = ' SELECT rounds.id, '
			       . ' SUM(CASE WHEN pt1.team_id ='.$this->teamid.' THEN matches.team1_result ELSE matches.team2_result END) AS goalsfor, '
			       . ' SUM(CASE WHEN pt1.team_id ='.$this->teamid.' THEN matches.team2_result ELSE matches.team1_result END) AS goalsagainst, '
			       . ' rounds.roundcode '
			       . ' FROM #__joomleague_round AS rounds '
			       . ' INNER JOIN #__joomleague_match AS matches ON rounds.id = matches.round_id '
			       . ' INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id = matches.projectteam2_id '
			       . ' WHERE rounds.project_id = '.$this->projectid
			       . '   AND ((pt1.team_id ='.$this->teamid.' ) '
			       . '     OR (pt2.team_id ='.$this->teamid.' )) '
				   . '   AND (matches.cancel IS NULL OR matches.cancel = 0)'
//			       . '   AND team1_result IS NOT NULL '
			       . ' GROUP BY rounds.roundcode'
			       ;
    		$this->_db->setQuery( $query );
    		$this->matchdaytotals = $this->_db->loadObjectList();
    		return $this->matchdaytotals;
    }
    
    function getMatchDayTotals( )
    {
    	if ( is_null( $this->matchdaytotals ) )
    	{
    		$query = ' SELECT rounds.id, '
			       . ' COUNT(matches.round_id) AS totalmatchespd, '
			       . ' COUNT(matches.id) as playedmatchespd, '
			       . ' SUM(matches.team1_result) AS homegoalspd, '
			       . ' SUM(matches.team2_result) AS guestgoalspd, '
			       . ' rounds.roundcode '
			       . ' FROM #__joomleague_round AS rounds '
			       . ' INNER JOIN #__joomleague_match AS matches ON rounds.id = matches.round_id '
			       . ' INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id = matches.projectteam1_id '
			       . ' INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id = matches.projectteam2_id '
			       . ' WHERE rounds.project_id = '.$this->projectid
			       . '   AND ((pt1.team_id ='.$this->teamid.' ) '
			       . '     OR (pt2.team_id ='.$this->teamid.' )) '
			       . ' GROUP BY rounds.roundcode'
				   . '   AND (matches.cancel IS NULL OR matches.cancel = 0)'
			       ;
    		$this->_db->setQuery( $query );
    		$this->matchdaytotals = $this->_db->loadObjectList();
    	}
    	return $this->matchdaytotals;
    }

    function getTotalRounds( )
    {
        if ( is_null( $this->totalrounds ) )
        {
            $query= "SELECT COUNT(id)
                     FROM #__joomleague_round
                     WHERE project_id= ".$this->projectid;
            $this->_db->setQuery($query);
            $this->totalrounds = $this->_db->loadResult();
        }
        return $this->totalrounds;
    }

    /**
     * return games attendance
     * @return unknown_type
     */
    function _getAttendance( )
    {
    	if ( is_null( $this->attendanceranking ) )
    	{
				$query = ' SELECT matches.crowd '
				       . ' FROM #__joomleague_match AS matches '
				       . ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
				       . ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
				       . ' LEFT JOIN #__joomleague_playground AS playground ON pt1.standard_playground = playground.id '
				       . ' WHERE pt1.team_id = '.$this->teamid
				       . '   AND matches.crowd > 0 '
				       . '   AND matches.published=1 '
				       ;
    		$this->_db->setQuery( $query );
    		$this->attendanceranking = $this->_db->loadResultArray();
    	}
    	return $this->attendanceranking;
    }

	function getBestAttendance( )
	{
		$attendance = $this->_getAttendance();
		return (count($attendance)>0) ? max($attendance) : 0;
	}

	function getWorstAttendance( )
	{
		$attendance = $this->_getAttendance();
		return (count($attendance)>0) ? min($attendance) : 0;
	}

	function getTotalAttendance( )
	{
		$attendance = $this->_getAttendance();
		return (count($attendance)>0) ? array_sum($attendance) : 0;
	}
	
	function getAverageAttendance( )
	{
		$attendance = $this->_getAttendance();
		return (count($attendance)>0) ? round(array_sum($attendance)/count($attendance), 0) : 0;
	}

	function getChartURL( )
	{
		$url = JoomleagueHelperRoute::getTeamStatsChartDataRoute( $this->projectid, $this->teamid );
		$url = str_replace( '&', '%26', $url );
		return $url;
	}

	function getLogo( )
	{
		$database = JFactory::getDBO();
	    $query = "SELECT logo_big
				FROM #__joomleague_club clubs
				LEFT JOIN #__joomleague_team teams ON clubs.id = teams.club_id
				WHERE teams.id = ".$this->teamid;

    	$database->setQuery( $query );
    	$logo = JURI::root().$database->loadResult();

		return $logo;
	}

	function getResults()
	{
		$query = ' SELECT m.id, m.projectteam1_id, m.projectteam2_id, pt1.team_id AS team1_id, pt2.team_id AS team2_id, '
		       . ' m.team1_result, m.team2_result, '
		       . ' m.alt_decision, m.team1_result_decision, m.team2_result_decision '
		       . ' FROM #__joomleague_match AS m '
		       . ' INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id = m.projectteam1_id '
		       . ' INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id = m.projectteam2_id '
		       . ' WHERE m.published = 1 '
		       . '   AND pt1.project_id = '. $this->_db->Quote($this->projectid)
		       . '   AND (pt1.team_id = '. $this->_db->Quote($this->teamid) . ' OR pt2.team_id = '. $this->_db->Quote($this->teamid) . ')'
		       . '   AND (m.team1_result IS NOT NULL OR m.alt_decision > 0)'
			   . '   AND (m.cancel IS NULL OR m.cancel = 0)'
		       ;
		$this->_db->setQuery($query);
		$matches = $this->_db->loadObjectList();
		
		$results = array(	'win' => array(), 'tie' => array(), 'loss' => array(), 'forfeit' => array(),
							'home_wins' => 0, 'home_draws' => 0, 'home_losses' => 0, 
							'away_wins' => 0, 'away_draws' => 0, 'away_losses' => 0,);
		foreach ($matches as $match)
		{
			if (!$match->alt_decision)
			{
				if ($match->team1_id == $this->teamid)
				{
					// We are the home team
					if ($match->team1_result > $match->team2_result)
					{
						$results['win'][] = $match;
						$results['home_wins']++;
					}
					else if ($match->team1_result < $match->team2_result)
					{
						$results['loss'][] = $match;
						$results['home_losses']++;
					}
					else
					{
						$results['tie'][] = $match;
						$results['home_draws']++;
					}
				}
				else
				{
					// We are the away team
					if ($match->team1_result > $match->team2_result)
					{
						$results['loss'][] = $match;
						$results['away_losses']++;
					}
					else if ($match->team1_result < $match->team2_result)
					{
						$results['win'][] = $match;
						$results['away_wins']++;
					}
					else
					{
						$results['tie'][] = $match;
						$results['away_draws']++;
					}
				}
			}
			else
			{
				if ($match->team1_id == $this->teamid)
				{
					// We are the home team
					if (empty($match->team1_result_decision)) {
						$results['forfeit'][] = $match;
					}
					else if (empty($match->team2_result_decision)) {
						$results['win'][] = $match;
					}
					else {
						if ($match->team1_result_decision > $match->team2_result_decision) {
							$results['win'][] = $match;
							$results['home_wins']++;
						}
						else if ($match->team1_result_decision < $match->team2_result_decision) {
							$results['loss'][] = $match;
							$results['home_losses']++;
						}
						else {
							$results['tie'][] = $match;
							$results['home_draws']++;
						}
					}
				}
				else
				{
					// We are the away team
					if (empty($match->team2_result_decision)) {
						$results['forfeit'][] = $match;
					}
					else if (empty($match->team1_result_decision)) {
						$results['win'][] = $match;
					}
					else {
						if ($match->team1_result_decision > $match->team2_result_decision) {
							$results['loss'][] = $match;
							$results['away_losses']++;
						}
						else if ($match->team1_result_decision < $match->team2_result_decision) {
							$results['win'][] = $match;
							$results['away_wins']++;
						}
						else {
							$results['tie'][] = $match;
							$results['away_draws']++;
						}
					}
				}
			}
		}
		
		return $results;
	}
	
	function getStats()
	{
		$stats = $this->getProjectStats();
		
		// those are per positions, group them so that we have team globlas stats
		
		$teamstats = array();
		foreach ($stats as $pos => $pos_stats)
		{
			foreach ($pos_stats as $k => $stat) 
			{
				if ($stat->getParam('show_in_teamstats', 1))
				{
					if (!isset($teamstats[$k])) 
					{
						$teamstats[$k] = $stat;
						$teamstats[$k]->value = $stat->getRosterTotalStats($this->teamid, $this->projectid);
					}
				}
			}
		}
		
		return $teamstats;
	}
}
?>