<?php
/**
 * @version	 $Id$
 * @package	 Joomla
 * @subpackage  Joomleague sports module
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
 * sports Module helper
 *
 * @package Joomla
 * @subpackage Joomleague sports module
 * @since		1.0
 */
class modJLGSportsHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	function getData(&$params)
	{
		if (!class_exists('JoomleagueModelSportsType')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'sportstype.php');
		}
		$model = JLGModel::getInstance('sportstype', 'JoomleagueModel');
		$model->setId($params->get('sportstypes'));
		
		return array(
			'sportstype' => $model->getData(),
			'projectscount' => $model->getProjectsCount(), 
			'leaguescount' => $model->getLeaguesOnlyCount(), 
			'seasonscount' => $model->getSeasonsOnlyCount(),
      'clubscount' => $model->getClubsOnlyCount(), 
			'projectteamscount' => $model->getProjectTeamsCount(),
			'projectteamsplayerscount' => $model->getProjectTeamsPlayersCount(),
			'projectdivisionscount' => $model->getProjectDivisionsCount(),
			'projectroundscount' => $model->getProjectRoundsCount(),
			'projectmatchescount' => $model->getProjectMatchesCount(),
			'projectmatcheseventscount' => $model->getProjectMatchesEventsCount(),
			'projectmatcheseventsnamecount' => $model->getProjectMatchesEventsNameCount(),
			'projectmatchesstatscount' => $model->getProjectMatchesStatsCount(),
		);
	}
}