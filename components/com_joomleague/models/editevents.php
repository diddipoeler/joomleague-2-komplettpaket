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

jimport('joomla.application.component.model');

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

/**
 * Joomleague Component editevents Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100709
 */
class JoomleagueModelEditEvents extends JoomleagueModelProject
{
	var $projectid=0;
	var $matchid=0;
	var $joomleague=null;
	var $project=null;
	var $match=null;
	var $matches=array();
	var $hometeam=null;
	var $awayteam=null;

	// Edit Squad
	var $positions=null;
	var $playerpositions=null;
	var $allplayers=null;
	var $squadoptions=null;

	// Show Events
	//var $sortedevents=null;

	function __construct()
	{
		parent::__construct();

		$this->projectid=JRequest::getInt('p',0);
		$this->matchid=JRequest::getInt('mid',0);
		$this->eventid=JRequest::getInt('e',0);
		$this->team1_id=0;
		$this->team2_id=0;
		$this->projectteam1_id=0;
		$this->projectteam2_id=0;
	}

	function getProject()
	{
		if (is_null($this->project))
		{
			$this->project=$this->getTable('Project','Table');
			$this->project->load($this->projectid);
		}
		return $this->project;
	}

	function getMatch()
	{
		if (is_null($this->match))
		{
			$this->match=$this->getTable('Match','Table');
			$this->match->load($this->matchid);
		}
		$this->projectteam1_id=$this->match->projectteam1_id;
		$this->projectteam2_id=$this->match->projectteam2_id;
		return $this->match;
	}

	function getHomeTeam()
	{
		if (is_null($this->hometeam))
		{
			$projectTeam =& $this->getTable('ProjectTeam','Table');
			$projectTeam->load($this->projectteam1_id);
			$this->hometeam =& $this->getTable('Team','Table');
			$this->hometeam->load($projectTeam->team_id);
			$this->team1_id=$projectTeam->team_id;
		}
		return $this->hometeam;
	}

	function getAwayTeam()
	{
		if (is_null($this->awayteam))
		{
			$projectTeam =& $this->getTable('ProjectTeam','Table');
			$projectTeam->load($this->projectteam2_id);
			$this->awayteam =& $this->getTable('Team','Table');
			$this->awayteam->load($projectTeam->team_id);
			$this->team2_id=$projectTeam->team_id;
		}
		return $this->awayteam;
	}

	function getMatchID()
	{
		return $this->matchid;
	}

	function getHomeTeamID()
	{
		return $this->team1_id;
	}

	function getAwayTeamID()
	{
		return $this->team2_id;
	}

	function getHomeProjectTeamID()
	{
		return $this->projectteam1_id;
	}

	function getAwayProjectTeamID()
	{
		return $this->projectteam2_id;
	}

	function getPositions()
	{
		if (is_null($this->positions))
		{
			$query='	SELECT	pos.id AS posid,
								ppos.position_id AS id,
								pos.*
						FROM #__joomleague_position AS pos
						LEFT JOIN #__joomleague_project_position AS ppos ON pos.id=ppos.position_id
						WHERE ppos.project_id='.(int)$this->projectid.'
					  	ORDER BY pos.ordering';
			$this->_db->setQuery($query);
			$this->positions=$this->_db->loadObjectList('posid');
		}
		return $this->positions;
	}

	function getPlayerPositionOptions($projectTeamID)
	{
		$positions=$this->getPositions();
		$squad=array();
		// generate selection list for each position
		foreach ($positions AS $key=> $pos)
		{
			// get players assigned to this position
			$squad[$key]=$this->_getRoster($key,$projectTeamID);
		}
		$options = array();
		if (count($squad) > 0)
		{
			foreach ($squad AS $key=> $players)
			{
				$x=0;
				$options[$key]=array();
				if (isset($players))
				{
					foreach($players AS $player)
					{
						$options[$key][$x]->value=$player;
						$p =& $this->getTable('TeamPlayer','Table');
						$p->load($player);
						$q =& $this->getTable('Person','Table');
						$q->load($p->person_id);
						$options[$key][$x]->text=$q->lastname.' '.$q->firstname;
						$x++;
					}
				}
			}
		}
		return $options;
	}

	function getInOutOptions($projectTeamID)
	{
		$inoutoptions=$this->_makePlayerOptions($projectTeamID);
		return $inoutoptions;
	}

