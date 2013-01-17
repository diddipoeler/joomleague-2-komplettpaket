<?php
/**
* @copyright	Copyright (C) 2007 Joomteam.de. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Joomleague Component Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerProjectteam extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
	}

	function edit()
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$view		= $this->getView  ('projectteam', $viewType);

		$projectws = $this->getModel ('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState('com_joomleagueproject', 0));
		$view->setModel($projectws);

		JRequest::setVar('view', 'projectteam');
		JRequest::setVar('layout', 'form' );
		JRequest::setVar('hidemainmenu', 0);

		$model 	= $this->getModel('projectteam');
		$user	= JFactory::getUser();

		// Error if checkedout by another administrator
		if ($model->isCheckedOut($user->get('id'))) {
			$this->setRedirect('index.php?option=com_joomleague&task=projectteam.display&view=projectteams', JText::_('EDITED BY ANOTHER ADMIN'));
		}

		$model->checkout();

		parent::display();
	}

	function display()
	{
		$option = JRequest::getCmd('option');

		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
	 	$model		= $this->getModel ('projectteams');
		$viewType	= $document->getType();
		$view		= $this->getView  ('projectteams', $viewType);
		$view->setModel($model, true);  // true is for the default model;

		$projectws = $this->getModel ('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option . 'project', 0));
		$view->setModel($projectws);

		parent::display();
	}

	function storechangeteams()
	{
	  	$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$model		= $this->getModel ('projectteams');
	  	$post=JRequest::get('post');
	 
	  	$oldteamid=JRequest::getVar('oldteamid',array(),'post','array');
		$newteamid=JRequest::getVar('newteamid',array(),'post','array');
	//  echo 'storechangeteams<pre>'.print_r($post,true).'</pre>';
	
		if ( $oldteamid )
	    {
	    	$model->setNewTeamID();
	    }	
	  	$this->setRedirect('index.php?option=com_joomleague&view=projectteams&task=projectteam.display');  
	  //parent::display();
	  }
  
  	function changeteams()
	{
		$option = JRequest::getCmd('option');

		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$model		= $this->getModel ('projectteams');
		$viewType	= $document->getType();
		$view		= $this->getView  ('projectteams', $viewType);
		$view->setModel($model, true);  // true is for the default model;

		$projectws = $this->getModel ('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option . 'project', 0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu', 0);
		JRequest::setVar('layout', 'changeteams' );
		JRequest::setVar('view', 'projectteams');
		JRequest::setVar('edit', true);

		// Checkout the project
		//	$model = $this->getModel('projectteam');

		parent::display();
	}
  
  function editlist()
	{
		$option = JRequest::getCmd('option');

		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$model		= $this->getModel ('projectteams');
		$viewType	= $document->getType();
		$view		= $this->getView  ('projectteams', $viewType);
		$view->setModel($model, true);  // true is for the default model;

		$projectws = $this->getModel ('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option . 'project', 0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu', 0);
		JRequest::setVar('layout', 'editlist' );
		JRequest::setVar('view', 'projectteams');
		JRequest::setVar('edit', true);

		// Checkout the project
		//	$model = $this->getModel('projectteam');

		parent::display();
	}

	function save_teamslist()
	{
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('projectteams');

		if ($model->store($post))
		{
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$post['id']);
			$cache->clean();
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_SAVED');
		}
		else
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_SAVE') . $model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		//$model->checkin();
		$link = 'index.php?option=com_joomleague&view=projectteams&task=projectteam.display';
		$this->setRedirect($link, $msg);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		//get the projectid
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
 		$project_id = $mainframe->getUserState($option . 'project');
		
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		// decription must be fetched without striping away html code
		$post['notes'] = JRequest:: getVar('notes', 'none', 'post', 'STRING', JREQUEST_ALLOWHTML);
		//$post['extended'] = JRequest:: getVar('extended', 'none', 'post', 'STRING', JREQUEST_ALLOWHTML);
		//echo '<pre>'.print_r($post,true).'</pre>';

		$model = $this->getModel('projectteam');

		if (isset($post['add_trainingData']))
		{
			if ($model->addNewTrainigData($post['id'],(int) $post['project_id']))
			{
				$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING');
			}
			else
			{
				$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_TRAINING').$model->getError();
			}
			//echo $msg;
		}

		if (isset($post['tdCount'])) // Existing Team Trainingdata
		{
			if ($model->saveTrainigData($post))
			{
				$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_SAVED');
			}
			else
			{
				$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_ERROR_SAVE').$model->getError();
			}

			if ($model->checkAndDeleteTrainigData($post))
			{
				$msg .= ' - '.JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_DELETED');
			}
			else
			{
				$msg = ' - '.JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_ERROR_DELETED').$model->getError();
			}
			$msg .= ' - ';
		}

		if ($model->store($post))
		{
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$project_id);
			$cache->clean();
			
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TEAM_SAVED');
		}
		else
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TEAM_ERROR_SAVE').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link = 'index.php?option=com_joomleague&view=projectteams&task=projectteam.display';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=projectteam.edit&cid[]=' . $post['id'];
		}
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	// save the checked rows inside the project teams list
	function saveshort()
	{
		//get the projectid
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
 		$project_id = $mainframe->getUserState($option . 'project');
		
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		
		$model = $this->getModel('projectteams');
		
		if ($model->storeshort($cid, $post))
		{
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$project_id);
			$cache->clean();
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_UPDATED');
		}
		else
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_UPDATED') . $model->getError();
		}

		$link = 'index.php?option=com_joomleague&view=projectteams&task=projectteam.display';
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JError::raiseError(500, JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));
		}

		$model = $this->getModel('team');

		if (!$model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_joomleague&view=teams&task=projectteam.display');
	}

	function publish()
	{
		$this->setRedirect('index.php?option=com_joomleague&view=teams&task=projectteam.display');
	}

	function unpublish()
	{
		$this->setRedirect('index.php?option=com_joomleague&view=teams&task=projectteam.display');
	}

	function cancel()
	{
		// Checkin the project
		$model = $this->getModel('projectteam');
		//$model->checkin();

		$this->setRedirect('index.php?option=com_joomleague&view=projectteams&task=projectteam.display');
	}

	function orderup()
	{
		$model = $this->getModel('projectteam');
		$model->move(-1);

		$this->setRedirect('index.php?option=com_joomleague&view=projectteam&task=projectteam.display');
	}

	function orderdown()
	{
		$model = $this->getModel('team');
		$model->move(1);

		$this->setRedirect('index.php?option=com_joomleague&view=projectteam&task=projectteam.display');
	}

	function saveorder()
	{
		$cid 	= JRequest::getVar('cid', array(), 'post', 'array');
		$order 	= JRequest::getVar('order', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('team');
		$model->saveorder($cid, $order);

		$msg =  JText::_( 'COM_JOOMLEAGUE_GLOBAL_NEW_ORDERING_SAVED' );
		$this->setRedirect('index.php?option=com_joomleague&view=projectteam&task=projectteam.display', $msg);
	}

	/**
	 * copy team to another project
	 */
	function copy()
	{
		$dest = JRequest::getInt('dest');
		$ptids = JRequest::getVar('ptids', array(), 'post', 'array');
		
		// check if this is the final step
		if (!$dest) 
		{
			JRequest::setVar('view',   'projectteams');
			JRequest::setVar('layout', 'copy');
			
			return parent::display();
		}
		
		$msg  = '';
		$type = 'message';
		
		$model = $this->getModel('projectteams');
		
		if (!$model->copy($dest, $ptids))
		{
			$msg = $model->getError();
			$type = 'error';
		}
		else
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_COPY_SUCCESS');	
		}
		$this->setRedirect('index.php?option=com_joomleague&view=projectteams&task=projectteam.display', $msg, $type);
		$this->redirect();
	}
}
?>
