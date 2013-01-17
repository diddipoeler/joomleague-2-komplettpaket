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

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT.DS.'models'.DS.'list.php');

/**
 * Joomleague Component Treetos Model
 *
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueModelTreetos extends JoomleagueModelList
{
	var $_identifier = "treetos";
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		
		$query = '	SELECT	tt.* ';
		$query .=	' FROM #__joomleague_treeto AS tt ';
		$query .=	' LEFT JOIN #__joomleague_division d on d.id = tt.division_id ';
		$query .=	$where . $orderby ;
		return $query;
	}

	function _buildContentOrderBy()
	{
		$orderby 	= ' ORDER BY tt.id DESC ';
		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
		$division = (int) $mainframe->getUserStateFromRequest( $option.'tt_division', 'division', 0 );
		$division=JString::strtolower($division);
		$where = ' WHERE  tt.project_id = ' . $project_id ;
		if($division > 0)
		{
			$where .= ' AND d.id = ' . $this->_db->Quote($division) ;
		}
		return $where;
	}

	function storeshort( $cid, $data )
	{
		$result = true;
		for ( $x = 0; $x < count( $cid ); $x++ )
		{
			
			$tblTreeto =& JTable::getInstance('Treeto','Table');
			$tblTreeto->id = $cid[$x];
			$tblTreeto->division_id =	$data['division_id' . $cid[$x]];
			
			if (!$tblTreeto->check())
			{
				$this->setError($tblTreeto->getError());
				$result = false;
			}
			if (!$tblTreeto->store())
			{
				$this->setError($tblTreeto->getError());
				$result = false;
			}
		}
		return $result;
	}

}
?>