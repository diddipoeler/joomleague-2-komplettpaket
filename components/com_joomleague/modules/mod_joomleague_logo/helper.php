<?php
/**
 * @version	 $Id$
 * @package	 Joomla
 * @subpackage  Joomleague logo module
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
 * Logo Module helper
 *
 * @package Joomla
 * @subpackage Joomleague logo module
 * @since		1.0
 */
class modJLGLogoHelper
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

		if (!class_exists('JoomleagueModelTeams')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'teams.php');
		}
		$model = &JLGModel::getInstance('teams', 'JoomleagueModel');
		$model->setProjectId($params->get('p'));
		
		$project = &$model->getProject();
		$division_id = $params->get('division_id');
		$division_id = explode(":", $division_id);
		$division_id = $division_id[0];
		$model->divisionid = $division_id;
		
		return array('project' => $project, 'teams' => $model->getTeams());

	}
	/**
	 * get img for team
	 * @param object teams row
	 * @param int type = 0 for club small logo, 1 for medium logo, 2 for big logo
	 * @return html string
	 */
	function getLogo($item, $type = 1)
	{
		if ($type == 0) // club small logo
		{
			$picture = $item->logo_small;
			if ( ( is_null( $item->logo_small ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('clublogosmall'); 
			}
			echo JHTML::image($picture, $item->team_name,'class="logo_small" title="View '.$item->team_name.'"');
				
		}
		else if ($type == 1) // club medium logo
		{
			$picture = $item->logo_middle;
			if ( ( is_null( $item->logo_middle ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('clublogomedium'); 
			}
			echo JHTML::image($picture, $item->team_name,'class="logo_middle" title="View '.$item->team_name.'"');

		}
		else if ($type == 2 ) // club big logo
		{
			$picture = $item->logo_big;
			if ( ( is_null( $item->logo_big ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('clublogobig'); 
			}
			echo JHTML::image($picture, $item->team_name,'class="logo_big" title="View '.$item->team_name.'"');
		}
		else if ($type == 3 ) // team logo
		{
			$picture = $item->team_picture;
			if ( ( is_null( $item->team_picture ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('team');
			}
			echo JHTML::image($picture, $item->team_name,'class="team_picture" title="View '.$item->team_name.'"');
		}
		else if ($type == 4 ) // projectteam logo
		{
			$picture = $item->projectteam_picture;
			if ( ( is_null( $item->projectteam_picture ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('team'); 
			}
			echo JHTML::image($picture, $item->team_name,'class="projecteam_picture" title="View '.$item->team_name.'"');
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
			case 'teamwww':
				return $item->team_www;
			case 'clubwww':
				return $item->club_www;
		}
	}
}