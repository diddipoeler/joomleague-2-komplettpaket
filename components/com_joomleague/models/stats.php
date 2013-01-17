<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model');

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );


class JoomleagueModelStats extends JoomleagueModelProject
{
	var $projectid = 0;
	var $divisionid = 0;
	var $highest_home = null;
	var $highest_away = null;
	var $totals = null;
	var $matchdaytotals = null;
	var $totalrounds = null;
	var $attendanceranking = null;

	function __construct( )
	{
		parent::__construct();

		$this->projectid = JRequest::getInt( "p", 0 );
		$this->divisionid = JRequest::getint( "division", 0 );
	}

	function getDivision()
	{
		$division = null;
		if ($this->divisionid != 0)
		{
			$division = parent::getDivision($this->divisionid);
		}
		return $division;
	}

	function getHighestHome( )
	{
		if ( is_null( $this->highest_home ) )
		{
			$query  = ' SELECT t1.name AS hometeam, '
				. ' t2.name AS guestteam, '
				. ' t1.id AS hometeam_id, '
				. ' pt1.id AS project_hometeam_id, '
				. ' team1_result AS homegoals, '
				. ' team2_result AS guestgoals, '
				. ' t2.id AS awayteam_id, '
				. ' pt2.id AS project_awayteam_id '
				. ' FROM #__joomleague_match as matches '
				. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
				. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
				. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
				. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
				. ' WHERE pt1.project_id = '.$this->projectid
			;
			if ($this->divisionid != 0)
			{
				$query .= ' AND pt1.division_id = '.$this->divisionid;
			}
			$query .= ' AND published=1 '
				. ' AND alt_decision=0 '
				. ' AND team1_result > team2_result '
				. ' AND (matches.cancel IS NULL OR matches.cancel = 0)'	
				. ' ORDER BY (team1_result-team2_result) DESC '
			;

			$this->_db->setQuery($query, 0, 1);
			//echo($this->_db->getQuery());
			$this->highest_home = $this->_db->loadObject();
		}
		return $this->highest_home;
	}

	function getHighestAway( )
	{
		if ( is_null( $this->highest_away ) )
		{
			$query  = ' SELECT t1.name AS hometeam, '
				. ' t1.id AS hometeam_id, '
				. ' pt1.id AS project_hometeam_id, '
				. ' t2.name AS guestteam, '
				. ' pt2.id AS project_awayteam_id, '
				. ' t2.id AS awayteam_id, '
				. ' team1_result AS homegoals, '
				. ' team2_result AS guestgoals '
				. ' FROM #__joomleague_match as matches '
				. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
				. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
				. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = matches.projectteam2_id '
				. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
				. ' WHERE pt1.project_id = '.$this->projectid
			;
			if ($this->divisionid != 0)
			{
				$query .= ' AND pt1.division_id = '.$this->divisionid;
			}
			$query .= ' AND published=1 '
				. ' AND alt_decision=0 '
				. ' AND team2_result > team1_result '
				. ' AND (matches.cancel IS NULL OR matches.cancel = 0)'	
				. ' ORDER BY (team2_result-team1_result) DESC '
			;

			$this->_db->setQuery($query, 0, 1);
			$this->highest_away = $this->_db->loadObject();
		}
		return $this->highest_away;
	}

	function getSeasonTotals( )
	{
		if ( is_null( $this->totals ) )
		{
			$query  = ' SELECT '
				. ' COUNT(matches.id) AS totalmatches, '
				. ' COUNT(team1_result) AS playedmatches, '
				. ' SUM(team1_result) AS homegoals, '
				. ' SUM(team2_result) AS guestgoals, '
				. ' SUM(team1_result + team2_result) AS sumgoals, '
				. ' (SELECT COUNT(crowd) '
				. '		 FROM #__joomleague_match AS sub1 '
				. '		 INNER JOIN #__joomleague_project_team sub2 ON sub2.id = sub1.projectteam1_id '
				. '		 WHERE sub1.crowd > 0 '
				. ' 		AND sub1.published = 1 '
				. ' 		AND (sub1.cancel IS NULL OR sub1.cancel = 0) '
				. ' 		AND sub2.project_id = '.$this->projectid.') AS attendedmatches, '
				. ' SUM(crowd) AS sumspectators '
				. ' FROM #__joomleague_match AS matches'
				. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
				. ' WHERE pt1.project_id = '.$this->projectid
			;
			if ($this->divisionid != 0)
			{
				$query .= ' AND pt1.division_id = '.$this->divisionid;
			}
			$query .= ' AND published=1 '
				. ' AND (matches.cancel IS NULL OR matches.cancel = 0)'	
			;
			$this->_db->setQuery($query, 0, 1);
			$this->totals = $this->_db->loadObject();
		}
		return $this->totals;
	}

