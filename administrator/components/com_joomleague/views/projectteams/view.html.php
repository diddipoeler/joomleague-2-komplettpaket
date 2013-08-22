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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package	JoomLeague
 * @since 	0.1
 */
class JoomleagueViewProjectteams extends JLGView
{
	function display($tpl=null)
	{
		if ($this->getLayout() == 'editlist')
		{
			$this->_displayEditlist($tpl);
			return;
		}

		if ($this->getLayout() == 'changeteams')
		{
			$this->_displayChangeTeams($tpl);
			return;
		}
		
		if ($this->getLayout() == 'default')
		{
			$this->_displayDefault($tpl);
			return;
		}
		
		if ($this->getLayout() == 'copy')
		{
			$this->_displayCopy($tpl);
			return;
		}

		parent::display($tpl);
	}

  function _displayChangeTeams($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
		
		$db = JFactory::getDBO();
		$uri = JFactory::getURI();
		
		$projectteam =& $this->get('Data');
		$total =& $this->get('Total');
		$pagination =& $this->get('Pagination');
		
		$model = $this->getModel();
		
		//build the html select list for all teams
		$allTeams = array();
		$all_teams[] = JHTML::_( 'select.option', '0', JText::_( 'COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM' ) );
		if( $allTeams =& $model->getAllTeams($project_id) ) 
    {
			$all_teams=array_merge($all_teams,$allTeams);
		}
		$lists['all_teams']=$all_teams;
		unset($all_teams);
		
		
		//$ress =& $model->getProjectTeams($project_id);
		
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGNED_NEW_TEAMS'),'install');
			
		JLToolBarHelper::custom('projectteam.storechangeteams','move.png','move_f2.png',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_BUTTON_STORE_CHANGE_TEAMS'),false);
		//JToolBarHelper::back('projectteam.back');	
		JLToolBarHelper::cancel( 'projectteam.cancel', 'COM_JOOMLEAGUE_GLOBAL_CLOSE' );
		
		$this->assignRef('projectteam',$projectteam);
		$this->assignRef('lists',$lists);
		$this->assignRef('request_url',$uri->toString());
		
		parent::display($tpl);
	}
  
  	
	function _displayEditlist($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
		
		$db = JFactory::getDBO();
		$uri = JFactory::getURI();

		$filter_state = $mainframe->getUserStateFromRequest($option.'tl_filter_state', 'filter_state', '', 'word');
		$filter_order = $mainframe->getUserStateFromRequest($option.'tl_filter_order', 'filter_order', 't.name', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'tl_filter_order_Dir', 'filter_order_Dir', '', 'word');
		$search	= $mainframe->getUserStateFromRequest($option.'tl_search', 'search', '', 'string');
		$search_mode = $mainframe->getUserStateFromRequest($option.'tl_search_mode', 'search_mode', '', 'string');
		$search	= JString::strtolower($search);

		$projectteam =& $this->get('Data');
		$total =& $this->get('Total');
		$pagination =& $this->get('Pagination');
		$model = $this->getModel();

		// state filter
		$lists['state'] = JHTML::_('grid.state',$filter_state);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search'] = $search;
		$lists['search_mode'] = $search_mode;
		$projectws =& $this->get('Data','projectws');

		//build the html select list for project assigned teams
		$ress = array();
		$res1 = array();
		$notusedteams = array();

		if ($ress =& $model->getProjectTeams($project_id))
		{
			$teamslist=array();
			foreach($ress as $res)
			{
				if(empty($res1->info))
				{
					$project_teamslist[] = JHTMLSelect::option($res->value,$res->text);
				}
				else
				{
					$project_teamslist[] = JHTMLSelect::option($res->value,$res->text.' ('.$res->info.')');
				}
			}

			$lists['project_teams'] = JHTMLSelect::genericlist($project_teamslist, 'project_teamslist[]',
																' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.min(30,count($ress)).'"',
																'value',
																'text');
		}
		else
		{
			$lists['project_teams']= '<select name="project_teamslist[]" id="project_teamslist" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}

		if ($ress1 =& $model->getTeams())
		{
			if ($ress =& $model->getProjectTeams($project_id))
			{
				foreach ($ress1 as $res1)
				{
					$used=0;
					foreach ($ress as $res)
					{
						if ($res1->value == $res->value){$used=1;}
					}

					if ($used == 0 && !empty($res1->info)){
						$notusedteams[]=JHTMLSelect::option($res1->value,$res1->text.' ('.$res1->info.')');
					}
					elseif($used == 0 && empty($res1->info))
					{
						$notusedteams[] = JHTMLSelect::option($res1->value,$res1->text);
					}
				}
			}
			else
			{
				foreach ($ress1 as $res1)
				{
					if(empty($res1->info))
					{
						$notusedteams[] = JHTMLSelect::option($res1->value,$res1->text);
					}
					else
					{
						$notusedteams[] = JHTMLSelect::option($res1->value,$res1->text.' ('.$res1->info.')');
					}
				}
			}
		}
		else
		{
			JError::raiseWarning('ERROR_CODE','<br />'.JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ADD_TEAM').'<br /><br />');
		}

		//build the html select list for teams
		if (count($notusedteams) > 0)
		{
			$lists['teams'] = JHTMLSelect::genericlist( $notusedteams,
														'teamslist[]',
														' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.min(30,count($notusedteams)).'"',
														'value',
														'text');
		}
		else
		{
			$lists['teams'] = '<select name="teamslist[]" id="teamslist" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}

		unset($res);
		unset($res1);
		unset($notusedteams);

		$this->assignRef('user',JFactory::getUser());
		$this->assignRef('lists',$lists);
		$this->assignRef('projectteam',$projectteam);
		$this->assignRef('projectws',$projectws);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('request_url',$uri->toString());

		$this->addToolbar_Editlist();		
		parent::display($tpl);
	}

	function _displayDefault($tpl)
	{
		$document = JFactory::getDocument();
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
        $show_debug_info = JComponentHelper::getParams($option)->get('show_debug_info',0);

		$db = JFactory::getDBO();
		$uri = JFactory::getURI();

		$baseurl    = JURI::root();
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Autocompleter.js');
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Autocompleter.Request.js');
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Observer.js');
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/quickaddteam.js');
		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');		

		$filter_state		= $mainframe->getUserStateFromRequest($option.'tl_filter_state',		'filter_state',		'',			'word');
		$filter_order		= $mainframe->getUserStateFromRequest($option.'tl_filter_order',		'filter_order',		't.name',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'tl_filter_order_Dir',	'filter_order_Dir',	'',			'word');
		$search				= $mainframe->getUserStateFromRequest($option.'tl_search',				'search',			'',			'string');
		$division			= $mainframe->getUserStateFromRequest($option.'tl_division',			'division',			'',			'string');
		
		$search_mode		= $mainframe->getUserStateFromRequest($option.'tl_search_mode',			'search_mode',		'',			'string');
		$search				= JString::strtolower($search);

		$projectteam	=& $this->get('Data');
		$total			=& $this->get('Total');
		$pagination 	=& $this->get('Pagination');
		$model			= $this->getModel();

		// state filter
		$lists['state']=JHTML::_('grid.state',$filter_state);

		// search filter
		$lists['search']=$search;
		$lists['search_mode']=$search_mode;

		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;

		//build the html options for divisions
		$divisions[]=JHTMLSelect::option('0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		$mdlDivisions = JModel::getInstance("divisions", "JoomLeagueModel");
		if ($res =& $mdlDivisions->getDivisions($project_id)){
			$divisions=array_merge($divisions,$res);
		}
		$lists['divisions']=$divisions;
		unset($divisions);

		$projectws	=& $this->get('Data','projectws');

		$this->assignRef('user',JFactory::getUser());
		$this->assignRef('lists',$lists);
		$this->assignRef('projectteam',$projectteam);
		$this->assignRef('division',$division);
		$this->assignRef('projectws',$projectws);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('request_url',$uri->toString());

		$this->addToolbar();			
		parent::display($tpl);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $tpl
	 */
	function _displayCopy($tpl)
	{
		$document = JFactory::getDocument();
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
	
		$uri = JFactory::getURI();
			
		$ptids = JRequest::getVar('cid', array(), 'post', 'array');
	
		$model = $this->getModel();
	
		$lists = array();
					
		//build the html select list for all teams
		$options = JoomleagueHelper::getProjects();
		
		$lists['projects'] = JHTML::_('select.genericlist', $options, 'dest', '', 'id', 'name');
		
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_COPY_DEST'),'Teams');
		
		JLToolBarHelper::apply('projectteam.copy');		
		JToolBarHelper::back();
		
		$this->assignRef('ptids', $ptids);
		$this->assignRef('lists', $lists);
		$this->assignRef('request_url',$uri->toString());
	
		parent::display($tpl);
	}
	/**
	* Add the page title and toolbar.
	*
	* @since	1.6
	*/
	protected function addToolbar()
	{ 	
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_TITLE'));
        JLToolBarHelper::deleteList('', 'projectteam.remove');

		JLToolBarHelper::apply('projectteam.saveshort');
		JLToolBarHelper::custom('projectteam.changeteams','move.png','move_f2.png',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_BUTTON_CHANGE_TEAMS'),false);
		JLToolBarHelper::custom('projectteam.editlist','upload.png','upload_f2.png',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_BUTTON_ASSIGN'),false);
		JLToolBarHelper::custom('projectteam.copy','copy','copy', JText::_('COM_JOOMLEAGUE_GLOBAL_COPY'), true);
		JToolBarHelper::divider();

		JLToolBarHelper::onlinehelp();
    JToolBarHelper::preferences(JRequest::getCmd('option'));	
	}
	
	/**
	* Add the page title and toolbar.
	*
	* @since	1.6
	*/
	protected function addToolbar_Editlist()
	{ 		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGN' ) );
		JLToolBarHelper::save( 'projectteam.save_teamslist' );
		
		// for existing items the button is renamed `close` and the apply button is showed
		JLToolBarHelper::cancel( 'projectteam.cancel', 'COM_JOOMLEAGUE_GLOBAL_CLOSE' );
		
		JLToolBarHelper::onlinehelp();	
	}
}
?>
