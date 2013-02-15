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
require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component league Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	Joomleague
 * @since	0.1
 */
class JoomleagueModeljlextringerposition extends JoomleagueModelItem
{
	/**
	 * Method to remove a league
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
			$query="SELECT id FROM #__joomleague_project WHERE sports_type_id IN ($cids)";
			//echo '<pre>'.print_r($query,true).'</pre>';
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('JL_ADMIN_LEAGUE_MODEL_ERROR_PROJECT_EXISTS'));
				return false;
			}
			$query="DELETE FROM #__joomleague_position_ringen WHERE id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to load content league data
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
			$query='SELECT * FROM #__joomleague_position_ringen WHERE id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the league data
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
			$object						= new stdClass();
			$object->id					= 0;
			$object->name				= null;
			$object->middle_name		= null;
			$object->short_name			= null;			
			$object->alias				= null;
			$object->country			= null;
			$object->checked_out		= 0;
			$object->checked_out_time	= 0;
			$object->extended			= null;
			$object->ordering			= 0;
			$this->_data				= $object;

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to add a league if not already exists
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 **/
	function addLeague($newLeague)
	{
		//check if league exists. If not add a new league to table
		$query="SELECT name FROM #__joomleague_position_ringen WHERE name='$newLeague'";
		$this->_db->setQuery($query);
		if ($leagueObject=$this->_db->loadObject())
		{
			//league already exists
			return $leagueObject->id;
		}
		//league does NOT exist and has to be created
		$p_league =& $this->getTable();
		$p_league->set('name',$newLeague);
		if (!$p_league->store())
		{
			$leagueObject->id=0;
		}
		else
		{
			$leagueObject->id=$this->_db->insertid();; //mysql_insert_id();
		}
		return $leagueObject->id;
	}

}
?>