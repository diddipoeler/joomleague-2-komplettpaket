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

class JoomleagueControllerjlextindividualsport extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
    $this->registerTask('show','display');
		$this->registerTask('apply','save');
	}

	function display()
	{		
		$option='com_joomleague';
		$document =& JFactory::getDocument();
		$mainframe =& JFactory::getApplication();
		
 		$cid = JRequest::getVar('cid', null, 'request', 'array');
    if ($cid && $cid[0]) 
    {
			$mainframe->setUserState($option.'match_id', $cid[0]);
		}
		if ($projectteam1_id = JRequest::getVar('team1'))
		{
			$mainframe->setUserState($option.'projectteam1_id',$projectteam1_id);
		}
		if ($projectteam2_id = JRequest::getVar('team2'))
		{
			$mainframe->setUserState($option.'projectteam2_id',$projectteam2_id);
		}
		
//     $post=JRequest::get('post');
    				
		// check if pid was specified in request
		$pids = JRequest::getVar('pid', null, 'request', 'array');
		if ($pids && $pids[0]) {
			$mainframe->setUserState($option.'project', $pids[0]);
		}
		
		$model=$this->getModel('jlextindividualsportes');
		$viewType=$document->getType();
		$view=$this->getView('jlextindividualsportes',$viewType);
		$view->setModel($model,true); // true is for the default model;
		
		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
		$projectws->setId($mainframe->getUserState($option.'project',0));
		
    JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','default');
		JRequest::setVar('view','jlextindividualsportes');
		JRequest::setVar('edit',false);
                
    //$projectws->projectteam1_id	= $post['projectteam1_id'];
    //$projectws->projectteam2_id	= $post['projectteam2_id'];
		
//  		$projectws->projectteam1_id	= JRequest::getCmd('team1');
//  		$projectws->projectteam2_id	= JRequest::getCmd('team2');
//  		$projectws->match_id	= $cid[0];
		
		$view->setModel($projectws);
		if ($rid=JRequest::getVar('rid',null,'','array'))
		{
			$mainframe->setUserState($option.'round_id',$rid[0]);
		}
		$roundws=$this->getModel('round');
		$roundws->_name='roundws';
		$roundws->setId($mainframe->getUserState($option.'round_id'));
		$view->setModel($roundws);

		switch ($this->getTask())
		{
			case 'add'		:	{
								JRequest::setVar('hidemainmenu',1);
								JRequest::setVar('layout','form');
								JRequest::setVar('view','jlextindividualsport');
								JRequest::setVar('edit',false);
								$model=$this->getModel('jlextindividualsport');
								$viewType=$document->getType();
								$view=$this->getView('jlextindividualsport',$viewType);
								$view->setModel($model,true);	// true is for the default model;

								$view->setModel($projectws);

								$model=$this->getModel('match');
								$model->checkout();
								}
								break;

			case 'edit'		:	{
								$model=$this->getModel('jlextindividualsport');
								$viewType=$document->getType();
								$view=$this->getView('jlextindividualsport',$viewType);
								$view->setModel($model,true);	// true is for the default model;

								$view->setModel($projectws);

								JRequest::setVar('hidemainmenu',1);
								JRequest::setVar('layout','form');
								JRequest::setVar('view','jlextindividualsport');
								JRequest::setVar('edit',true);

								// Checkout the project
								$model=$this->getModel('jlextindividualsport');
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
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
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
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
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
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
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
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
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
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$proj=$mainframe->getUserState($option.'project',0);
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'get','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('match');

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
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
		$option='com_joomleague';
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');

		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
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

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editlineup');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the match
		$model=$this->getModel('match');
		$model->checkout();
		$link='index.php?option=com_joomleague&tmpl=component&controller=match&view=match&task=Editlineup&cid[]='.$cid[0].'&team='.$team;

		#echo $link.'<br />';
		//$this->setRedirect($link,$msg);
		$this->setRedirect($link);
	}

	function saveReferees()
	{
		$option='com_joomleague';
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');

		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$model=$this->getModel('match');
		$positions=$model->getProjectRefereePositions();
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'','array');
		$post['mid']=$cid[0];
		$post['positions']=$positions;

		if ($model->updateReferees($post))
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_SAVED_MR');
		}
		else
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_SAVE_MR').'<br />'.$model->getError();
		}

		$viewType=$document->getType();
		$view=$this->getView('match',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->_name='projectws';
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editreferees');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);

		// Checkout the match
		$model=$this->getModel('match');
		$model->checkout();
		$link='index.php?option=com_joomleague&tmpl=component&controller=match&view=match&task=editreferees&cid[]='.$cid[0];

		//echo $link.'<br />';
		$this->setRedirect($link,$msg);
	}

	// save the checked rows inside the round matches list
	function saveshort()
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
	
		$proj=$mainframe->getUserState($option.'project',0);
		$sporttype = $mainframe->getUserState( $option . 'sporttype' );
		
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);

		$model=$this->getModel('jlextindividualsport');
		for ($x=0; $x < count($cid); $x++)
		{
			//$post['match_date'.$cid[$x]]=JoomleagueHelper::convertDate($post['match_date'.$cid[$x]],0);
			
			//clear ranking cache
			$cache = & JFactory::getCache('joomleague.project'.$proj);
			$cache->clean();
			
			if (!$model->save_array($cid[$x],$post,true,$proj))
			{
				JError::raiseWarning($model->getError());
			}
		}
		
