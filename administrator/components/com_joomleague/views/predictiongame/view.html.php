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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionGame extends JLGView
{
	function display($tpl=null)
	{
		if ($this->getLayout()=='form')
		{
			$this->_displayForm($tpl);
			return;
		}
		elseif ($this->getLayout()=='predsettings')
		{
			$this->_displayPredSettings($tpl);
			return;
		}

		//get the prediction game
		$prediction =& $this->get('data');

		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
	  /* 
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$user =& JFactory::getUser();
		*/
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$uri 	= JFactory::getURI();
		$user 	= JFactory::getUser();
		$model	= $this->getModel();
		
		
		$lists=array();

		//get the prediction game and its admins
		$prediction =& $this->get('data');
		//$this->assignRef('prediction',$prediction);
		$pred_admins=$model->getAdmins();
		$pred_projects=$model->getPredictionProjectIDs();

		$isNew=($prediction->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('JL_ADMIN_PGAME_THE_PREDICTIONGAME'),$prediction->name);
			$mainframe->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew){$model->checkout($user->get('id'));}

		//build the html select list for Joomla users
		$jl_users[]=array();
		if ($res =& $model->getJLUsers()){$jl_users=array_merge($jl_users,$res);}
		$lists['jl_users']=JHTMLSelect::genericlist(	$res,
														'user_ids[]',
														'class="inputbox validate-select-required" size="5" multiple="multiple"',
														'value',
														'text',
														$pred_admins);
		unset($jl_users);

		//build the html select list for projects
		$projects[]=array();
		if ($res =& $model->getProjects()){$projects=array_merge($projects,$res);}
		$lists['projects']=JHTMLSelect::genericlist(	$res,
														'project_ids[]',
														'class="inputbox validate-select-required" size="5" multiple="multiple"',
														'value',
														'text',
														$pred_projects);
		#echo '<pre>'.print_r($projects,true).'</pre>';
		unset($res);

		// build the html radio for auto_activate_user
		$lists['auto_activate_user']=JHTMLSelect::booleanlist('auto_approve','class="inputbox"',$prediction->auto_approve);

		// build the html radio for only_favteams
		$lists['only_favteams']=JHTMLSelect::booleanlist('only_favteams','class="inputbox"',$prediction->only_favteams);

		// build the html radio for admin_tipp
		$lists['admin_tipp']=JHTMLSelect::booleanlist('admin_tipp','class="inputbox"',$prediction->admin_tipp);

    $this->assignRef('form'      	, $this->get('form'));
    
		$this->assignRef('lists',$lists);
		$this->assignRef('prediction',$prediction);
		$this->assignRef('pred_admins',$pred_admins);
		$this->assignRef('pred_projects',$pred_projects);

		parent::display($tpl);
	}

	function _displayPredSettings($tpl)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$user =& JFactory::getUser();
		$model =& $this->getModel();
		$lists=array();

		//get the prediction game and the predicition project
		$prediction =& $this->get('data');
		$pred_project=$model->getPredictionProject();

		$isNew=($prediction->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('JL_ADMIN_PGAME_THE_PREDICTIONGAME'),$pred_project->project_name);
			$mainframe->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew){$model->checkout($user->get('id'));}

		// build the html radio for usage of published
		$lists['published']=JHTML::_('select.booleanlist','published','class="inputbox" onclick="change_published(); " ',$pred_project->published);

		// build the html dropdown for Prediction game mode
		$mode=array(	JHTMLSelect::option('1',JText::_('JL_ADMIN_PGAME_PRED_TOTO'),'id','name'),
						JHTMLSelect::option('0',JText::_('JL_ADMIN_PGAME_PRED_TIPP'),'id','name'));
		$lists['mode']=JHTMLSelect::genericlist($mode,'mode','class="inputbox" size="1" disabled="disabled" ','id','name',$pred_project->mode);
		unset($mode);

		// build the html dropdown for Prediction game mode
		$overview=array(	JHTMLSelect::option('1',JText::_('JL_ADMIN_PGAME_TIPP_HALF'),'id','name'),
							JHTMLSelect::option('0',JText::_('JL_ADMIN_PGAME_TIPP_COMPLETE'),'id','name'));
		$lists['overview']=JHTMLSelect::genericlist($overview,'overview','class="inputbox" size="1" disabled="disabled" ','id','name',$pred_project->overview);
		unset($overview);

		// build the html radio for usage of tipp joker
		$lists['use_joker']=JHTMLSelect::booleanlist('joker','class="inputbox" onclick="change_joker(); " disabled="disabled" ',$pred_project->joker);

		// build the html radio for limitation of tipp joker
		$joker_limit=($pred_project->joker_limit > 0);
		$lists['joker_limit']=JHTMLSelect::booleanlist('joker_limit_select','class="inputbox" onclick="change_jokerlimit(); " disabled="disabled" ',$joker_limit);

		// build the html radio for usage of tipp champ
		$lists['use_champ']=JHTMLSelect::booleanlist('champ','class="inputbox" onclick="change_champ(); " disabled="disabled" ',$pred_project->champ);

    
    
    $league_teams[]=JHTML::_('select.option','0',JText::_('JL_ADMIN_PGAME_SET_CHAMPION'),'id','name');
		if($allLeagues =& $model->getProjectTeams($pred_project->project_id)) 
    {
			$league_teams=array_merge($league_teams,$allLeagues);
		}
		$lists['league_teams']=JHTMLSelect::genericlist($league_teams,'league_champ','class="inputbox" size="'.sizeof($allLeagues).'"','id','name',$pred_project->league_champ);                            

		#echo '<pre>'.print_r($projects,true).'</pre>';
		unset($allLeagues);
    
    
		$this->assignRef('lists',$lists);
		$this->assignRef('prediction',$prediction);
		$this->assignRef('pred_project',$pred_project);
		parent::display($tpl);
	}

}
?>