	function getPositionAdd()
	{
		$positions=$this->getPositions();
		$post_add='';
		foreach ($positions AS $key=>$pos){$post_add .= "selectAll($('asqad".$key."'));";}
		return $post_add;
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function _getTeamPlayers($projectteamid,$filter=false)
	{
		$query='	SELECT	ppl.id AS value,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							ppl.projectteam_id,
							pl.info,
							pos.name AS positionname,
							ppl.project_position_id,
							ppos.position_id
					FROM #__joomleague_person AS pl
						INNER JOIN #__joomleague_team_player AS ppl ON ppl.person_id=pl.id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ppl.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE	ppl.projectteam_id='.$this->_db->Quote($projectteamid).' 
					  AND pl.published = 1
					  AND ppl.published = 1 ';
		if (is_array($filter) && count($filter) > 0)
		{
			$query .= " AND ppl.id NOT IN (".implode(',',$filter).")";
		}
		$query .= " ORDER BY pl.lastname ASC ";

		$this->_db->setQuery($query);
		$teamplayers=$this->_db->loadObjectList();
		return $teamplayers;
	}

	function getRosterOptions($projectteamid,$filter=false)
	{
		$allplayers=$this->_getRoster(0,$projectteamid);
		$teamplayers=$this->_getTeamPlayers($projectteamid,$allplayers);
		$playeroptions=array();
		$config = $this->getTemplateConfig( "player" );
		foreach($teamplayers as $player)
		{
			$nameStr = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
			$playeroptions[]=JHTML::_(	'select.option',
			$player->value, $nameStr.' ('.JText::_($player->positionname).')');
		}
		return $playeroptions;
	}

	/**
	 * Method to return the team players array
	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function getGhostPlayer()
	{
		$ghost=new JObject();
		$ghost->set('value',0);
		$ghost->set('tpid',0);
		$ghost->set('firstname','');
		$ghost->set('nickname','');
		$ghost->set('lastname','Unknown');
		$ghost->set('info','');
		$ghost->set('positionname','');
		$ghost->set('project_position_id',0);
		return array($ghost);
	}

	function createRosterArray()
	{
		$homeRoster=$this->_getTeamPlayers($this->projectteam1_id);
		if (count($homeRoster)==0)
		{
			$homeRoster=$this->getGhostPlayer();
		}
		$awayRoster=$this->_getTeamPlayers($this->projectteam2_id);
		if (count($awayRoster)==0)
		{
			$awayRoster=$this->getGhostPlayer();
		}
		$rosters=array('home'=> $homeRoster,'away'=> $awayRoster);
		return $rosters;
	}

	function _getRoster($positionid=0,$projectteamid)
	{
		$playerids=array();
		$query='	SELECT	p.teamplayer_id AS value,
							pl.firstname AS firstname,
							pl.nickname,
							pl.lastname AS lastname
					FROM #__joomleague_match_player AS p
						INNER JOIN #__joomleague_team_player AS tpl ON tpl.id=p.teamplayer_id
						INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
					WHERE p.match_id='.(int) $this->matchid
		. ' AND tpl.projectteam_id='.(int) $projectteamid
		. ' AND tpl.published=1 '
		. ' AND p.came_in=0 '
		. ' AND pl.published = 1 ';
		if ($positionid > 0){$query .= " AND p.project_position_id='".$positionid."'";}
		$query .= " ORDER BY p.project_position_id, p.ordering ASC";
		$this->_db->setQuery($query);
		$playerids=$this->_db->loadResultArray();
		return $playerids;
	}

	function _makePlayerOptions($teamid,$multiple=false,$already_sel=false)
	{
		$query='	SELECT	p.teamplayer_id AS value,
							pl.firstname AS firstname,
							pl.nickname,
							pl.lastname AS lastname,
							pos.name AS positionname
					FROM #__joomleague_match_player AS p
						INNER JOIN #__joomleague_team_player AS tpl ON tpl.id=p.teamplayer_id
						INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
						LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=tpl.project_position_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE p.match_id='.(int) $this->matchid
				. '   AND tpl.projectteam_id='.(int) $teamid
				. '   AND tpl.published=1 '
				. '   AND p.came_in=0 '
				. '   AND pl.published = 1';
		if ((is_array($already_sel)) && (count($already_sel) > 0))
		{
			$query .= " AND p.teamplayer_id NOT IN (".implode(',',$already_sel).")";
		}
		$query .=' ORDER BY pl.lastname, p.project_position_id, p.ordering ASC';

		$this->_db->setQuery($query);
		$teamplayers=$this->_db->loadObjectList();

		$playeroptions=array();
		if (! $multiple){ $playeroptions[0]=JText::_('COM_JOOMLEAGUE_PLAYER_SELECT');}
		$config = $this->getTemplateConfig( "player" );
		foreach($teamplayers as $player)
		{
			$nameStr = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
			if ($multiple)
			{
				$playeroptions[]=JHTML::_(	'select.option',
				$player->value, $nameStr.' ('.JText::_($player->positionname).')');
			}
			else
			{
				$playeroptions[$player->value]=$nameStr.$player->lastname.' '.$player->firstname;
			}
		}
		if ($multiple)
		{
			return $playeroptions;
		}
		else
		{
			$playeroptions[0]=JText::_('COM_JOOMLEAGUE_PLAYER_SELECT');
		}
		return $playeroptions;
	}

	function getHomeOptions()
	{
		$match=$this->getMatch();
		return $this->_makePlayerOptions($match->projectteam1_id);
	}

	function getAwayOptions()
	{
		$match=$this->getMatch();
		return $this->_makePlayerOptions($match->projectteam2_id);
	}

	function getEventsOptions($project_id)
	{
		$query='	SELECT DISTINCT	et.id AS value,
									et.name AS text,
									et.icon AS icon
					FROM #__joomleague_match AS m
					INNER JOIN #__joomleague_project_position AS pj ON pj.project_id='.$project_id.'
					INNER JOIN #__joomleague_position_eventtype AS pet ON pet.position_id=pj.position_id
					INNER JOIN #__joomleague_eventtype AS et ON et.id=pet.eventtype_id
					WHERE m.id='.$this->_db->Quote((int) $this->matchid).'
					ORDER BY pet.ordering ASC ';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		foreach ($result as $event){$event->text=JText::_($event->text);}
		return $result;
	}

	function getEventTypes()
	{
		$query='	SELECT	et.id,
							et.name,
							et.icon
					FROM #__joomleague_eventtype AS et
						LEFT JOIN #__joomleague_match_event AS me ON et.id=me.event_type_id
					WHERE me.match_id='.(int)$this->matchid.'
					GROUP BY et.id';

		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		return $result;
	}

	/**
	 * Method to return the project positions array (id,name)
	 * filtered by persontype 1=player / 2=staff / 3=referee
	 * @access	public
	 * @return	array
	 * @since 1.5
	 */
	function getProjectPositions($id=0,$persontype)
	{
		$query='SELECT	ppos.id AS value,
						pos.name AS text
				FROM #__joomleague_position AS pos
				INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pos.id
				WHERE ppos.project_id='.(int)$this->projectid;
		if ($persontype==0)
		{
			$query .=' AND (pos.persontype=1 OR pos.persontype=2)';
		}
		else
		{
			$query .=' AND pos.persontype='.$persontype;
		}

		if ($id > 0)
		{
			$query .=' AND ppos.id='.$id;
		}
		$query .=' ORDER BY pos.ordering';

		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList('value'))
		{
			$this->setError($this->_db->getErrorMsg());
			return array();
		}
		return $result;
	}

