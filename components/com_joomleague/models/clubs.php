<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelClubs extends JoomleagueModelProject
{
	var $projectid = 0;
	var $divisionid = 0;

	function __construct( )
	{
		parent::__construct( );

		$this->projectid = JRequest::getInt( "p", 0 );
		$this->divisionid = JRequest::getInt( "division", 0 );
	}

	function getDivision($id=0)
	{
		$division = null;
		if ($this->divisionid != 0)
		{
			$division = parent::getDivision($this->divisionid);
		}
		return $division;
	}

	function getClubs( $ordering = null)
	{
		$teams = array();
		$query = "SELECT c.*, '' as teams,
				CASE WHEN CHAR_LENGTH( c.alias ) THEN CONCAT_WS( ':', c.id, c.alias ) ELSE c.id END AS club_slug
				  FROM #__joomleague_club c
				  LEFT JOIN #__joomleague_team t ON t.club_id= c.id
				  LEFT JOIN #__joomleague_project_team pt ON pt.team_id = t.id ";
		if ($this->projectid > 0) {
			$query .= " WHERE pt.project_id = " . $this->projectid;
			if ($this->divisionid > 0) {
				$query .= " AND pt.division_id = " . $this->divisionid;
			}
		}
		$query .= " GROUP BY c.id ";
		$query .= " ORDER BY ";

		if ( $ordering )
		{
			$query .= $ordering;
		}
		else
		{
			$query .= "c.name";
		}
		$this->_db->setQuery($query);
		if ( ! $clubs = $this->_db->loadObjectList() )
		{
			echo $this->_db->getErrorMsg();
		}
		for ($index = 0; $index < count($clubs); $index++) {
			$teams = array();
			$query  = " SELECT t.*, t.picture AS team_picture, pt.picture AS projectteam_picture, "
				. " CASE WHEN CHAR_LENGTH( t.alias ) THEN CONCAT_WS( ':', t.id, t.alias ) ELSE t.id END AS team_slug "
				. " FROM #__joomleague_team t "
				. " LEFT JOIN #__joomleague_project_team pt ON pt.team_id = t.id "
				. " WHERE pt.project_id = " . $this->projectid;
			if ( $this->divisionid != 0 )
			{
				$query .= " AND pt.division_id = " . $this->divisionid;
			}
			$query .= " AND t.club_id = " . $clubs[$index]->id;
			$this->_db->setQuery($query);
			if ( ! $teams = $this->_db->loadObjectList() )
			{
				echo $this->_db->getErrorMsg();
			}
			$clubs[$index]->teams = $teams;
		}
		return $clubs;
	}

}
?>