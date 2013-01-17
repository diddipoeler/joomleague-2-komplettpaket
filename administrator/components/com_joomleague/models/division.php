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
require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component division Model
 *
 * @author	Marco Vaninetti <martizva@libero.it>
 * @package	JoomLeague
 * @
 */
class JoomleagueModelDivision extends JoomleagueModelItem
{

	/**
	 * Method to remove divisions of only one project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function deleteOne($project_id)
	{
		if ($project_id > 0)
		{
			$query='SELECT id FROM #__joomleague_division WHERE project_id='.$project_id;
			$this->_db->setQuery($query);
			if (!$result=$this->_db->loadResultArray())
			{
				if ($this->_db->getErrorNum() > 0)
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			$this->delete($result);
		}
		return true;
	}

	/**
	 * Method to remove a division
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete($cid=array())
	{
		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids=implode(',', $cid);
			$query="UPDATE #__joomleague_project_team SET division_id=0 WHERE division_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$query="UPDATE #__joomleague_treeto SET division_id=0 WHERE division_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return parent::delete($cids);
		}
		return true;
	}

	/**
	 * Method to load content division data
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
			$query = 'SELECT * FROM #__joomleague_division WHERE id='.(int)$this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the division data
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
			$division=new stdClass();
			$division->id			 = 0;
			$division->project_id	 = 0;
			$division->parent_id	 = 0;
			$division->tree_id		 = 0;
			$division->name			 = null;
			$division->shortname	 = null;
			$division->notes		 = null;

			$division->published	 = 0;
			$division->ordering		 = 0;

			$division->checked_out	 = 0;
			$division->checked_out_time = 0;
			$division->alias		 = null;
			$division->modified		= null;
			$division->modified_by	= null;
			$this->_data			 = $division;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to return the division events array (id, name)
	 	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function getParentsDivisions()
	{
		$option = JRequest::getCmd('option');
		$mainframe= JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');

		//get divisions already in project for parents list
		//support only 2 sublevel, so parent must not have parents themselves

		$query='	SELECT	dv.id AS value,
							dv.name AS text

					FROM #__joomleague_division AS dv
					WHERE dv.project_id='.$project_id.'
					AND dv.parent_id=0
					ORDER BY dv.ordering ASC ';

		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	* Method to assign divisions of an existing project to a copied project
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function cpCopyDivisions($post)
	{
		$o_source_to_copy_division=Array('0' => 0);
		$source_to_copy_division=$o_source_to_copy_division;
		if ($post['project_type'] != 'DIVISIONS_LEAGUE')
		{
			//No divisions to copy
			return $source_to_copy_division;
		}

		$old_id=(int)$post['old_id'];
		$project_id=(int)$post['id'];

		//copy divisions
		$query="SELECT * FROM #__joomleague_division WHERE project_id=$old_id AND parent_id=0";
		$this->_db->setQuery($query);
		if ($results=$this->_db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_division =& $this->getTable();
				$p_division->bind($result);
				$p_division->set('id', NULL);
				$p_division->set('project_id', $project_id);
				$p_division->set('parent_id', 0);

				if (!$p_division->store())
				{
					echo $this->_db->getErrorMsg();
					return $o_source_to_copy_division;
				}

				$source_to_copy_division[$result['id']]=$p_division->get('id');
				//subdivisions
				$query="SELECT * FROM #__joomleague_division WHERE project_id=$old_id AND parent_id=".$result['id'];
				$this->_db->setQuery($query);
				if ($subs=$this->_db->loadAssocList())
				{
					foreach ($subs as $sub)
					{
						$p_subdiv =& $this->getTable();
						$p_subdiv->bind($sub);
						$p_subdiv->set('id', NULL);
						$p_subdiv->set('project_id', $project_id);
						$p_subdiv->set('parent_id', $p_division->get('id'));

						if ($p_subdiv->store())
						{
							$source_to_copy_division[$sub['id']]=$p_subdiv->get('id');
						}

					}
				}
			}
		}
		return $source_to_copy_division;
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
	public function getTable($type = 'division', $prefix = 'table', $config = array())
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
