<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelRosteralltime extends JoomleagueModelProject
{
	var $projectid=0;
	var $projectteamid=0;
	var $projectteam=null;
	var $team=null;

	/**
	 * caching for team in out stats
	 * @var array
	 */
	var $_teaminout=null;

	/**
	 * caching players
	 * @var array
	 */
	var $_players=null;

	function __construct()
	{
		parent::__construct();

		$this->projectid=JRequest::getInt('p',0);
		$this->teamid=JRequest::getInt('tid',0);
		$this->projectteamid=JRequest::getInt('ttid',0);
		//$this->getProjectTeam();
	}
    
    function getPlayerPosition()
    {
    $query = "SELECT po.*
from #__joomleague_position as po
where po.parent_id != '0' 
and persontype = '1'
";

$this->_db->setQuery( $query );
return $this->_db->loadObjectList();    
        
    }
    
    function getPositionEventTypes($positionId=0)
	{
		$result=array();
		$query='	SELECT	pet.*,
							
							et.name AS name,
							et.icon AS icon
					FROM #__joomleague_position_eventtype AS pet
					INNER JOIN #__joomleague_eventtype AS et ON et.id = pet.eventtype_id
					WHERE et.published=1 ';
		
		$query .= ' ORDER BY pet.ordering, et.ordering';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		if ($result)
		{
			if ($positionId)
			{
				return $result;
			}
			else
			{
				$posEvents=array();
				foreach ($result as $r)
				{
					$posEvents[$r->position_id][]=$r;
				}
				return ($posEvents);
			}
		}
		return array();
	}


	/**
	 * return team players by positions
	 * @return array
	 */
	function getTeamPlayers()
	{
		//$projectteam =& $this->getprojectteam();
		if (empty($this->_players))
		{
			$query='	SELECT	pr.firstname, 
								pr.nickname,
								pr.lastname,
								pr.country,
								pr.birthday,
								pr.deathday,
								tp.id AS playerid,
								pr.id AS pid,
								pr.picture AS ppic,
								tp.jerseynumber AS position_number,
								tp.notes AS description,
								tp.injury AS injury,
                tp.market_value AS market_value,
								tp.suspension AS suspension,
								pt.team_id,
								tp.away AS away,tp.picture,
								pos.name AS position,
								ppos.position_id,
								ppos.id as pposid,
								CASE WHEN CHAR_LENGTH(pr.alias) THEN CONCAT_WS(\':\',pr.id,pr.alias) ELSE pr.id END AS slug
						FROM #__joomleague_team_player tp
						INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id
						INNER JOIN #__joomleague_person AS pr ON tp.person_id = pr.id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id = tp.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id = ppos.position_id
						WHERE pt.team_id = '.$this->_db->Quote($this->teamid).'
						AND pr.published = 1
						AND tp.published = 1
						ORDER BY pos.ordering, ppos.position_id, tp.jerseynumber, pr.lastname, pr.firstname';
			$this->_db->setQuery($query);
			$this->_players = $this->_db->loadObjectList();
			$this->_all_time_players = $this->_db->loadObjectList('pid');
		}
		
		
		foreach ($this->_players as $player)
		{
		$player->start = 0;
		$player->came_in = 0;
		$player->out = 0;
$query = '	SELECT count(*) as total
FROM #__joomleague_match_player
WHERE came_in = 0  
and teamplayer_id = ' . $player->playerid;
$this->_db->setQuery( $query );
$player->start = $this->_db->loadResult();
$this->_all_time_players[$player->pid]->start = $this->_all_time_players[$player->pid]->start + $player->start;
		
$query = '	SELECT count(*) as total
FROM #__joomleague_match_player
WHERE came_in = 1  
and teamplayer_id = ' . $player->playerid;
$this->_db->setQuery( $query );
$player->came_in = $this->_db->loadResult();
$this->_all_time_players[$player->pid]->came_in = $this->_all_time_players[$player->pid]->came_in + $player->came_in;

$query = '	SELECT count(*) as total
FROM #__joomleague_match_player
WHERE out = 1  
and teamplayer_id = ' . $player->playerid;
$this->_db->setQuery( $query );
$player->out = $this->_db->loadResult();
$this->_all_time_players[$player->pid]->out = $this->_all_time_players[$player->pid]->out + $player->out;

for($a=1; $a < 5; $a++ )
{
$query = '	SELECT count(*) as total
FROM #__joomleague_match_event
WHERE event_type_id = '.$a .' and teamplayer_id = ' . $player->playerid;
$this->_db->setQuery( $query );
$event_type_id = 'event_type_id_'.$a;
$player->$event_type_id = $this->_db->loadResult();
$this->_all_time_players[$player->pid]->$event_type_id = $this->_all_time_players[$player->pid]->$event_type_id + $player->$event_type_id;
}		
		
		
		
		}
		
		//return $this->_players;
		return $this->_all_time_players;
	}

	function getStaffList()
	{
		$projectteam =& $this->getprojectteam();
		$query='	SELECT	pr.firstname, 
							pr.nickname,
							pr.lastname,
							pr.country,
							pr.birthday,
							pr.deathday,
							ts.id AS ptid,
							ppos.position_id,
							ppos.id AS pposid,
							pr.id AS pid,
							pr.picture AS ppic,
							pos.name AS position,
							ts.picture,
							ts.notes AS description,
							ts.injury AS injury,
							ts.suspension AS suspension,
							ts.away AS away,
							pos.parent_id,
							posparent.name AS parentname,				
							CASE WHEN CHAR_LENGTH(pr.alias) THEN CONCAT_WS(\':\',pr.id,pr.alias) ELSE pr.id END AS slug
					FROM #__joomleague_team_staff ts
					INNER JOIN #__joomleague_person AS pr ON ts.person_id=pr.id
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id
					INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					LEFT JOIN #__joomleague_position AS posparent ON pos.parent_id=posparent.id
					WHERE ts.projectteam_id='.$this->_db->Quote($projectteam->id).'
					  AND pr.published = 1
					  AND ts.published = 1
					ORDER BY pos.parent_id, pos.ordering';
		$this->_db->setQuery($query);
		$stafflist=$this->_db->loadObjectList();
		return $stafflist;
	}

	
	

	

	

	

	

	

}
?>