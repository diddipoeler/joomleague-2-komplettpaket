<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
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
 * Joomleague Component PredictionGames Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueModelPredictionGames extends JoomleagueModelList
{
	var $_identifier = "predgames";
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query		=	"	SELECT	pre.*,
									u.name AS editor

							FROM	#__joomleague_prediction_game AS pre
							LEFT JOIN #__users AS u ON u.id = pre.checked_out
						" . $where . $orderby;
//echo '#'.$query.'#<br /><br />';
		return $query;
	}

	function _buildContentOrderBy()
	{
		$mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';

		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'pre_filter_order',		'filter_order',		'pre.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'pre_filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		if ( $filter_order == 'pre.name' )
		{
			$orderby 	= ' ORDER BY pre.name ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , pre.name ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';

		$filter_state		= $mainframe->getUserStateFromRequest( $option . 'pre_filter_state',		'filter_state',		'',			'word' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'pre_filter_order',		'filter_order',		'pre.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'pre_filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $option . 'pre_search',				'search',			'',			'string' );
		$search_mode		= $mainframe->getUserStateFromRequest( $option . 'pre_search_mode',			'search_mode',		'',			'string' );
		$search				= JString::strtolower( $search );

		$where = array();
		$prediction_id = (int) $mainframe->getUserState( 'com_joomleague' . 'prediction_id' );
		if ( $prediction_id > 0 )
		{
			$where[] = 'pre.id = ' . $prediction_id;
		}

		if ( $search )
		{
			$where[] = "LOWER(pre.name) LIKE " . $this->_db->Quote( $search . '%' );
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'pre.published = 1';
			}
			elseif ($filter_state == 'U' )
				{
					$where[] = 'pre.published = 0';
				}
		}

		$where 	= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

	function getChilds( $pred_id, $all = false )
	{
		$what = 'pro.*';
		if ( $all )
		{
			$what = 'pro.project_id';
		}
		//$query = "SELECT " . $what . " FROM #__joomleague_predictiongame_project WHERE prediction_id = '" . $this->id . "'";
		$query = "	SELECT	" . $what . ",
							joo.name AS project_name

					FROM #__joomleague_prediction_project AS pro
					LEFT JOIN #__joomleague_project AS joo ON joo.id=pro.project_id

					WHERE pro.prediction_id = '" . $pred_id . "'";

		$this->_db->setQuery( $query );
		if ( $all )
		{
			return $this->_db->loadResultArray();
		}
		return $this->_db->loadAssocList( 'id' );
	}

	function getAdmins( $pred_id, $list = false )
	{
		$as_what = '';
		if ( $list )
		{
			$as_what = ' AS value';
		}
		#$query = "SELECT user_id" . $as_what . " FROM #__joomleague_predictiongame_admins WHERE prediction_id = " . $this->id;
		$query = "SELECT user_id" . $as_what . " FROM #__joomleague_prediction_admin WHERE prediction_id = " . $pred_id;

		$this->_db->setQuery( $query );
		if ( $list )
		{
			return $this->_db->loadObjectList();
		}
		else
		{
			return $this->_db->loadResultArray();
		}
	}

	/**
	* Method to return a prediction games array
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getPredictionGames()
	{
		$query = "	SELECT	id AS value,
							name AS text
					FROM #__joomleague_prediction_game
					ORDER by name";

		$this->_db->setQuery( $query );

		if ( !$result = $this->_db->loadObjectList() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		else
		{
			return $result;
		}
	}

}
?>