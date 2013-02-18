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
 * Joomleague Component Match Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueControllerMatch extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	function display() {
		$option = JRequest::getCmd('option');
		$document = JFactory::getDocument();
		$mainframe = JFactory::getApplication();

		// check if pid was specified in request
		$pids = JRequest::getVar('pid', null, 'request', 'array');
		    
		$cid = JRequest::getVar('cid',array(0),'','array');
		$match_id = $cid[0];
		
		if ($pids && $pids[0]) {
			$mainframe->setUserState($option.'project', $pids[0]);
		}

		$model=$this->getModel('matches');
		$is_path = $model->checkMatchPicturePath($match_id);
		
		$viewType=$document->getType();
		$view=$this->getView('matches',$viewType);
		$view->setModel($model,true); // true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws, false);
		if ($rid=JRequest::getVar('rid',null,'','array'))
		{
			$mainframe->setUserState($option.'round_id',$rid[0]);
		}
		$roundws=$this->getModel('round');
		$roundws->set('name', 'roundws');
		$roundws->setId($mainframe->getUserState($option.'round_id'));
		$view->setModel($roundws);

		switch ($this->getTask())
		{
			case 'add'		:	{
				JRequest::setVar('hidemainmenu',1);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','match');
				JRequest::setVar('edit',false);
				$model=$this->getModel('match');
				$viewType=$document->getType();
				$view=$this->getView('match',$viewType);
				$view->setModel($model,true);	// true is for the default model;

				$view->setModel($projectws);

				$model=$this->getModel('match');
				$model->checkout();
			}
			break;

			case 'edit'		:	{
				$model=$this->getModel('match');
				$viewType=$document->getType();
				$view=$this->getView('match',$viewType);
				$view->setModel($model,true);	// true is for the default model;

				$view->setModel($projectws);

				JRequest::setVar('hidemainmenu',1);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','match');
				JRequest::setVar('edit',true);

				// Checkout the project
				$model=$this->getModel('match');
				$model->checkout();
			}
			break;

			case 'massadd'	:	{
				JRequest::setVar('massadd',true);
			}
		}

		parent::display();

	}

	function editEvents()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editevents');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the project
		//$model=$this->getModel('match');
		$model->checkout();
		parent::display();
	}

	function editEventsbb()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editeventsbb');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the project
		//$model=$this->getModel('match');
		$model->checkout();
		parent::display();

	}

	function editstats()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editstats');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the project
		//$model=$this->getModel('match');
		$model->checkout();
		parent::display();

	}

	function editReferees()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editreferees');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the project
		//$model=$this->getModel('match');
		$model->checkout();
		parent::display();

	}

	function editlineup()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editlineup');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the match
		$model->checkout();
		parent::display();

	}

	function saveroster()
	{
		$option = JRequest::getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$model=$this->getModel('match');
		$positions=$model->getProjectPositions();
		$staffpositions=$model->getProjectStaffPositions();
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'','array');
		$post['mid']=$cid[0];
		$post['positions']=$positions;
		$post['staffpositions']=$staffpositions;
		$team=$post['team'];

		$model->updateRoster($post);
		$model->updateStaff($post);
    $model->updateTrikotNumber($post);
    
		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editlineup');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the match
		$model=$this->getModel('match');
		$model->checkout();
		$link='index.php?option=com_joomleague&close='.JRequest::getString('close', 0).'&tmpl=component&view=match&task=match.editlineup&cid[]='.$cid[0].'&team='.$team;

		#echo $link.'<br />';
		//$this->setRedirect($link,$msg);
		$this->setRedirect($link);
	}

	function saveReferees()
	{
		$option = JRequest::getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$model=$this->getModel('match');
		$positions=$model->getProjectRefereePositions();
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'','array');
		$post['mid']=$cid[0];
		$post['positions']=$positions;

		if ($model->updateReferees($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_MR');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVE_MR').'<br />'.$model->getError();
		}

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editreferees');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the match
		$model=$this->getModel('match');
		$model->checkout();
		$link='index.php?option=com_joomleague&close='.JRequest::getString('close', 0).'&tmpl=component&view=match&task=match.editreferees&cid[]='.$cid[0];

		//echo $link.'<br />';
		$this->setRedirect($link,$msg);
	}

	// save the checked rows inside the round matches list
	function saveshort()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();

		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');
		for ($x=0; $x < count($cid); $x++)
		{
			$post['match_date'.$cid[$x]]=JoomleagueHelper::convertDate($post['match_date'.$cid[$x]],0);
				
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$proj);
			$cache->clean();
				
			if (!$model->save_array($cid[$x],$post,true,$proj))
			{
				JError::raiseWarning($model->getError());
			}
		}
		$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_MATCH');
		$link='index.php?option=com_joomleague&view=matches&task=match.display';
		$this->setRedirect($link,$msg);
	}

	function copyfrom()
	{
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$msg='';
		$post=JRequest::get('post');
		$model=$this->getModel('match');
		$add_match_count=JRequest::getInt('add_match_count');
		$round_id=JRequest::getInt('rid');
		$post['project_id']=$mainframe->getUserState($option.'project',0);
		$post['round_id']=$mainframe->getUserState($option.'round_id',0);
		$post['match_date']= JoomleagueHelper::convertDate($post['match_date'], 0).' ';
		if ($post['addtype']==1)
		{
			if ($add_match_count > 0) // Only MassAdd a number of new and empty matches
			{
				if (!empty($post['autoPublish'])) // 1=YES Publish new matches
				{
					$post['published']=1;
				}

				$matchNumber= JRequest::getInt('firstMatchNumber',1);
				$roundFound=false;
				if ($projectRounds=$model->getProjectRoundCodes($post['project_id']))
				{
					foreach ($projectRounds AS $projectRound)
					{
						if ($projectRound->id==$post['round_id']){
							$roundFound=true;
						}
						if ($roundFound)
						{
							$post['round_id']=$projectRound->id;
							$post['roundcode']=$projectRound->roundcode;
							for ($x=1; $x <= $add_match_count; $x++)
							{
								if (!empty($post['firstMatchNumber'])) // 1=YES Add continuos match Number to new matches
								{
									$post['match_number']=$matchNumber;
								}
								$post['match_date'].=(empty($post['startTime'])) ? '00:00:00' : $post['startTime'].':00';
								if ($model->store($post))
								{
									$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ADD_MATCH');
									$matchNumber++;
								}
								else
								{
									$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_ADD_MATCH').$model->getError();
									break;
								}
							}
							if (empty($post['addToRound'])) // 1=YES Add matches to all rounds
							{
								break;
							}
						}
					}
				}
			}
		}
		if ($post['addtype']==2)// Copy or mirror new matches from a seleted existing round
		{
			if ($matches=$model->getRoundMatches($round_id))
			{
				foreach($matches as $match)
				{
					//aufpassen,was uebernommen werden soll und welche daten durch die aus der post ueberschrieben werden muessen
					//manche daten muessen auf null gesetzt werden

					$dmatch['match_date']	= JoomleagueHelper::convertDate($post['date'],0).' '.$post['time'];
					if ($post['mirror'] == '1')
					{
						$dmatch['projectteam1_id']	= $match->projectteam2_id;
						$dmatch['projectteam2_id']	= $match->projectteam1_id;
					}
					else
					{
						$dmatch['projectteam1_id']	= $match->projectteam1_id;
						$dmatch['projectteam2_id']	= $match->projectteam2_id;
					}
					$dmatch['project_id']	= $post['project_id'];
					$dmatch['round_id']		= $post['round_id'];
					if ($post['start_match_number'] != '')
					{
						$dmatch['match_number']=$post['start_match_number'];
						$post['start_match_number']++;
					}

					#echo '<pre>'; print_r($dmatch); echo '</pre>';
					if ($model->store($dmatch))
					{
						$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_COPY_MATCH');
					}
					else
					{
						$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_COPY_MATCH').$model->getError();
					}
				}
			}
			else
			{
				$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_COPY_MATCH2').$model->getError();
			}
		}
		//echo $msg;
		$link='index.php?option=com_joomleague&view=matches&task=match.display';
		$this->setRedirect($link,$msg);
	}

	// bilder pro spiel
	function picture()
  {
  $cid = JRequest::getVar('cid',array(0),'','array');
	$match_id = $cid[0];
  $dest = JPATH_ROOT.'/images/com_joomleague/database/matchreport/'.$match_id;
  $folder = 'matchreport/'.$match_id;
  //$this->setState('folder', $folder);
  if(JFolder::exists($dest)) {
  }
  else
  {
  JFolder::create($dest);
  }
  // http://joomla25.diddipoeler.de/administrator/index.php?option=com_media&view=images&tmpl=component&asset=com_joomleague&author=&fieldid=logo_big&folder=com_joomleague/database/clubs/large
  $msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_MATCHPICTURE');
  $link='index.php?option=com_media&view=images&tmpl=component&asset=com_joomleague&author=&folder=com_joomleague/database/'.$folder;
	$this->setRedirect($link,$msg);
  
  }
  //	add a match to round
	function addmatch()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$post=JRequest::get('post');
		$post['project_id']=$mainframe->getUserState($option.'project',0);
		$post['round_id']=$mainframe->getUserState($option.'round_id',0);
		$model=$this->getModel('match');
		if ($model->store($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ADD_MATCH');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_ADD_MATCH').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=matches&task=match.display';
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){
			JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));
		}
		$model=$this->getModel('match');
		if (!$model->delete($cid)){
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&task=match.display&view=matches');
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('matches');
		//$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&task=match.display&view=matches');
	}

	function publish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){
			JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_PUBLISH'));
		}
		$model=$this->getModel('match');
		if (!$model->publish($cid,1)){
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&task=match.display&view=matches');
	}

	function unpublish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){
			JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_UNPUBLISH'));
		}
		$model=$this->getModel('match');
		if (!$model->publish($cid,0)){
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&task=match.display&view=matches');
	}

	function savedetails()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$proj=$mainframe->getUserState($option.'project',0);

		$cid=JRequest::getVar('cid',array(),'post','array');
		$post=JRequest::get('post');
		$summary=JRequest::getVar('summary','','post','string',JREQUEST_ALLOWRAW);
		$preview=JRequest::getVar('preview','','post','string',JREQUEST_ALLOWRAW);
		$post['id']=(int)$cid[0];
		$post['summary']=$summary;
		$post['preview']=$preview;
		$model=$this->getModel('match');
		if ($returnid=$model->savedetails($post))
		{
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$proj);
			$cache->clean();

			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_MD');
			$type='message';
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_MD').': '.$model->getError();
			$type='error';
		}

		$this->setRedirect('index.php?option=com_joomleague&close='.JRequest::getString('close', 0).'&tmpl=component&task=match.edit&cid[]='.$cid[0],$msg,$type);
	}

	function saveevent()
	{
		$option = JRequest::getCmd('option');

		// Check for request forgeries
		JRequest::checkToken("GET") or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$mainframe = JFactory::getApplication();
		$data = array();
		$data['teamplayer_id']	= JRequest::getInt('teamplayer_id');
		$data['projectteam_id']	= JRequest::getInt('projectteam_id');
		$data['event_type_id']	= JRequest::getInt('event_type_id');
		$data['event_time']		= JRequest::getVar('event_time', '');
		$data['match_id']		= JRequest::getInt('match_id');
		$data['event_sum']		= JRequest::getVar('event_sum', '');
		$data['notice']			= JRequest::getVar('notice', '');
		$data['notes']			= JRequest::getVar('notes', '');
		
		$model=$this->getModel('match');
		$project_id=$mainframe->getUserState($option.'project',0);
		if (!$result=$model->saveevent($data, $project_id)) {
			$result="0"."\n".JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_EVENT').': '.$model->getError();
		} else {
			$result=JRequest::getVar('rowid',0).'\n'.JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_EVENT');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	function savesubst()
	{
		// Check for request forgeries
		JRequest::checkToken("GET") or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$data = array();
		$data['in'] 					= JRequest::getInt('in');
		$data['out'] 					= JRequest::getInt('out');
		$data['matchid'] 				= JRequest::getInt('matchid');
		$data['in_out_time'] 			= JRequest::getVar('in_out_time');
		$data['project_position_id'] 	= JRequest::getInt('project_position_id');

		$model=$this->getModel('match');
		if (!$result=$model->savesubstitution($data)){
			$result="0"."\n".JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_SUBST').': '.$model->getError();
		} else {
			$result=JRequest::getVar('rowid',0).'\n'.JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_SUBST');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	function removeSubst()
	{
		JRequest::checkToken("GET") or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$substid = JRequest::getInt('substid',0);
		$model=$this->getModel('match');
		if (!$result=$model->removeSubstitution($substid))
		{
			$result="0"."\n".JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_REMOVE_SUBST').': '.$model->getError();
		}
		else
		{
			$result="1"."\n".JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_REMOVE_SUBST');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	// save the checked rows inside matcheventsbb list
	function saveeventbb()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'','array');
		$model=$this->getModel('match');
		$project_id=$mainframe->getUserState($option.'project',0);
		if ($model->saveeventbb($post,$project_id))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_UPDATE_EVENTS');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_UPDATE_EVENTS').$model->getError();
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editeventsbb');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);
		$link='index.php?option=com_joomleague&close='.JRequest::getString('close', 0).'&view=match&task=match.editeventsbb&cid[]='.$cid[0];
		$this->setRedirect($link, $msg);
	}


	/**
	 * save the match stats
	 */
	function savestats()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getInt('match_id');
		$model=$this->getModel('match');
		if ($model->savestats($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_UPDATE_STATS');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_UPDATE_STATS').$model->getError();
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editstats');
		JRequest::setVar('view','match');
		JRequest::setVar('tmpl','component');
		JRequest::setVar('cid',array($post['match_id']));
		JRequest::setVar('msg',$msg);
		$link='index.php?option=com_joomleague&close='.JRequest::getString('close', 0).'&tmpl=component&view=match&task=match.editstats&cid[]='.$cid;
		$this->setRedirect($link, $msg);
	}

	function removeEvent()
	{
		// Check for request forgeries
		JRequest::checkToken("GET") or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$event_id=JRequest::getInt('event_id');
		$model=$this->getModel('match');
		if (!$result=$model->deleteevent($event_id))
		{
			$result="0"."\n".JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_DELETE_EVENTS').': '.$model->getError();
		}
		else
		{
			$result="1"."\n".JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_DELETE_EVENTS');
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

}
?>