<?php
/**
 * @version	 $Id: helper.php 2010-09-04 Ingalb$
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
 * @subpackage Joomleague teamplayers module
 * @since		1.0
 */
class modJLGTeamPlayersHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	function getData(&$params)
	{
		$p = $params->get('p');
		$p = explode(":", $p);
		$p = $p[0];
		$t = $params->get('team');
		$t = explode(":", $t);
		$t = $t[0];
		$db  = JFactory::getDBO();

		$query = "SELECT tt.id AS id, t.name AS team_name
					FROM #__joomleague_project_team tt
					INNER JOIN #__joomleague_team t ON t.id = tt.team_id
					WHERE tt.project_id = ". $p . "
					AND tt.team_id = ". $t;
		$query .= " LIMIT 1";
		$db->setQuery( $query );
		$result = $db->loadRow();
		$projectteamid = $result[0];
		$team_name     = $result[1];
		

		JRequest::setVar( 'p', $p );
		JRequest::setVar( 'tid', $t);
		JRequest::setVar( 'ttid', $projectteamid);

		if (!class_exists('JoomleagueModelRoster')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'roster.php');
		}
		$model 	= &JLGModel::getInstance('Roster', 'JoomleagueModel');
		$model->setProjectId($p);
		$project = &$model->getProject();
		$project->team_name = $team_name;
		return array('project' => $project, 'roster' => $model->getTeamPlayers());
	}

	function getPlayerLink($item, $params, $project)
	{
		$flag = "";
		if ($params->get('show_player_flag')) {
			$flag = Countries::getCountryFlag($item->country) . "&nbsp;";
		}
		$text = "<i>".JoomleagueHelper::formatName(null, $item->firstname, 
													$item->nickname, 
													$item->lastname, 
													$params->get("name_format")) . "</i>";
		if ($params->get('show_player_link'))
		{
			$link = JoomleagueHelperRoute::getPlayerRoute($params->get('p'), 
															$params->get('teams'), 
															$item->slug );
			echo $flag . JHTML::link($link, $text);
		}
		else
		{
			echo '<i>' . JText::sprintf( '%1$s', $flag . $text) . '</i>';
		}

	}
}