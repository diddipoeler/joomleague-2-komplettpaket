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

//jimport('joomla.application.component.model');
//require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

//require_once(JPATH_COMPONENT.DS.'models'.DS.'item.php');
//require_once(JPATH_COMPONENT.DS.'helpers'.DS.'jltoolbar.php');

// Include dependancy of the main model form
jimport('joomla.application.component.modelform');

/**
 * Joomleague Component Club Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelEditProjectteam extends JModelForm
{
	
  /* interfaces */
	var $latitude	= null;
	var $longitude	= null;
	var $projectid = 0;
	var $ptid = 0;
    var $tid = 0;
	var $projectteam = null;
    var $projectteamname = '';
  
  function __construct()
	{
		parent::__construct();

		$this->projectid = JRequest::getInt( 'p', 0 );
		$this->ptid = JRequest::getInt( 'ptid', 0 );
        $this->tid = JRequest::getInt( 'tid', 0 );
	}
    
  function getProjectteam()
	{
		
        $query = '	SELECT	name
						FROM #__joomleague_project
						WHERE id = '. (int) $this->projectid;

			$this->_db->setQuery($query);
            $this->projectname = $this->_db->loadResult();   
            
        if ( empty($this->ptid) )
        {
        $query = '	SELECT	pt.*,
								t.name AS name
						FROM #__joomleague_project_team AS pt
						LEFT JOIN #__joomleague_team AS t ON pt.team_id = t.id
						WHERE pt.team_id = '. (int) $this->tid . ' AND pt.project_id = '. (int) $this->projectid;

			$this->_db->setQuery($query);
			$res = $this->_db->loadObject();
			$this->ptid = $res->id;  
            $this->projectteamname = $res->name;   
        }
        
        if ( is_null( $this->projectteam  ) )
		{
			$this->projectteam = $this->getTable( 'Projectteam', 'Table' );
			$this->projectteam->load( $this->ptid );
		}
		return $this->projectteam;
	}  


/**
         * Get the data for a new qualification
         */
        public function getForm($data = array(), $loadData = true)
        {
 
        $app = JFactory::getApplication('site');
 
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
			$data = $this->getProjectteam();
		}
		return $data;
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

 		$project_id = $this->projectid;
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

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>