<?php
/**
 * @version	 $Id: helper.php 4971 2010-02-09 05:00:49Z julienv $
 * @package	 Joomla
 * @subpackage  Joomleague stats module
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
 * Stat Module helper
 *
 * @package Joomleague
 * @subpackage stat Module
 * @since		1.5
 */
class modJLGStatHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	function getData(&$params)
	{
		global $mainframe;

		if (!class_exists('JoomleagueModelStatsranking')) {
			require_once(JLG_PATH_SITE. DS . 'models' . DS . 'statsranking.php');
		}
		$divisionid = explode(':', $params->get('division_id', 0));
		$divisionid = $divisionid[0];
		$model = &JLGModel::getInstance('statsranking', 'JoomleagueModel');
		$model->setProjectId($params->get('p'));
		$model->teamid = (int)$params->get('tid', 0);
		$model->setStatid($params->get('sid'));
		$model->limit = $params->get('limit');
		$model->limitstart = 0;
		$model->divisionid = $divisionid;
		$project = &$model->getProject();
		$stattypes = $model->getProjectUniqueStats();
		$stats = $model->getPlayersStats($params->get('ranking_order', 'DESC'));
		$teams = &$model->getTeamsIndexedById();
		
		return array('project' => $project, 'ranking' => $stats, 'teams' => $teams, 'stattypes' => $stattypes);
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
		else if ($type == 3) {
			if (!empty($item->team_picture))
			{
				return JHTML::image(JURI::root().$item->team_picture, $item->short_name, 'class="teamlogo"');
			}
		}
		else if ($type == 4) {
			if (!empty($item->projectteam_picture))
			{
				return JHTML::image(JURI::root().$item->projecteam_picture, $item->short_name, 'class="teamlogo"');
			}
		}
		return '';
	}

	function getTeamLink($item, $params, $project)
	{
		switch ($params->get('teamlink'))
		{
			case 'teaminfo':
				return JoomleagueHelperRoute::getTeamInfoRoute($project->slug, $item->team_slug);
			case 'roster':
				return JoomleagueHelperRoute::getPlayersRoute($project->slug, $item->team_slug);
			case 'teamplan':
				return JoomleagueHelperRoute::getTeamPlanRoute($project->slug, $item->team_slug);
			case 'clubinfo':
				return JoomleagueHelperRoute::getClubInfoRoute($project->slug, $item->club_slug);
				
		}
	}
	
	function printName($item, $team, $params, $project)
	{
				$name = JoomleagueHelper::formatName(null, $item->firstname, 
													$item->nickname, 
													$item->lastname, 
													$params->get("name_format"));
				if ($params->get('show_player_link')) 
				{
					return JHTML::link(JoomleagueHelperRoute::getPlayerRoute($project->slug, $team->team_slug, $item->person_id), $name);	
				}
				else
				{
					echo $name;
				}
				

	}

	function getStatIcon($stat)
	{
		if ($stat->icon == 'media/com_joomleague/event_icons/event.gif')
		{
			$txt = JText::_($stat->name);
		}
		else
		{
			$imgTitle=JText::_($stat->name);
			$imgTitle2=array(' title' => $imgTitle, ' alt' => $imgTitle);
			$txt=JHTML::image($stat->icon, $imgTitle, $imgTitle2);
		}
		return $txt;
	}
}