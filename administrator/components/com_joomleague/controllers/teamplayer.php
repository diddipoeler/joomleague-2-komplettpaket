<?php defined('_JEXEC') or die('Restricted access'); // Check to ensure this file is included in Joomla!
/**
* @copyright	Copyright (C) 2007 Joomteam.de. All rights reserved.
* @license		GNU/GPL,see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License,and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

jimport('joomla.application.component.controller');

/**
 * Joomleague Component Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerTeamPlayer extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	public function display($cachable = false, $urlparams = false)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$model=$this->getModel('teamplayers');
		$viewType=$document->getType();
		$view=$this->getView('teamplayers',$viewType);
		$view->setModel($model,true);  // true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		$teamws=$this->getModel ('projectteam');
		$teamws->set('name','teamws');
		$teamws->setId($mainframe->getUserState($option.'project_team_id',0));
		$view->setModel($teamws);

		switch($this->getTask())
		{
			case 'add'	 :
				{
					JRequest::setVar('hidemainmenu',0);
					JRequest::setVar('layout','form');
					JRequest::setVar('view','teamplayer');
					JRequest::setVar('edit',false);

					$model=$this->getModel('teamplayer');
					#$model->checkout();
				} break;

			case 'edit'	:
				{
					$model=$this->getModel('teamplayer');
					$viewType=$document->getType();
					$view=$this->getView('teamplayer',$viewType);
					$view->setModel($model,true);  // true is for the default model;

					$projectws=$this->getModel('project');
					$projectws->set('name','projectws');
					$projectws->setId($mainframe->getUserState($option.'project',0));
					$view->setModel($projectws);

					$teamws=$this->getModel('projectteam');
					$teamws->set('name','teamws');

					$teamws->setId($mainframe->getUserState($option.'project_team_id',0));
					$view->setModel($teamws);

					JRequest::setVar('hidemainmenu',0);
					JRequest::setVar('layout','form');
					JRequest::setVar('view','teamplayer');
					JRequest::setVar('edit',true);

					// Checkout the project
					$model=$this->getModel('teamplayer');
					#$model->checkout();
				} break;

		}
		parent::display();
	}

	function editlist()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$model=$this->getModel('teamplayers');
		$viewType=$document->getType();
		$view=$this->getView('teamplayers',$viewType);
		$view->setModel($model,true);  // true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);
		$teamws=$this->getModel ('projectteam');
		$teamws->set('name','teamws');

		$teamws->setId($mainframe->getUserState($option.'project_team_id',0));
		$view->setModel($teamws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editlist');
		JRequest::setVar('view','teamplayers');
		JRequest::setVar('edit',true);

		parent::display();
	}

	function save_playerslist()
	{
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$project=JRequest::getVar('project','post');
		$team_id=JRequest::getVar('team','post');
		$post['id']=(int)$cid[0];
		$post['project_id']=(int)$project;
		$post['team_id']=(int)$team_id;
		$model=$this->getModel('teamplayers');

		if ($model->store($post,'TeamPlayer'))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_SAVED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_ERROR_PLAYERS_SAVE').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		//$model->checkin();
		$link='index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display';
		$this->setRedirect($link,$msg);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		// decription must be fetched without striping away html code
		$post['notes']=JRequest::getVar('notes','none','post','STRING',JREQUEST_ALLOWHTML);
		$model=$this->getModel('TeamPlayer');
		if ($model->store($post,'TeamPlayer'))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYER_SAVED');

		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_ERROR_PLAYER_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=teamplayer.edit&cid[]='.$post['id'];
		}
		#echo $msg;
		$this->setRedirect($link,$msg);
	}

	// save the checked rows inside the project teams list
	function saveshort()
	{
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('teamplayers');
		$model->storeshort($cid,$post);

		if ($model->storeshort($cid,$post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_UPDATED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_ERROR_PLAYERS_UPDATED').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display';
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));}
		$model=$this->getModel('teamplayer');
		if(!$model->delete($cid)){echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";}
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display');
	}

	public function publish() {
		$this->view_list = 'teamplayers&task=teamplayer.display';
		parent::publish();
	}
	
	public function unpublish() {
		$this->view_list = 'teamplayers&task=teamplayer.display';
		parent::unpublish();
	}
	
	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('teamplayer');
		//$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display');
	}

	/*
	// diddipoeler
  function orderup()
	{
		$model=$this->getModel('teamplayer');
		$model->move(-1);
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display');
	}
  */
  
  /*
  // diddipoeler
	function orderdown()
	{
		$model=$this->getModel('teamplayer');
		$model->move(1);
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display');
	}
  */
  
	function saveorder()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		$order=JRequest::getVar('order',array(),'post','array');
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		$model=$this->getModel('teamplayer');
		$model->saveorder($cid,$order);
		$msg= JText::_('COM_JOOMLEAGUE_GLOBAL_NEW_ORDERING_SAVED');
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display',$msg);
	}

	function select()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$mainframe->setUserState($option.'project_team_id',JRequest::getVar('project_team_id'));
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display');
	}

	function assign()
	{
		//redirect to players page,with a message
		$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_ASSIGN');
		JRequest::setVar('project_team_id',JRequest::getVar('project_team_id'));
		$this->setRedirect('index.php?option=com_joomleague&task=person.display&view=persons&layout=assignplayers&hidemainmenu=1',$msg);
	}

	function unassign()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		$model=$this->getModel('teamplayers');
		$nDeleted=$model->remove($cid);
		if ($nDeleted != count($cid))
		{
			$msg=JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_UNASSIGN',$nDeleted);
			$msg .= '<br/>'.$model->getError();
			$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display',$msg,'error');
		}
		else
		{
			$msg=JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_UNASSIGN',$nDeleted);
			$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&task=teamplayer.display',$msg);
		}
	}
	
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'Teamplayer', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
}
?>