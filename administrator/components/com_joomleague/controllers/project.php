<?php
/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license		GNU/GPL,see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License,and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Joomleague Component Project Model
 *
 * @author 	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerProject extends JoomleagueController
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
		$mainframe 		= JFactory::getApplication();
		$sports_type	= JRequest::getInt('filter_sports_type',0);
		$season			= JRequest::getInt('filter_season',0);
		$mainframe->setUserState($option.'.projects.filter_sports_type', $sports_type);
		$mainframe->setUserState($option.'.projects.filter_season', $season);

		$document = JFactory::getDocument();
		$model=$this->getModel('project');
		$viewType=$document->getType();
		$view=$this->getView('project',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		$view->setLayout('form');
		
		switch($this->getTask())
		{
			case 'add'	:
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('view','project');
				JRequest::setVar('edit',false);
				JRequest::setVar('layout', 'form');
				
				// Checkout the project
				$model=$this->getModel('project');
				$model->checkout();
			} break;

			case 'edit'	:
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('view','project');
				JRequest::setVar('edit',true);
				JRequest::setVar('layout', 'form');
				
				// Checkout the project
				$model=$this->getModel('project');
				$model->checkout();
			} break;

			case 'copy'	:
			{
				$cid=JRequest::getVar('cid',array(0),'post','array');
				$copyID=(int) $cid[0];

				JRequest::setVar('hidemainmenu',1);
				JRequest::setVar('view','project');
				JRequest::setVar('edit',true);
				JRequest::setVar('copy',true);
			} break;
		}
		parent::display($cachable = false, $urlparams = false);
	}

	function copy()
	{
		JRequest::checkToken() or die(JText::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_PROJECT_COPY_TITLE'),'generic.png');
		JToolBarHelper::back('COM_JOOMLEAGUE_PROJECT_BACK','index.php?option=com_joomleague&view=projects&task=project.display');
		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid',array(0),'post','array');
		$model=$this->getModel('project');
		$post = JArrayHelper::fromObject($model->getData());
		$post['old_id'] = $cid;
		$post['id'] = 0; //will save it as new project
		$post['name'] = JText::_('COM_JOOMLEAGUE_PROJECT_COPY_COPY_OF') . ' ' . $post['name'];
		echo '<br />'.JText::_('COM_JOOMLEAGUE_PROJECT_COPY_SETTINGS').'<br />';
		if ($model->store($post)) //copy project data and get a new project_id
		{
			//	save the templates params
			if ($post['id']==0){$post['id']=$model->getDbo()->insertid();}

			$templatesModel =& JLGModel::getInstance('Templates','JoomleagueModel');
			$templatesModel->setProjectId($post['id']);
			$templatesModel->checklist();

			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();
			echo JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'<br />';

			echo '<br />'.JText::_('COM_JOOMLEAGUE_PROJECT_COPY_DIVISIONS').'<br />';
			$source_to_copy_division=Array('0' => 0);
			$model=$this->getModel('division');
			if ($source_to_copy_division=$model->cpCopyDivisions($post)) //copy project divisions
			{
				echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'<br />';

				echo '<br />'.JText::_('COM_JOOMLEAGUE_PROJECT_COPY_TEAMS').'<br />';
				$model=$this->getModel('projectteam');
				if ($model->cpCopyTeams($post,$source_to_copy_division)) //copy project teams
				{
					echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'<br />';
				}
				else
				{
					echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$model->getError().'<br />';
				}

				echo '<br />'.JText::_('COM_JOOMLEAGUE_PROJECT_COPY_POSITIONS').'<br />';
				$model=$this->getModel('projectposition');
				if ($model->cpCopyPositions($post)) //copy project team-positions
				{
					echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'<br />';

					echo '<br />'.JText::_('COM_JOOMLEAGUE_PROJECT_COPY_ROUNDS').'<br />';
					$model=$this->getModel('round');
					if ($model->cpCopyRounds($post)) //copy project team-positions
					{
						echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'<br />';
					}
					else
					{
						echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$model->getError().'<br />';
					}
				}
				else
				{
					echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$model->getError().'<br />';
				}
			}
			else
			{
				echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$model->getError().'<br />';
			}

			echo '<br />'.JText::_('COM_JOOMLEAGUE_PROJECT_COPY_REFEREES').'<br />';
			$model=$this->getModel('projectreferee');
			if ($model->cpCopyProjectReferees($post))
			{
				echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'<br />';
			}
			else
			{
				echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$model->getError().'<br />';
			}
			$link='index.php?option=com_joomleague&view=projects&task=project.display';
		}
		else
		{
			echo '<br />'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$model->getError().'<br />';
		}
		#$this->setRedirect($link,$msg);
	}

	function remove()
	{
		JRequest::checkToken() or die(JText::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();

		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_PROJECT_DELETE_TITLE'),'generic.png');
		JToolBarHelper::back('COM_JOOMLEAGUE_PROJECT_BACK','index.php?option=com_joomleague&view=projects&task=project.display');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));}

		foreach ($cid as $pid)
		{
			//delete project
			$model=$this->getModel('project');
			$project_id=(int)$pid;
			if (!$model->exists($project_id))
			{
				echo JText::sprintf('COM_JOOMLEAGUE_PROJECT_NOT_EXISTS',"<b>$project_id</b>").'<br />';
				break;
			}

			echo '<h3>'.JText::sprintf('COM_JOOMLEAGUE_PROJECT_DELETING','<i>'.$model->getProjectName($project_id).'</i>').'</h3>';
			
			//delete matches
			echo JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_MATCHES').'&nbsp;&nbsp;';
			$model=$this->getModel('match');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			
			//delete rounds
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_ROUNDS').'&nbsp;&nbsp;';
			$model=$this->getModel('round');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			
			//delete projectpositions
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_POSITIONS').'&nbsp;&nbsp;';
			$model=$this->getModel('position');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			
			//delete projectreferees
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_REFEREES').'&nbsp;&nbsp;';
			$model=$this->getModel('projectreferee');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			
			//delete teamplayers
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_PLAYERS').'&nbsp;&nbsp;';
			$mdlProject=$this->getModel('project');
			$mdlProject->setId($project_id);
			$mdlProject->getData();
			if (!$mdlProject->removeProjectPlayers($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProject->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			//delete teamstaff
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_STAFFS').'&nbsp;&nbsp;';
			if (!$mdlProject->removeProjectStaff($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProject->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			
			//delete projectteams
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_TEAMS').'&nbsp;&nbsp;';
			$model=$this->getModel('team');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			//delete treetos
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_TREETOS').'&nbsp;&nbsp;';
			$model=$this->getModel('treeto');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			// Delete project divisions in table #__joomleague_division
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_DIVISIONS').'&nbsp;&nbsp;';
			$model=$this->getModel('division');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
			
			//delete projectteamplates
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_TEMPLATES').'&nbsp;&nbsp;';
			$model=$this->getModel('template');
			if (!$model->deleteOne($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}

			//delete projectsettings?
			echo '<br /><br />'.JText::_('COM_JOOMLEAGUE_PROJECT_DELETING_SETTINGS').'&nbsp;&nbsp;';
			if (!$mdlProject->delete($project_id))
			{
				echo '<span style="color:red">'.JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$model->getError();
				break;
			}
			else
			{
				echo '<span style="color:green">'.JText::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
			}
		}
		//$this->setRedirect('index.php?option=com_joomleague&view=projects');
	}

	function save()
	{
		$app = JFactory::getApplication();
		
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		$msg='';
		// convert dates back to mysql date format
		if (isset($post['start_date']))
		{
			$post['start_date']=strtotime($post['start_date']) ? strftime('%Y-%m-%d',strtotime($post['start_date'])) : null;
		}
		else
		{
			$post['start_date']=null;
		}

		if (isset($post['fav_team']))
		{
			if (count($post['fav_team']) > 0)
			{
				$temp=implode(",",$post['fav_team']);
			}
			else
			{
				$temp='';
			}
			$post['fav_team']=$temp;
		}
		else
		{
			$post['fav_team']='';
		}
		if (isset($post['extension']))
		{
			if (count($post['extension']) > 0)
			{
				$temp=implode(",",$post['extension']);
			}
			else
			{
				$temp='';
			}
			$post['extension']=$temp;
		}
		else
		{
			$post['extension']='';
		}

		if (isset($post['leagueNew']))
		{
			$mdlLeague=$this->getModel('league');
			$post['league_id']=$mdlLeague->addLeague($post['leagueNew']);
			$msg .= JText::_('COM_JOOMLEAGUE_LEAGUE_CREATED').',';

		}
		if (isset($post['seasonNew']))
		{
			$mdlSeason=$this->getModel('season');
			$post['season_id']=$mdlSeason->addSeason($post['seasonNew']);
			$msg .= JText::_('COM_JOOMLEAGUE_SEASON_CREATED').',';
		}

		$model=$this->getModel('project');
		
		$form = $model->getForm($post, false);
		// Test whether the data is valid.
		$validData = $model->validate($form, $post);
				
		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
		
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
			
			// Save the data in the session.
			$app->setUserState('com_joomleague.edit.project' . '.data', $post);
		
			// Redirect back to the edit screen.
			$this->setRedirect(
					JRoute::_('index.php?option=com_joomleague&task=project.edit&cid[]='.$post['id'], false)
			);
			
			return false;
		}
				
		if ($model->store($post))
		{
			
			// clear data in the session.
			$app->setUserState('com_joomleague.edit.project' . '.data', null);
			
			//	save the templates params
			if ($post['id']==0){$post['id']=$model->getDbo()->insertid();}
			$templatesModel =& JLGModel::getInstance('Templates','JoomleagueModel');
			$templatesModel->setProjectId($post['id']);
			$templatesModel->checklist();
			$msg .= JText::_('COM_JOOMLEAGUE_PROJECT_SAVED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ERROR_SAVING_PROJECT').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=projects&task=project.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=project.edit&cid[]='.$post['id'];
		}
		//echo $msg;
		$this->setRedirect($link,$msg);
	}
	
	public function publish() {
		$this->view_list = 'projects';
		parent::publish();
	}
	
	public function unpublish() {
		$this->view_list = 'projects';
		parent::unpublish();
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('project');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=projects&task=project.display');
	}

	function orderup()
	{
		$model=$this->getModel('project');
		$model->move(-1);
		$this->setRedirect('index.php?option=com_joomleague&view=projects&task=project.display');
	}

	function orderdown()
	{
		$model=$this->getModel('project');
		$model->move(1);
		$this->setRedirect('index.php?option=com_joomleague&view=projects&task=project.display');
	}
	
	function saveorder()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		$order=JRequest::getVar('order',array(),'post','array');
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		$model=$this->getModel('project');
		$model->saveorder($cid,$order);
		$this->setRedirect('index.php?option=com_joomleague&view=projects&task=project.display',JText::_('COM_JOOMLEAGUE_GLOBAL_NEW_ORDERING_SAVED'));
	}

	function import()
	{
		JRequest::setVar('view','import');
		JRequest::setVar('table','project');
		parent::display();
	}
	
	function export()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'));}
		$model = $this->getModel("project");
		$model->export($cid, "project", "Joomleague15");
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
	function getModel($name = 'Project', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
}
?>
