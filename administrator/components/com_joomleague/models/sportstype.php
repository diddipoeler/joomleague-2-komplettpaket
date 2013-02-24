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
 * Joomleague Component sportstype Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelSportsType extends JoomleagueModelItem
{
	/**
	 * Method to remove a sportstype
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete($pks=array())
	{
		$mainframe	=& JFactory::getApplication();
    $result=false;
		if (count($pks))
		{
			//JArrayHelper::toInteger($pks);
			$cids=implode(',',$pks);
			$mainframe->enqueueMessage(JText::_('JoomleagueModelSportsType-delete id->'.$cids),'Notice');
			$query="SELECT COUNT(id) FROM #__joomleague_sports_type";
			$this->_db->setQuery($query);
			if ($this->_db->loadResult()==count($cid))
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_LAST_SPORTSTYPE'));
				return false;
			}
			$query="SELECT id FROM #__joomleague_eventtype WHERE sports_type_id IN ($cids)";
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_EVENT_EXISTS'));
				return false;
			}
			$query="SELECT id FROM #__joomleague_position WHERE sports_type_id IN ($cids)";
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_POSITION_EXISTS'));
				return false;
			}
			$query="SELECT id FROM #__joomleague_project WHERE sports_type_id IN ($cids)";
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_PROJECT_EXISTS'));
				return false;
			}
			return parent::delete($pks);
		}
		return true;
	}

	/**
	 * Method to load content sportstype data
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
			$query='SELECT * FROM #__joomleague_sports_type WHERE id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the sportstype data
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
			$sportstype						= new stdClass();
			$sportstype->id					= 0;
			$sportstype->name				= null;
			$sportstype->icon				= '';
			$sportstype->ordering			= 0;
			$sportstype->checked_out		= 0;
			$sportstype->checked_out_time	= 0;
			$sportstype->modified			= null;
			$sportstype->modified_by		= null;
				
			$this->_data					= $sportstype;

			return (boolean) $this->_data;
		}

		return true;
	}
	
	/**
	 * Method to add a new sportstype if not already exists
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 **/
	function addSportsType($newsportstype)
	{
		//check if sportstype exists. If not add a new sportstype to table
		$query="SELECT * FROM #__joomleague_sport_type WHERE name=".$this->_db->Quote($newsportstype);
		$this->_db->setQuery($query);
		$sportstypeObject=$this->_db->loadObject();
		if ($sportstypeObject->id)
		{
			//sportstype already exists
			return $sportstypeObject->id;
		}
		
		JFolder::create(JPATH::clean(JPATH_ROOT.'/images/com_joomleague/database/events/'.JFolder::makesafe($name)));
		
		//sportstype does NOT exist and has to be created
		$p_sportstype =& $this->getTable();
		$p_sportstype->set('name',$newsportstype);

		if (!$p_sportstype->store())
		{
			$sportstypeObject->id=0;
		}
		else
		{
			$sportstypeObject->id=$this->_db->insertid(); //mysql_insert_id();
		}
		return $sportstypeObject->id;
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
	public function getTable($type = 'sportstype', $prefix = 'table', $config = array())
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