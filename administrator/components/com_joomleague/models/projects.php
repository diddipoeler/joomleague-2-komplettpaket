<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
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
require_once ( JPATH_COMPONENT . DS . 'models' . DS . 'list.php' );

/**
 * Joomleague	Component Projects Model
 *
 * @package		JoomLeague
 * @since		0.1
 */
class JoomleagueModelProjects extends JoomleagueModelList
{
	var $_identifier = "projects";
	
	function _buildQuery()
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

		$filter_order		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_order',		'filter_order',		'p.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_order_Dir',	'filter_order_Dir',	'',				'word');
		
		if ( $filter_order == 'p.ordering' )
		{
			$orderby 	= ' ORDER BY p.ordering ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , p.ordering ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_league		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_league',		'filter_league','',			'int');
		$filter_sports_type	= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_sports_type',	'filter_sports_type','',	'int');
		$filter_season		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_season',		'filter_season','',				'int');
		$filter_state		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_state',		'filter_state',		'',		'word');
		$search				= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.search',				'search',			'',		'string');
		$search_mode		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.search_mode',		'search_mode',		'',		'string');
		$search=JString::strtolower($search);
		$where = array();
		
		if($filter_league > 0) {
			$where[] = 'p.league_id = ' . $filter_league;
		}
		if($filter_season > 0) {
			$where[] = 'p.season_id = ' . $filter_season;
		}
		if ($filter_sports_type > 0)
		{
			$where[] = 'p.sports_type_id = ' . $this->_db->Quote($filter_sports_type);
		}
		if ( $search )
		{
			$where[] = 'LOWER(p.name) LIKE ' . $this->_db->Quote( '%' . $search . '%' );
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'p.published = 1';
			}
			elseif ($filter_state == 'U' )
				{
					$where[] = 'p.published = 0';
				}
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	* Method to check if the project to be copied already exists
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function cpCheckPExists( $post )
	{
		$name = 		$post['name'];
		$league_id = 	$post['league_id'];
		$season_id = 	$post['season_id'];
		$old_id = 		$post['old_id'];

		//check project unicity if season and league are not both new
		if ( $league_id && $season_id )
		{
			$query = '	SELECT id FROM #__joomleague_project
						WHERE name = ' . $this->_db->Quote($name) . '
						AND league_id = ' . $league_id . '
						AND season_id = ' . $season_id;

			$this->_db->setQuery( $query );
			$this->_db->query();
			$num = $this->_db->getAffectedRows();

			if ( $num > 0 )
			{
				return false;
			}
		}

		return true;
	}

	/**
	* Method to assign teams of an existing project to a copied project
	*
	* @access	public
	* @return	array
	* @since 0.1
	*/
	function cpCopyStaff( $post )
	{
		$old_id = (int)$post['old_id'];
		$project_id = (int)$post['id'];

		$query = '	SELECT	ts.projectteam_id,
							ts.person_id,
							ts.project_position_id,
							jt.id,
							jt.team_id
					FROM #__joomleague_team_staff ts
					LEFT JOIN #__joomleague_project_team as jt ON jt.id = ts.projectteam_id
					WHERE jt.project_id = ' . $old_id . '
					ORDER BY jt.id ';

		$this->_db->setQuery( $query );

		if ( $results = $this->_db->loadAssocList() )
		{
			foreach( $results as $result )
			{
				$query = '	SELECT	jt.id,
									jt.team_id
							FROM #__joomleague_project_team jt
							WHERE jt.project_id = ' . $project_id . ' AND jt.team_id = ' . $result['team_id'] . '
							ORDER BY jt.id ';

				$this->_db->setQuery( $query );
				$newprojectteam_id = $this->_db->loadResult();

				$p_staff =& $this->getTable();
				$p_staff->bind( $result );
				$p_staff->set( 'teamstaff_id', NULL );
				$p_staff->set( 'projectteam_id', $newprojectteam_id );

				if ( !$p_staff->store() )
				{
					echo $this->_db->getErrorMsg();
					return false;
				}
			}
		}
		return true;
	}

	/**
	* Method to return a season array (id, name)
	*
	* @access	public
	* @return	array seasons
	* @since	1.5.03a
	*/
	function getSeasons()
	{
		$query = '	SELECT	id,
							name
					FROM #__joomleague_season
					ORDER BY name ASC ';

		$this->_db->setQuery( $query );

		if ( !$result = $this->_db->loadObjectList() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		return $result;
	}

}
?>