	/**
	 * returns staff who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchStaffs($projectteam_id,$project_position_id=0)
	{
		$query='	SELECT	mp.team_staff_id,
							pl.firstname AS firstname,
							pl.nickname,
							pl.lastname AS lastname,
							mp.project_position_id,
							tpl.projectteam_id,
							pos.id AS position_id
					FROM #__joomleague_match_staff AS mp
						INNER JOIN #__joomleague_team_staff AS tpl ON tpl.id=mp.team_staff_id
						INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
						INNER JOIN #__joomleague_project_position AS ppos ON mp.project_position_id = ppos.id
						INNER JOIN #__joomleague_position AS pos ON ppos.position_id = pos.id
					WHERE	mp.match_id='.(int) $this->matchid.' 
					  AND pl.published = 1 
					  AND tpl.published = 1 
					  AND tpl.projectteam_id = '.$projectteam_id;
		if ($project_position_id > 0)
		{
			$query .= " AND mp.project_position_id='".$project_position_id."'";
		}
		$query .= " ORDER BY mp.project_position_id, mp.ordering, pl.lastname, pl.firstname ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('team_staff_id');
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getTeamStaffs($projectteam_id,$filter=false)
	{
		$query='	SELECT	ppl.id AS value,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							ppl.projectteam_id,
							pl.info,
							pos.name AS positionname,
							ppl.project_position_id
					FROM #__joomleague_person AS pl
						INNER JOIN #__joomleague_team_staff AS ppl ON ppl.person_id=pl.id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ppl.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE ppl.projectteam_id='. $this->_db->Quote($projectteam_id).' 
					  AND ppl.published = 1
					  AND pl.published = 1';
		if (is_array($filter) && count($filter) > 0)
		{
			$query .=' AND ppl.id NOT IN ('.implode(',',$filter).')';
		}
		$query .=' ORDER BY pl.lastname ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getNotAssignedTeamStaffPersons($projectteam_id)
	{
		// assigned staff
		$assigned=$this->getMatchStaffs($projectteam_id);
		$assigned_id=array_keys($assigned);
		// not assigned staff
		$not_assigned=$this->getTeamStaffs($projectteam_id,$assigned_id);
		// build select list for not assigned
		$not_assigned_options=array();
		$config = $this->getTemplateConfig( "player" );
		foreach ((array) $not_assigned AS $player)
		{
			$nameStr = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
			$not_assigned_options[]=JHTML::_(	'select.option',
			$player->value, $nameStr.' ('.JText::_($player->positionname).')');
		}
		return $not_assigned_options;
	}

	
    /**
	 * get match commentary
	 *
	 * @return array
	 */
	function getMatchCommentary()
	{
		$query=' SELECT	me.*'
        .' FROM #__joomleague_match_commentary AS me '
		.' WHERE me.match_id='.(int) $this->matchid
		.' ORDER BY me.event_time ASC ';
		$this->_db->setQuery($query);
		return ($this->_db->loadObjectList());
	}
    
    
    /**
	 * returns players who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchPlayers($projectteam_id,$project_position_id=0)
	{
		$query='	SELECT	mp.teamplayer_id,
							pl.firstname AS firstname,
							pl.nickname,
							pl.lastname AS lastname,
							mp.project_position_id,
							ppos.position_id,
							tpl.projectteam_id
					FROM #__joomleague_match_player AS mp
						INNER JOIN #__joomleague_team_player AS tpl ON tpl.id=mp.teamplayer_id
						INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE	mp.match_id='.(int) $this->matchid.' 
					  AND pl.published = 1 
					  AND tpl.published = 1 
					  AND mp.came_in=0 
					  AND tpl.projectteam_id='.$projectteam_id;
		if ($project_position_id > 0)
		{
			$query .= " AND mp.project_position_id='".$project_position_id."'";
		}
		$query .= " ORDER BY mp.project_position_id, mp.ordering, pl.lastname, pl.firstname ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('teamplayer_id');
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getTeamPlayers($projectteam_id,$filter=false)
	{
		$query='	SELECT	ppl.id AS value,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							ppl.projectteam_id,
							pl.info,
							pos.name AS positionname,
							ppl.project_position_id,
							ppos.position_id
					FROM #__joomleague_person AS pl
						INNER JOIN #__joomleague_team_player AS ppl ON ppl.person_id=pl.id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ppl.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE ppl.projectteam_id='. $this->_db->Quote($projectteam_id).' 
					  AND pl.published = 1
					  AND ppl.published = 1';
		if (is_array($filter) && count($filter) > 0)
		{
			$query .=' AND ppl.id NOT IN ('.implode(',',$filter).')';
		}
		$query .=' ORDER BY pl.lastname ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getNotAssignedTeamPlayerPersons($projectteam_id)
	{
		// assigned players
		$assigned =& $this->getMatchPlayers($projectteam_id);
		$assigned_id=array_keys($assigned);
		// not assigned players
		$not_assigned=$this->getTeamPlayers($projectteam_id,$assigned_id);
		// build select list for not assigned
		$not_assigned_options=array();
		$config = $this->getTemplateConfig( "player" );
		foreach ((array) $not_assigned AS $player)
		{
			$nameStr = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
			$not_assigned_options[]=JHTML::_(	'select.option',
			$player->value,
			$nameStr.' ('.JText::_($player->positionname).')');
		}
		return $not_assigned_options;
	}

	/**
	 * returns referees who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchReferees($project_position_id=0)
	{
		$query='	SELECT	mr.project_referee_id,
							mr.project_position_id,
							pr.firstname AS firstname,
							pr.nickname,
							pr.lastname AS lastname
					FROM #__joomleague_match_referee AS mr
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=mr.project_position_id
					INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					LEFT JOIN #__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id
					  AND pref.published = 1
					LEFT JOIN #__joomleague_person AS pr ON pref.person_id=pr.id
					  AND pr.published = 1
					WHERE mr.match_id='.(int)$this->matchid;
		if ($project_position_id > 0)
		{
			$query .= " AND mr.project_position_id='".$project_position_id."'";
		}
		$query .= " ORDER BY mr.project_position_id, mr.ordering, pr.lastname, pr.firstname ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('project_referee_id');
	}

	/**
	 * returns referees who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchRefereeTeams($project_position_id=0)
	{
		$query='	SELECT	mr.project_referee_id,
							mr.project_position_id,
							t.name AS teamname
					FROM #__joomleague_match_referee AS mr
						LEFT JOIN #__joomleague_project_team AS pt ON pt.id=mr.project_referee_id
						LEFT JOIN #__joomleague_team AS t ON t.id=pt.team_id
					WHERE mr.match_id='.(int) $this->matchid;

		if ($project_position_id > 0)
		{
			$query .= " AND mr.project_position_id='".$project_position_id."'";
		}
		$query .= " ORDER BY mr.project_position_id,t.name ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('project_referee_id');
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getProjectReferees($filter=false)
	{
		$query='	SELECT	pr.id AS value,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							pl.info,
							pos.name AS positionname
					FROM #__joomleague_person AS pl
						LEFT JOIN #__joomleague_project_referee AS pr ON pr.person_id=pl.id
						    AND pr.published = 1
						LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=pr.project_position_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE pr.project_id='.(int)$this->projectid.' AND pl.published = 1';
		if (is_array($filter) && count($filter) > 0)
		{
			$query .=' AND pr.id NOT IN ('.implode(',',$filter).')';
		}
		$query .=' ORDER BY pl.lastname ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('value');
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getProjectTeams($filter=false)
	{
		$query='	SELECT	pt.id AS value,
							t.name
					FROM #__joomleague_project_team AS pt
						LEFT JOIN #__joomleague_team AS t ON t.id=pt.team_id
					WHERE pt.project_id='.(int)$this->projectid;
		if (is_array($filter) && count($filter) > 0)
		{
			$query .=' AND pt.id NOT IN ('.implode(',',$filter).')';
		}
		$query .=' ORDER BY t.name ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('value');
	}

	function getNotAssignedProjectReferees()
	{
		// assigned referees
		$assigned=$this->getMatchReferees();
		$assigned_id=array_keys($assigned);
		// not assigned referees
		$not_assigned=$this->getProjectReferees($assigned_id);
		// build select list for not assigned
		$not_assigned_options=array();
		$config = $this->getTemplateConfig( "player" );
		foreach ((array) $not_assigned AS $referee)
		{
			$nameStr = JoomleagueHelper::formatName(null, $referee->firstname, $referee->nickname, $referee->lastname, $config["name_format"]);
			$not_assigned_options[]=JHTML::_(	'select.option',
			$referee->value, $nameStr.' ('.JText::_($referee->positionname).')');
		}
		return $not_assigned_options;
	}

	function getNotAssignedProjectRefereeTeams()
	{
		// assigned refereeteams
		$assigned=$this->getMatchRefereeTeams();
		$assigned_id=array_keys($assigned);
		// not assigned referees
		$not_assigned=$this->getProjectTeams($assigned_id);
		// build select list for not assigned
		$not_assigned_options=array();
		foreach ((array) $not_assigned AS $referee)
		{
			$not_assigned_options[]=JHTML::_('select.option',$referee->value,$referee->name);
		}
		return $not_assigned_options;
	}

	/**
	 * Method to return teams and match data
		*
		* @access	public
		* @return	array
		* @since 0.1
		*/
	function getMatchTeams()
	{
		$query='	SELECT	mc.*,
							t1.name AS team1,
							t2.name AS team2,
							u.name AS editor
					FROM #__joomleague_match AS mc
					INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=mc.projectteam1_id
					INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
					INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=mc.projectteam2_id
					INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
					LEFT JOIN #__users u ON u.id=mc.checked_out
					WHERE mc.id='.(int) $this->matchid;
		$this->_db->setQuery($query);
		return	$this->_db->loadObject();
	}

