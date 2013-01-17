<?php
/**
 * @version	 $Id: helper.php 496 2010-06-01 13:04:30Z And_One $
 * @package	 Joomla
 * @subpackage  Joomleague results module
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
 * Ranking Module helper
 *
 * @package Joomla
 * @subpackage Joomleague results module
 * @since		1.0
 */
class modJLGResultsHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	function getData(&$params)
	{		
		if (!class_exists('JoomleagueModelResults')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'results.php');
		}
		$model = &JLGModel::getInstance('Results', 'JoomleagueModel');
		$model->setProjectId($params->get('p'));
		
		$project = &$model->getProject();
		
		switch ($params->get('round_selection', 0))
		{
			case 0: // latest
				$roundid = modJLGResultsHelper::getLatestRoundId($project->id);
				break;
			case 1: // next
				$roundid = modJLGResultsHelper::getNextRoundId($project->id);
				break;
			case 2: //manual
				$roundid = ((int) $params->get('id') ? (int) $params->get('id') : $model->getCurrentRound());
				break;
		}
		if (!$roundid) {
			$roundid = $model->getCurrentRound();
		}
		
		$model->set('divisionid',	(int) $params->get('division_id') );
		$model->set('roundid',	    $roundid );
		
		$round   = modJLGResultsHelper::getRound($project->id, $roundid);
		
		$matches = $model->getMatches();
		uasort($matches, array('modJLGResultsHelper', '_cmpDate'));
		$matches = array_slice($matches, 0, $params->get('limit', 10));
		
		$teams   = $model->getTeamsIndexedByPtid();		
		 
		return array('project' => $project, 'round' => $round, 'matches' => $matches, 'teams' => $teams);
	}
	
	function sortByDate($matches)
	{
		$sorted = array();
		foreach ($matches as $m)
		{
			$date = substr($m->match_date, 0, 10);
			if (isset($sorted[$date])) {
				$sorted[$date][] = $m;
			}
			else {
				$sorted[$date] = array($m);
			}
		}
		return $sorted;
	}
	
	function convertDate($date, $params)
	{
		return strftime($params->get('date_format', "%a, %d.%m."), strtotime($date));
	}

	function convertTime($date, $params)
	{
		return strftime($params->get('time_format', "%H:%M"), strtotime($date));
	}
	
	function _cmpDate($a, $b)
	{
		$res = strtotime($a->match_date) - strtotime($b->match_date);
		if ($res !== 0) {
			return $res;
		}
				
		$res = $a->match_number - $b->match_number;
		if ($res !== 0) {
			return $res;
		}		
		
		return $a->id - $b->id;
	}
	
	function getRound($project_id, $roundid)
	{
		$db = &JFactory::getDBO();
		
		$query = ' SELECT * FROM #__joomleague_round '
		       . ' WHERE id = '. $db->Quote($roundid)
		       . '   AND project_id = '. $db->Quote($project_id)
		            ;
		$db->setQuery($query);
		$res = $db->loadObject();
		return $res;
	}
	/**
	 * get img for team
	 * @param object ranking row
	 * @param int type = 1 for club small logo, 2 for country
	 * @return html string
	 */
	function getLogo($item, $params)
	{
		$type = $params->get('show_logo');
		if ($type == 'country_flag' && !empty($item->country))
		{
			return Countries::getCountryFlag($item->country, 'class="teamcountry"');
		}
		
		//dynamic object property string
		$pic = $params->get('show_picture');
		return JoomleagueHelper::getPictureThumb($item->$pic,
				$item->name,
				$params->get('team_picture_width'),
				$params->get('team_picture_height'),
				3);
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
	
	function getLatestRoundId($project_id)
	{		
		$db = &JFactory::getDBO();
		
		$query = ' SELECT r.id AS roundid, r.round_date_first '
		       . ' FROM #__joomleague_round AS r '
		       . ' WHERE project_id = '. $db->Quote($project_id)
		       . '   AND DATEDIFF(CURDATE(), CASE WHEN r.round_date_last IS NOT NULL THEN DATE(r.round_date_last) ELSE DATE(r.round_date_first) END) >= 0'
		       . ' ORDER BY r.round_date_first DESC '
		            ;
		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;
	}
	

	function getNextRoundId($project_id)
	{
		$db = &JFactory::getDBO();
		
		$query = ' SELECT r.id AS roundid, r.round_date_first '
		       . ' FROM #__joomleague_round AS r '
		       . ' WHERE project_id = '. $db->Quote($project_id)
		       . '   AND DATEDIFF(CURDATE(), DATE(r.round_date_first)) < 0'
		       . ' ORDER BY r.round_date_first ASC '
		            ;
		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;		
	}
	
	function getScoreLink($game, $project)
	{
		if (isset($game->team1_result) || $game->alt_decision)	{
			return JoomleagueHelperRoute::getMatchReportRoute($project->slug, $game->id);
		}
		else {
			return JoomleagueHelperRoute::getNextMatchRoute($project->slug, $game->id);
		}
	}
}