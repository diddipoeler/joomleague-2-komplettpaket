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
 * Joomleague Component position Model
 *
 * @author	JoomLeague Team
 * @package	JoomLeague
 * @
 */
class JoomleagueModelPosition extends JoomleagueModelItem
{

	/**
	 * Method to remove positons of only one project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function deleteOne($project_id)
	{
		if ($project_id > 0)
		{
			$query='DELETE FROM #__joomleague_project_position WHERE project_id='.(int)$project_id;
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
	 * Method to remove a position
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

			$query="SELECT pos.id FROM #__joomleague_project_position AS ppos
              INNER JOIN #__joomleague_position AS pos ON pos.id = ppos.position_id
              WHERE pos.id IN ($cids)";
			//echo '<pre>'.print_r($query,true).'</pre>';
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_P_POSITION_EXISTS'));
				return false;
			}

			$query="SELECT id FROM #__joomleague_project_referee AS pref 
              INNER JOIN #__joomleague_project_position AS ppos ON ppos.id = pref.project_position_id
              INNER JOIN #__joomleague_position AS pos ON pos.id = ppos.position_id
              WHERE pos.id IN ($cids)";
			//echo '<pre>'.print_r($query,true).'</pre>';
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_P_REFEREE_EXISTS'));
				return false;
			}

			$query="SELECT id FROM #__joomleague_team_player AS tp
              INNER JOIN #__joomleague_project_position AS ppos ON ppos.id = tp.project_position_id
              INNER JOIN #__joomleague_position AS pos ON pos.id = ppos.position_id
              WHERE pos.id IN ($cids)";
			//echo '<pre>'.print_r($query,true).'</pre>';
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_PLAYER_EXISTS'));
				return false;
			}

			$query="SELECT id FROM #__joomleague_team_staff AS ts
              INNER JOIN #__joomleague_project_position AS ppos ON ppos.id = ts.project_position_id
              INNER JOIN #__joomleague_position AS pos ON pos.id = ppos.position_id
              WHERE pos.position_id IN ($cids)";
			//echo '<pre>'.print_r($query,true).'</pre>';
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_STAFF_EXISTS'));
				return false;
			}

			$query="SELECT id FROM #__joomleague_person 
              WHERE position_id IN ($cids)";
			//echo '<pre>'.print_r($query,true).'</pre>';
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_PERSON_EXISTS'));
				return false;
			}
			return parent::delete($cids);
		}
		return true;
	}

	/**
	 * Method to load content position data
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
			$query='SELECT * FROM #__joomleague_position WHERE id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the position data
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
			$position					= new stdClass();
			$position->id				= 0;
			$position->name				= null;
			$position->parent_id		= 0;
			$position->persontype		= '';
			$position->sports_type_id	= '1';
			$position->published		= 0;
			$position->ordering			= 0;
			$position->checked_out		= 0;
			$position->checked_out_time	= 0;
			$position->modifie			= null;
			$position->modified_by		= null;
			$position->alias			= null;
			$this->_data				= $position;

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	* Method to return the query that will obtain all ordering versus positions (with sportstype between brackets)
	* It can be used to fill a list box with value/text data.
	*
	* @access  public
	* @return  string
	* @since 1.5
	*/
	function getOrderingAndPositionsQuery()
	{
		return 'SELECT pos.ordering AS value, concat(pos.name, " (", st.name, ")")  AS text 
			FROM #__joomleague_position pos 
			LEFT JOIN #__joomleague_sports_type st ON st.id = pos.sports_type_id 
			ORDER BY pos.ordering';
	}