	/**
	 * Method to return substitutions made by a team during a match
	 * if no team id is passed,all substitutions should be returned (to be done!!)
	 * @access	public
	 * @return	array of substitutions
	 *
	 */
	function getSubstitutions($projectTeamID)
	{
		$substitutes=array();
		$query='	SELECT	mp.*,
							p1.firstname,
							p1.nickname,
							p1.lastname,
							pos.name AS in_position,
							p2.firstname AS out_firstname,p2.nickname AS out_nickname,
							p2.lastname AS out_lastname
					FROM #__joomleague_match_player AS mp
						INNER JOIN #__joomleague_team_player AS tp1 ON tp1.id=mp.teamplayer_id
						INNER JOIN #__joomleague_person AS p1 ON tp1.person_id=p1.id
						INNER JOIN #__joomleague_team_player AS tp2 ON tp2.id=mp.in_for
						INNER JOIN #__joomleague_person AS p2 ON tp2.person_id=p2.id
						LEFT JOIN #__joomleague_project_position AS ppos ON mp.project_position_id=ppos.id
						LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id
					WHERE	mp.match_id='.(int)$this->matchid.' 
					  AND tp1.projectteam_id='.(int)$projectTeamID.' 
					  AND mp.came_in=1 
					  AND p1.published = 1 
					  AND p2.published = 1 
					  AND tp1.published = 1 
					  AND tp2.published = 1 
					  ORDER by ABS(mp.in_out_time) ASC';
		$this->_db->setQuery($query);
		$substitutes=$this->_db->loadObjectList();
		return $substitutes;
	}

