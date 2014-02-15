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

//jimport('joomla.application.component.model');
//require_once (JPATH_COMPONENT.DS.'models'.DS.'list.php');
jimport('joomla.application.component.modellist');

/**
 * Joomleague Component currentseasons Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelcurrentseasons extends JModelList
{
	var $_identifier = "currentseasons";
    
    protected function getListQuery()

	{
		// Get the WHERE and ORDER BY clauses for the query
		$where      = $this->_buildContentWhere();
		$orderby    = $this->_buildContentOrderBy();

		$query = '	SELECT	p.*,
							st.name AS sportstype,
							s.name AS season,
							l.name AS league,
							u.name AS editor
					FROM	#__joomleague_project AS p
					LEFT JOIN #__joomleague_season AS s ON s.id = p.season_id
					LEFT JOIN #__joomleague_league AS l ON l.id = p.league_id
					LEFT JOIN #__joomleague_sports_type AS st ON st.id = p.sports_type_id
					LEFT JOIN #__users AS u ON u.id = p.checked_out ' .
					$where .
					$orderby;

		return $query;
	}
    
    function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();

        $orderby 	= ' ORDER BY p.name ';

		return $orderby;
	}
    
    function _buildContentWhere()
	{
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
        $where = array();
		$filter_season = JComponentHelper::getParams($option)->get('current_season',0);

		if($filter_season > 0) {
			$where[] = 'p.season_id = ' . $filter_season;
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
    
    
}

?>    