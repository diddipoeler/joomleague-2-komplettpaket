<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model');

require_once( JPATH_COMPONENT.DS . 'helpers' . DS . 'ranking.php' );
require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelNextMatch extends JoomleagueModelProject
{
	var $project = null;
	var $matchid = 0;
	var $projectteamid = 0;
	var $projectid = 0;
	var $divisionid = 0;
	var $showpics = 0;
	var $ranking = null;
	var $teams = null;

	/**
	 * caching match data
	 * @var object
	 */
	var $_match = null;

	function __construct( )
	{
		parent::__construct( );
		$this->projectid = JRequest::getInt( "p", 0 );
		$this->matchid = JRequest::getInt( "mid", 0 );
		$this->showpics = JRequest::getInt( "pics", 0 );
		$this->projectteamid = JRequest::getInt( "ptid", 0 );
		$this->getSpecifiedMatch($this->projectid, $this->projectteamid, $this->matchid);
	}

	function getSpecifiedMatch($projectId, $projectTeamId, $matchId)
	{
		if (!$this->_match)
		{
			$config = $this->getTemplateConfig($this->getName());
			$expiry_time = $config ? $config['expiry_time'] : 0;
			$query =  ' SELECT m.*, DATE_FORMAT(m.time_present, "%H:%i") time_present, t1.project_id '
			  . '              , r.roundcode '
				. ' FROM #__joomleague_match AS m '
			  . ' INNER JOIN #__joomleague_round AS r ON r.id = m.round_id '
				. ' INNER JOIN #__joomleague_project_team AS t1 ON t1.id = m.projectteam1_id '
				. ' INNER JOIN #__joomleague_project_team AS t2 ON t2.id = m.projectteam2_id '
				. ' INNER JOIN #__joomleague_project AS p ON p.id = t1.project_id '
				. ' WHERE DATE_ADD(m.match_date, INTERVAL '.$this->_db->Quote($expiry_time).' MINUTE)'
// TODO: for now the timezone implementation does not work for quite some users, so it is temporarily disabled.
// Because the old serveroffset field is not available anymore in the database schema, this means that the times
// are not correctly translated to some timezone; the times as present in the database are just taken. 
//				. '    >= CONVERT_TZ(UTC_TIMESTAMP(), '.$this->_db->Quote('UTC').', p.timezone)'
				. '    >= NOW()'
			. ' AND m.cancel=0';
			if ($matchId)
			{
				$query .= ' AND m.id = ' . $this->_db->Quote($matchId);
			}
			else
			{
				$query .= ' AND (team1_result is null  OR  team2_result is null) ';
				if ($projectTeamId)
				{
					$query .= ' AND '
						. ' ( '
						. '       m.projectteam1_id = '. $this->_db->Quote($projectTeamId).' OR '
						. '       m.projectteam2_id = '. $this->_db->Quote($projectTeamId)
						. ' ) ';
				}
				else
				{
					$query .= ' AND (m.projectteam1_id > 0  OR  m.projectteam2_id > 0) ';
				}
			}
			if ($projectId)
			{
				$query .= ' AND t1.project_id = '.$this->_db->Quote($projectId);
			}
			$query .= ' ORDER BY m.match_date';
			$this->_db->setQuery($query, 0, 1);
			$this->_match = $this->_db->loadObject();
			if($this->_match)
			{
				$this->projectid = $this->_match->project_id;
				$this->matchid = $this->_match->id;
			}
		}
		return $this->_match;
	}

	/**
	 * get match info
	 * @return object
	 */
	function getMatch()
	{
		if (empty($this->_match))
		{
			$query = ' SELECT m.*, DATE_FORMAT(m.time_present, "%H:%i") time_present, t1.project_id, r.roundcode '
			. ' FROM #__joomleague_match AS m '
			. ' INNER JOIN #__joomleague_project_team AS t1 ON t1.id = m.projectteam1_id '
		  . ' INNER JOIN #__joomleague_round AS r ON r.id = m.round_id '
			. ' WHERE m.id = '. $this->_db->Quote($this->matchid);
			$this->_db->setQuery($query, 0, 1);
			$this->_match = $this->_db->loadObject();
		}
		return $this->_match;
	}
	
	function getShowPics( )
	{
		return $this->showpics;
	}

	/**
	 * get match teams details
	 * @return array
	 */
	function getMatchTeams()
	{
		if (empty($this->teams))
		{
			$this->teams = array();

			$match = $this->getMatch();
			if ( is_null ( $match ) )
			{
				return null;
			}

			$team1 = $this->getTeaminfo($match->projectteam1_id);
			$team2 = $this->getTeaminfo($match->projectteam2_id);
			$this->teams[] = $team1;
			$this->teams[] = $team2;
			// Set the division id, so that the home and away ranks are 
			// determined for a division, if the team is part of a division
			$this->divisionid = $team1->division_id;
		}
		return $this->teams;
	}

	function getMatchCommentary()
    {
        $query = "SELECT *  
    FROM #__joomleague_match_commentary
    WHERE match_id = ".(int)$this->matchid." 
    ORDER BY event_time DESC";
    $this->_db->setQuery($query);
		return $this->_db->loadObjectList();
    }
    
    function getReferees()
	{
		$match = &$this->getMatch();
		$query = ' SELECT p.firstname, p.nickname, p.lastname, p.country, pos.name AS position_name, p.id as person_id '
		. ' FROM #__joomleague_match_referee AS mr '
		. ' LEFT JOIN #__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id '
		. ' INNER JOIN #__joomleague_person AS p ON p.id = pref.person_id '
		. ' INNER JOIN #__joomleague_project_position ppos ON ppos.id = mr.project_position_id'
		. ' INNER JOIN #__joomleague_position AS pos ON pos.id = ppos.position_id '
		. ' WHERE mr.match_id = '. $this->_db->Quote($match->id)
		. '  AND p.published = 1 '
		;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function _getRanking()
	{
		if (empty($this->ranking))
		{
			$project = $this->getProject();
			$division = $this->divisionid;
			$ranking = JLGRanking::getInstance($project);
			$ranking->setProjectId( $project->id );
			$this->ranking = $ranking->getRanking(0, $this->getCurrentRound(), $division);
		}
		return $this->ranking;
	}

	function getHomeRanked()
	{
		$match = &$this->getMatch();
		$rankings = &$this->_getRanking();
		foreach ($rankings as $ptid => $team)
		{
			if ($ptid == $match->projectteam1_id) {
				return $team;
			}
		}
		return false;
	}

	function getAwayRanked()
	{
		$match = &$this->getMatch();
		$rankings = &$this->_getRanking();

		foreach ($rankings as $ptid => $team)
		{
			if ($ptid == $match->projectteam2_id) {
				return $team;
			}
		}
		return false;
	}

	function _getHighestHomeWin($teamid)
	{
		$match = $this->getMatch();

		$query = ' SELECT t1.name AS hometeam, '
		. ' t2.name AS awayteam, '
		. ' team1_result AS homegoals, '
		. ' team2_result AS awaygoals, '
		. ' pt1.project_id AS pid, '
		. ' m.id AS mid '
		. ' FROM #__joomleague_match as m '
		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id '
		. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id '
		. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
		. ' WHERE pt1.project_id = ' . $this->_db->Quote($match->project_id)
		. ' AND m.published = 1 '
		. ' AND m.alt_decision = 0 '
		. ' AND t1.id = '. $this->_db->Quote($teamid)
		. ' AND (team1_result - team2_result > 0) '
		. ' ORDER BY (team1_result - team2_result) DESC '
		;
		$this->_db->setQuery($query, 0, 1);
		return $this->_db->loadObject();
	}

	function getHomeHighestHomeWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeWin( $teams[0]->team_id );
	}

	function getAwayHighestHomeWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeWin( $teams[1]->team_id );
	}

	function _getHighestHomeDef( $teamid )
	{
		$match = $this->getMatch();

		$query = ' SELECT t1.name AS hometeam, '
		. ' t2.name AS awayteam, '
		. ' team1_result AS homegoals, '
		. ' team2_result AS awaygoals, '
		. ' pt1.project_id AS pid, '
		. ' m.id AS mid '
		. ' FROM #__joomleague_match as m '
		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id '
		. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id '
		. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
		. ' WHERE pt1.project_id = ' . $this->_db->Quote($match->project_id)
		. ' AND m.published = 1 '
		. ' AND m.alt_decision = 0 '
		. ' AND t1.id = '. $this->_db->Quote($teamid)
		. ' AND (team1_result - team2_result < 0) '
		. ' ORDER BY (team1_result - team2_result) ASC '
		;
		$this->_db->setQuery($query, 0, 1);
		return $this->_db->loadObject();
	}

	function getHomeHighestHomeDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeDef( $teams[0]->team_id );
	}

	function getAwayHighestHomeDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeDef( $teams[1]->team_id );
	}

	function _getHighestAwayWin( $teamid )
	{
		$match = $this->getMatch();

		$query = ' SELECT t1.name AS hometeam, '
		. ' t2.name AS awayteam, '
		. ' team1_result AS homegoals, '
		. ' team2_result AS awaygoals, '
		. ' pt1.project_id AS pid, '
		. ' m.id AS mid '
		. ' FROM #__joomleague_match as m '
		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id '
		. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id '
		. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
		. ' WHERE pt1.project_id = ' . $this->_db->Quote($match->project_id)
		. ' AND m.published = 1 '
		. ' AND m.alt_decision = 0 '
		. ' AND t2.id = '. $this->_db->Quote($teamid)
		. ' AND (team2_result - team1_result > 0) '
		. ' ORDER BY (team2_result - team1_result) DESC '
		;
		$this->_db->setQuery($query, 0, 1);
		return $this->_db->loadObject();
	}

	function getHomeHighestAwayWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayWin( $teams[0]->team_id );
	}

	function getAwayHighestAwayWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayWin( $teams[1]->team_id );
	}

	function _getHighestAwayDef( $teamid )
	{
		$match = $this->getMatch();

		$query = ' SELECT t1.name AS hometeam, '
		. ' t2.name AS awayteam, '
		. ' team1_result AS homegoals, '
		. ' team2_result AS awaygoals, '
		. ' pt1.project_id AS pid, '
		. ' m.id AS mid '
		. ' FROM #__joomleague_match as m '
		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id '
		. ' INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id '
		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id '
		. ' INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id '
		. ' WHERE pt1.project_id = ' . $this->_db->Quote($match->project_id)
		. ' AND m.published = 1 '
		. ' AND m.alt_decision = 0 '
		. ' AND t2.id = '. $this->_db->Quote($teamid)
		. ' AND (team1_result - team2_result > 0) '
		. ' ORDER BY (team2_result - team1_result) ASC '
		;
		$this->_db->setQuery($query, 0, 1);
		return $this->_db->loadObject();
	}


	function getHomeHighestAwayDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayDef( $teams[0]->team_id );
	}

	function getAwayHighestAwayDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayDef( $teams[1]->team_id );
	}

	/**
	 * get all games in all projects for these 2 teams
	 * @return array
	 */
	function getGames( )
	{
		$result = array();
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}

		$query = ' SELECT m.*, DATE_FORMAT(m.time_present, "%H:%i") time_present, pt1.project_id, '
		. ' p.name AS project_name, '
		. ' r.id AS roundid, '
		. ' r.roundcode AS roundcode, '
		. ' r.name AS mname, '
		. ' p.id AS prid '
		. ' FROM #__joomleague_match as m '
		. ' INNER JOIN #__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id '
		. ' INNER JOIN #__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id '
		. ' INNER JOIN #__joomleague_project AS p ON p.id = pt1.project_id '
		. ' INNER JOIN #__joomleague_round r ON m.round_id=r.id '
		. ' WHERE ((pt1.team_id = '. $teams[0]->team_id .' AND pt2.team_id = '.$teams[1]->team_id .') '
		. '        OR (pt1.team_id = '.$teams[1]->team_id .' AND pt2.team_id = '.$teams[0]->team_id .')) '
		. ' AND p.published = 1 '
		. ' AND m.published = 1 '
		. ' AND m.team1_result IS NOT NULL AND m.team2_result IS NOT NULL';

		$query .= " GROUP BY m.id ORDER BY p.ordering, m.match_date ASC";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		return $result;
	}

	function getTeamsFromMatches( & $games )
	{
		$teams = Array();

		if ( !count( $games ) )
		{
			return $teams;
		}

		foreach ( $games as $m )
		{
			$teamsId[] = $m->projectteam1_id;
			$teamsId[] = $m->projectteam2_id;
		}
		$listTeamId = implode( ",", array_unique( $teamsId ) );

		$query = "SELECT t.id, t.name, pt.id as ptid
                 FROM #__joomleague_project_team AS pt
                 INNER JOIN #__joomleague_team AS t ON t.id = pt.team_id
                 WHERE pt.id IN (".$listTeamId.")";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		foreach ( $result as $r )
		{
			$teams[$r->ptid] = $r;
		}

		return $teams;
	}

	function getPlayground( $pgid )
	{
		$query = 'SELECT * FROM #__joomleague_playground
					WHERE id = '. $this->_db->Quote($pgid);
		$this->_db->setQuery($query, 0, 1);
		return $this->_db->loadObject();
	}

	function getMatchText($match_id)
	{
		$query = "SELECT m.*, t1.name t1name,  t2.name t2name
					FROM #__joomleague_match AS m
					INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id = pt1.id
					INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id = pt2.id
					INNER JOIN #__joomleague_team AS t1 ON pt1.team_id=t1.id
					INNER JOIN #__joomleague_team AS t2 ON pt2.team_id=t2.id
					WHERE m.id = " . $match_id . "
					AND m.published = 1
					ORDER BY m.match_date, t1.short_name"
					;
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
	/**
	 * Calculates chances between 2 team
	 * Code is from LMO, all credits go to the LMO developers
	 * @return array
	 */
	function getChances()
	{
		$home=$this->getHomeRanked();
		$away=$this->getAwayRanked();

		if ((($home->cnt_matches)>0) && (($away->cnt_matches)>0))
		{
			$won1=$home->cnt_won;		
			$won2=$away->cnt_won;
			$loss1=$home->cnt_lost;
			$loss2=$away->cnt_lost;
			$matches1=$home->cnt_matches;
			$matches2=$away->cnt_matches;
			$goalsfor1=$home->sum_team1_result;
			$goalsfor2=$away->sum_team1_result;
			$goalsagainst1=$home->sum_team2_result;
			$goalsagainst2=$away->sum_team2_result;
		
			$ax=(100*$won1/$matches1)+(100*$loss2/$matches2);
			$bx=(100*$won2/$matches2)+(100*$loss1/$matches1);
			$cx=($goalsfor1/$matches1)+($goalsagainst2/$matches2);
			$dx=($goalsfor2/$matches2)+($goalsagainst1/$matches1);
			$ex=$ax+$bx;
			$fx=$cx+$dx;
		
			if (isset($ex) && ($ex>0) && isset($fx) &&($fx>0)) 
			{	 
				$ax=round(10000*$ax/$ex);
				$bx=round(10000*$bx/$ex);
				$cx=round(10000*$cx/$fx);
				$dx=round(10000*$dx/$fx);
		
				$chg1=number_format((($ax+$cx)/200),2,",",".");
				$chg2=number_format((($bx+$dx)/200),2,",",".");
				$result=array($chg1,$chg2);

				return $result;
			}
		}	
	}
		
	/**
	* get Previous X games of each team
	*
	* @return array
	*/
	function getPreviousX()
	{
		if (!$this->_match) {
			return false;
		}
		$games = array();
		$games[$this->_match->projectteam1_id] = $this->_getTeamPreviousX($this->_match->roundcode, $this->_match->projectteam1_id);
		$games[$this->_match->projectteam2_id] = $this->_getTeamPreviousX($this->_match->roundcode, $this->_match->projectteam2_id);
		
		return $games;
	}
	
	/**
	* returns last X games
	*
	* @param int $current_roundcode
	* @param int $ptid project team id
	* @return array
	*/
	function _getTeamPreviousX($current_roundcode, $ptid)
	{		
		$config = $this->getTemplateConfig('nextmatch');
		$nblast = $config['nb_previous'];
		$query = ' SELECT m.*, r.project_id, r.id AS roundid, r.roundcode '
		       . ' FROM #__joomleague_match AS m '
		       . ' INNER JOIN #__joomleague_round AS r ON r.id = m.round_id '
		       . ' WHERE (m.projectteam1_id = ' . $ptid
		       . '       OR m.projectteam2_id = ' . $ptid.')'
		       . '   AND r.roundcode < '.$current_roundcode
		       . '   AND m.published = 1 '
		       . ' ORDER BY r.roundcode DESC '
		       .  ' LIMIT 0, '.$nblast
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) {
			$res = array_reverse($res);
		}
		return $res;
	}
}
?>