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
 * @since	0.1
 */
class JoomleagueViewProjects extends JLGView
{
	function display($tpl=null)
	{
		$option 	= JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$uri		= JFactory::getUri();

		$filter_league		= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_league',		'filter_league','',					'int');
		$filter_sports_type	= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_sports_type',	'filter_sports_type','',			'int');
		$filter_season		= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_season',		'filter_season','',					'int');
		$filter_state		= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_state',			'filter_state',		'',				'word');
		$filter_order		= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_order',			'filter_order',		'p.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_order_Dir',		'filter_order_Dir',	'',				'word');
		$search				= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.search',				'search',			'',				'string');
		$search=JString::strtolower($search);
		
		// Get data from the model
		$items		= $this->get('Data');
		$total		= $this->get('Total');
		$pagination = $this->get('Pagination');
		$javascript = "onchange=\"$('adminForm').submit();\"";

		// state filter
		$lists['state'] = JHtml::_('grid.state',$filter_state);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search'] = $search;

		//build the html select list for leagues
		$leagues[]=JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_LEAGUES_FILTER'),'id','name');
		$mdlLeagues = JModel::getInstance('Leagues','JoomleagueModel');
		$allLeagues = $mdlLeagues->getLeagues();
		$leagues=array_merge($leagues,$allLeagues);
		$lists['leagues']=JHtml::_( 'select.genericList',
									$leagues,
									'filter_league',
									'class="inputbox" onChange="this.form.submit();" style="width:120px"',
									'id',
									'name',
									$filter_league);
		unset($leagues);
		
		
		//build the html select list for sportstypes
		$sportstypes[]=JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_SPORTSTYPE_FILTER'),'id','name');
		$mdlSportsTypes = JModel::getInstance('SportsTypes', 'JoomleagueModel');
		$allSportstypes = $mdlSportsTypes->getSportsTypes();
		$sportstypes=array_merge($sportstypes,$allSportstypes);
		$lists['sportstypes']=JHtml::_( 'select.genericList',
										$sportstypes,
										'filter_sports_type',
										'class="inputbox" onChange="this.form.submit();" style="width:120px"',
										'id',
										'name',
										$filter_sports_type);
		unset($sportstypes);
		
		
		//build the html select list for seasons
		$seasons[]=JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_SEASON_FILTER'),'id','name');

		if ($res = $this->get('Seasons')){$seasons=array_merge($seasons,$res);}
		
		$lists['seasons']=JHtml::_( 'select.genericList',
									$seasons,
									'filter_season',
									'class="inputbox" onChange="this.form.submit();" style="width:120px"',
									'id',
									'name',
									$filter_season);

		unset($seasons);
		$user = JFactory::getUser();
		$this->assignRef('user',  $user);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$url=$uri->toString();
		$this->assignRef('request_url',$url);
		$this->addToolbar();
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
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_TITLE'),'ProjectSettings');
		JLToolBarHelper::publishList('project.publish');
		JLToolBarHelper::unpublishList('project.unpublish');
		JToolBarHelper::divider();
		
		JLToolBarHelper::addNew('project.add');
		JLToolBarHelper::editList('project.edit');
		JLToolBarHelper::custom('project.import','upload','upload',Jtext::_('COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT'),false);
		JLToolBarHelper::archiveList('project.export',JText::_('COM_JOOMLEAGUE_GLOBAL_XML_EXPORT'));
		JLToolBarHelper::custom('project.copy','copy.png','copy_f2.png',JText::_('COM_JOOMLEAGUE_GLOBAL_COPY'),false);
		JLToolBarHelper::deleteList('', 'project.remove');
		JToolBarHelper::divider();
		
		JLToolBarHelper::onlinehelp();
	}
}
?>
