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
jimport('joomla.html.pane');

/**
 * Joomleague Component editmatch View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100709
 */
class JoomleagueViewEditEvents extends JLGView
{
//index2.php?option=com_joomleague&tmpl=component&view=editevents&p=108&mid=14487Itemid=53

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$css = 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);

		// Joomleague model
		$model=$this->getModel();
		$user = JFactory::getUser();

		$this->assignRef('project',$model->getProject());
		$this->assignRef('match',$model->getMatch());
		$this->assignRef('matchid',$model->getMatchID());

		$isAllowed=(($model->isAllowed()) || ($model->isMatchAdmin($this->matchid,$user->id)));
		$config = $model->getTemplateConfig( "player" );
		$this->assignRef('config',$config);
		$this->assignRef('isAllowed',$isAllowed);
		$this->assignRef('hometeam',$model->getHomeTeam());
		$this->assignRef('team1_id',$model->getHomeTeamID());
		$this->assignRef('projectteam1_id',$model->getHomeProjectTeamID());
        
        // diddipoeler
        $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
        $this->assignRef('eventsprojecttime',$model->getProjectGameRegularTime($this->project->id ) );
        $matchcommentary =& $model->getMatchCommentary();
        $this->assignRef('matchcommentary',$matchcommentary);

		$this->assignRef('awayteam',$model->getAwayTeam());
		$this->assignRef('team2_id',$model->getAwayTeamID());
		$this->assignRef('projectteam2_id',$model->getAwayProjectTeamID());
		$this->assignRef('rosters',$model->createRosterArray());

		$this->assignRef('homeroster',$model->getRosterOptions($this->projectteam1_id));
		$this->assignRef('awayroster',$model->getRosterOptions($this->projectteam2_id));
		$this->assignRef('homeplayeroptions',$model->getPlayerPositionOptions($this->projectteam1_id));
		$this->assignRef('awayplayeroptions',$model->getPlayerPositionOptions($this->projectteam2_id));
		$this->assignRef('homeinoutoptions',$model->getInOutOptions($this->projectteam1_id));
		$this->assignRef('awayinoutoptions',$model->getInOutOptions($this->projectteam2_id));

		$this->assignRef('post_add',$model->getPositionAdd());

		$this->assignRef('homesubstitutions',$model->getSubstitutions($this->projectteam1_id));
		$this->assignRef('awaysubstitutions',$model->getSubstitutions($this->projectteam2_id));

		$this->assignRef('eventtypes',$model->getEventTypes());
		$this->assignRef('homeoptions',$model->getHomeOptions());
		$this->assignRef('awayoptions',$model->getAwayOptions());
		$this->assignRef('matchevents',$model->getMatchEvents($model->getMatchID(),1));

		$this->assignRef('playerprojectpositions',$model->getProjectPositions(0,1));
		$this->assignRef('staffprojectpositions',$model->getProjectPositions(0,2));
		$this->assignRef('refereeprojectpositions',$model->getProjectPositions(0,3));

		$this->assignRef('homeplayersoptions',$model->getPlayersOptions($this->projectteam1_id));
		$this->assignRef('awayplayersoptions',$model->getPlayersOptions($this->projectteam2_id));

		$this->assignRef('overallconfig',$model->getTemplateConfig('overall'));

		$lists=array();

		// teams
		$teamlist=array();
		$teamlist[]=JHTML::_('select.option',$this->projectteam1_id,$this->hometeam->name);
		$teamlist[]=JHTML::_('select.option',$this->projectteam2_id,$this->awayteam->name);
		$lists['teams']=JHTML::_('select.genericlist',$teamlist,'team_id','class="inputbox select-team"');

		// events
		$events=$model->getEventsOptions($this->project->id);
		$eventlist=array();
		$eventlist=array_merge($eventlist,$events);
		unset($events);
		$lists['events']=JHTML::_('select.genericlist',$eventlist,'event_type_id','class="inputbox select-event"');

		// player
		$lists['team_players']['home']=JHTML::_(	'select.genericlist',
													$model->getNotAssignedTeamPlayerPersons($this->projectteam1_id),
													'hplayer[]',
													'style="font-size:12px;height:auto;min-width:15em;" class="inputbox" multiple="true" size="10"',
													'value',
													'text');
//echo '<br /><pre>~'.print_r($lists['team_players']['home'],true).'~</pre><br />';

		$lists['team_players']['away']=JHTML::_(	'select.genericlist',
													$model->getNotAssignedTeamPlayerPersons($this->projectteam2_id),
													'aplayer[]',
													'style="font-size:12px;height:auto;min-width:15em;" class="inputbox" multiple="true" size="10"',
													'value',
													'text');
