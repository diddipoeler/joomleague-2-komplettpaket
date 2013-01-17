<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelEditMatch extends JoomleagueModelProject
{
	var $projectid = 0;
	var $matchid = 0;
	var $match = null;

	function __construct()
	{
		parent::__construct();

		$this->projectid = JRequest::getInt( 'p', 0 );
		$this->matchid = JRequest::getInt( 'mid', 0 );
	}

	function getMatch()
	{
		if ( is_null( $this->match  ) )
		{
			$this->match = $this->getTable( 'Match', 'Table' );
			$this->match->load( $this->matchid );
		}
		return $this->match;
	}

	function isAllowed()
	{
		$allowed = false;
		$user = JFactory::getUser();

		if ($user->id != 0)
		{
			$project =& $this->getProject();

			if(($user->authorise('editmatch.display', 'com_joomleague')) &&
			   (($user->id == $project->admin) || ($user->id == $project->editor)))
			{
				$allowed = true;
			}
		}
		return $allowed;
	}

	function isMatchAdmin( $matchid, $userid )
	{
		$query = '	SELECT count(*)
					FROM #__joomleague_match AS m
						INNER JOIN #__joomleague_project_team AS tt1 ON m.projectteam1_id = tt1.id
						INNER JOIN #__joomleague_project_team AS tt2 ON m.projectteam2_id = tt2.id
						WHERE	m.id=' . $matchid . ' AND
								(	tt1.admin = '. $userid . ' OR
									tt2.admin = ' . $userid . ')';
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadResult())
		{
			return false;
		}
		return true;
	}

	/**
	* Save match details from modal window
	*
	* @param array $data
	* @return boolean
	*/
	function savedetails( $data )
	{
		if ( !$data['mid'] )
		{
			$this->setError( JText::_( 'MATCH ID IS NULL' ) );
			return false;
		}

		$object =& $this->getTable('match');
		if ( !$object->load( $data['mid'] ) )
		{
			$this->setError( JText::_( 'GAME NOT FOUND' ) );
			return false;
		}
		$object->bind( $data );

		echo 'Step1';
		if ( !$object->check() )
		{
			$this->setError( JText::_( 'CHECK FAILED' ) );
			return false;
		}

		echo 'Step2';
		if ( !$object->store('match') )
		{
			$this->setError( JText::_( 'STORE FAILED' ) );
			return false;
		}
		echo 'Step3';
		return true;
	}

	function getMatchRelationsOptions($project_id, $exclududeMatchId=0)
	{
		$query = "SELECT	m.id AS value,
							CONCAT('(', m.match_date, ') - ', t1.name, ' - ', t2.name) AS text
							FROM #__joomleague_match AS m
							INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id = pt1.id
							INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id = pt2.id
							INNER JOIN #__joomleague_team AS t1 ON pt1.team_id=t1.id
							INNER JOIN #__joomleague_team AS t2 ON pt2.team_id=t2.id
							WHERE pt1.project_id = ".$this->_db->Quote($project_id)."
							AND m.id NOT in (" . $exclududeMatchId . ")
							AND m.published = 1
							ORDER BY m.match_date DESC, t1.short_name"
							;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

}
?>