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

jimport('joomla.application.component.controller');

/**
 * Joomleague Component editevents Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100709
 */
class JoomleagueControllerEditEvents extends JLGController
{
	function display()
	{
		// Get the view name from the query string
		$viewName=JRequest::getVar('view','editevents');

		// Get the view
		$view =& $this->getView($viewName);

		// Get the joomleague model
		$jl = $this->getModel('project','JoomleagueModel');
		$jl->set('_name','project');
		if (!JError::isError($jl))
		{
			$view->setModel ($jl);
		}

		// Get the joomleague model
		$sr = $this->getModel('editevents','JoomleagueModel');
		$sr->set('_name','editevents');
		if (!JError::isError($sr))
		{
			$view->setModel($sr);
		}
		$view->display();
	}

	function save()
	{
		JRequest::checkToken() or jexit(JText::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$msg='';
		$link='';
		$post=JRequest::get('post');
		$project_id=JRequest::getInt('p',0);
		$match_id=JRequest::getInt('mid',0);
		$changes=JRequest::getInt('changes_check',0);
		$model=$this->getModel('editevents');
		$user = JFactory::getUser();
		$post['playerpositions'] = $model->getProjectPositions(0,1);
		$post['staffpositions'] = $model->getProjectPositions(0,2);
		$post['refereepositions'] = $model->getProjectPositions(0,3);

		if (($changes) && (($model->isAllowed()) || ($model->isMatchAdmin($match_id,$user->id))))
		{

			if ($model->saveMatchStartingLineUps($post))
			{
				$msg .= JText::_('Changes on selected match were saved');
			}
			else
			{
				$msg .= JText::_('Error while saving changes on selected match');
			}
		}
		else
		{
			$msg .= JText::_('Found any changes or you are no match admin. So nothing is saved!');
		}
		$link=JoomleagueHelperRoute::getEditEventsRouteNew($project_id,$match_id);
		//echo '<br />'; echo $link; echo '<br />'; echo $msg; echo '<br />'; die();
		$this->setRedirect($link,$msg);
	}

	function saveevent()
	{
		$mainframe = JFactory::getApplication();
		$model = $this->getModel('editevents');
		$data = array();
		$data['teamplayer_id'] = JRequest::getInt('teamplayer_id');
		$data['projectteam_id'] = JRequest::getInt('projectteam_id');
		$data['event_type_id'] = JRequest::getInt('event_type_id');
		$data['event_time'] = JRequest::getVar('event_time', '');
		$data['match_id'] = JRequest::getInt('match_id');
		$data['event_sum'] = JRequest::getVar('event_sum', '');
		$data['notice'] = JRequest::getVar('notice', '');
		
		$project_id=$mainframe->getUserState('com_joomleague'.'project',0);
		if (!$result=$model->saveevent($data,$project_id))
		{
			$result="0\n".JText::sprintf('Adding of new Event failed: %1$s',$model->getError());
		} else {
			$result='1\n'.JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_EVENT');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	function deleteevent()
	{
		$event_id=JRequest::getInt('event_id');
		$model = $this->getModel('editevents');

		if (!$result=$model->deleteevent($event_id))
		{
			$result="0"."\n".JText::sprintf('EVENT DELETE FAILED: %1$s',$model->getError());
		}
		else
		{
			$result="1"."\n".JText::_('Event deleted');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	function removesubst()
	{
		$substid=JRequest::getVar('substid',0,'post','int');
		$model=$this->getModel('editevents');
		if (!$result=$model->removesubstitution($substid))
		{
			$result="0"."\n".JText::_('SUBSTITUTION REMOVING FAILED').': '.$model->getError();
		}
		else
		{
			$result="1"."\n".JText::_('SUBSTITUTION REMOVED');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	function savesubst()
	{
		$post=JRequest::get('post');
		$model=$this->getModel('editevents');
		if (!$result=$model->savesubstitution($post))
		{
			$result="0"."\n".JText::_('Save failed').': '.$model->getError();
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

}
?>