	/**
	* Method to return a events array (id,name)
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getEvents()
	{
		$query='SELECT evt.id AS value, concat(evt.name, " (" , st.name, ")") AS text 
				FROM #__joomleague_eventtype AS evt 
				LEFT JOIN #__joomleague_sports_type AS st ON st.id = evt.sports_type_id 
				WHERE evt.published=1 
				ORDER BY evt.name ASC ';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		foreach ($result as $position){$position->text=JText::_($position->text);}
		return $result;
	}

	/**
	* Method to return the position events array (id,name)
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getEventsPosition()
	{
		$query='	SELECT	p.id AS value,
							concat(p.name, " (" , st.name, ")") AS text 
					FROM #__joomleague_eventtype AS p
					LEFT JOIN #__joomleague_position_eventtype AS pe
						ON pe.eventtype_id=p.id
					LEFT JOIN #__joomleague_sports_type AS st ON st.id = p.sports_type_id 
					WHERE pe.position_id='.(int) $this->_id.'
					ORDER BY pe.ordering ASC ';

		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		foreach ($result as $event){$event->text=JText::_($event->text);}
		return $result;
	}

	/**
	* Method to return the position stats array (value,text)
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getPositionStatsOptions()
	{
		$query=' SELECT	s.id AS value,
				concat(s.name, " (" , st.name, ")") AS text
				FROM #__joomleague_statistic AS s 
				INNER JOIN #__joomleague_position_statistic AS ps ON ps.statistic_id=s.id 
				LEFT JOIN #__joomleague_sports_type AS st ON st.id = s.sports_type_id 
				WHERE ps.position_id='.(int) $this->_id . '
				ORDER BY ps.ordering ASC ';

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	* Method to return the stats not yet assigned to position (value,text)
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getAvailablePositionStatsOptions()
	{
		$query=' SELECT	s.id AS value, 
				concat(s.name, " (" , st.name, ")") AS text 
				FROM #__joomleague_statistic AS s 
				LEFT JOIN #__joomleague_position_statistic AS ps ON ps.statistic_id = s.id 
				AND ps.position_id='.(int) $this->_id . '
				LEFT JOIN #__joomleague_sports_type AS st ON st.id = s.sports_type_id 
				WHERE ps.id IS NULL 
				ORDER BY s.ordering ASC ';

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Method to return the positions array (id,name)
	 	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function getParentsPositions()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		//get positionss already in project for parents list
		//support only 2 sublevel, so parent must not have parents themselves
		$query='	SELECT	pos.id AS value,
							pos.name AS text
					FROM #__joomleague_position AS pos
					WHERE pos.parent_id=0
					ORDER BY pos.ordering ASC ';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		foreach ($result as $position){$position->text=JText::_($position->text);}
		return $result;
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
			$query="	UPDATE	#__joomleague_position
						SET parent_id='" .	$post['parent_id' .		$cid[$x]]."',
							checked_out=		0,
							checked_out_time =	0
							WHERE id=".$cid[$x];

			$this->_db->setQuery($query);
			if(!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				$result=false;
			}
		}
		return $result;
	}

	/**
	 * Method to export one or more positions
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function export($cid=array(),$table,$record_name)
	{
		$result=false;
		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids=implode(',',$cid);
			$query="SELECT * FROM #__joomleague_position WHERE id IN ($cids)";
			$this->_db->setQuery($query);
			$exportData=$this->_db->loadObjectList();
			$SportsTypeArray=array();
			$x=0;
			foreach ($exportData as $position){$SportsTypeArray[$x]=$position->sports_type_id;}
			$st_cids=implode(',',$SportsTypeArray);
			$query="SELECT * FROM #__joomleague_sports_type WHERE id IN ($st_cids)";
			//echo $query;
			$this->_db->setQuery($query);
			$exportDataSportsType=$this->_db->loadObjectList();
			$output	= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			// open the positions
			$output .= "<positions>\n";
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
			$record_name_position='Position';
			$record_name_parent_position='ParentPosition';
			foreach ($exportData as $name => $value)
			{
				if ($value->parent_id==0)
				{
					$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name_parent_position)."\">\n";
				}
				else
				{
					$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name_position)."\">\n";
				}
				foreach ($value as $name2=>$value2)
				{
					if (($name2!='checked_out') && ($name2!='checked_out_time'))
					{
						$output .= $tabVar.'<'.$name2.'><![CDATA['.JoomleagueHelper::stripInvalidXml(trim($value2)).']]></'.$name2.">\n";
						//echo "<pre>".$name2."#".$value2."<br /></pre>";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			// close positions
			$output .= '</positions>';
			
			$mdlJLXExports = JModel::getInstance("jlxmlexports", 'JoomleagueModel');
			$mdlJLXExports->downloadXml($output, $table);
			
			// close the application
			$app = JFactory::getApplication();
			$app->close();
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
	public function getTable($type = 'position', $prefix = 'table', $config = array())
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