	function getSelectPositions($prefix)
	{
		$projectpositions =& $this->getProjectPositions(0,0);
		$selectpositions[]=JHTML::_('select.option','0',JText::_('- Select in position -'));
		$selectpositions=array_merge($selectpositions,$projectpositions);
		return JHtml::_('select.genericlist',$selectpositions,$prefix.'_project_position_id','class="inputbox" size="1"','value','text');
	}

	function getPlayersOptions($projectTeamID)
	{
		$allplayers =& $this->getTeamPlayers($projectTeamID);
		$playersoptions=array();
		$playersoptions[]=JHTML::_('select.option','0',JText::_('- Select Player -'));
		$config = $this->getTemplateConfig( "player" );
		foreach ((array)$allplayers AS $player)
		{
			$nameStr = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
			$playersoptions[]=JHTML::_('select.option', $player->value, $nameStr);
		}
		return $playersoptions;
	}
    
    function deletecommentary($event_id)
	{
		$object =& JTable::getInstance('MatchCommentary','Table');
		if (!$object->canDelete($event_id))
		{
			$this->setError('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_ERROR_DELETE_COMMENTARY');
			return false;
		}
		if (!$object->delete($event_id))
		{
			$this->setError('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_DELETE_FAILED_COMMENTARY');
			return false;
		}
		return true;
	}

