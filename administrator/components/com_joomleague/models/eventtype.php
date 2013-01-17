<?php
/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
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
 * Joomleague Component Event Model
 *
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueModelEventtype extends JoomleagueModelItem
{
	/**
	 * Method to export one or more events
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5.0a
	 */
	function export($cid=array(),$table, $record_name)
	{
		$result=false;
		if (count($cid))
		{
			$mdlJLXExports = JModel::getInstance("jlxmlexports", 'JoomleagueModel');
			JArrayHelper::toInteger($cid);
			$cids=implode(',',$cid);
			$query="SELECT * FROM #__joomleague_eventtype WHERE id IN ($cids)";
			$this->_db->setQuery($query);
			$exportData=$this->_db->loadObjectList();
			$SportsTypeArray=array();
			$x=0;
			foreach ($exportData as $event){$SportsTypeArray[$x]=$event->sports_type_id;}
			$st_cids=implode(',',$SportsTypeArray);
			$query="SELECT * FROM #__joomleague_sports_type WHERE id IN ($st_cids)";
			$this->_db->setQuery($query);
			$exportDataSportsType=$this->_db->loadObjectList();
			$output="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			// open the events
			$output .= "<events>\n";
			
			$output .= $mdlJLXExports->_addToXml($mdlJLXExports->_getJoomLeagueVersion());
				
			$record_name='SportsType';
			//$tabVar='	';
			$tabVar='  ';
			foreach ($exportDataSportsType as $name=>$value)
			{
				$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name)."\">\n";
				foreach ($value as $name2=>$value2)
				{
					if (($name2!='checked_out') && ($name2!='checked_out_time'))
					{
						$output .= $tabVar.'<'.$name2.'><![CDATA['.JoomleagueHelper::stripInvalidXml(trim($value2)).']]></'.$name2.">\n";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			$record_name='EventType';
			foreach ($exportData as $name=>$value)
			{
				$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name)."\">\n";
				foreach ($value as $name2=>$value2)
				{
					if (($name2!='checked_out') && ($name2!='checked_out_time'))
					{
						$output .= $tabVar.'<'.$name2.'><![CDATA['.JoomleagueHelper::stripInvalidXml(trim($value2)).']]></'.$name2.">\n";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			// close events
			$output .= '</events>';
			
			$mdlJLXExports = JModel::getInstance("jlxmlexports", 'JoomleagueModel');
			$mdlJLXExports->downloadXml($output, $table);
			
			// close the application
			$app = JFactory::getApplication();
			$app->close();
		}
		return true;
	}

	/**
	 * Method to remove match_events of only one project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function deleteOne($project_id)
	{
		if ($project_id > 0)
		{
			$query='	DELETE
						FROM #__joomleague_match_event
						WHERE match_id in (
						SELECT DISTINCT
						  #__joomleague_match_1.id AS match_id
						FROM
						  #__joomleague_project_team
						  INNER JOIN #__joomleague_match #__joomleague_match_1 ON #__joomleague_project_team.id=#__joomleague_match_1.projectteam2_id
						  INNER JOIN #__joomleague_project_team #__joomleague_project_team_1 ON #__joomleague_project_team_1.id=#__joomleague_match_1.projectteam1_id
						WHERE
						  #__joomleague_project_team.project_id='.(int) $project_id.'
						)';
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
	 * Method to remove an event
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
			// first check that they are not used in any match events
			$query='	SELECT event_type_id
						FROM #__joomleague_match_event
						WHERE event_type_id IN ('.implode(',',$cid).')';
			$this->_db->setQuery($query);
			$this->_db->query();
			if ($this->_db->getAffectedRows())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_EVENT_MODEL_ERROR_MATCHES_EXISTS'));
				return false;
			}
			// then check that they are not assigned to any positions
			$query='	SELECT eventtype_id
						FROM #__joomleague_position_eventtype
						WHERE eventtype_id IN ('.implode(',',$cid).')';
			$this->_db->setQuery($query);
			$this->_db->query();
			if ($this->_db->getAffectedRows())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_EVENT_MODEL_ERROR_POSITION_EXISTS'));
				return false;
			}
			return parent::delete($cids);
		}
		return true;
	}

	/**
	 * Method to load content event data
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
			$query='	SELECT *
						FROM #__joomleague_eventtype
						WHERE id='.(int) $this->_id;

			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the event data
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
			$event						= new stdClass();
			$event->id					= 0;
			$event->name				= null;
			$event->icon				= "";
			$event->direction			= "DESC";
			$event->splitt				= 0;
			$event->double				= 0;
			$event->sports_type_id		= 1;
			$event->published			= 0;
			$event->ordering			= 0;
			$event->checked_out			= 0;
			$event->checked_out_time	= 0;
			$event->modified			= null;
			$event->modified_by			= null;
			$event->alias				= null;
			$this->_data				= $event;
			return (boolean) $this->_data;
		}
		return true;
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
	public function getTable($type = 'eventtype', $prefix = 'table', $config = array())
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