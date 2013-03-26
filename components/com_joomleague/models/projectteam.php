<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
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
require_once(JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component projectTeam Model
 *
 * @author	Marco Vaninetti <martizva@libero.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelProjectteam extends JoomleagueModelItem
{

	/**
	 * Method to load content team data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = '	SELECT	pt.*,
								t.name AS name

						FROM #__joomleague_project_team AS pt
						LEFT JOIN #__joomleague_team AS t ON pt.team_id=t.id
						WHERE pt.id='. (int) $this->_id;

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the team data
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
			$team = new stdClass();
			$team->id					= 0;
			$team->project_id			= null;
			$team->team_id		 		= null;
			$team->division_id			= null;
			$team->start_points			= null;
			$team->points_finally		= 0;
			$team->neg_points_finally 	= 0;
			$team->matches_finally		= 0;
			$team->won_finally			= 0;
			$team->draws_finally		= 0;
			$team->lost_finally			= 0;
			$team->homegoals_finally	= 0;
			$team->guestgoals_finally	= 0;
			$team->diffgoals_finally	= 0;
			$team->is_in_score			= 1;
			$team->use_finally			= 0;
			$team->admin				= null;
			$team->info					= null;
			$team->notes				= null;
			$team->standard_playground	= null;
			$team->picture 				= null;
			$team->checked_out			= 0;
			$team->checked_out_time		= 0;
			$team->ordering				= 0;
			$team->name 				= null;
			$team->reason				= null;
			$team->extended				= null;
			$team->modified				= null;
			$team->modified_by			= null;
			$this->_data				= $team;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	* Method to return a playgrounds array (id, name)
	 	*
	 	* @access	public
	 	* @return	array
	 	* @since 0.1
	 	*/
	function getPlaygrounds()
	{

		$query = 'SELECT id AS value, name AS text FROM #__joomleague_playground ORDER BY text ASC ';

		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
				$this->setError($this->_db->getErrorMsg());
				return false;
		}
		return $result;
	}

	/**
	* Method to return a divisions array (id, name)
	*
	* @access	public
	* @return	array
	* @since 0.1
	*/
	function getDivisions()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();

 		$project_id = $mainframe->getUserState($option.'project');
		$query = "	SELECT id AS value, name As text FROM #__joomleague_division WHERE project_id=$project_id ORDER BY name ASC ";
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	* Method to assign teams of an existing project to a copied project
	*
	* @access	public
	* @return	array
	* @since 0.1
	*/
	function cpCopyTeams($post,$source_to_copy_division)
	{
		$mdlTeamPlayer = & JLGModel::getInstance('teamplayer','JoomleagueModel');
		$mdlTeamStaff = & JLGModel::getInstance('teamstaff','JoomleagueModel');

		$old_id = (int)$post['old_id'];
		$project_id = (int)$post['id'];

		//copy teams
		$query = 'SELECT * FROM #__joomleague_project_team WHERE project_id='.$old_id;
		$this->_db->setQuery($query);
		if ($results = $this->_db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_team =& $this->getTable();
				$p_team->bind($result);
				$p_team->set('id', NULL);
				$p_team->set('project_id', $project_id);
				$p_team->set('start_points', 0);
				$p_team->set('start_points', 0);
				$p_team->set('points_finally', 0);
				$p_team->set('neg_points_finally', 0);
				$p_team->set('matches_finally', 0);
				$p_team->set('won_finally', 0);
				$p_team->set('draws_finally', 0);
				$p_team->set('lost_finally', 0);
				$p_team->set('homegoals_finally', 0);
				$p_team->set('guestgoals_finally', 0);
				$p_team->set('diffgoals_finally', 0);
				$p_team->set('is_in_score', 1);
				$p_team->set('use_finally', 0);

				//divisions have to be copied first to get a new division id to replace it here
				if ($post['project_type'] == 'DIVISIONS_LEAGUE')
				{
					if($result['division_id'] != null && array_key_exists($result['division_id'],$source_to_copy_division))
					{
					  $p_team->set('division_id',$source_to_copy_division[$result['division_id']]);
					}
				}

				if (!$p_team->store())
				{
					echo $this->_db->getErrorMsg();
					return false;
				}

				$to_projectteam_id = $this->_db->insertid(); //mysql_insert_id();
				$from_projectteam_id = $result['id'];

				//copy project team-players
				if ($mdlTeamPlayer->cpCopyPlayers($from_projectteam_id,$to_projectteam_id))
				{
					echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_TP_COPIED',$from_projectteam_id).'<br />';
				}
				else
				{
					echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TP_COPIED',$from_projectteam_id).'<br />'.$model->getError().'<br />';
				}

				//copy project team-staff
				if ($mdlTeamStaff->cpCopyTeamStaffs($from_projectteam_id,$to_projectteam_id))
				{
					echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_TS_COPIED',$from_projectteam_id).'<br />';
				}
				else
				{
					echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TS_COPIED',$from_projectteam_id).'<br />'.$model->getError().'<br />';
				}

				//copy project team trainingdata
				$query = 'SELECT * FROM #__joomleague_team_trainingdata WHERE project_team_id='.$from_projectteam_id;
				$this->_db->setQuery($query);
				if ($results = $this->_db->loadAssocList())
				{
					foreach($results as $result)
					{
						$tData =& $this->getTable('teamtrainingdata');
						$tData->bind($result);
						$tData->set('id',NULL);
						$tData->set('project_team_id',$to_projectteam_id);
						//echo '<pre>'.print_r($tData,true).'</pre>';
						if (!$tData->store())
						{
							echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TP_COPIED',$from_projectteam_id).'<br />'.$model->getError().'<br />';
						}
						else
						{
							echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TRAINING_COPIED',$from_projectteam_id).'<br />';
						}
					}
				}
			}
		}
		return true;
	}

	/**
	* Method to return a team trainingdata array
	*
	* @access	public
	* @return	array
	* @since	0.1
	*/
	function getTrainigData($projectTeamID)
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();

 		$project_id = $mainframe->getUserState($option . 'project');
		$query = "SELECT * FROM #__joomleague_team_trainingdata WHERE project_id=$project_id AND project_team_id=$projectTeamID ORDER BY dayofweek ASC ";
		//echo $query;
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	function addNewTrainigData($projectTeamID,$projectID)
	{
		$result=true;
		$query="INSERT INTO #__joomleague_team_trainingdata (project_id,team_id,project_team_id) VALUES ('$projectID','0','$projectTeamID')";
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result=false;
		}
		return $result;
	}

	function saveTrainigData($post)
	{
		$result=true;
		//echo '<pre>'.print_r($post,true).'</pre>';
		$tdids=JRequest::getVar('tdids',array(),'post','array');
		JArrayHelper::toInteger($tdids);
		foreach ($tdids AS $tdid)
		{
			$timeStr1=preg_split('[:]',$post['time_start_'.$tdid]);
			$start=($timeStr1[0]*3600)+($timeStr1[1]*60);
			$timeStr2=preg_split('[:]',$post['time_end_'.$tdid]);
			$end=($timeStr2[0]*3600)+($timeStr2[1]*60);

			$query = "	UPDATE	#__joomleague_team_trainingdata
						SET
								dayofweek='".$post['dw_'.$tdid]."',
								time_start='$start',
								time_end='$end',
								place='".$post['place_'.$tdid]."',
								notes='".JRequest::getVar('notes_'.$tdid,'none','post','STRING',JREQUEST_ALLOWHTML)."'
						WHERE id='$tdid'";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				$result=false;
			}
		}
		return $result;
	}

	function checkAndDeleteTrainigData($post)
	{
		$result=true;
		$tdids=JRequest::getVar('tdids',array(),'post','array');
		JArrayHelper::toInteger($tdids);
		foreach ($tdids AS $tdid)
		{
			if (isset($post['delete_'.$tdid]))
			{
				$query = "DELETE FROM #__joomleague_team_trainingdata WHERE id='$tdid'";
				$this->_db->setQuery($query);
				if (!$this->_db->query())
				{
					$this->setError($this->_db->getErrorMsg());
					$result=false;
				}
			}
		}
		return $result;
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
	public function getTable($type = 'projectteam', $prefix = 'table', $config = array())
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