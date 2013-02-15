<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
require_once(JPATH_COMPONENT.DS.'models'.DS.'list.php');

/**
 * Joomleague Component Matches Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueModeljlextindividualsportes extends JoomleagueModelList
{
	var $_identifier = "jlextindividualsportes";

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
		$match_id = $mainframe->getUserState($option . 'match_id');

		$where[] = ' mc.round_id = ' . $round_id;
		$where[] = ' mc.match_id = ' . $match_id;
		if ($division>0)
		{
			$where[]=' divhome.id = '.$this->_db->Quote($division);
		}
		$where=(count($where) ? ' WHERE '.implode(' AND ',$where) : '');
		
		return $where;
	}

	/**
	 * Method to return the project teams array (id, name)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getProjectTeams()
	{
		$option='com_joomleague';

		$mainframe	=& JFactory::getApplication();
		$project_id = $mainframe->getUserState($option . 'project');

		$query = '	SELECT	pt.id AS value,
							t.name AS text,
							t.short_name AS short_name,
							t.notes

					FROM #__joomleague_team AS t
					LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id
					WHERE pt.project_id = ' . $project_id . '
					ORDER BY text ASC ';

		$this->_db->setQuery($query);

		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * @param int iDivisionId
	 * return project teams as options
	 * @return unknown_type
	 */
	function getProjectTeamsOptions($iDivisionId=0)
	{
		$option='com_joomleague';

		$mainframe	=& JFactory::getApplication();
		$project_id = $mainframe->getUserState($option . 'project');

		$query = ' SELECT	pt.id AS value, '
		. ' CASE WHEN CHAR_LENGTH(t.name) < 25 THEN t.name ELSE t.middle_name END AS text '
		. ' FROM #__joomleague_team AS t '
		. ' LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id '
		. ' WHERE pt.project_id = ' . $project_id;
		if($iDivisionId>0)  {
			$query .=' AND pt.division_id = ' .$iDivisionId;
		}
		$query .= ' ORDER BY text ASC ';

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result === FALSE)
		{
			JError::raiseError(0, $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return $result;
		}
	}

	function getMatchesByRound($roundId)
	{
		$query = 'SELECT * FROM #__joomleague_match_single WHERE round_id='.$roundId;
		$this->_db->setQuery($query);
		//echo($this->_db->getQuery());
		$result = $this->_db->loadObjectList();
		if ($result === FALSE)
		{
			JError::raiseError(0, $this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}
	
	
	function getHomePlayer()
	{
  $option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
//  $projectteam_id	= JRequest::getCmd('team1');
  $projectteam1_id		= $mainframe->getUserState( $option . 'projectteam1_id' );
  $query='SELECT tpl.id AS value,
							
							concat(pl.firstname,
							pl.nickname,
							pl.lastname) as text
							
					FROM #__joomleague_person AS pl
					INNER JOIN #__joomleague_team_player AS tpl ON tpl.person_id=pl.id
					
					WHERE tpl.projectteam_id='. $this->_db->Quote($projectteam1_id).
					'AND pl.published = 1';
		$query .= " ORDER BY  pl.lastname ASC ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
  
  }

	function getAwayPlayer()
	{
 	$option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
//  $projectteam_id	= JRequest::getCmd('team2');
  $projectteam2_id		= $mainframe->getUserState( $option . 'projectteam2_id' );
  $query='SELECT tpl.id AS value,
							
							concat(pl.firstname,
							pl.nickname,
							pl.lastname) as text
							
					FROM #__joomleague_person AS pl
					INNER JOIN #__joomleague_team_player AS tpl ON tpl.person_id=pl.id
					
					WHERE tpl.projectteam_id='. $this->_db->Quote($projectteam2_id).
					'AND pl.published = 1';
		$query .= " ORDER BY pl.lastname ASC ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
  
  }
  
  function getSportType($id)
  {
  $option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
  $query='SELECT name
					FROM #__joomleague_sports_type
					WHERE id='. $this->_db->Quote($id);
		$this->_db->setQuery($query);
		$sporttype = $this->_db->loadResult();
		$mainframe->setUserState($option.'sporttype',$sporttype);
		$mainframe->enqueueMessage(JText::_('Sporttype: '.$sporttype ),'');
		
		switch ( strtolower($sporttype) )
		{
    case 'ringen':
    $this->_getSinglefile();
    break;
    }
		
		
		return $sporttype;
		
  }
  
  function _getSinglefile()
  {
  $option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
	
	$match_id		= $mainframe->getUserState( $option . 'match_id' );
	$query='SELECT match_number
					FROM #__joomleague_match
					WHERE id='. $this->_db->Quote($match_id);
		$this->_db->setQuery($query);
		$match_number = $this->_db->loadResult();
	
	$dir = JPATH_SITE.DS.'tmp'.DS.'ringerdateien';
  $files = JFolder::files($dir, '^MKEinzelkaempfe_Data_'.$match_number, false, false, array('^Termine_Schema') );
  
  $mainframe->enqueueMessage(JText::_('_getSinglefile: '.print_r($files,true) ),'');
  
  if ( $files )
  {
  $mainframe->enqueueMessage(JText::_('Einzelk&auml;mpfe '.$match_number.' vorhanden' ),'Notice');
  }
  else
  {
  $mainframe->enqueueMessage(JText::_('Einzelk&auml;mpfe '.$match_number.' nicht vorhanden' ),'Error');
  }
  
  }

}
?>