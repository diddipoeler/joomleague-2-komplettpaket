<?php
/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * Joomleague Component ajax Model
 *
 * @package	JoomLeague
 * @since	1.505a
 */
class JoomleagueModelAjax extends JModel
{
	function getProjectsOptions($season_id = 0, $league_id = 0, $ordering = 0)
	{
		$query_base = ' SELECT p.id AS value, p.name AS text, s.name AS season_name, l.name AS league_name ' 
		       . ' FROM #__joomleague_project AS p '
		       . ' INNER JOIN #__joomleague_season AS s on s.id = p.season_id '
		       . ' INNER JOIN #__joomleague_league AS l on l.id = p.league_id ' 
		       . ' WHERE p.published = 1 ';
		       
		$query = $query_base;
		if ($season_id) {
			$query .= ' AND p.season_id = '. $season_id;
		}
		if ($league_id) {
			$query .= ' AND p.league_id = '. $league_id;
		}
	
		switch ($ordering) 
		{
			
			case 1:
				$query .= ' ORDER BY p.ordering DESC';				
			break;
			
			case 2:
				$query .= ' ORDER BY s.ordering ASC, l.ordering ASC, p.ordering ASC';				
			break;
			
			case 3:
				$query .= ' ORDER BY s.ordering DESC, l.ordering DESC, p.ordering DESC';				
			break;
			
			case 4:
				$query .= ' ORDER BY p.name ASC';				
			break;
			
			case 5:
				$query .= ' ORDER BY p.name DESC';				
			break;
			
			case 0:
			default:
				$query .= ' ORDER BY p.ordering ASC';				
			break;
		}
		
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		return $res;
	}
}