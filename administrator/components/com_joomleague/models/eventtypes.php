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
require_once(JPATH_COMPONENT.DS.'models'.DS.'list.php');

/**
 * Joomleague Component Events Model
 *
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueModelEventtypes extends JoomleagueModelList
{
	var $_identifier = "eventtypes";
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where=$this->_buildContentWhere();
		$orderby=$this->_buildContentOrderBy();
        
        // Create a new query object.
        $query = $this->_db->getQuery(true);
        $query->select(array('obj.*', 'st.name AS sportstype', 'u.name AS editor'))
        ->from('#__joomleague_eventtype AS obj')
        ->join('LEFT', '#__joomleague_sports_type AS st ON st.id=obj.sports_type_id')
        ->join('LEFT', '#__users AS u ON u.id = obj.checked_out');

        if ($where)
        {
            $query->where($where);
        }
        if ($orderby)
        {
            $query->order($orderby);
        }
        
		
		return $query;
	}

	function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_order',		'filter_order',		'obj.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_order_Dir',	'filter_order_Dir',	'',				'word');
		if ($filter_order=='obj.ordering')
		{
			$orderby='obj.ordering '.$filter_order_Dir;
		}
		else
		{
			$orderby=''.$filter_order.' '.$filter_order_Dir.',obj.ordering ';
		}
		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_sports_type	= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_sports_type',	'filter_sports_type','',	'int');
		$filter_state		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_state',		'filter_state',		'',				'word');
		$filter_order		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_order',		'filter_order',		'obj.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search				= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.search',				'search',			'',				'string');
		$search_mode		= $mainframe->getUserStateFromRequest($option.'.'.$this->_identifier.'.search_mode',		'search_mode',		'',				'string');

		$search=JString::strtolower($search);
		$where=array();
		if ($filter_sports_type > 0)
		{
			$where[]='obj.sports_type_id='.$this->_db->Quote($filter_sports_type);
		}
		if ($search)
		{
			$where[]='LOWER(obj.name) LIKE '.$this->_db->Quote('%'.$search.'%');
		}
		if ($filter_state)
		{
			if ($filter_state == 'P')
			{
				$where[]='obj.published=1';
			}
			elseif ($filter_state == 'U')
			{
				$where[]='obj.published=0';
			}
		}
		$where=(count($where) ? ''. implode(' AND ',$where) : '');
		return $where;
	}

}
?>