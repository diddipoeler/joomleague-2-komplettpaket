<?php
/**
 * @copyright	Copyright (C) 2006-2011 JoomLeague.net. All rights reserved.
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
 * @since	1.5.0a
 */
class JoomleagueViewrosterpositions extends JLGView
{
	function display($tpl=null)
	{
		$mainframe =& JFactory::getApplication();
    $db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$document	=& JFactory::getDocument();
    $option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    $this->assignRef( 'optiontext',			$optiontext );

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ROSTERPOSITIONS_TITLE'),'generic.png');
		/*
    JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
		JToolBarHelper::custom('import','upload','upload',JText::_('COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT'),false);
		JToolBarHelper::archiveList('export',JText::_('COM_JOOMLEAGUE_GLOBAL_XML_EXPORT'));
		JToolBarHelper::deleteList();
		JToolBarHelper::divider();
		*/
//    JLToolBarHelper::addNew('rosterposition.add');
    JToolBarHelper::custom('rosterposition.addhome','new','new',JText::_('COM_JOOMLEAGUE_ADMIN_ROSTERPOSITIONS_HOME'),false);
    JToolBarHelper::custom('rosterposition.addaway','new','new',JText::_('COM_JOOMLEAGUE_ADMIN_ROSTERPOSITIONS_AWAY'),false);
		JLToolBarHelper::editList('rosterposition.edit');
		JToolBarHelper::custom('rosterposition.import','upload','upload',JText::_('COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT'),false);
		JToolBarHelper::archiveList('rosterposition.export',JText::_('COM_JOOMLEAGUE_GLOBAL_XML_EXPORT'));
		//JToolBarHelper::deleteList();
		JLToolBarHelper::deleteList('', 'rosterposition.remove');
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.joomleague',true);

		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();

		$filter_order		= $mainframe->getUserStateFromRequest($option.'l_filter_order',		'filter_order',		'obj.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'l_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search				= $mainframe->getUserStateFromRequest($option.'l_search',			'search',			'',				'string');
		$search=JString::strtolower($search);

		$items =& $this->get('Data');
		$total =& $this->get('Total');
		$pagination =& $this->get('Pagination');

		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;

		// search filter
		$lists['search']=$search;

		$this->assignRef('user',JFactory::getUser());
		$this->assignRef('lists',$lists);
		$this->assignRef('items',$items);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('request_url',$uri->toString());

		parent::display($tpl);
	}

}
?>