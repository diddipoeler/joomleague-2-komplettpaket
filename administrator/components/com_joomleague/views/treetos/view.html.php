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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueViewTreetos extends JLGView
{

	function display($tpl=null)
	{
		$mainframe = JFactory::getApplication();
		if ($this->getLayout()=='default')
		{
			$this->_displayDefault($tpl);
			return;
		}
		parent::display($tpl);
	}

	function _displayDefault($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
		$db = JFactory::getDBO();
		$uri = JFactory::getURI();
		
		// Get data from the model
		$items		= & $this->get('Data');
		$total		= & $this->get('Total');
		$pagination     =& $this->get('Pagination');
		
		$model = $this->getModel();
		$projectws =& $this->get('Data','projectws');
		$division = $mainframe->getUserStateFromRequest($option.'tt_division','division','','string');

		//build the html options for divisions
		$divisions[]=JHTMLSelect::option('0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		$mdlDivisions = JModel::getInstance("divisions", "JoomLeagueModel");
		if ($res =& $mdlDivisions->getDivisions($project_id)){
			$divisions=array_merge($divisions,$res);
		}
		$lists['divisions']=$divisions;
		unset($divisions);

		$this->assignRef('user',JFactory::getUser());
		$this->assignRef('lists',$lists);
		$this->assignRef('items',$items);
		$this->assignRef('projectws',$projectws);
		$this->assignRef('division',$division);
		$this->assignRef('total',$total);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('request_url',$uri->toString());

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_TREETOS_TITLE'),'Tree');

		JLToolBarHelper::apply('treeto.saveshort');
		JLToolBarHelper::publishList('treeto.publish');
		JLToolBarHelper::unpublishList('treeto.unpublish');
		JToolBarHelper::divider();

		JLToolBarHelper::addNew('treeto.save');
		JLToolBarHelper::deleteList(JText::_('COM_JOOMLEAGUE_ADMIN_TREETOS_WARNING'), 'treeto.remove');
		JToolBarHelper::divider();

		JLToolBarHelper::onlinehelp();
	}

}
?>
