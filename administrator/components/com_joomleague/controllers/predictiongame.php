<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
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
//require_once(JPATH_COMPONENT.DS.'controllers'.DS.'joomleague.php');

/**
 * Joomleague Prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueControllerPredictionGame extends JoomleagueController
{

//protected $view_list = 'predictiongames';

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
		$this->registerTask('apply_project_settings','save_project_settings');
		$this->registerTask('copy','copysave');
	}

	function display()
	{
		//$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
    $mainframe->enqueueMessage(JText::_('PredictionGame Task -> '.$this->getTask()),'');
	 	$model=$this->getModel('predictiongames');
		$viewType=$document->getType();
		$view=$this->getView('predictiongames',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$prediction_id1=JRequest::getVar('prediction_id','-1','','int');
		$prediction_id2=(int) $mainframe->getUserState('com_joomleague'.'prediction_id');

		if ($prediction_id1 > (-1))
		{
			$mainframe->setUserState('com_joomleague'.'prediction_id',(int) $prediction_id1);
		}
		else
		{
			$mainframe->setUserState('com_joomleague'.'prediction_id',(int) $prediction_id2);
		}
		$prediction_id=(int) $mainframe->getUserState('com_joomleague'.'prediction_id');

		switch($this->getTask())
		{
			case 'add'	 :
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','predictiongame');
				JRequest::setVar('edit',false);

				// Checkout the project
				$model=$this->getModel('predictiongame');
				$model->checkout();
			} break;

			case 'edit'	:
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','predictiongame');
				JRequest::setVar('edit',true);

				// Checkout the project
				$model=$this->getModel('predictiongame');
				$model->checkout();
			} break;

			case 'predsettings'	:
			{
				$cid	= JRequest::getVar('cid');
				JRequest::setVar('prediction_project',(int) $cid[0]);
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','predsettings');
				JRequest::setVar('view','predictiongame');
				JRequest::setVar('edit',true);

				// Checkout the project
				$model=$this->getModel('predictiongame');
				$model->checkout();
			} break;

		}

		parent::display();
	}

	// save prediction_game in cid and save/update also the pred_admins and pred_projects associated with the saved predction_game
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
    $option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');

		//echo '<pre>'; print_r($post); echo '</pre>'; 

		$post['id']=(int)$cid[0];
		$msg='';
		$d=' - ';

		$model=$this->getModel('predictiongame');

		if ($model->store($post))
		{
			$msg .= JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_SAVED_PGAME');

			if ($post['id'] == 0){$post['id']=$model->getDbo()->insertid();}

			if ($model->storePredictionAdmins($post))
			{
				$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_SAVED_ADMINS');
			}
			else
			{
				$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_ERROR_SAVE_ADMINS').$model->getError();
			}

			if ($model->storePredictionProjects($post))
			{
				$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_SAVED_PROJECTS');
			}
			else
			{
				$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_ERROR_SAVE_PROJECTS').$model->getError();
			}
		}
		else
		{
			$msg .= JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_ERROR_SAVE_PGAME').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=predictiongames&task=predictiongame.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=predictiongame.edit&cid[]='.$post['id'];
		}
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	// remove the prediction_game(s) in cid and remove also the projects,members and tipps associated with the deleted prediction_game(s)
	function remove()
	{
    $option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    
		$d=' - ';
		$msg='';
		$cid=JRequest::getVar('cid',array(),'post','array'); JArrayHelper::toInteger($cid);

		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_ITEM'));}

		$model=$this->getModel('predictiongame');

		if (!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$msg .= JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PGAME');
		if (!$model->deletePredictionAdmins($cid))
		{
			$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_ADMINS_MSG').$model->getError();
		}

		$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_ADMINS');
		if (!$model->deletePredictionProjects($cid))
		{
			$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PROJECTS_MSG').$model->getError();
		}

		$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PROJECTS');
		if (!$model->deletePredictionMembers($cid))
		{
			$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PMEMBERS_MSG').$model->getError();
		}

		$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PMEMBERS');
		if (!$model->deletePredictionResults($cid))
		{
			$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PRESULTS_MSG').$model->getError();
		}
		$msg .= $d.JText::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PRESULTS');

		$link='index.php?option=com_joomleague&view=predictiongames';
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('predictiongame');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongames');
	}
  
  public function publish() {
		$this->view_list = 'predictiongames';
		parent::publish();
	}
	
	public function unpublish() {
		$this->view_list = 'predictiongames';
		parent::unpublish();
	}
	
/*
	function publish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_ADMIN_PGAME_CTRL_PUBLISH_ITEM'));}
		$model=$this->getModel('predictiongame');
		if(!$model->publish($cid,1))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongames');
	}

	function unpublish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_ADMIN_PGAME_CTRL_UNPUBLISH_ITEM'));}
		$model=$this->getModel('predictiongame');
		if (!$model->publish($cid,0))
		{
			echo "<script> alert('".$model->getError(true)  ."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongames');
	}
*/

	// copy and save prediction_game in cid and save/update also the pred_admins and pred_projects associated with the saved predction_game
	function copysave()
	{
		JToolBarHelper::title(JText::_('JL_ADMIN_PGAME_CTRL_COPY_PGAME'),'generic.png');
		JToolBarHelper::back(JText::_('JL_ADMIN_PGAME_CTRL_BACK'),JRoute::_('index.php?option=com_joomleague&view=predictiongames'));
		$post		= JRequest::get('post');
		$cid		= JRequest::getVar('cid',array(0),'post','array');

		//echo '<pre>'; print_r($post); echo '</pre>';

		$post['id']=(int) $cid[0];
		$msg		= '';
		$d			= ' - ';

		$model=$this->getModel('predictiongame');

		/*
		if ($model->store($post))
		{
			$msg .= JText::_('Prediction Game Saved');

			if ($post['id'] == 0)
			{
				$post['id']=mysql_insert_id();
			}

			if ($model->storePredictionAdmins($post))
			{
				$msg .= $d.JText::_('Admins of Prediction Game Saved');
			}
			else
			{
				$msg .= $d.JText::_('Error while saving admins data of predictiongame').$model->getError();
			}

			if ($model->storePredictionProjects($post))
			{
				$msg .= $d.JText::_('Projects of Prediction Game Saved');
			}
			else
			{
				$msg .= $d.JText::_('Error while saving projects data of predictiongame').$model->getError();
			}
		}
		else
		{
			$msg .= JText::_('Error while saving general data of predictiongame').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask() == 'save')
		{
			$link='index.php?option=com_joomleague&view=predictiongames';
		}
		else
		{
			$link='index.php?option=com_joomleague&controller=predictiongame&task=edit&cid[]='.$post['id'];
		}
		*/
		echo $msg;
		//$this->setRedirect($link,$msg);
	}

	function save_project_settings()
	{
		JToolBarHelper::title(JText::_('JL_ADMIN_PGAME_CTRL_SAVE'),'generic.png');
		JToolBarHelper::back(JText::_('JL_ADMIN_PGAME_CTRL_BACK'),JRoute::_('index.php?option=com_joomleague&view=predictiongames'));

		$msg='';
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		//$psapply=JRequest::getInt('psapply',0,'post');
		$post['id']=(int) $cid[0];
		//echo '<pre>'; print_r($this->getTask()); echo '</pre>';

		$model=$this->getModel('predictiongame');

		if ($model->savePredictionProjectSettings($post))
		{
			$msg .= JText::_('JL_ADMIN_PGAME_CTRL_SAVE_PPROJECT');
		}
		else
		{
			$msg .= JText::_('JL_ADMIN_PGAME_CTRL_ERROR_SAVE_PPROJECT').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask() == 'save_project_settings')
		{
			$link='index.php?option=com_joomleague&view=predictiongames';
		}
		else
		{
			$link='index.php?option=com_joomleague&view=predictiongame&controller=predictiongame&task=predsettings&cid[]='.$post['id'];
			/*
			if ($psapply==0)
			{
				$link='index.php?option=com_joomleague&controller=predictiongame&task=edit&cid[]='.$post['id'];
			}
			else
			{
				//$link='index.php?option=com_joomleague&controller=predictiongame&task=edit&cid[]='.$post['id'];
				$link='index.php?option=com_joomleague&view=predictiongame&controller=predictiongame&task=predsettings&cid[]='.$post['id'];
			}
			*/
		}
		//echo $psapply.'#<br />';
		//echo $msg.'<br />';
		//echo $link.'<br />';
		$this->setRedirect($link,$msg);
	}

	function rebuild()
	{
		JToolBarHelper::title(JText::_('JL_ADMIN_PGAME_CTRL_REBUILD'),'generic.png');
		JToolBarHelper::back(JText::_('JL_ADMIN_PGAME_CTRL_BACK'),JRoute::_('index.php?option=com_joomleague&view=predictiongames'));

		$cid=JRequest::getVar('cid',array(0),'post','array');
		$msg='';
		$model=$this->getModel('predictiongame');

		if ($model->rebuildPredictionProjectSPoints($cid))
		{
			$msg .= JText::_('JL_ADMIN_PGAME_CTRL_REBUILT');
		}
		else
		{
			$msg .= JText::_('JL_ADMIN_PGAME_CTRL_ERROR_REBUILT').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		//$model->checkin();
		$link='index.php?option=com_joomleague&view=predictiongames';
		//echo $msg.'<br />';
		//echo $link.'<br />';
		$this->setRedirect($link,$msg);
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
	function getModel($name = 'predictiongame', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
/*

	function copysave()
	{
		JToolBarHelper::title(JText::_('JoomLeague - Copy project'),'generic.png');
		JToolBarHelper::back('Back to project list','index.php?option=com_joomleague&view=projects');
		$post	= JRequest::get('post');
		#$cid	= JRequest::getVar('cid',array(0),'post','array');
		#$post['id']=(int) $cid[0];

		$newLeagueCheck=JRequest::getVar('newLeagueCheck',0,'post','int');
		$leagueNew=trim(JRequest::getVar('leagueNew',JText::_('New league'),'post','string'));
		$newLeagueId=JRequest::getVar('oldleague',0,'post','int');
		$newSeasonCheck=JRequest::getVar('newSeasonCheck',0,'post','int');
		$seasonNew=trim(JRequest::getVar('seasonNew',JText::_('New Season'),'post','string'));
		$newSeasonId=JRequest::getVar('oldseason',0,'post','int');

		//echo '<pre>'; print_r($post); echo '</pre>';

		if (($newLeagueCheck == 1) && ($leagueNew != '')) // add new league if needed
		{
			echo JText::_('Adding new league...').'&nbsp;&nbsp;';
			$model=$this->getModel('league');

			$newLeagueId=$model->addLeague($leagueNew);
			echo $newLeagueId.'<br />';
		}
		JRequest::setVar('league_id',$newLeagueId,'post',true);

		if (($newSeasonCheck == 1) && ($seasonNew != '')) // add new season if needed
		{
			echo JText::_('Adding new season...').'&nbsp;&nbsp;';
			$model=$this->getModel('season');

			$newSeasonId=$model->addSeason($seasonNew);

			echo $newSeasonId.'<br />';
		}
		JRequest::setVar('season_id',$newSeasonId,'post',true);

		$model=$this->getModel('projects');

		$post	= JRequest::get('post');
		$cid	= JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		#echo '<pre>'; print_r($post); echo '</pre>';

		if (!$model->cpCheckPExists($post)) //check project unicity if season and league are not both new
		{
			$link='index.php?option=com_joomleague&controller=project&view=projects';
			$msg='This project already exists! Please change name,league or season!';
			$this->setRedirect($link,$msg);
		}

		if ((isset($post['fav_team'])) && (count($post['fav_team']) > 0))
		{
			$temp=implode(",",$post['fav_team']);
		}
		else
		{
			$temp="";
		}
		$post['fav_team']=$temp;

		echo JText::_('Copying project settings...<br />');
		$model=$this->getModel('project');
		if ($model->store($post)) //copy project data and get a new project_id
		{
			//	save the templates params
			if ($post['id'] == 0)
			{
				$post['id']=mysql_insert_id();
			}

			$templatesModel =& JLGModel::getInstance('Templates','JoomleagueModel');
			$templatesModel->setProjectId($post['id']);
			$templatesModel->checklist();

			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();

			echo JText::_('Project settings were saved...<br /><br />');
			echo JText::_('Copying project divisions...<br />');
			$source_to_copy_division=Array("0" => 0);
			$model=$this->getModel('division');
			if ($source_to_copy_division=$model->cpCopyDivisions($post)) //copy project divisions
			{
				echo JText::_('Project divisions copied...<br /><br />');
				echo JText::_('Copying project teams...<br />');
				$model=$this->getModel('projectteam');
				if ($model->cpCopyTeams($post,$source_to_copy_division)) //copy project teams
				{
					echo JText::_('Project teams copied...<br /><br />');
				}
				else
				{
					echo JText::_('Error copying project teams!<br /><br />').$model->getError().'<br />';
				}
				echo JText::_('Copying project positions...<br />');
				$model=$this->getModel('projectposition');
				if ($model->cpCopyPositions($post)) //copy project team-positions
				{
					echo JText::_('Project positions copied...').'<br /><br />';
					echo JText::_('Copying project rounds...').'<br />';
					$model=$this->getModel('round');
					if ($model->cpCopyRounds($post)) //copy project team-positions
					{
						echo JText::_('Project rounds copied...').'<br /><br />';
					}
					else
					{
						echo JText::_('Error copying project positions!').'<br />'.$model->getError().'<br />';
					}
				}
				else
				{
					echo JText::_('Error copying project positions!<br /><br />').$model->getError().'<br />';
				}

			}
			else
			{
				echo JText::_('Error copying project divisions!<br /><br />').$model->getError().'<br />';
			}
			$link='index.php?option=com_joomleague&controller=project&view=projects';
		}
		else
		{
			echo JText::_('Error saving project settings!<br /><br />').$model->getError().'<br />';
		}
		#$this->setRedirect($link,$msg);
	}
*/

}
?>