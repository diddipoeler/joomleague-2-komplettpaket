<?php
/**
 * @version	 $Id: helper.php 4971 2010-02-09 05:00:49Z timoline $
 * @package	 Joomla
 * @subpackage  Joomleague eventsranking module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * eventsranking Module helper
 *
 * @package Joomleague
 * @subpackage eventsranking Module
 * @since		1.5
 */
class modJLGEventsrankingHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	function getData(&$params)
	{
		if (!class_exists('JoomleagueModelEventsRanking')) {
			require_once(JLG_PATH_SITE. DS . 'models' . DS . 'eventsranking.php');
		}
		$model = &JLGModel::getInstance('eventsranking', 'JoomleagueModel');
		$model->projectid	= modJLGEventsrankingHelper::getId($params, 'p');
		$model->divisionid  = modJLGEventsrankingHelper::getId($params, 'divisionid');
		$model->teamid		= modJLGEventsrankingHelper::getId($params, 'tid');
		$model->setEventid($params->get('evid'));
		$model->matchid		= modJLGEventsrankingHelper::getId($params, 'mid');
		$model->limit		= $params->get('limit');
		$model->limitstart 	= 0;
		$project = $model->getProject();
		$eventtypes = $model->getEventTypes();
		$events	= $model->getEventRankings($model->limit,$model->limitstart, $params->get('ranking_order', 'DESC'));
		$teams = &$model->getTeamsIndexedById();

		return array('project' => $project, 'ranking' => $events, 'eventtypes' => $eventtypes, 'teams' => $teams, 'model' => $model);
	}

	/**
	 * get id from the module configuration parameters
	 * (the parameter can either be the id by itself or a complete slug).
	 * @param object configuration parameters for the module
	 * @param string name of the configuration parameter
	 * @return id string for the requested parameter (e.g. project id or statistics id)
	 */
	function getId($params, $paramName)
	{
		$id = $params->get($paramName);
		preg_match('/(?P<id>\d+):.*/', $id, $matches);
		if (array_key_exists('id', $matches))
		{
			$id = $matches['id'];
		}
		return $id;
	}
		
	/**
	 * get img for team
	 * @param object ranking row
	 * @param int type = 1 for club small logo, 2 for country
	 * @return html string
	 */
	function getLogo($item, $type = 1)
	{
		if ($type == 1) // club small logo
		{
			if (!empty($item->logo_small))
			{
				return JHTML::image(JURI::root().$item->logo_small, $item->short_name, 'class="teamlogo"');
			}
		}		
		else if ($type == 2 && !empty($item->country))
		{
			return Countries::getCountryFlag($item->country, 'class="teamcountry"');
		}
		
		return '';
	}

	function getTeamLink($team, $params, $project)
	{
		switch ($params->get('teamlink'))
		{
			case 'teaminfo':
				return JoomleagueHelperRoute::getTeamInfoRoute($project->slug, $team->team_slug);
			case 'roster':
				return JoomleagueHelperRoute::getPlayersRoute($project->slug, $team->team_slug);
			case 'teamplan':
				return JoomleagueHelperRoute::getTeamPlanRoute($project->slug, $team->team_slug);
			case 'clubinfo':
				return JoomleagueHelperRoute::getClubInfoRoute($project->slug, $team->club_slug);
				
		}
	}
	
	function printName($item, $team, $params, $project)
	{
				$name = JoomleagueHelper::formatName(null, $item->fname, 
													$item->nname, 
													$item->lname, 
													$params->get("name_format"));
				if ($params->get('show_player_link')) 
				{																	
					return JHTML::link(JoomleagueHelperRoute::getPlayerRoute($project->slug, $team->team_slug, $item->pid), $name);
				}
				else
				{
					echo $name;
				}				

	}

	function getEventIcon($event)
	{
		if ($event->icon == 'media/com_joomleague/event_icons/event.gif')
		{
			$txt = $event->name;
		}
		else
		{
			$imgTitle=JText::_($event->name);
			$imgTitle2=array(' title' => $imgTitle, ' alt' => $imgTitle);
			$txt=JHTML::image($event->icon, $imgTitle, $imgTitle2);
		}
		return $txt;
	}
}
