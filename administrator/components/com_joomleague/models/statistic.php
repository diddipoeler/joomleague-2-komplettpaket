<?php
/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
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
 * Joomleague Component statistic Model
 *
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueModelStatistic extends JoomleagueModelItem
{
	/**
	 * overrides to load params and classparams
	 * @see JModelAdmin::getItem()
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();
		
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');
		
		if ($item) {
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->baseparams);
			$item->baseparams = $registry->toArray();
			
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		return $item;
	}
	/**
	 * Method to remove an event
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete($cid = array())
	{
		$result = false;

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);

			// first check that it not used in any match events
			$query = ' SELECT ms.id '
			       . ' FROM #__joomleague_match_statistic AS ms '
			       . ' WHERE ms.statistic_id IN ('. implode(',', $cid) .')'
			       ;
			$this->_db->setQuery($query);
			$this->_db->query();
			if ($this->_db->getAffectedRows()) {
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_STATISTIC_MODEL_CANT_DELETE_STATS_MATCHES'));
				return false;
			}

			// then check that it is not assigned to positions
			$query = ' SELECT id '
			       . ' FROM #__joomleague_position_statistic '
			       . ' WHERE statistic_id IN ('. implode(',', $cid) .')'
			       ;
			$this->_db->setQuery($query);
			$this->_db->query();
			if ($this->_db->getAffectedRows()) {
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_STATISTIC_MODEL_CANT_DELETE_STATS_MATCHES'));
				return false;
			}
			return parent::delete($cids);
		}
		return true;
	}


	/**
	 * Method to remove a statistics and associated data
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function fulldelete($cid = array())
	{
		$result = false;

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);

			// first check that it not used in any match events
			$query = ' DELETE '
			       . ' FROM #__joomleague_match_statistic '
			       . ' WHERE statistic_id IN ('. implode(',', $cid) .')'
			       ;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_STATISTIC_MODEL_ERROR_DELETE_STATS_MATCHES').': '.$this->_db->getErrorMsg());
				return false;
			}

			// then check that it is not assigned to positions
			$query = ' DELETE '
			       . ' FROM #__joomleague_position_statistic '
			       . ' WHERE statistic_id IN ('. implode(',', $cid) .')'
			       ;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_STATISTIC_MODEL_ERROR_DELETE_STATS_POS').': '.$this->_db->getErrorMsg());
				return false;
			}

			$query = ' DELETE
						FROM #__joomleague_statistic
						WHERE id IN (' . $cids . ')';

			$this->_db->setQuery($query);
			if(!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
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
			$query = '	SELECT *
						FROM #__joomleague_statistic
						WHERE id = ' . (int) $this->_id;

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
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
			$statistic					= new stdClass();
			$statistic->id				= 0;
			$statistic->name			= null;
			$statistic->short			= null;
			$statistic->icon			= '';
			$statistic->class			= '';
			$statistic->calculated		= 0;
			$statistic->note			= '';
			$statistic->baseparams		= null;
			$statistic->params			= null;
			$statistic->sports_type_id	= 1;
			$statistic->published		= 1;
			$statistic->ordering		= 0;
			$statistic->checked_out		= 0;
			$statistic->checked_out_time= 0;
			$statistic->alias 			= null;
			$statistic->modified		= null;
			$statistic->modified_by		= null;
			$this->_data				= $statistic;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	* Method to return the query that will obtain all ordering versus statistics
	* It can be used to fill a list box with value/text data.
	*
	* @access  public
	* @return  string
	* @since 1.5
	*/
	function getOrderingAndStatisticQuery()
	{
		return 'SELECT ordering AS value,name AS text FROM #__joomleague_statistic ORDER BY ordering';
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
	public function getTable($type = 'statistic', $prefix = 'table', $config = array())
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
			$data = $this->getItem();
		}
		return $data;
	}
	
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);
	
		// Create a signature hash.
		$hash = md5($source . serialize($options));
	
		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear)
		{
			return $this->_forms[$hash];
		}
	
		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
	
		try
		{
			$form = JForm::getInstance($name, $source, $options, false, $xpath);
			// load base configuration xml for stats
			$form->loadFile(JLG_PATH_ADMIN.DS.'statistics' . DS . 'base.xml');
			
			// specific xml configuration depends on stat type
			$item = $this->loadFormData();			
			if ($item && $item->class) 
			{
				$class = &JLGStatistic::getInstance($item->class);
				$xmlpath = $class->getXmlPath();
				$form->loadFile($xmlpath);
			} 
			
			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}
			
			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);
	
			// Load the data into the form after the plugins have operated.
			$form->bind($data);
// 			echo '<pre>';print_r($form); echo '</pre>';exit;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
	
		// Store the form for later.
		$this->_forms[$hash] = $form;
	
		return $form;
	}
}
