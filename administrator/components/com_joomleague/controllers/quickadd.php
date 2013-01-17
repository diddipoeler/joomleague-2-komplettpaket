<?php defined( '_JEXEC' ) or die( 'Restricted access' ); // Check to ensure this file is included in Joomla!
/**
 * @copyright	Copyright (C) 2007 Joomteam.de. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Joomleague Component Controller
 *
 * @package	 joomleague
 * @since 1.5
 */
class JoomleagueControllerQuickAdd extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'searchplayer',	'searchPlayer' );
		$this->registerTask( 'searchstaff',		'searchStaff' );
		$this->registerTask( 'searchreferee',	'searchReferee' );
		$this->registerTask( 'searchteam',		'searchTeam' );
		$this->registerTask( 'addplayer', 		'addPlayer' );
		$this->registerTask( 'addstaff', 		'addstaff' );
		$this->registerTask( 'addreferee', 		'addReferee' );
		$this->registerTask( 'addteam', 		'addTeam' );
	}

	function searchPlayer()
	{
		$model 			= &JLGModel::getInstance('Quickadd', 'JoomleagueModel');
		$query 			= JRequest::getVar("query", "", "", "string");
		$projectteam_id = JRequest::getInt("projectteam_id");
		$results 		= $model->getNotAssignedPlayers($query, $projectteam_id);
		$response = array(
			"totalCount" => count($results),
			"rows" => array()
		);

		foreach ($results as $row) {
			$name = JoomleagueHelper::formatName(null,$row->firstname, $row->nickname, $row->lastname, 0) . " (" . $row->id . ")";
			$response["rows"][] = array(
				"id" => $row->id,
				"name" => $name
			);
		}
		echo json_encode($response);
		exit;
	}

	function searchStaff()
	{
		$model 			= &JLGModel::getInstance('Quickadd', 'JoomleagueModel');
		$query 			= JRequest::getVar("query", "", "", "string");
		$projectteam_id = JRequest::getInt("projectteam_id");
		$results 		= $model->getNotAssignedStaff($query, $projectteam_id);
		$response = array(
			"totalCount" => count($results),
			"rows" => array()
		);

		foreach ($results as $row) {
			$name = JoomleagueHelper::formatName(null, $row->firstname, $row->nickname, $row->lastname, 0) . " (" . $row->id . ")";
			$response["rows"][] = array(
				"id" => $row->id,
				"name" => $name
			);
		}

		echo json_encode($response);
		exit;
	}

	function searchReferee()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$model 		= &JLGModel::getInstance('Quickadd', 'JoomleagueModel');
		$query 		= JRequest::getVar("query", "", "", "string");
		$projectid 	= $mainframe->getUserState($option."project");
		$results 	= $model->getNotAssignedReferees($query, $projectid);
		$response = array(
			"totalCount" => count($results),
			"rows" => array()
		);

		foreach ($results as $row) {
			$name = JoomleagueHelper::formatName(null, $row->firstname, $row->nickname, $row->lastname, 0) . " (" . $row->id . ")";
			$response["rows"][] = array(
				"id" => $row->id,
				"name" => $name
			);
		}

		echo json_encode($response);
		exit;
	}

	function searchTeam()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$model 		= &JLGModel::getInstance('Quickadd', 'JoomleagueModel');
		$query 		= JRequest::getVar("query", "", "", "string");
		$projectid 	= $mainframe->getUserState($option."project");
		$results 	= $model->getNotAssignedTeams($query, $projectid);
		$response = array(
			"totalCount" => count($results),
			"rows" => array()
		);

		foreach ($results as $row) {
			$name = $row->name;
			$name .= " (" . $row->info . ")";
			$name .= " (" . $row->id . ")";

			$response["rows"][] = array(
				"id" => $row->id,
				"name" => $name
			);
		}

		echo json_encode($response);
		exit;
	}

	function addPlayer()
	{
		$personid = JRequest::getInt("cpersonid", 0);
		$name = JRequest::getVar("quickadd", '', 'request', 'string');
		$projectteam_id = JRequest::getInt("projectteam_id");

		$model = $this->getModel('quickadd');
		$res = $model->addPlayer($projectteam_id, $personid, $name);
		
		if ($res) {
			$msgtype = 'message';
			$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED');
		} else {
			$msgtype = 'error';
			$msg = $model->getError();
		}

		$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display&project_team_id=".$projectteam_id, $msg);
	}

	function addStaff()
	{
		$db = &JFactory::getDBO();
		$personid = JRequest::getInt("cpersonid", 0);
		$name = JRequest::getVar("quickadd", '', 'request', 'string');
		$projectteam_id = JRequest::getInt("projectteam_id", 0);

		// add the new individual as their name was sent through.
		if (!$personid)
		{
			$model = &JLGModel::getInstance('Person', 'JoomleagueModel');
			$name = explode(" ", $name);
			$firstname = ucfirst(array_shift($name));
			$lastname = ucfirst(implode(" ", $name));
			$data = array(
				"firstname" => $firstname,
				"lastname" => $lastname,
			);
			$model->store($data);
			$personid = $model->getDbo()->insertid();
		}

		if (!$personid) {
			$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED');
			$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&task=teamstaff.display&project_team_id=".$projectteam_id, $msg, 'error');
		}

		// check if indivual belongs to project
		$query = ' SELECT person_id FROM #__joomleague_team_staff '
		. ' WHERE projectteam_id = '. $db->Quote($projectteam_id)
		. '   AND person_id = '. $db->Quote($personid)
		;
		$db->setQuery($query);
		$res = $db->loadResult();
		if (!$res)
		{
			$tblTeamstaff =& JTable::getInstance('Teamstaff','Table');
			$tblTeamstaff->person_id = $personid;
			$tblTeamstaff->projectteam_id = $projectteam_id;
			
			$tblProjectTeam =& JTable::getInstance( 'Projectteam', 'Table' );
			$tblProjectTeam->load($projectteam_id);
			
			if (!$tblTeamstaff->check())
			{
				$this->setError($tblTeamstaff->getError());
			}
			//Get data from person
			$query = "	SELECT picture, position_id
						FROM #__joomleague_person AS pl
						WHERE pl.id=". $db->Quote($personid)."
						AND pl.published = 1";
			$db->setQuery( $query );
			$person = $db->loadObject();
			if ( $person )
			{
				$query = "SELECT id FROM #__joomleague_project_position "; 
				$query.= " WHERE position_id = " . $db->Quote($person->position_id);
				$query.= " AND project_id = " . $db->Quote($tblProjectTeam->project_id);
				$db->setQuery($query);
				if ($resPrjPosition = $db->loadObject())
				{
					$tblTeamstaff->project_position_id = $resPrjPosition->id;	
				}
				
				$tblTeamstaff->picture			= $person->picture;
				$tblTeamstaff->projectteam_id	= $projectteam_id;
				
			}
			$query = "	SELECT max(ordering) count
						FROM #__joomleague_team_staff";
			$db->setQuery( $query );
			$ts = $db->loadObject();
			$tblTeamstaff->ordering = (int) $ts->count + 1;
			if (!$tblTeamstaff->store())
			{
				$this->setError($tblTeamstaff->getError());
			}
		}
		$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED');
		$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&task=teamstaff.display&project_team_id=".$projectteam_id, $msg);
	}

	function addReferee()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();

		$db = &JFactory::getDBO();
		$personid = JRequest::getInt("cpersonid", 0);
		$name = JRequest::getVar("quickadd", '', 'request', 'string');
		$project_id = $mainframe->getUserState($option."project");
		
		// add the new individual as their name was sent through.
		if (!$personid)
		{
			$model = &JLGModel::getInstance('Person', 'JoomleagueModel');
			$name = explode(" ", $name);
			$firstname = ucfirst(array_shift($name));
			$lastname = ucfirst(implode(" ", $name));
			$data = array(
				"firstname" => $firstname,
				"lastname" => $lastname,
			);
			$model->store($data);
			$personid = $model->getDbo()->insertid();
		}

		if (!$personid) {
			$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED');
			$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display&projectid=".$project_id, $msg, 'error');
		}

		// check if indivual belongs to project
		$query = ' SELECT person_id FROM #__joomleague_project_referee '
		. ' WHERE project_id = '. $db->Quote($project_id)
		. '   AND person_id = '. $db->Quote($personid)
		;
		$db->setQuery($query);
		$res = $db->loadResult();
		if (!$res)
		{
				$tblProjectReferee =& JTable::getInstance('Projectreferee','Table');
				$tblProjectReferee->person_id=$personid;
				$tblProjectReferee->projectteam_id=$projectteam_id;
				
				if (!$tblProjectReferee->check())
				{
					$this->setError($tblProjectReferee->getError());
				}
				//Get data from person
				$query = "	SELECT picture, position_id
							FROM #__joomleague_person AS pl
							WHERE pl.id=". $db->Quote($personid)."
							AND pl.published = 1";
				$db->setQuery( $query );
				$person = $db->loadObject();
				if ( $person )
				{
					$query = "SELECT id FROM #__joomleague_project_position "; 
					$query.= " WHERE position_id = " . $db->Quote($person->position_id);
					$query.= " AND project_id = " . $db->Quote($project_id);
					$db->setQuery($query);
					if ($resPrjPosition = $db->loadObject())
					{
						$tblProjectReferee->project_position_id = $resPrjPosition->id;	
					}
					
					$tblProjectReferee->picture			= $person->picture;
					$tblProjectReferee->project_id		= $project_id;
					
				}
				$query = "	SELECT max(ordering) count
							FROM #__joomleague_project_referee";
				$db->setQuery( $query );
				$pref = $db->loadObject();
				$tblProjectReferee->ordering = (int) $pref->count + 1;
					
				if (!$tblProjectReferee->store())
				{
					$this->setError($tblProjectReferee->getError());
				}
		}
		$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED');
		$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display&projectid=".$project_id, $msg);
	}

	function addTeam()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();

		$db = &JFactory::getDBO();
		$teamid = JRequest::getInt("cteamid", 0);
		$name = JRequest::getVar("quickadd", '', 'request', 'string');
		$project_id = $mainframe->getUserState($option."project");

		// add the new team as their name was sent through.
		if (!$teamid)
		{
			$model = &JLGModel::getInstance('Team', 'JoomleagueModel');
			$data = array(
				"name" => $name
			);
			$model->store($data);
			$teamid = $model->getDbo()->insertid();
		}

		if (!$teamid) {
			$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_ERROR_TEAM');
			$this->setRedirect("index.php?option=com_joomleague&view=projectteams&task=projectteam.display&projectid=".$project_id, $msg, 'error');
		}

		// check if team belongs to project
		$query = ' SELECT id FROM #__joomleague_project_team '
		. ' WHERE project_id = '. $db->Quote($project_id)
		. '   AND team_id = '. $db->Quote($teamid)
		;
		$db->setQuery($query);
		$res = $db->loadResult();
		if (!$res)
		{
			$new =& JTable::getInstance( 'Projectteam', 'Table' );
			$new->team_id		= $teamid;
			$new->project_id	= $project_id;

			if ( !$new->check() )
			{
				$this->setError( $new->getError() );
			}
			// Get data from player
			$query = "	SELECT picture
						FROM #__joomleague_team AS t
						WHERE t.id=". $db->Quote($teamid);

			$db->setQuery( $query );
			$team = $db->loadObject();
			if ( $team )
			{
				$new->picture		= $team->picture;
			}
			if ( !$new->store() )
			{
	  			$this->setError( $new->getError() );
			}
		}
		$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_TEAM_ASSIGNED');
		$this->setRedirect("index.php?option=com_joomleague&view=projectteams&task=projectteam.display&projectid=".$project_id, $msg);
	}
}
?>
