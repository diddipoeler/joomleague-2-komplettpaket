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
require_once ('item.php');

/**
 * Joomleague Component sportstype Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelSportsType extends JoomleagueModelItem
{

	/**
	 * Method to load content sportstype data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query='SELECT * FROM #__joomleague_sports_type WHERE id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}
	
	/**
	*
	* get count of related projects for this sports_type
	*/
	public function getProjectsCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
							WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
	
  /**
	*
	* get count of related leagues 
	*/
	public function getLeaguesOnlyCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_league AS l 
							';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
  
  /**
	*
	* get count of related leagues 
	*/
	public function getPersonsOnlyCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_person AS c 
							';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
  
  /**
	*
	* get count of related leagues 
	*/
	public function getClubsOnlyCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_club AS c 
							';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
	
	/**
	*
	* get count of related leagues for this sports_type
	*/
	public function getLeaguesCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
							INNER JOIN #__joomleague_league AS l ON l.id = p.league_id
							WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
	
  /**
	*
	* get count of related seasons for this sports_type
	*/
	public function getSeasonsOnlyCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_season AS s 
							';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
  
	/**
	*
	* get count of related seasons for this sports_type
	*/
	public function getSeasonsCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
							INNER JOIN #__joomleague_season AS s ON s.id = p.season_id
							WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
	
	/**
	 * 
	 * get count of related projectteams for this sports_type
	 */
	public function getProjectTeamsCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
		 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
					INNER JOIN #__joomleague_project_team AS ptt ON ptt.project_id = p.id
					WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * 
	 * get count of related projectteamsplayers for this sports_type
	 */
	public function getProjectTeamsPlayersCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
		 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
					INNER JOIN #__joomleague_project_team AS ptt ON ptt.project_id = p.id
					INNER JOIN #__joomleague_team_player AS ptp ON ptp.projectteam_id = ptt.id
					WHERE st.id='.(int) $this->_id;
		
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
	
	/**
	*
	* get count of related projectdivisions for this sports_type
	*/
	public function getProjectDivisionsCount() {
	$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
			 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
						INNER JOIN #__joomleague_division AS d ON d.project_id = p.id
						WHERE st.id='.(int) $this->_id;
	
	$this->_db->setQuery($query);
	if (!$this->_db->query())
	{
	$this->setError($this->_db->getErrorMsg());
	return false;
	}
	return $this->_db->loadObject()->count;
	}

	/**
	 * 
	 * get count of related projectrounds for this sports_type
	 */
	public function getProjectRoundsCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
		 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
					INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
					WHERE st.id='.(int) $this->_id;
		
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * 
	 * get count of related projectmatches for this sports_type
	 */
	public function getProjectMatchesCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
		 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
					INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
					INNER JOIN #__joomleague_match AS m ON m.round_id = r.id
					WHERE st.id='.(int) $this->_id;
		
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}

	
   /**
	 * 
	 * get count of related projectmatches for this sports_type
	 */
	public function getProjectMatchesEventsNameCount() 
  {
	$query = 'SELECT count( me.id ) as total, me.event_type_id,p.sports_type_id,et.name,et.icon
FROM #__joomleague_match_event as me
INNER JOIN #__joomleague_match AS m 
ON me.match_id= m.id
INNER JOIN #__joomleague_round AS r 
ON m.round_id = r.id
INNER JOIN #__joomleague_project AS p 
ON r.project_id = p.id
INNER JOIN #__joomleague_eventtype AS et 
ON me.event_type_id = et.id
WHERE p.sports_type_id = '.(int) $this->_id.' GROUP BY me.event_type_id';
	$this->_db->setQuery($query);
			if (!$result = $this->_db->loadObjectList())
	    {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}
  /**
	 * 
	 * get count of related projectmatches for this sports_type
	 */
	public function getProjectMatchesEventsCount() {
	  
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
		 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
					INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
					INNER JOIN #__joomleague_match AS m ON m.round_id = r.id
					INNER JOIN #__joomleague_match_event AS me ON me.match_id = m.id
					WHERE st.id='.(int) $this->_id;
		
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * 
	 * get count of related projectmatches for this sports_type
	 */
	public function getProjectMatchesStatsCount() {
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
		 			INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
					INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
					INNER JOIN #__joomleague_match AS m ON m.round_id = r.id
					INNER JOIN #__joomleague_match_statistic AS ms ON ms.match_id = m.id
					WHERE st.id='.(int) $this->_id;
		
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->_db->loadObject()->count;
	}
}
?>