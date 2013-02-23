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
jimport('joomla.html.parameter.element.timezones');

require_once(JPATH_COMPONENT.DS.'models'.DS.'sportstypes.php');
require_once(JPATH_COMPONENT.DS.'models'.DS.'leagues.php');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueViewProject extends JLGView
{
	function display($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$model = $this->getModel();

		$lists=array();
		//get the project
		$project 	=& $this->get('data');
		
		$project->fav_team = explode(",", $project->fav_team);
		
		$isNew		= ($project->id < 1);
		$append		= '';
		if ($isNew)
		{
			$append	= ' style="background-color:#FFCCCC;"';
		}
		$edit=JRequest::getVar('edit');
		$copy=JRequest::getVar('copy');

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_THE_PROJECT'),$project->name);
			$mainframe->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout($user->get('id'));
		}
		else
		{
			// initialise new record
			$project->published=1;
			$project->order=0;
		}

		// add javascript
		$document = JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript(JURI::root() . 'administrator/components/com_joomleague/models/forms/project.js');

		$this->assignRef('edit',$edit);
		$this->assignRef('copy',$copy);
		$this->assignRef('lists',$lists);
		$this->assignRef('project',$project);
		$this->assignRef('leagues',$res);
				
		// 'regular' joomla 2.5 form associated to view
		// TODO: all fields should be migrated to this xml file !
		$this->assignRef('form'      	, $this->get('form'));
		$extended = $this->getExtended($project->extended, 'project');		
		$this->assignRef( 'extended', $extended );
        $this->assign('cfg_which_media_tool', JComponentHelper::getParams('com_joomleague')->get('cfg_which_media_tool',0) );
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
		if ($this->copy)
		{
			$toolbarTitle=JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_COPY_PROJECT');
		}
		else
		{
			$toolbarTitle=(!$this->edit) ? JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_ADD_NEW') : JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_EDIT');
			JToolBarHelper::divider();
		}
		JToolBarHelper::title($toolbarTitle,'ProjectSettings');
		
		if (!$this->copy)
		{
			JLToolBarHelper::apply('project.apply');
			JLToolBarHelper::save('project.save');
		}
		else
		{
			JLToolBarHelper::save('project.copysave');
		}
		JToolBarHelper::divider();
		if ((!$this->edit) || ($this->copy))
		{
			JLToolBarHelper::cancel('project.cancel');
		}
		else
		{
			// for existing items the button is renamed `close`
			JLToolBarHelper::cancel('project.cancel',JText::_('COM_JOOMLEAGUE_GLOBAL_CLOSE'));
		}
		JLToolBarHelper::onlinehelp();
	}
}
?>
