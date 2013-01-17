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
require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component Person Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5
 */
class JoomleagueModelPerson extends JoomleagueModelItem
{

	/**
	 * Method to load content person data
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
			$query='SELECT * FROM #__joomleague_person WHERE id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the player data
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
			$person=new stdClass();
			$person->id							= null;

			$person->user_id					= null;

			$person->firstname					= null;
			$person->lastname					= null;
			$person->nickname					= null;
			$person->knvbnr						= null;
			$person->birthday					= "0000-00-00";
			$person->deathday					= "0000-00-00";
			$person->country					= null;

			$person->height						= null;
			$person->weight						= null;
			$person->picture					= null;

			$person->show_pic					= 1;
			$person->show_persdata				= 1;
			$person->show_teamdata				= 1;
			$person->show_on_frontend			= 1;

			$person->info						= null;
			$person->notes						= null;

			$person->phone						= null;
			$person->mobile						= null;
			$person->email						= null;
			$person->website					= null;

			$person->address					= null;
			$person->zipcode					= null;
			$person->location					= null;
			$person->state						= null;
			$person->address_country			= null;

			$person->extended					= null;
			$person->published					= 1;

			$person->ordering			 		= 0;
			$person->checked_out				= 0;
			$person->checked_out_time	 		= 0;
			$person->alias						= null;
			$person->position_id				= null;
			$person->modified					= null;
			$person->modified_by				= null;

			$this->_data						= $person;

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to remove a person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete($cid=array())
	{
		$result=false;

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids=implode(',',$cid);

			//First select all subpersons of the selected ids
			$query="SELECT * FROM #__joomleague_person WHERE id IN ($cids)";
			$this->_db->setQuery($query);
			if($results=$this->_db->loadObjectList())
			{
				foreach ($results as $result)
				{
					//Now delete all match-persons assigned as player to subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_event
								WHERE teamplayer_id in (select id from #__joomleague_team_player where person_id = " . $result->id . ")
									OR teamplayer_id2 in (select id from #__joomleague_team_player where person_id = " . $result->id . ")";
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//Now delete all match-events assigned as referee to subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_referee
								WHERE project_referee_id in (select id from #__joomleague_project_referee where person_id = " . $result->id . ")";
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//Now delete all match-events assigned as player1 to subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_player
								WHERE teamplayer_id in (select id from #__joomleague_team_player where person_id = " . $result->id . ")";
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//Now delete all match-events assigned as player2 to subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_staff
								WHERE team_staff_id in (select id from #__joomleague_team_staff where person_id = " . $result->id . ")";
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					$query = "DELETE FROM #__joomleague_match_statistic
								WHERE teamplayer_id in (select id from #__joomleague_team_player where person_id = " . $result->id . ")";
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					$query = "DELETE FROM #__joomleague_match_staff_statistic
								WHERE team_staff_id in (select id from #__joomleague_team_staff where person_id = " . $result->id . ")";
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//Now delete all person assigned as referee in a project of the selected ids
					$query = "DELETE FROM #__joomleague_project_referee
								WHERE person_id=".$result->id;
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//Now delete all person assigned as player in a team of the selected ids
					$query = "DELETE FROM #__joomleague_team_player
								WHERE person_id=".$result->id;
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//Now delete all person assigned as staff in a team of the selected ids
					$query = "DELETE FROM #__joomleague_team_staff
								WHERE person_id=".$result->id;
					$this->_db->setQuery($query);
					if(!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
			}
			return parent::delete($cids);
		}
		return true;
	}

	/**
	 * Method to update checked persons
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function storeshort($cid,$post)
	{
		$result=true;
		for ($x=0; $x < count($cid); $x++)
		{
			$tblPerson = $this->getTable("person");
			$tblPerson->id			= $cid[$x];
			$tblPerson->firstname	= $post['firstname'.$cid[$x]];
			$tblPerson->lastname	= $post['lastname'.$cid[$x]];
			$tblPerson->nickname	= $post['nickname'.$cid[$x]];
			$tblPerson->birthday	= JoomleagueHelper::convertDate($post['birthday'.$cid[$x]],0);
			$tblPerson->deathday	= $post['deathday'.$cid[$x]];
			$tblPerson->country		= $post['country'.$cid[$x]];
			$tblPerson->position_id	= $post['position'.$cid[$x]];
			if(!$tblPerson->store()) {
				$this->setError($this->_db->getErrorMsg());
				$result=false;
			}
		}
		return $result;
	}

	/**
	 * Method to return a positions array (id,position + (sports_type_name))
	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function getPositions()
	{
		$query='	SELECT	pos.id AS value,
							pos.name AS posName,
							s.name AS sName
					FROM #__joomleague_position pos
					INNER JOIN #__joomleague_sports_type AS s ON s.id=pos.sports_type_id
					WHERE pos.published=1
					ORDER BY pos.ordering,pos.name';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return array();
		}
		else
		{
			foreach ($result as $position){$position->text=JText::_($position->posName).' ('.JText::_($position->sName).')';}
			return $result;
		}
	}

	/**
	 * Method to return a joomla users array (id,name)
	 *
	 * @access	public
	 * @return	array
	 *

	function getJLUsers()
	{
		$query="SELECT id AS value,name AS text FROM #__users ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}
	 */
	/**
	 * Method to update checked persons
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function assign($post)
	{
		$result=true;
		$query	= "	INSERT IGNORE
					INTO #__joomleague_person
					(person_id,project_id,team_id,is_person,is_player)
					VALUES
					('".$post['id']."','".$post['project']."','".$post['team']."','0','1')
					WHERE
					published = '1'";
		$this->_db->setQuery($query);
		if(!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result=false;
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
	public function getTable($type = 'person', $prefix = 'table', $config = array())
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