//echo '<br /><pre>~'.print_r($lists['team_players']['away'],true).'~</pre><br />';
		
		foreach ($this->playerprojectpositions AS $project_position_id => $pos)
		{
			// get home players assigned to this position
			$options=array();
			foreach ($model->getMatchPlayers($this->projectteam1_id) as $player)
			{
				if ($player->project_position_id==$project_position_id)
				{
					$nameStr = JoomleagueHelper::formatName(null,$player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
					$options[]=JHTML::_('select.option',$player->teamplayer_id, $nameStr);
				}
			}
			$lists['team_players'.$project_position_id]['home']=JHTML::_(	'select.genericlist',
																	$options,
																	'hplayerposition'.$project_position_id.'[]',
																	'style="font-size:12px;height:auto;min-width:15em;" class="inputbox position-staff" multiple="true" ',
																	'value',
																	'text');
			// get away players assigned to this position
			$options=array();
			foreach ($model->getMatchPlayers($this->projectteam2_id) as $player)
			{
				if ($player->project_position_id==$project_position_id)
				{
					$nameStr = JoomleagueHelper::formatName(null,$player->firstname, $player->nickname, $player->lastname, $config["name_format"]);
					$options[]=JHTML::_(	'select.option',
											$player->teamplayer_id, $nameStr);
				}
			}
			$lists['team_players'.$project_position_id]['away']=JHTML::_(	'select.genericlist',
																	$options,
																	'aplayerposition'.$project_position_id.'[]',
																	'style="font-size:12px;height:auto;min-width:15em;" class="inputbox position-staff" multiple="true" ',
																	'value',
																	'text');
		}

		// staff
		$lists['team_staffs']['home']=JHTML::_(	'select.genericlist',
												$model->getNotAssignedTeamStaffPersons($this->projectteam1_id),
												'hstaff[]',
												'style="font-size:12px;height:auto;min-width:15em;" class="inputbox" multiple="true" size="10"',
												'value',
												'text');

		$lists['team_staffs']['away']=JHTML::_(	'select.genericlist',
												$model->getNotAssignedTeamStaffPersons($this->projectteam2_id),
												'astaff[]',
												'style="font-size:12px;height:auto;min-width:15em;" class="inputbox" multiple="true" size="10"',
												'value',
												'text');
		foreach ($this->staffprojectpositions AS $project_position_id => $pos)
		{
			// get home staff assigned to this position
			$options=array();
			foreach ($model->getMatchStaffs($this->projectteam1_id) as $staff)
			{
				if ($staff->project_position_id==$project_position_id)
				{
					// TODO: Aad thinks that the order of home and away staff should be the same...
					$nameStr = JoomleagueHelper::formatName(null,$staff->firstname, $staff->nickname, $staff->lastname, $config["name_format"]);
					$options[]=JHTML::_('select.option', $staff->team_staff_id, $nameStr);
				}
			}
			$lists['team_staffs'.$project_position_id]['home']=JHTML::_(	'select.genericlist',
																	$options,
																	'hstaffposition'.$project_position_id.'[]',
																	'style="font-size:12px;height:auto;min-width:15em;" class="inputbox position-staff" multiple="true" ',
																	'value',
																	'text');
			// get away staff assigned to this position
			$options=array();
			foreach ($model->getMatchStaffs($this->projectteam2_id) as $staff)
			{
				if ($staff->project_position_id==$project_position_id)
				{
					$nameStr = JoomleagueHelper::formatName(null,$staff->firstname, $staff->nickname, $staff->lastname, $config["name_format"]);
					$options[]=JHTML::_('select.option', $staff->team_staff_id, $nameStr);
				}
			}

			$lists['team_staffs'.$project_position_id]['away']=JHTML::_(	'select.genericlist',
																	$options,
																	'astaffposition'.$project_position_id.'[]',
																	'style="font-size:12px;height:auto;min-width:15em;" class="inputbox position-staff" multiple="true" ',
																	'value',
																	'text');
		}

		// referees
		$referees = $this->project->teams_as_referees ? $model->getNotAssignedProjectRefereeTeams() : $model->getNotAssignedProjectReferees();
		$lists['project_referees']=JHTML::_(	'select.genericlist',
												$referees,
												'referee[]',
												'style="font-size:12px;height:auto;min-width:15em;" class="inputbox" multiple="true" size="10"',
												'value',
												'text');
		unset($referees);
		foreach ($this->refereeprojectpositions AS $project_position_id => $pos)
		{
			// get match referees assigned to this position
			$options=array();

			$referees = $this->project->teams_as_referees ? $model->getMatchRefereeTeams() : $model->getMatchReferees();
			foreach ($referees as $referee)
			{
				if ($referee->project_position_id==$project_position_id)
				{
					if ($this->project->teams_as_referees)
					{
						$options[]=JHTML::_('select.option',$referee->project_referee_id,$referee->teamname);
					}
					else
					{
						$nameStr = JoomleagueHelper::formatName(null,$referee->firstname, $referee->nickname, $referee->lastname, $config["name_format"]);
						$options[]=JHTML::_('select.option', $referee->project_referee_id, $nameStr);
					}
				}
			}

			$lists['match_referees'.$project_position_id]=JHTML::_(	'select.genericlist',
															$options,
															'refereeposition'.$project_position_id.'[]',
															'style="font-size:12px;height:auto;min-width:15em;" class="inputbox position-staff" multiple="true" ',
															'value',
															'text');
		}

		// substitutions
		$lists['home_projectpositions']=$model->getSelectPositions('home');
		$lists['away_projectpositions']=$model->getSelectPositions('away');

		$this->assignRef('lists',$lists);

		$pageTitle=JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_TITLE');
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}

}
?>