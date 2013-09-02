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
jimport('joomla.filesystem.file');

require_once(JPATH_COMPONENT.DS.'models'.DS.'sportstypes.php');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @packag	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueViewStatistics extends JLGView
{
	function display($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$user = JFactory::getUser();
		$uri = JFactory::getURI();
		
		$filter_sports_type	= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_sports_type',	'filter_sports_type','',	'int');
		$filter_state		= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_state',		'filter_state',		'',				'word');
		$filter_order		= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_order',		'filter_order',		'obj.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search				= $mainframe->getUserStateFromRequest($option.'.'.$this->get('identifier').'.search',			'search',			'',				'string');
		$search				= JString::strtolower($search);

		$items =& $this->get('Data');
		$total =& $this->get('Total');
		$pagination =& $this->get('Pagination');

		// state filter
		$lists['state']=JHTML::_('grid.state',$filter_state);

		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;

		// search filter
		$lists['search']=$search;
		
				//build the html select list for sportstypes
		$sportstypes[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_EVENTS_SPORTSTYPE_FILTER'),'id','name');
		$allSportstypes =& JoomleagueModelSportsTypes::getSportsTypes();
		$sportstypes=array_merge($sportstypes,$allSportstypes);
		$lists['sportstypes']=JHTML::_( 'select.genericList',
										$sportstypes,
										'filter_sports_type',
										'class="inputbox" onChange="this.form.submit();" style="width:120px"',
										'id',
										'name',
										$filter_sports_type);
		unset($sportstypes);

		$this->assignRef('user',$user);
		$this->assignRef('config',JFactory::getConfig());
		$this->assignRef('lists',$lists);
		$this->assignRef('items',$items);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('request_url',$uri->toString());
		$this->addToolbar();
		parent::display($tpl);
	}
	
	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_TITLE'),'statistics');
		
		JLToolBarHelper::publishList();
		JLToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JLToolBarHelper::editList('statistic.edit');
		JLToolBarHelper::addNew('statistic.add');
		JLToolBarHelper::custom('statistic.import','upload','upload',JText::_('COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT'),false);
		JLToolBarHelper::archiveList('statistic.export',JText::_('COM_JOOMLEAGUE_GLOBAL_XML_EXPORT'));
		JLToolBarHelper::deleteList(JText::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_DELETE_WARNING'),'statistic.fulldelete',JTEXT::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_FULL_DELETE'));
		JToolBarHelper::divider();
		
		JLToolBarHelper::onlinehelp();
		JToolBarHelper::preferences(JRequest::getCmd('option'));
	}
}
?>