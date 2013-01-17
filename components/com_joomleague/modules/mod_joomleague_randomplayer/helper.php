<?php
/**
 * @version	 $Id: helper.php 2010-07-04 marco vaninetti$
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
 * @subpackage Joomleague randomplayer module
 * @since		1.0
 */
class modJLGRandomplayerHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	function getData(&$params)
	{
		$usedp = $params->get('projects');
		$usedtid = $params->get('teams', '0');
		$projectstring = (is_array($usedp)) ? implode(",", $usedp) : $usedp;
		$teamstring = (is_array($usedtid)) ? implode(",", $usedtid) : $usedtid;

		$db  = JFactory::getDBO();

		$query = "SELECT id
					FROM #__joomleague_project_team tt WHERE tt.project_id > 0 ";
		if($projectstring!="" && $projectstring > 0) {
			$query .=	" AND tt.project_id IN (". $projectstring .") ";
		}
		if($teamstring!="" && $teamstring > 0) {
			$query .= " AND tt.team_id IN (". $teamstring .") ";
		}
		$query .= " ORDER BY rand() ";
		$query .= " LIMIT 1";
		$db->setQuery( $query );
		$projectteamid = $db->loadResult();
		$query = '	SELECT	 pt.person_id, tt.project_id '
		. ' FROM #__joomleague_team_player pt '
		. ' INNER JOIN #__joomleague_project_team AS tt ON tt.id = pt.projectteam_id '
		. ' WHERE pt.projectteam_id = ' . $projectteamid
		. ' ORDER BY rand() '
		. ' LIMIT 1';

		$db->setQuery( $query );
		$res=$db->loadRow();

		JRequest::setVar( 'p', $res[1] );
		JRequest::setVar( 'pid', $res[0]);
		JRequest::setVar( 'pt', $projectteamid);

		if (!class_exists('JoomleagueModelPlayer')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'player.php');
		}

		$mdlPerson 	= &JLGModel::getInstance('Player', 'JoomleagueModel');

		$person 	= &$mdlPerson->getPerson();
		$project	= &$mdlPerson->getProject();
		$info		= &$mdlPerson->getTeamPlayer();
		$infoteam	= &$mdlPerson->getTeaminfo($projectteamid);

		return array(	'project' 		=> $project,
						'player' 		=> $person, 
						'inprojectinfo'	=> is_array($info) && count($info) ? $info[0] : $info,
						'infoteam'		=> $infoteam);
	}
}