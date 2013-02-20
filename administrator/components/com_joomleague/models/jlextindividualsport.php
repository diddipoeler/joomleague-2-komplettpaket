<?php
/**
 * @copyright	Copyright (C) 2006-2009 JoomLeague.net. All rights reserved.
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
//require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');
require_once(JPATH_COMPONENT.DS.'models'.DS.'list.php');

/**
 * Joomleague Component league Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	Joomleague
 * @since	0.1
 */
class JoomleagueModeljlextindividualsport extends JoomleagueModelList
{
	
/**
	 * Method to load content matchday data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_singledata))
		{
			$query=' SELECT	m.*,
							CASE m.time_present 
							when NULL then NULL
							else DATE_FORMAT(m.time_present, "%H:%i")
							END AS time_present,
							t1.name AS hometeam, t1.id AS t1id, 
							t2.name as awayteam, t2.id AS t2id,
							pt1.project_id,
							m.extended as matchextended
						FROM #__joomleague_match_single AS m
						INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id
						INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
						INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id
						INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
						WHERE m.id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_singledata=$this->_db->loadObject();
			return (boolean) $this->_singledata;
		}
		return true;
	}
  
  /**
	 * Method to initialise the match data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$match=new stdClass();
			$match->id						= 0;
			$match->round_id				= null;
			$match->match_number			= null;
			$match->projectteam1_id			= null;
			$match->projectteam2_id			= null;
			$match->playground_id			= null;
			$match->match_date				= null;
			$match->time_present			= null;
			$match->team1_result			= null;
			$match->team2_result			= null;
			$match->team1_bonus				= null;
			$match->team2_bonus				= null;
			$match->team1_legs				= null;
			$match->team2_legs				= null;
			$match->team1_result_split		= null;
			$match->team2_result_split		= null;
			$match->match_result_type		= 0;
			$match->team1_result_ot			= null;
			$match->team2_result_ot			= null;
			$match->team1_result_so			= null;
			$match->team2_result_so			= null;
			$match->alt_decision			= null;
			$match->decision_info			= null;
			$match->team1_result_decision	= null;
			$match->team2_result_decision	= null;
			$match->match_state				= null;
			$match->count_result			= null;
			$match->crowd					= null;
			$match->summary					= null;
			$match->show_report				= null;
			$match->preview					= null;
			$match->match_result_detail		= null;
			$match->new_matchid				= null;
			$match->extended				= null;
			$match->published				= 0;
			$match->checked_out				= 0;
			$match->checked_out_time		= 0;
			$match->old_match_id			= 0;
			$match->new_match_id			= 0;
			$match->team_won 				= 0;
			$match->modified				= null;
			$match->modified_by				= null;
			$this->_singledata				= $match;
			return (boolean) $this->_singledata;
		}
		return true;
	}
  
  /**
	 * Method to load content matchday data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function getMatchData($mid)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query=' SELECT	m.*,
							CASE m.time_present
							when NULL then NULL
							else DATE_FORMAT(m.time_present, "%H:%i")
							END AS time_present,
							t1.name AS hometeam, t1.id AS t1id,
							t2.name as awayteam, t2.id AS t2id,
							pt1.project_id,
							m.extended as matchextended
						FROM #__joomleague_match AS m
						INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id
						INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
						INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id
						INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
						WHERE m.id='.(int) $mid;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}
  
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = '	SELECT	mc.*, 
						CASE mc.time_present 
						when "00:00:00" then NULL
						else DATE_FORMAT(mc.time_present, "%H:%i")
						END AS time_present, IFNULL(divhome.shortname, divhome.name) divhome, 
						divhome.id divhomeid,
						divaway.id divawayid,
						t1.name AS team1,
							t2.name AS team2,
							u.name AS editor, 
							(Select count(mp.id) 
							 FROM #__joomleague_match_player AS mp 
							 WHERE mp.match_id = mc.id
							   AND (came_in=0 OR came_in=1) 
							   AND mp.teamplayer_id in (
							     SELECT id 
							     FROM #__joomleague_team_player AS tp
							     WHERE tp.projectteam_id = mc.projectteam1_id
							   )
							 ) AS homeplayers_count, 
							(Select count(ms.id) 
							 FROM #__joomleague_match_staff AS ms
							 WHERE ms.match_id = mc.id
							   AND ms.team_staff_id in (
							     SELECT id 
							     FROM #__joomleague_team_staff AS ts
							     WHERE ts.projectteam_id = mc.projectteam1_id
							   )
							) AS homestaff_count, 
							(Select count(mp.id) 
							 FROM #__joomleague_match_player AS mp 
							 WHERE mp.match_id = mc.id
							   AND (came_in=0 OR came_in=1) 
							   AND mp.teamplayer_id in (
							     SELECT id 
							     FROM #__joomleague_team_player AS tp
							     WHERE tp.projectteam_id = mc.projectteam2_id
							   )
							 ) AS awayplayers_count, 
							(Select count(ms.id) 
							 FROM #__joomleague_match_staff AS ms
							 WHERE ms.match_id = mc.id
							   AND ms.team_staff_id in (
							     SELECT id 
							     FROM #__joomleague_team_staff AS ts
							     WHERE ts.projectteam_id = mc.projectteam2_id
							   )
							) AS awaystaff_count,
							(Select count(mr.id) 
							  FROM #__joomleague_match_referee AS mr 
							  WHERE mr.match_id = mc.id
							) AS referees_count 
					FROM #__joomleague_match_single AS mc
					LEFT JOIN #__users u ON u.id = mc.checked_out
					LEFT JOIN #__joomleague_project_team AS pthome ON pthome.id = mc.projectteam1_id
					LEFT JOIN #__joomleague_project_team AS ptaway ON ptaway.id = mc.projectteam2_id
					LEFT JOIN #__joomleague_team AS t1 ON t1.id = pthome.id
					LEFT JOIN #__joomleague_team AS t2 ON t2.id = ptaway.id
					LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id 
					LEFT JOIN #__joomleague_division AS divaway ON divaway.id = ptaway.division_id 
					LEFT JOIN #__joomleague_division AS divhome ON divhome.id = pthome.division_id ' .
		
		$where . $orderby;
		return $query;
	}

	function _buildContentOrderBy()
	{
		$option='com_joomleague';

		$mainframe	=& JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest($option . 'mc_filter_order', 'filter_order', 'mc.match_date', 'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option . 'mc_filter_order_Dir', 'filter_order_Dir', '', 'word');

		if ($filter_order == 'mc.match_number')
		{
			$orderby    = ' ORDER BY mc.match_number +0 '. $filter_order_Dir .', divhome.id, divaway.id ' ;
		}
		elseif ($filter_order == 'mc.match_date')
		{
			$orderby 	= ' ORDER BY mc.match_date '. $filter_order_Dir .', divhome.id, divaway.id ';
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , mc.match_date, divhome.id, divaway.id';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$option='com_joomleague';
		$where=array();
		
		$mainframe	=& JFactory::getApplication();
		// $project_id = $mainframe->getUserState($option . 'project');
		$division	= (int) $mainframe->getUserStateFromRequest($option.'mc_division', 'division', 0);
		$round_id = $mainframe->getUserState($option . 'round_id');

		$where[] = ' mc.round_id = ' . $round_id;
		if ($division>0)
		{
			$where[]=' divhome.id = '.$this->_db->Quote($division);
		}
		$where=(count($where) ? ' WHERE '.implode(' AND ',$where) : '');
		
		return $where;
	}  
  
  	
function HomeTeamPlayer()
{
$projectteam1_id = JRequest::getCmd('team1');
$query=' SELECT	tp.id as value,
concat(pe.firstname,pe.lastname) AS text
						FROM #__joomleague_team_player AS tp
						inner join #__joomleague_person AS pe
            on pe.id = tp.person_id 
						WHERE tp.projectteam_id = '.(int) $projectteam1_id;
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
			
}

function AwayTeamPlayer()
{
$projectteam2_id = JRequest::getCmd('team2');
$query=' SELECT	tp.id as value,
concat(pe.firstname,pe.lastname) AS text
						FROM #__joomleague_team_player AS tp
						inner join #__joomleague_person AS pe
            on pe.id = tp.person_id 
						WHERE tp.projectteam_id = '.(int) $projectteam2_id;
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
			
}
	
/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'matchsingle', $prefix = 'table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}	

  /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
}
?>