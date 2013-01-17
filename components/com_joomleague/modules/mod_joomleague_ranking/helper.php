<?php
/**
 * @version	 $Id: helper.php 4905 2010-01-30 08:51:33Z and_one $
 * @package	 Joomla
 * @subpackage  Joomleague ranking module
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
 * @subpackage Joomleague ranking module
 * @since		1.0
 */
class modJLGRankingHelper
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

		if (!class_exists('JoomleagueModelRanking')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'ranking.php');
		}
		$model = &JLGModel::getInstance('project', 'JoomleagueModel');
		$model->setProjectId($params->get('p'));

		$project = &$model->getProject();

		$ranking = JLGRanking::getInstance($project);
		$ranking->setProjectId($params->get('p'));
		$divisionid = explode(':', $params->get('division_id', 0));
		$divisionid = $divisionid[0];
		$res   = $ranking->getRanking(null, null, $divisionid);
		$teams = $model->getTeamsIndexedByPtid();

		$list = array();
		foreach ($res as $ptid => $t) {
			$t->team = $teams[$ptid];
			$list[] = $t;
		}

		if( $params->get('visible_team') != '' ){
			$exParam=explode(':',$params->get('visible_team'));
			$list = modJLGRankingHelper::getShrinkedDataAroundOneTeam($list,$exParam[0],$params->get('limit', 5));
		}
		$colors = array();
		if ($params->get('show_rank_colors', 0)) {
			$mdlRanking = &JLGModel::getInstance("Ranking", "JoomleagueModel");
			$mdlRanking->setProjectid($params->get('p'));
			$config = $mdlRanking->getTemplateConfig("ranking");
			$colors = $mdlRanking->getColors($config["colors"]);
		}
		return array('project' => $project, 'ranking' => $list, 'colors' => $colors);

	}

	/**
	 * Method to shrinked list so that the alwaysVisibleTeamId is always visible in the middle of the list
	 *
	 * @access public
	 * @return array
	 */
	function getShrinkedDataAroundOneTeam($completeRankingList, $alwaysVisibleTeamId, $paramRowLimit){
		// First Fav-Team should be always visible in the ranking view
		$rank = $completeRankingList;
		$i=0;
		foreach( $rank as $item ){
			$isFav= $item->team->id == $alwaysVisibleTeamId;
			if( $isFav ) {
				$limit=$paramRowLimit-1; // Limit-Parameter -1 because fav-team should be in the middle

				$startOffset = $i - floor($limit/2);
				if( $limit%2 > 0 ){
					// odd-number then more ranks before fav-team should be visible
					$startOffset -= $limit%2; //+Rest
				}

				// startOffset out of range then start with 0
				if( $startOffset < 0 ){
					$startOffset = 0;
				}

				// Array anpassen
				return array_slice($rank,$startOffset,$paramRowLimit);
			}

			$i++;
		}
		return $rank;
	}


	/**
	 * returns value corresponding to specified column
	 * @param string column
	 * @param object ranking item
	 * @return value POINTS, RESULTS, DIFF, BONUS, START....see the cases here below :)
	 */
	function getColValue($column, $item)
	{
		$column = ucfirst(str_replace("jl_", "", strtolower(trim($column))));
		$column = strtolower($column);
		switch ($column)
		{
			case 'points':
				return $item->getPoints();
			case 'played':
				return $item->cnt_matches;
			case 'wins':
				return $item->cnt_won;
			case 'ties':
				return $item->cnt_draw;
			case 'losses':
				return $item->cnt_lost;
			case 'wot':
				return $item->cnt_wot;
			case 'wso':
				return $item->cnt_wso;
			case 'lot':
				return $item->cnt_lot;
			case 'lso':
				return $item->cnt_lso;				
			case 'scorefor':
				return $item->sum_team1_result;
			case 'scoreagainst':
				return $item->sum_team2_result;
			case 'results':
				return $item->sum_team1_result.':'. $item->sum_team2_result;
			case 'diff':
			case 'scorediff':
				return $item->diff_team_results;
			case 'scorepct':
				return round(($item->scorePct()),2);				
			case 'bonus':
				return $item->bonus_points;
			case 'start':
				return $item->cnt_lost;
			case 'winpct':
				return round(($item->winpct()),2);
			case 'legs':
				return $item->sum_team1_legs.':'. $item->sum_team2_legs;
			case 'legsdiff':
				return $item->diff_team_legs;
			case 'legsratio':
				return round(($item->legsRatio()),2);
			case 'negpoints':
				return $item->neg_points;
			case 'oldnegpoints':
				return $item->getPoints().':'. $item->neg_points;
			case 'pointsratio':
				return round(($item->pointsRatio()),2);
			case 'gfa':
				return round(($item->getGFA()),2);
			case 'gaa':
				return round(($item->getGAA()),2);	
			case 'ppg':
				return round(($item->getPPG()),2);	
			case 'ppp':
				return round(($item->getPPP()),2);					
				
			default:
				if (isset($item->$column)) {
					return $item->$column;
				}
		}
		return '?';
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
			if (!empty($item->team->logo_small))
			{
				return JHTML::image(JURI::root().$item->team->logo_small, $item->team->short_name, 'class="teamlogo"');
			}
		}
		else if ($type == 2 && !empty($item->team->country))
		{
			return Countries::getCountryFlag($item->team->country, 'class="teamcountry"');
		}

		return '';
	}

	function getTeamLink($item, $params, $project)
	{
		switch ($params->get('teamlink'))
		{
			case 'teaminfo':
				return JoomleagueHelperRoute::getTeamInfoRoute($project->slug, $item->team->team_slug);
			case 'roster':
				return JoomleagueHelperRoute::getPlayersRoute($project->slug, $item->team->team_slug);
			case 'teamplan':
				return JoomleagueHelperRoute::getTeamPlanRoute($project->slug, $item->team->team_slug);
			case 'clubinfo':
				return JoomleagueHelperRoute::getClubInfoRoute($project->slug, $item->team->club_slug);

		}
	}
}