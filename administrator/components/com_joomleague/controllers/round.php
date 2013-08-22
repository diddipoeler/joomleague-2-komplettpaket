<?php
/**
* @copyright	Copyright (C) 2007 Joomteam.de. All rights reserved.
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
 * Joomleague Component Matchday Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerRound extends JoomleagueController
{

	function __construct()
	{
		$mainframe	= JFactory::getApplication();
		$option = JRequest::getCmd('option');
		parent::__construct();

		$pid	= JRequest::getVar('pid',	array(0),'','array');
		if ( $pid[0] )
		{
		$mainframe->setUserState($option.'project',(int)$pid[0]);
		}

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	function display()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

	 	$model=$this->getModel('rounds');
		$viewType=$document->getType();
		$view=$this->getView('rounds',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		switch($this->getTask())
		{
			case 'add':
			{
				JRequest::setVar('hidemainmenu',1);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','round');
				JRequest::setVar('edit',false);
 				$model=$this->getModel('round');
				$viewType=$document->getType();
				$view=$this->getView('round',$viewType);
				$view->setModel($model,true);	// true is for the default model;

 				$projectws=$this->getModel('project');
				$projectws->set('name','projectws');
				$projectws->setId($mainframe->getUserState($option.'project',0));
				$view->setModel($projectws);

				$model=$this->getModel('round');
				$model->checkout();
			} break;

			case 'edit'	:
			{
				$model=$this->getModel('round');
				$viewType=$document->getType();
				$view=$this->getView('round',$viewType);
				$view->setModel($model,true);	// true is for the default model;

				$projectws=$this->getModel('project');
				$projectws->set('name','projectws');
				$projectws->setId($mainframe->getUserState($option.'project',0));
				$view->setModel($projectws);

				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','round');
				JRequest::setVar('edit',true);

				// Checkout the round
				$model=$this->getModel('round');
				$model->checkout();
			} break;

			case 'massadd'	:
			{
				JRequest::setVar('massadd',true);
			} break;

		}
		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$post=JRequest::get('post');
		$model=$this->getModel('round');
		// convert dates back to mysql date format
		if (isset($post['round_date_first']))
		{
			$post['round_date_first']=strtotime($post['round_date_first']) ? strftime('%Y-%m-%d',strtotime($post['round_date_first'])) : null;
		}
		else
		{
			$post['round_date_first']=null;
		}
		if (isset($post['round_date_last']))
		{
			$post['round_date_last']=strtotime($post['round_date_last']) ? strftime('%Y-%m-%d',strtotime($post['round_date_last'])) : null;
		}
		else
		{
			$post['round_date_last']=null;
		}
		$max=$model->getMaxRound($mainframe->getUserState($option.'project',0));
		$max++;
		if (!isset($post['roundcode']) || empty($post['roundcode']))
		{
			//$max=$model->getMaxRound($mainframe->getUserState($option.'project',0));
			$post['roundcode']=$max;
		}
		if (!isset($post['name']) || empty($post['name']))
		{
			//$max=$model->getMaxRound($mainframe->getUserState($option.'project',0));
			//$mrc=$max + 1;
			$post['name']=JText::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_NAME',$max);
		}
		if ($model->store($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_SAVED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ERROR_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=rounds&task=round.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=round.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	// save the checked rows inside the rounds list
	function saveshort()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		$model=$this->getModel('round');
		for ($x=0; $x < count($cid); $x++)
		{
			$post['round_date_first'.$cid[$x]]=JoomleagueHelper::convertDate($post['round_date_first'.$cid[$x]],0);
			$post['round_date_last'.$cid[$x]]=JoomleagueHelper::convertDate($post['round_date_last'.$cid[$x]],0);
			if (isset($post['roundcode'. $cid[$x]]))
			{
				if ($post['roundcode'.$cid[$x]]=='0')
				{
					$max=$model->getMaxRound($mainframe->getUserState($option.'project',0));
					$post['roundcode'.$cid[$x]]=$max + 1;
				}
			}
		}
		if ($model->storeshort($cid,$post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_SAVED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ERROR_SAVE').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=rounds&task=round.display';
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));}
		$mdlMatches=$this->getModel('matches');
		$mdlMatch  =$this->getModel('match');
		$model=$this->getModel('round');
		if (!$model->delete($cid,$mdlMatches,$mdlMatch))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_DELETED');
		$this->setRedirect('index.php?option=com_joomleague&view=rounds&task=round.display',$msg);
	}

	function deletematches()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_SELECT_TO_DELETE_MATCHES'));}
		$mdlMatches=$this->getModel('matches');
		$mdlMatch=$this->getModel('match');
		$model=$this->getModel('round');
		if (!$model->deleteMatches($cid,$mdlMatches,$mdlMatch,true))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_MATCHES_DELETED');
		$this->setRedirect('index.php?option=com_joomleague&view=rounds&task=round.display',$msg);
	}

	function cancel()
	{
		// Checkin the project
		#$model=$this->getModel('rounds');
		#$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=rounds&task=round.display');
	}

  public function publish() {
		$this->view_list = 'rounds';
		parent::publish();
	}
	
	public function unpublish() {
		$this->view_list = 'rounds';
		parent::unpublish();
	}
  
  
	function copyfrom()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$model=$this->getModel('round');
		$add_round_count=(int)$post['add_round_count'];
		$max=0;
		if ($add_round_count > 0) // Only MassAdd a number of new and empty rounds
		{
			$max=$model->getMaxRound($post['project_id']);
			$max++;
			$i=0;
			for ($x=0; $x < $add_round_count; $x++)
			{
				$i++;
				$post['roundcode']=$max;
				$post['name']=JText::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_NAME',$max);
//echo '<pre>'.print_r($post,true).'</pre>';
				if ($model->store($post))
				{
					$msg=JText::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUNDS_ADDED',$i);
				}
				else
				{
					$msg=JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ERROR_ADD').$model->getError();
				}
				$max++;
			}
		}
		$link='index.php?option=com_joomleague&view=rounds&task=round.display';
		$this->setRedirect($link,$msg);
	}

	/**
	 * display the populate form
	 * 
	 */
	function populate()
	{		
		$option = JRequest::getCmd('option');
		$mainframe = &JFactory::getApplication();
		$document = &JFactory::getDocument();
		$model    = $this->getModel('round');
		$viewType = $document->getType();
		$view     = $this->getView('rounds',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);
		
//		$view->setLayout('populate');
//		$view->display();

		JRequest::setVar('hidemainmenu',0);
		JRequest::setVar('view','rounds');
		JRequest::setVar('layout','populate');

		parent::display();
	}
	
	/**
	 * does the populate operation
	 */
	function startpopulate()
	{
		$msgType = 'message';
		$model = $this->getModel('rounds');
		$project_id = JRequest::getInt('project_id');
		$scheduling = JRequest::getInt('scheduling');
		$time       = JRequest::getVar('time');
		$interval   = JRequest::getInt('interval');
		$start      = JRequest::getVar('start');
		$roundname  = JRequest::getVar('roundname');
		
		$teamsorder = JRequest::getVar('teamsorder', array(), 'post', 'array');
		JArrayHelper::toInteger($teamsorder);
		
		if ($res = $model->populate($project_id, $scheduling, $time, $interval, $start, $roundname, $teamsorder))
		{
			$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_SUCCESSFULL');
		}
		else 
		{
			$msg = Jtext::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR'.': '.$model->getError());
			$msgType = 'error';
		}
		$this->setRedirect('index.php?option=com_joomleague&view=rounds&task=round.display', $msg, $msgType);
	}
  
  function roundrobin()
  {
  $project_id = JRequest::getInt('project_id');
  
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
	function getModel($name = 'Round', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
  
}
?>
