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
require_once (JLG_PATH_ADMIN.DS.'models'.DS.'list.php');

/**
 * Joomleague Component Matchday Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueModelRounds extends JoomleagueModelList
{
	var $_identifier = "rounds";
	var $_project_id;
	
	function __construct()
	{
		parent::__construct();
		
		$option     = 'com_joomleague';
		$mainframe	= &JFactory::getApplication();
		$params     = &JComponentHelper::getParams('com_joomleague');
		$defaultorder_dir = $params->get('cfg_be_show_matchdays_order', '');
		
		$filter_order		    = $mainframe->getUserStateFromRequest( $option . 'rounds_filter_order', 'filter_order', 'r.roundcode', 'cmd' );
		$filter_order_Dir 	= $mainframe->getUserStateFromRequest( $option . 'rounds__filter_order_Dir', 'filter_order_Dir', $defaultorder_dir, 'word' );
		
		$this->setState('filter_order',     $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
	}
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where=$this->_buildContentWhere();
		$orderby=$this->_buildContentOrderBy();
		$query='	SELECT	r.*,
							u.name AS editor,

							(select count(published) FROM #__joomleague_match
					 		WHERE round_id=r.id and published=0) countUnPublished,

							(select count(*) FROM #__joomleague_match
					 		WHERE round_id=r.id
							AND cancel=0 AND (team1_result is null OR team2_result is null)) countNoResults,

							(select count(*) FROM #__joomleague_match
					 		WHERE round_id=r.id) countMatches

					FROM #__joomleague_round AS r
					LEFT JOIN #__users u ON u.id=r.checked_out ' .
					$where.$orderby;
					//ORDER BY r.roundcode '. $roundorder. ',r.round_date_first ';
		return $query;
	}

	function _buildContentOrderBy()
	{		
		$filter_order		    = $this->getState('filter_order');
		$filter_order_Dir 	= $this->getState('filter_order_Dir');

		$orderby = ' ORDER BY ' . $filter_order .' '. $filter_order_Dir . ', r.round_date_first ';

		return $orderby;
	}

	
	function _buildContentWhere()
	{
		//$option = JRequest::getCmd('option');
		//$mainframe = JFactory::getApplication();
		//$project_id=$mainframe->getUserState($option.'project');
		$where=' WHERE  r.project_id='.$this->_project_id;
//		$where=' WHERE  r.project_id= 2';
		return $where;
	}

	function setProjectId($project_id) {
		$this->_project_id = $project_id;
	}
	
	function getProjectId() {
		return $this->_project_id;
	}
	
	/**
	* Method to return the project teams array (id,name)
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getProjectTeams()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query="	SELECT	t.id AS value,
							t.name As text,
							t.notes, pt.id AS projectteam_id
					FROM #__joomleague_team AS t
					LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id=t.id
					WHERE pt.project_id=$project_id
					ORDER BY text ASC ";

		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}
	
	
	/**
	 * 
	 * @param int $projectid
	 * @return assocarray
	 */
	function getFirstRound($projectid) {
		$query="	SELECT	id, roundcode
					FROM #__joomleague_round
					WHERE project_id=".$projectid."
					ORDER BY roundcode ASC, id ASC ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadAssocList ())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result[0];
	}
	
	/**
	 * 
	 * @param int $projectid
	 * @return assocarray
	 */
	function getLastRound($projectid) {
		$query="	SELECT	id, roundcode
					FROM #__joomleague_round
					WHERE project_id=".$projectid."
					ORDER BY roundcode DESC, id DESC ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadAssocList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result[0];
	}
	
	/**
	 * 
	 * @param int $roundid
	 * @param int $projectid
	 * @return assocarray
	 */
	function getNextRound($roundid, $projectid) {
		$query="	SELECT	id, roundcode
					FROM #__joomleague_round
					WHERE project_id=".$projectid."
					ORDER BY id ASC ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadAssocList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		for ($i=0,$n=count($result); $i < $n; $i++) {
			if($result[$i]['id']==$roundid) {
				if(isset($result[$i+1])) {
					return $result[$i+1];
				} else {
					return $result[$i];
				}
			}
		}
	}
	
	/**
	 * Get the next round by todays date
	 * @param int $project_id
	 * @return assocarray
	 */
	function getNextRoundByToday($projectid)
	{
		$query = ' SELECT r.id, r.roundcode, r.round_date_first , r.round_date_last '
		       . ' FROM #__joomleague_round AS r '
		       . ' WHERE project_id = '. $db->Quote($projectid)
		       . '   AND DATEDIFF(CURDATE(), DATE(r.round_date_first)) < 0'
		       . ' ORDER BY r.round_date_first ASC '
		            ;
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadAssocList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;		
	}
	
	/**
	 * 
	 * @param int $roundid
	 * @param int $projectid
	 * @return assocarray
	 */
	function getPreviousRound($roundid, $projectid) {
		$query="	SELECT	id, roundcode
					FROM #__joomleague_round
					WHERE project_id=".$projectid."
					ORDER BY id ASC ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadAssocList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		for ($i=0,$n=count($result); $i < $n; $i++) {
			if(isset($result[$i-1])) {
				return $result[$i-1];
			} else {
				return $result[$i];
			}
		}
	}
	
	/**
	 * return project rounds as array of objects(roundid as value, name as text)
	 *
	 * @param string $ordering
	 * @return array
	 */
	function getRoundsOptions($project_id, $ordering='ASC')
	{
		$query="SELECT
					id as value,
				    CASE LENGTH(name)
				    	when 0 then CONCAT('".JText::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAY_NAME'). "',' ', id)
				    	else name
				    END as text, id, name, round_date_first, round_date_last, roundcode 
				  FROM #__joomleague_round
				  WHERE project_id=".$project_id."
				  ORDER BY roundcode ".$ordering;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * return count of  project rounds
	 *
	 * @param int project_id
	 * @return int
	 */
	function getRoundsCount($project_id)
	{
		$query='SELECT count(*) AS count
				  FROM #__joomleague_round
				  WHERE project_id='.$project_id;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	/**
	 * Populate project with matchdays
	 * 
	 * @param int $scheduling scheduling type
	 * @param string $time start time for games
	 * @param int $interval interval between rounds
	 * @param string $start start date for new roundsrounds
	 * @param string $roundname round name format (use %d for round number)
	 * @return boolean true on success
	 */
	function populate($project_id, $scheduling, $time, $interval, $start, $roundname, $teamsorder = null)
	{		
		if (!strtotime($start)) {
			$start = strftime('%Y-%m-%d');
		}
		if (!preg_match("/^[0-9]+:[0-9]+$/", $time)) {
			$time = '20:00';
		}
		
		$teams = $this->getProjectTeams();
		
		if ($teamsorder)
		{
			$ordered = array();
			foreach ($teamsorder as $ptid) 
			{
				foreach ($teams as $t) 
				{
					if ($t->projectteam_id == $ptid) {
						$ordered[] = $t;
						break;
					}
				}
			}
			if (count($ordered)) {
				$teams = $ordered;
			}
		}
		
		if (!$teams || !count($teams)) {
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_NO_TEAM'));
			return false;
		}
		$rounds = $this->getData();
		$rounds = $rounds ? $rounds : array();
		
		if ($scheduling < 2)
		{
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'RRobin.class.php');
			$helper = new RRobin();
			$helper->create($teams);
			$schedule = $helper->getSchedule($scheduling+1);			
		}
		else
		{
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_UNDEFINED_SCHEDULING'));
			return false;			
		}
		
		$current_date = null;
		$current_code = 0;
		foreach ($schedule as $k => $games)
		{
			if (isset($rounds[$k])) // round exists
			{
				$round_id  = $rounds[$k]->id;
				$current_date = $rounds[$k]->round_date_first;
				$current_code = $rounds[$k]->roundcode;
			}
			else // create the round !
			{
				$round = JTable::getInstance('Round', 'Table');
				$round->project_id       = $project_id;
				$round->round_date_first = strtotime($current_date) ? strftime('%Y-%m-%d', strtotime($current_date) + $interval*24*3600) : $start;
				$round->round_date_last = $round->round_date_first;
				$round->roundcode = $current_code ? $current_code + 1 : 1;
				$round->name      = sprintf($roundname, $round->roundcode);
				if (!($round->check() && $round->store())) {
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_CREATING_ROUND').': '.$round->getError());
					return false;	
				}				
				$current_date = $round->round_date_first;
				$current_code = $round->roundcode;
				
				$round_id = $round->id;
			}
			
			// create games !
			foreach ($games as $g)
			{
				if (!isset($g[0]) || !isset($g[1])) { // happens if number of team is odd ! one team gets a by
					continue;
				}
				$game = JTable::getInstance('Match', 'Table');
				$game->round_id = $round_id;
				$game->projectteam1_id = $g[0]->projectteam_id;
				$game->projectteam2_id = $g[1]->projectteam_id;
				$game->published = 1;
				$game->match_date = $current_date.' '.$time;
				if (!($game->check() && $game->store())) {
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_CREATING_GAME').': '.$game->getError());
					return false;	
				}
			}
		}
		//echo '<pre>';print_r($schedule); echo '</pre>';exit;
		
		return true;
	}
}
?>
