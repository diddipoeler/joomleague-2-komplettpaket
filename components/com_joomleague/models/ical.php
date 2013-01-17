<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelIcal extends JoomleagueModelProject
{
	var $projectid	= 0;
	var $teamid	= 0;
	var $divisionid	= 0;
	var $playgroundid = 0;

	function __construct()
	{
		parent::__construct();

		$this->projectid	= JRequest::getInt( 'p',	0 );
		$this->teamid		= JRequest::getInt( 'teamid',	0 );
		$this->divisionid	= JRequest::getInt( 'division',	0 );
		$this->playgroundid	= JRequest::getInt( 'pgid',	0 );
	}



	function getMatches($config)
	{


		$ordering = 'ASC';
		
		return $this->_getResultsPlan(	$this->teamid,
		$this->divisionid,
		$this->playgroundid,
		$config['show_referee'],
		$ordering
		);
	}


	function _getResultsPlan($team = 0,$division=0, $playground = 0, $getreferee = 0,$ordering = 'ASC')
	{
		$matches = array();
	


		$query_SELECT = ' SELECT m.projectteam1_id,
		m.projectteam2_id,
		m.match_date,
		t1.id AS team1, t2.id AS team2, r.roundcode, r.name,  
		r.project_id, '
		. 'plcd.id AS club_playground_id,
			     plcd.name AS club_playground_name,
			     pltd.id AS team_playground_id, 
			     pltd.name AS team_playground_name, 
			     pl.id AS playground_id, 
			     pl.name AS playground_name,
				 plcd.address AS club_playground_address, 
			     plcd.zipcode AS club_playground_zipcode,
			     plcd.city AS club_playground_city,
			     pltd.address AS team_playground_address, 
			     pltd.zipcode AS team_playground_zipcode,
			     pltd.city AS team_playground_city,
			     pl.address AS playground_address, 
			     pl.zipcode AS playground_zipcode,
			     pl.city AS playground_city';

		$query_FROM   = ' FROM #__joomleague_match AS m '
		. ' INNER JOIN #__joomleague_round r ON m.round_id = r.id '
		. ' INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id = pt1.id '
		. ' INNER JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id '
		. ' INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id = pt2.id '
		. ' INNER JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id '
		. ' INNER JOIN #__joomleague_project AS p ON p.id = r.project_id '
		. ' INNER JOIN #__joomleague_club c ON c.id = t1.club_id ';

		$query_FROM .= ' LEFT JOIN #__joomleague_playground AS pl ON pl.id = m.playground_id '
		.  ' LEFT JOIN #__joomleague_playground AS plcd ON c.standard_playground = plcd.id '
		.  ' LEFT JOIN #__joomleague_playground AS pltd ON pt1.standard_playground = pltd.id ';

		$query_WHERE = ' WHERE m.published = 1 AND p.published = 1';


		if ($this->projectid !=0)
		{
			$query_WHERE .= " AND r.project_id = '".$this->projectid."'";
		}
		if ($this->teamid != 0)
		{
			$query_WHERE .= " AND (t1.id = ".$this->teamid." OR t2.id = ".$this->teamid.")";
		}
		if ($this->playgroundid !=0)
		{
			
			$query_WHERE .= ' AND ( m.playground_id = "'. $this->playgroundid .'"
                          OR (pt1.standard_playground = "'. $this->playgroundid .'" AND m.playground_id IS NULL)
                          OR (c.standard_playground = "'. $this->playgroundid .'" AND (m.playground_id IS NULL AND pt1.standard_playground IS NULL  )))
                      AND m.match_date > NOW() ';
		}
		
		$query_END = " GROUP BY m.id";
		$query_END .=" ORDER BY r.roundcode ".$ordering.", m.match_date, m.match_number";


		$query = $query_SELECT.$query_FROM.$query_WHERE.$query_END;

		
		$this->_db->setQuery( $query );

		$matches = $this->_db->loadObjectList();


		return $matches;
	}


}
?>