	function deleteevent($event_id)
	{
		$object =& JTable::getInstance('MatchEvent','Table');
		if (!$object->canDelete($event_id))
		{
			$this->setError('CAN NOT DELETE');
			return false;
		}
		if (!$object->delete($event_id))
		{
			$this->setError('DELETE FAILED');
			return false;
		}
		return true;
	}
    
    function savecomment($data, $project_id)
	{
		
        // live kommentar speichern
        if ( empty($data['event_time']) )
		{
		$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_COMMENT_NO_TIME'));
		return false;
		}

        
        if ( empty($data['notes']) )
		{
		$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_COMMENT_NO_COMMENT'));
		return false;
		}
            
        if ( (int)$data['event_time'] > (int)$data['projecttime'] )
		{
		$this->setError(JText::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_COMMENT_TIME_OVER_PROJECTTIME',$data['event_time'],$data['projecttime']));
		return false;
		}
        
        $object =& JTable::getInstance('MatchCommentary','Table');
		$object->bind($data);
		if (!$object->check())
		{
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
			return false;
		}
		if (!$object->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
        else
        {
            $object->id = $this->_db->insertid();
        }
        
		return $object->id;
	}

	function saveevent($data,$project_id)
	{
		if ( empty($data['event_time']) )
		{
		$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_EVENT_NO_TIME'));
		return false;
		}
        
        if ( empty($data['event_sum']) )
		{
		$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_EVENT_NO_EVENT_SUM'));
		return false;
		}
        
        if ( (int)$data['event_time'] > (int)$data['projecttime'] )
		{
		$this->setError(JText::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_EVENT_TIME_OVER_PROJECTTIME',$data['event_time'],$data['projecttime']));
		return false;
		}
   
        $object =& JTable::getInstance('MatchEvent','Table');
		$object->bind($data);
		if (!$object->check())
		{
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
			return false;
		}
		if (!$object->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
        else
        {
            $object->id = $this->_db->insertid();
        }
		return $object->id;
	}

	/**
	 * remove specified subsitution
	 *
	 * @param int $substitution_id
	 * @return boolean
	 */
	function removesubstitution($substitution_id)
	{
		// get corresponding substitution
		$query="SELECT * FROM #__joomleague_match_player WHERE id='".$substitution_id."'";
		$this->_db->setQuery($query);
		if (!$sub=$this->_db->loadObject())
		{
			$this->setError(JText::_('SUBSTITUTION NOT FOUND'));
			return false;
		}

		// delete a substitution
		// the starter is not going out any more
		$query='	UPDATE IGNORE #__joomleague_match_player
					SET `out`=0,in_out_time=null
					WHERE match_id='.$this->_db->Quote($sub->match_id).'
					AND teamplayer_id='.$this->_db->Quote($sub->in_for).'
					AND came_in=0 ';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{

		}

		// the subsitute isn't getting in so we delete the substitution
		$query="DELETE FROM #__joomleague_match_player WHERE id=".$this->_db->Quote($substitution_id);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError(JText::_('ERROR DELETING SUBSTITUTION'));
			return false;
		}
		return true;
	}

	/**
	 * save the submitted substitution
	 *
	 * @param array $data
	 * @return boolean
	 */
	function savesubstitution($data)
	{
		if (! ($data['in'] && $data['out'] && $data['matchid'])){return false;}

		$player_in=$data['in'];
		$player_out=$data['out'];
		$match_id=$data['matchid'];
		$in_out_time=$data['in_out_time'];

		// update starter entry with substitution info
		$query='	UPDATE	IGNORE #__joomleague_match_player
							SET `out`=1,in_out_time='.$this->_db->Quote($in_out_time).'
							WHERE	match_id='.$this->_db->Quote($match_id).' AND
									came_in=0 AND teamplayer_id='.$this->_db->Quote($player_out);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError(JText::_('ERROR UPDATING STARTER'));
			return false;
		}

		if ($data['project_position_id']==0)
		{
			// retrieve normal position of player getting in
			$query='	SELECT project_position_id
						FROM #__joomleague_team_player AS tp
						WHERE tp.player_id='.$this->_db->Quote($player_in);
			$this->_db->setQuery($query);
			$project_position=$this->_db->loadResult();
		}
		else
		{
			$project_position=$data['project_position_id'];
		}

		// insert an entry for the substitute
		$query='	INSERT	INTO #__joomleague_match_player
							(match_id, teamplayer_id, came_in, in_for, in_out_time, project_position_id)
							VALUES
							('. $this->_db->Quote($match_id) .','. 
								$this->_db->Quote($player_in) .','.
								'1,'. 
								$this->_db->Quote($player_out) .','.
								$this->_db->Quote($in_out_time) .','. 
								$this->_db->Quote($project_position).
							')';

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError(JText::_('ERROR SAVING NEW SUBSTITUTION'));
			return false;
		}
		return $this->_db->insertid();
	}

	function saveMatchStartingLineUps($data)
	{
		$project_id=JRequest::getInt('pid',0);
		$match_id=JRequest::getInt('mid',0);
		//### Save Teams Players Start ##########################################################################################
		$playerPositions = $data['playerpositions'];
		$projectPlayerPositions=array_keys($playerPositions);
		$homePlayerPositions=array();
		$awayPlayerPositions=array();
		foreach ($projectPlayerPositions as $project_position_id)
		{
			$dArray=JRequest::getVar('hplayerposition'.$project_position_id,array(),'post','array');
			if (!empty($dArray))
			{
				$homePlayerPositions[$project_position_id]=$dArray;
			}
			$dArray=JRequest::getVar('aplayerposition'.$project_position_id,array(),'post','array');
			if (!empty($dArray))
			{
				$awayPlayerPositions[$project_position_id]=$dArray;
			}
		}
		// first delete all match player records connected with match_id
		$query='	DELETE	mp
					FROM #__joomleague_match_player AS mp
					WHERE came_in=0 AND mp.match_id='.$this->_db->Quote($match_id);
		$this->_db->setQuery($query);
		if (!$this->_db->query()){$this->setError($this->_db->getErrorMsg()); $result=false;}

		// now ADD all match player records,which are checked for usage for home team
		foreach ($homePlayerPositions AS $project_position_id => $value)
		{
			foreach ($value AS $ordering => $teamplayer_id)
			{
				$record =& JTable::getInstance('Matchplayer','Table');
				$record->match_id=$match_id;
				$record->teamplayer_id=$teamplayer_id;
				$record->project_position_id=$project_position_id;
				$record->came_in=0;
				$record->ordering=$ordering;
				if (!$record->check())
				{
					$this->setError($record->getError());
					$result=false;
				}
				if (!$record->store())
				{
					$this->setError($record->getError());
					$result=false;
				}
			}
		}
		// now ADD all match player records,which are checked for usage for away team
		foreach ($awayPlayerPositions AS $project_position_id => $value)
		{
			foreach ($value AS $ordering => $teamplayer_id)
			{
				$record =& JTable::getInstance('Matchplayer','Table');
				$record->match_id=$match_id;
				$record->teamplayer_id=$teamplayer_id;
				$record->project_position_id=$project_position_id;
				$record->came_in=0;
				$record->ordering=$ordering;
				if (!$record->check())
				{
					$this->setError($record->getError());
					$result=false;
				}
				if (!$record->store())
				{
					$this->setError($record->getError());
					$result=false;
				}
			}
		}
		//### Save Teams Players End ############################################################################################

		//die();
		//return;

		//### Save Teams Staff Start ############################################################################################
		$staffPositions = $data['staffpositions'];
		$projectStaffPositions = array_keys($staffPositions);
		$homeStaffPositions=array();
		$awayStaffPositions=array();
		foreach ($projectStaffPositions as $project_position_id)
		{
			$dArray=JRequest::getVar('hstaffposition'.$project_position_id,array(),'post','array');
			if (!empty($dArray))
			{
				$homeStaffPositions[$project_position_id]=$dArray;
			}
			$dArray=JRequest::getVar('astaffposition'.$project_position_id,array(),'post','array');
			if (!empty($dArray))
			{
				$awayStaffPositions[$project_position_id]=$dArray;
			}
		}
		// first delete all match staff records connected with match_id
		$query='	DELETE	ms
					FROM #__joomleague_match_staff AS ms
					WHERE	ms.match_id='.$this->_db->Quote($match_id);
		$this->_db->setQuery($query);
		if (!$this->_db->query()){$this->setError($this->_db->getErrorMsg()); $result=false;}

		// now ADD all match staff records,which are checked for usage for home team
		foreach ($homeStaffPositions as $project_position_id=> $value)
		{
			foreach ($value AS $ordering => $team_staff_id)
			{
				$record =& JTable::getInstance('Matchstaff','Table');
				$record->match_id=$match_id;
				$record->team_staff_id=$team_staff_id;
				$record->project_position_id=$project_position_id;
				$record->ordering=$ordering;
				if (!$record->check())
				{
					$this->setError($record->getError());
					$result=false;
				}
				if (!$record->store())
				{
					$this->setError($record->getError());
					$result=false;
				}
			}
		}
		// now ADD all match staff records,which are checked for usage for away team
		foreach ($awayStaffPositions as $project_position_id=> $value)
		{
			//foreach ($value as $team_staff_id)
			foreach ($value AS $ordering => $team_staff_id)
			{
				$record =& JTable::getInstance('Matchstaff','Table');
				$record->match_id=$match_id;
				$record->team_staff_id=$team_staff_id;
				$record->project_position_id=$project_position_id;
				$record->ordering=$ordering;
				if (!$record->check())
				{
					$this->setError($record->getError());
					$result=false;
				}
				if (!$record->store())
				{
					$this->setError($record->getError());
					$result=false;
				}
			}
		}
		//### Save Teams Staff End ##############################################################################################

		//### Save Referees Start ###############################################################################################
		$refereePositions = $data['refereepositions'];
		$projectRefereePositions = array_keys($refereePositions);
		$refereePositionValues=array();
		foreach ($projectRefereePositions as $project_position_id)
		{
			$dArray=JRequest::getVar('refereeposition'.$project_position_id,array(),'post','array');
			if (!empty($dArray))
			{
				$refereePositionValues[$project_position_id]=$dArray;
			}
		}

		// first delete all match referee records connected with match_id
		$query='	DELETE	mr
					FROM #__joomleague_match_referee AS mr
					WHERE	mr.match_id='. $this->_db->Quote($match_id);
		$this->_db->setQuery($query);
		if (!$this->_db->query()){$this->setError($this->_db->getErrorMsg()); $result=false;}

		// now ADD all match referee records,which are checked for usage
		foreach ($refereePositionValues as $project_position_id=> $value)
		{
			foreach ($value AS $ordering => $project_referee_id)
			{
				$record =& JTable::getInstance('Matchreferee','Table');
				$record->match_id=$match_id;
				$record->project_referee_id=$project_referee_id;
				$record->project_position_id=$project_position_id;
				$record->ordering=$ordering;
				if (!$record->check())
				{
					$this->setError($record->getError());
					$result=false;
				}
				if (!$record->store())
				{
					$this->setError($record->getError());
					$result=false;
				}
			}
		}
		//### Save Referees End #################################################################################################

		return true;
	}

	function isAllowed()
	{
		$mainframe	= JFactory::getApplication();
        $allowed = false;
		$user = JFactory::getUser();
        
        //$mainframe->enqueueMessage(JText::_('isAllowed user-> '.'<pre>'.print_r($user,true).'</pre>' ),'');

		if ($user->id != 0)
		{
			$project =& $this->getProject();
            
            //$mainframe->enqueueMessage(JText::_('isAllowed user->id-> '.'<pre>'.print_r($user->id,true).'</pre>' ),'');
            //$mainframe->enqueueMessage(JText::_('isAllowed project->admin-> '.'<pre>'.print_r($project->admin,true).'</pre>' ),'');
            //$mainframe->enqueueMessage(JText::_('isAllowed project->editor-> '.'<pre>'.print_r($project->editor,true).'</pre>' ),'');

			if (($user->authorise('editmatch.editevents', 'com_joomleague')) &&
			    (($user->id == $project->admin) || ($user->id == $project->editor)))
			{
				$allowed = true;
			}
		}
		return $allowed;
	}

	function isMatchAdmin($matchid,$userid)
	{
		$mainframe	= JFactory::getApplication();
        $query='	SELECT count(*)
					FROM #__joomleague_match AS m
						INNER JOIN #__joomleague_project_team AS tt1 ON m.projectteam1_id=tt1.id
						INNER JOIN #__joomleague_project_team AS tt2 ON m.projectteam2_id=tt2.id
						WHERE	m.id='.$matchid.' AND
								(	tt1.admin='. $userid.' OR
									tt2.admin='.$userid.')';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadResult())
		{
			return false;
		}
        
        //$mainframe->enqueueMessage(JText::_('isMatchAdmin matchid-> '.'<pre>'.print_r($matchid,true).'</pre>' ),'');
        //$mainframe->enqueueMessage(JText::_('isMatchAdmin userid-> '.'<pre>'.print_r($userid,true).'</pre>' ),'');
        //$mainframe->enqueueMessage(JText::_('isMatchAdmin result-> '.'<pre>'.print_r($result,true).'</pre>' ),'');
        
		return true;
	}

}
?>