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
class JoomleagueViewCurrentseasons extends JView
{
	function display($tpl=null)
	{
		$option 	= JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$uri		= JFactory::getUri();
        // Get data from the model
		$items		= $this->get('Items');
        $this->assignRef('items', $items);
        
        foreach ($this->items as $item)
	{
	   $item->count_projectdivisions = 0;
		$mdlProjectDivisions = JModel::getInstance("divisions", "JoomleagueModel");
		$item->count_projectdivisions = $mdlProjectDivisions->getProjectDivisionsCount($item->id);
		
		$item->count_projectpositions = 0;
		$mdlProjectPositions = JModel::getInstance("Projectposition", "JoomleagueModel");
		$item->count_projectpositions = $mdlProjectPositions->getProjectPositionsCount($item->id);
		
		$item->count_projectreferees = 0;
		$mdlProjectReferees = JModel::getInstance("Projectreferees", "JoomleagueModel");
		$item->count_projectreferees = $mdlProjectReferees->getProjectRefereesCount($item->id);
		
		$item->count_projectteams = 0;
		$mdlProjecteams = JModel::getInstance("Projectteams", "JoomleagueModel");
		$item->count_projectteams = $mdlProjecteams->getProjectTeamsCount($item->id);
        
        $item->count_matchdays = 0;
		$mdlRounds = JModel::getInstance("Rounds", "JoomleagueModel");
		$item->count_matchdays = $mdlRounds->getRoundsCount($item->id);
	   
       }

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
		JToolBarHelper::divider();
		
		JLToolBarHelper::onlinehelp();
		JToolBarHelper::preferences(JRequest::getCmd('option'));
	}
}
?>
