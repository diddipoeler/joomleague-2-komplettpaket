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

require_once(JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component teamplayer Model
 *
 * @author	Marco Vaninetti <martizva@libero.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelTeamPlayer extends JoomleagueModelItem
{

	/**
	 * Method to load content project player data
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
			$query="	SELECT	ppl.*,
								pl.firstname AS firstname,
								pl.lastname AS lastname,
								pl.nickname AS nickname,
								pl.knvbnr AS knvbnr,
								pl.birthday AS birthday,
								pl.country AS country,
								pl.height AS default_height,
								pl.weight AS default_weight,
								pl.picture AS default_picture,
								pl.notes AS default_notes

						FROM #__joomleague_team_player AS ppl
						INNER JOIN #__joomleague_person AS pl ON pl.id=ppl.person_id
						WHERE ppl.id=".(int) $this->_id." AND pl.published = '1'";
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
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
			$teamplayer=new stdClass();
			$teamplayer->id					= 0;

			$teamplayer->projectteam_id		= 0;
			$teamplayer->person_id			= 0;
			$teamplayer->project_position_id= null;
			$teamplayer->active				= 1;
			$teamplayer->jerseynumber		= 0;

			$teamplayer->notes				= null;

			$teamplayer->picture			= '';

			$teamplayer->injury				= 0;
			$teamplayer->injury_date		= 0;
			$teamplayer->injury_end			= 0;
			$teamplayer->injury_detail		= null;
			$teamplayer->injury_date_start	= "0000-00-00";
			$teamplayer->injury_date_end	= "0000-00-00";

			$teamplayer->suspension			= 0;
			$teamplayer->suspension_date	= 0;
			$teamplayer->suspension_end		= 0;
			$teamplayer->suspension_detail	= null;
			$teamplayer->susp_date_start	= "0000-00-00";
			$teamplayer->susp_date_end		= "0000-00-00";

			$teamplayer->away				= 0;
			$teamplayer->away_date			= 0;
			$teamplayer->away_end			= 0;
			$teamplayer->away_detail		= null;
			$teamplayer->away_date_start	= "0000-00-00";
			$teamplayer->away_date_end		= "0000-00-00";

			$teamplayer->extended			= null;

			$teamplayer->published			= 1;
			$teamplayer->ordering			= 0;
			$teamplayer->checked_out		= 0;
			$teamplayer->checked_out_time	= 0;
			$teamplayer->modified			= null;
			$teamplayer->modified_by		= null;
			
			$this->_data					= $teamplayer;

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to return a positions array (id,position)
	 *
	 * @access	public
	 * @return	array
	 *
	 */
	function getProjectPositions()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');

		$query="	SELECT ppos.id AS value,pos.name AS text
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON pos.id=ppos.position_id
					WHERE ppos.project_id=$project_id AND pos.persontype=1 ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		foreach ($result as $position){
			$position->text=JText::_($position->text);
		}
		return $result;
	}

	/**
	 * Method to return a matchdays array (id,position)
	 *
	 * @access	public
	 * @return	array
	 *
	 */
	function getProjectMatchdays()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query="	SELECT	roundcode AS value,
							name AS text
					FROM #__joomleague_round
					WHERE project_id=$project_id ORDER by roundcode ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * Method to assign teamplayers of an existing project to a copied project
	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function cpCopyPlayers($from_projectteam_id,$to_projectteam_id)
	{
		//copy players
		$query='	SELECT tp.*
					FROM #__joomleague_team_player tp
					INNER JOIN #__joomleague_project_team pt ON pt.id=tp.projectteam_id
					WHERE pt.id='.$from_projectteam_id;
		$this->_db->setQuery($query);
		if ($results=$this->_db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_player =& $this->getTable();
				$p_player->bind($result);
				$p_player->set('id',NULL);
				$p_player->set('projectteam_id',$to_projectteam_id);

				if (!$p_player->store())
				{
					echo $this->_db->getErrorMsg();
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to return the teams array (id,name)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getPerson($id)
	{
		$query="SELECT * FROM #__joomleague_person WHERE team_id=0 AND id=".$id." AND published = '1'";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObject())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * remove all players from a team
	 */
	function removeTeamPlayers($projectteamid)
	{
		$query='DELETE FROM #__joomleague_team_player WHERE projectteam_id='.(int) $projectteamid;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
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
	public function getTable($type = 'teamplayer', $prefix = 'table', $config = array())
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