//		$model->save_round_match();
		$model->save_roster_match();
		
		//$model2 = $this->getModel('project');
switch(strtolower($sporttype))
{
case 'kegeln':
$model->save_round_match_kegeln();
$event_config = $model->getTemplateConfig(strtolower($sporttype));
$model->save_events_match($event_config);
break;

case 'tennis':
$model->save_round_match_tennis();
break;

}  

		
		
		$msg=JText::_('JL_ADMIN_MATCH_CTRL_SAVED_SINGLE_MATCH');
		$link='index.php?option=com_joomleague&view=jlextindividualsportes&controller=jlextindividualsport';
		$this->setRedirect($link,$msg);
	}

	function copyfrom()
	{
		$mainframe =& JFactory::getApplication();
		$option='com_joomleague';
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
						if ($projectRound->id==$post['round_id']){$roundFound=true;}
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
									$msg=JText::_('JL_ADMIN_MATCH_CTRL_ADD_MATCH');
									$matchNumber++;
								}
								else
								{
									$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_ADD_MATCH').$model->getError();
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
						$msg=JText::_('JL_ADMIN_MATCH_CTRL_COPY_MATCH');
					}
					else
					{
						$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_COPY_MATCH').$model->getError();
					}
				}
			}
			else
			{
				$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_COPY_MATCH2').$model->getError();
			}
		}
		//echo $msg;
		$link='index.php?option=com_joomleague&view=matches&controller=match';
		$this->setRedirect($link,$msg);
	}

	//	add a match to round
	function addmatch()
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$post=JRequest::get('post');
		
		$post['match_id']		= $mainframe->getUserState( $option . 'match_id',0 );
		$post['project_id']=$mainframe->getUserState($option.'project',0);
		$post['round_id']=$mainframe->getUserState($option.'round_id',0);
		$model=$this->getModel('jlextindividualsport');
		if ($model->store($post))
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ADD_SINGLE_MATCH');
		}
		else
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_ADD_SINGLE_MATCH').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=jlextindividualsportes&controller=jlextindividualsport';
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_GLOBAL_SELECT_TO_DELETE'));}
		$model=$this->getModel('jlextindividualsport');
		if (!$model->delete($cid)){echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";}
		$this->setRedirect('index.php?option=com_joomleague&view=jlextindividualsportes&controller=jlextindividualsport');
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('matches');
		//$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&controller=match&view=matches');
	}

	function publish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_GLOBAL_SELECT_TO_PUBLISH'));}
		$model=$this->getModel('jlextindividualsport');
		if (!$model->publish($cid,1)){echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";}
		$this->setRedirect('index.php?option=com_joomleague&view=jlextindividualsportes&controller=jlextindividualsport');
	}

	function unpublish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_GLOBAL_SELECT_TO_UNPUBLISH'));}
		$model=$this->getModel('jlextindividualsport');
		if (!$model->publish($cid,0)){echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";}
		$this->setRedirect('index.php?option=com_joomleague&view=jlextindividualsportes&controller=jlextindividualsport');
	}

	function savedetails()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');

		$option='com_joomleague';		
		$mainframe =& JFactory::getApplication();		
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
			$cache = & JFactory::getCache('joomleague.project'.$proj);
			$cache->clean();		
				
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_SAVED_MD');
			$type='message';
		}
		else
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_SAVED_MD').': '.$model->getError();
			$type='error';
		}
		$this->setRedirect('index.php?option=com_joomleague&tmpl=component&controller=match&task=edit&cid[]='.$cid[0],$msg,$type);
	}

	function saveevent()
	{
		$option='com_joomleague';

		// Check for request forgeries
		JRequest::checkToken("GET") or die('JL_GLOBAL_INVALID_TOKEN');

		$mainframe =& JFactory::getApplication();
		$post=JRequest::get('post');
		$model=$this->getModel('match');
		$project_id=$mainframe->getUserState($option.'project',0);
		if (!$result=$model->saveevent($post,$project_id)){$result="0"."\n".JText::_('JL_ADMIN_MATCH_CTRL_ERROR_SAVED_EVENT').': '.$model->getError();}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','saveevent');
		JRequest::setVar('view','match');
		JRequest::setVar('result',$result);
		parent::display();
	}

	function savesubst()
	{
		// Check for request forgeries
		JRequest::checkToken("GET") or die('JL_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$model=$this->getModel('match');
		if (!$result=$model->savesubstitution($post)){
			$result="0"."\n".JText::_('JL_ADMIN_MATCH_CTRL_ERROR_SAVED_SUBST').': '.$model->getError();
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','savesubst');
		JRequest::setVar('view','match');
		JRequest::setVar('result',$result);
		parent::display();
	}

	function removeSubst()
	{
		JRequest::checkToken("GET") or die('JL_GLOBAL_INVALID_TOKEN');
		$substid=JRequest::getVar('substid',0,'post','int');
		$model=$this->getModel('match');
		if (!$result=$model->removeSubstitution($substid))
		{
			$result="0"."\n".JText::_('JL_ADMIN_MATCH_CTRL_ERROR_REMOVE_SUBST').': '.$model->getError();
		}
		else
		{
			$result="1"."\n".JText::_('JL_ADMIN_MATCH_CTRL_REMOVE_SUBST');
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','removesubst');
		JRequest::setVar('view','match');
		JRequest::setVar('result',$result);
		parent::display();
	}

	// save the checked rows inside matcheventsbb list
	function saveeventbb()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$post=JRequest::get('post');
		$model=$this->getModel('match');
		$project_id=$mainframe->getUserState($option.'project',0);
		if ($model->saveeventbb($post,$project_id))
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_UPDATE_EVENTS');
		}
		else
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_UPDATE_EVENTS').$model->getError();
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editeventsbb');
		JRequest::setVar('view','match');
		JRequest::setVar('edit',true);
		parent::display();
	}


	/**
	 * save the match stats
	 */
	function savestats()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');

		$post=JRequest::get('post');
		$model=$this->getModel('match');
		if ($model->savestats($post))
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_UPDATE_STATS');
		}
		else
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_UPDATE_STATS').$model->getError();
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','editstats');
		JRequest::setVar('view','match');
		JRequest::setVar('tmpl','component');
		JRequest::setVar('cid',array($post['match_id']));
		JRequest::setVar('msg',$msg);
		parent::display();
	}

	function removeEvent()
	{
		// Check for request forgeries
		JRequest::checkToken("GET") or die('JL_GLOBAL_INVALID_TOKEN');
		
		$event_id=JRequest::getVar('event_id',0,'post','int');
		$model=$this->getModel('match');
		if (!$result=$model->deleteevent($event_id))
		{
			$result="0"."\n".JText::_('JL_ADMIN_MATCH_CTRL_ERROR_DELETE_EVENTS').': '.$model->getError();
		}
		else
		{
			$result="1"."\n".JText::_('JL_ADMIN_MATCH_CTRL_DELETE_EVENTS');
		}
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','removeevent');
		JRequest::setVar('view','match');
		JRequest::setVar('result',$result);
		parent::display();
	}

}
?>