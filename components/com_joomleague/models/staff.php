<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once('person.php');

class JoomleagueModelStaff extends JoomleagueModelPerson
{
	/**
	 * data array for staff history
	 * @var array
	 */
	var $_history=null;

	function __construct()
	{
		parent::__construct();
		$this->projectid=JRequest::getInt('p',0);
		$this->personid=JRequest::getInt('pid',0);
		$this->teamid=JRequest::getInt('tid',0);
	}

	function &getTeamStaff()
	{
		if (is_null($this->_inproject))
		{
			$query='	SELECT	ts.*,
								ts.picture as picture,
								pos.name AS position_name,
								ppos.id AS pPosID, ppos.position_id
						FROM #__joomleague_team_staff AS ts
						INNER JOIN #__joomleague_project_team AS pt ON pt.id=ts.projectteam_id
						LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						WHERE pt.project_id='.$this->_db->Quote($this->projectid).' 
						  AND ts.person_id='.$this->_db->Quote($this->personid).'
						  AND ts.published=1';
			$this->_db->setQuery($query);
			$this->_inproject=$this->_db->loadObject();
		}
		return $this->_inproject;
	}

	/**
	 * get person history across all projects,with team,season,position,... info
	 *
	 * @param int $person_id,linked to player_id from Person object
	 * @param int $order ordering for season and league,default is ASC ordering
	 * @param string $filter e.g. "s.name=2007/2008",default empty string
	 * @return array of objects
	 */
	function &getStaffHistory($order='ASC')
	{
		if (empty($this->_history))
		{
			$personid=$this->personid;
			$query='	SELECT	pr.id AS pid,
								o.person_id,
								tt.project_id,
								pr.firstname,
								pr.lastname,
								p.name AS project_name,
								s.name AS season_name,
								t.name AS team_name,
								pos.name AS position_name,
								ppos.position_id,
								t.id AS team_id,
								tt.id AS ptid,
								pos.id AS posID,
								CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,
								CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS project_slug
						FROM #__joomleague_person AS pr
						INNER JOIN #__joomleague_team_staff AS o ON o.person_id=pr.id
						INNER JOIN #__joomleague_project_team AS tt ON tt.id=o.projectteam_id
						INNER JOIN #__joomleague_team AS t ON t.id=tt.team_id
						INNER JOIN #__joomleague_project AS p ON p.id=tt.project_id
						INNER JOIN #__joomleague_season AS s ON s.id=p.season_id
						INNER JOIN #__joomleague_league AS l ON l.id=p.league_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=o.project_position_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						WHERE pr.id='.$this->_db->Quote($personid).'
						  AND pr.published = 1
						  AND o.published = 1
						  AND p.published = 1
						  ORDER BY s.ordering '.$order.', l.ordering ASC, p.name ASC ';
			$this->_db->setQuery($query);
			$this->_history=$this->_db->loadObjectList();
		}
		return $this->_history;
	}

	function getContactID($catid)
	{
		$person=$this->getPerson();
		$query='SELECT id FROM #__contact_details WHERE user_id='.$person->jl_user_id.' AND catid='.$catid;
		$this->_db->setQuery($query);
		$contact_id=$this->_db->loadResult();
		return $contact_id;
	}

	function getPresenceStats($project_id,$person_id)
	{
		$query='	SELECT	count(mp.id) AS present
					FROM #__joomleague_match_staff AS mp
					INNER JOIN #__joomleague_match AS m ON mp.match_id=m.id
					INNER JOIN #__joomleague_team_staff AS tp ON tp.id=mp.team_staff_id
					INNER JOIN #__joomleague_project_team AS pt ON m.projectteam1_id=pt.id
					WHERE tp.person_id='.$this->_db->Quote((int)$person_id).' 
					  AND pt.project_id='.$this->_db->Quote((int)$project_id) . '
					  AND tp.published = 1';
		$this->_db->setQuery($query,0,1);
		$inoutstat=$this->_db->loadResult();
		return $inoutstat;
	}

	/**
	 * get stats for the player position
	 * @return array
	 */
	function getStats()
	{
		$staff =& $this->getTeamStaff();
		if(!isset($staff->position_id)){$staff->position_id=0;}
		$result=$this->getProjectStats(0,$staff->position_id);
		return $result;
	}

	/**
	 * get player stats
	 * @return array
	 */
	function getStaffStats()
	{
		$staff =& $this->getTeamStaff();
		if (!isset($staff->position_id)){$staff->position_id=0;}
		$stats =& $this->getProjectStats(0,$staff->position_id);
		$history =& $this->getStaffHistory();
		$result=array();
		if(count($history) > 0 && count($stats) > 0)
		{
			foreach ($history as $player)
			{
				foreach ($stats as $stat)
				{
					if(!isset($stat) && $stat->position_id != null)
					{
						$result[$stat->id][$player->project_id]=$stat->getStaffStats($player->person_id,$player->team_id,$player->project_id);
					}
				}
			}
		}
		return $result;
	}

	function getHistoryStaffStats()
	{
		$staff =& $this->getTeamStaff();
		$stats =& $this->getProjectStats(0,$staff->position_id);
		$result=array();
		if (count($stats) > 0)
		{
			foreach ($stats as $stat)
			{
				if (!isset($stat))
				{
					$result[$stat->id]=$stat->getHistoryStaffStats($staff->person_id);
				}
			}
		}
		return $result;
	}

}
?>