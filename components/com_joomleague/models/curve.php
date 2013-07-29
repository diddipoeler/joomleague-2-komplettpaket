<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model');

require_once (JLG_PATH_ADMIN .DS.'models'.DS.'rounds.php');
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'ranking.php');
require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelCurve extends JoomleagueModelProject
{
	var $project = null;
	var $projectid = 0;
	var $teamid1 = 0;
	var $team1 = array();
	var $teamid2 = 0;
	var $team2 = array();
	var $allteams = null;
	var $divisions = null;
	var $favteams = null;
	var $divlevel = null;
	var $height = 180;
	var $selectoptions = array();
	var $teamlist2options = array();

	// Chart Data
	var $division = 0;
	var $round = 0;
	var $roundsName = array();
	var $ranking1 = array();
	var $ranking2 = array();
	var $ranking = array(); // cache for ranking function return data
	var $teamcount = array();
	
	function __construct( )
	{
		parent::__construct( );
		$this->projectid = JRequest::getInt('p', 0);
		$this->division  = JRequest::getInt('division', 0);
		$this->teamid1   = JRequest::getInt('tid1', 0);
		$this->teamid2   = JRequest::getInt('tid2', 0);
		$this->both      = JRequest::getInt('both', 0);
		$this->determineTeam1And2();
	}

	function determineTeam1And2()
	{
		// Use favorite team(s) in case both teamids are 0
		if (($this->teamid1 == 0) && ($this->teamid2 == 0))
		{
			$favteams = $this->getFavTeams();
			$selteam1 = ( isset( $favteams[0] ) ) ? $favteams[0] : 0;
			$selteam2 = ( isset( $favteams[1] ) ) ? $favteams[1] : 0;
			$this->teamid1 = ($this->teamid1 == 0 ) ? $selteam1 : $this->teamid1;
			$this->teamid2 = ($this->teamid2 == 0 ) ? $selteam2 : $this->teamid2;
		}

		// When (one of) the teams are not specified, search for the next unplayed or the latest played match
		if (($this->teamid1 == 0) || ($this->teamid2 == 0))
		{
			$query  = ' SELECT t1.id AS teamid1, t2.id AS teamid2'
				. ' FROM #__joomleague_match AS m'
				. ' INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id=pt1.id'
				. ' AND pt1.project_id='.$this->_db->Quote($this->projectid);
			if ($this->division)
			{
				$query .= ' AND pt1.division_id='.$this->_db->Quote($this->division);
			}
			$query .= ' INNER JOIN #__joomleague_team AS t1 ON pt1.team_id=t1.id'
				. ' INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id=pt2.id'
				. ' AND pt2.project_id='.$this->_db->Quote($this->projectid);
			if ($this->division)
			{
				$query .= ' AND pt2.division_id='.$this->_db->Quote($this->division);
			}
			$query .= ' INNER JOIN #__joomleague_team AS t2 ON pt2.team_id=t2.id'
				. ' INNER JOIN #__joomleague_project AS p ON pt1.project_id=p.id AND pt2.project_id=p.id';

			$where = ' WHERE m.published=1 AND m.cancel=0';
			if ($this->teamid1)
			{
				$quoted_team_id = $this->_db->Quote($this->teamid1);
				$team = 't1';
			}
			else
			{
				$quoted_team_id = $this->_db->Quote($this->teamid2);
				$team = 't2';
			}
			if ($this->both)
			{
				$where .= ' AND (t1.id='.$quoted_team_id.' OR t2.id='.$quoted_team_id.')';
			}
			else
			{
				$where .= ' AND '.$team.'.id='.$quoted_team_id;
			}
			$config = $this->getTemplateConfig($this->getName());
			$expiry_time = $config ? $config['expiry_time'] : 0;
			$where_unplayed = ' AND (m.team1_result IS NULL OR m.team2_result IS NULL)'
					. ' AND DATE_ADD(m.match_date, INTERVAL '.$this->_db->Quote($expiry_time).' MINUTE)'
					// . '    >= CONVERT_TZ(UTC_TIMESTAMP(), '.$this->_db->Quote('UTC').', p.timezone)';
                    . ' >= NOW()';
			$order = ' ORDER BY m.match_date';
			$this->_db->setQuery($query.$where.$where_unplayed.$order);
			$match = $this->_db->loadObject();

			// If there is no unplayed match left, take the latest match played
			if (!isset($match))
			{
				$order = ' ORDER BY m.match_date DESC';
				$this->_db->setQuery($query.$where.$order);
				$match = $this->_db->loadObject();
			}
			if (isset($match))
			{
				$this->teamid1 = $match->teamid1;
				$this->teamid2 = $match->teamid2;
			}
		}
	}

	function getDivLevel()
	{
		if ( is_null( $this->divlevel ) )
		{
			$config = $this->getTemplateConfig("ranking");
			$this->divlevel = $config['default_division_view'];
		}
		return $this->divlevel;
	}

	function getTeam1($division=0)
	{
		if (!$this->teamid1) {
			return false;
		}
		$data = $this->getDataByDivision($division);
		foreach ($data as $team)
		{
			if ($team->id == $this->teamid1) {
				return $team;
			}
		}
		return false;
	}

	function getTeam2($division=0)
	{
		if (!$this->teamid2) {
			return false;
		}
		$data = $this->getDataByDivision($division);
		foreach ($data as $team)
		{
			if ($team->id == $this->teamid2) {
				return $team;
			}
		}
		return false;
	}

	function getDivision( )
	{
		return parent::getDivision($this->division);
	}

	function getDivisionId( )
	{
		return $this->division;
	}
	
	function getDataByDivision($division=0)
	{
		$project = $this->getProject();
		$rounds  = $this->getRounds();
		$teams   = $this->getTeamsIndexedByPtid($division);
			
		$rankinghelper = JLGRanking::getInstance($project);
		$rankinghelper->setProjectId( $project->id );
		$mdlRounds = JModel::getInstance("Rounds", "JoomleagueModel");
		$mdlRounds->setProjectId($project->id);
		$firstRound = $mdlRounds->getFirstRound($project->id);
		$firstRoundId = $firstRound['id'];
		
		$rankings = array();
		foreach ($rounds as $r)
		{
			$rankings[$r->id] = $rankinghelper->getRanking($firstRoundId, 
															$r->id, 
															$division);
		}
		foreach ($teams as $ptid => $team)
		{
			if($team->is_in_score==0) continue;
			$team_rankings = array();
			foreach ($rankings as $roundcode => $t)
			{
				if(empty($t[$ptid])) continue;
				$team_rankings[] = $t[$ptid]->rank;
			}
			$teams[$ptid]->rankings = $team_rankings;
		}
		return $teams;
	}
}
?>