	function getChartData( )
	{
		if ( is_null( $this->matchdaytotals ) )
		{
			$query  = ' SELECT rounds.id,'
				. ' COUNT(matches.id) AS totalmatchespd,'
				. ' COUNT(matches.team1_result) as playedmatchespd,'
				. ' SUM(matches.team1_result) AS homegoalspd,'
				. ' SUM(matches.team2_result) AS guestgoalspd,'
				. ' rounds.roundcode'
				. ' FROM #__joomleague_round AS rounds'
				. ' LEFT JOIN #__joomleague_match AS matches ON rounds.id = matches.round_id'
			;
			if ($this->divisionid != 0)
			{
				$query .= ' INNER JOIN #__joomleague_division AS division ON division.project_id=rounds.project_id'
					. ' WHERE rounds.project_id = '.$this->projectid
					. ' AND division.id = '.$this->divisionid;
			}
			else
			{
				$query .= ' WHERE rounds.project_id = '.$this->projectid;
 			}
			$query .= ' AND (matches.cancel IS NULL OR matches.cancel = 0)'
				. ' GROUP BY rounds.roundcode'
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
			$query  = ' SELECT COUNT(id)'
				. ' FROM #__joomleague_round'
				. ' WHERE project_id = '.$this->projectid
			;
			$this->_db->setQuery($query);
			$this->totalrounds = $this->_db->loadResult();
		}
		return $this->totalrounds;
	}

	function getAttendanceRanking( )
	{
		if ( is_null( $this->attendanceranking ) )
		{
			$query  = ' SELECT '
				. ' SUM(matches.crowd) AS sumspectatorspt, '
				. ' AVG(matches.crowd) AS avgspectatorspt, '
				. ' t1.name AS team, '
				. ' t1.id AS teamid, '
				. ' playground.max_visitors AS capacity '
				. ' FROM #__joomleague_match AS matches '
				. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = matches.projectteam1_id '
				. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
				. ' LEFT JOIN #__joomleague_playground AS playground ON pt1.standard_playground = playground.id '
				. ' WHERE pt1.project_id = '.$this->projectid
			;
			if ($this->divisionid != 0)
			{
				$query .= ' AND pt1.division_id = '.$this->divisionid;
			}
			$query .= ' AND matches.published=1 '
				. ' AND matches.crowd > 0 '
				. ' GROUP BY matches.projectteam1_id '
				. ' ORDER BY avgspectatorspt DESC'
			;

			$this->_db->setQuery($query);
			$this->attendanceranking = $this->_db->loadObjectList();
		}
		return $this->attendanceranking;
	}

	function getBestAvg( )
	{
		$attendanceranking = $this->getAttendanceRanking();
		return (count($attendanceranking)>0) ? round( $attendanceranking[0]->avgspectatorspt ) : 0;
	}

	function getBestAvgTeam( )
	{
		$attendanceranking = $this->getAttendanceRanking();
		return (count($attendanceranking)>0) ? $attendanceranking[0]->team : 0;
	}

	function getWorstAvg( )
	{
		$attendanceranking = $this->getAttendanceRanking();
		$worstavg = 0;
		if ( count( $attendanceranking ) )
		{
			$n = count( $attendanceranking );
			$worstavg = round( $attendanceranking[$n-1]->avgspectatorspt );
		}
		return $worstavg;
	}

	function getWorstAvgTeam( )
	{
		$attendanceranking = $this->getAttendanceRanking();
		$worstavgteam = 0;
		if ( count( $attendanceranking ) )
		{
			$n = count( $attendanceranking );
			$worstavgteam = $attendanceranking[$n-1]->team;
		}
		return $worstavgteam;
	}

	function getChartURL( )
	{
		$url = JoomleagueHelperRoute::getStatsChartDataRoute( $this->projectid, $this->divisionid );
		$url = str_replace( '&', '%26', $url );
		return $url;
	}
	
	//comparisations in stats view	
	function teamNameCmp2( &$a, &$b){
	  return strcasecmp ($a->team, $b->team);
	}

	function totalattendCmp( &$a, &$b){
	  $res = ($a->sumspectatorspt - $b->sumspectatorspt);

	  return $res;
	}

	function avgattendCmp( &$a, &$b){
	  $res = ($a->avgspectatorspt - $b->avgspectatorspt);
	  return $res;
	}

	function capacityCmp( &$a, &$b){
	  $res = ($a->capacity - $b->capacity);
	  return $res;
	}

	function utilisationCmp( &$a, &$b){
	  $res = (($a->capacity?($a->avgspectatorspt / $a->capacity):0) - ($b->capacity>0?($b->avgspectatorspt / $b->capacity):0));
	  return $res;
	}
}

?>