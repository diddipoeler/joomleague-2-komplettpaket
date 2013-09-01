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
 * Joomleague Component prediction members Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */

class JoomleagueModelPredictionMembers extends JoomleagueModelList
{
	var $_identifier = "predmembers";
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
        
        // Create a new query object.
        $query = $this->_db->getQuery(true);
        $query->select(array('tmb.*','u.name AS realname', 'u.username AS username', 'p.name AS predictionname' ))
        ->from('#__joomleague_prediction_member AS tmb')
        ->join('LEFT', '#__joomleague_prediction_game AS p ON p.id = tmb.prediction_id')
        ->join('LEFT', '#__users AS u ON u.id = tmb.user_id');

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
		$mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';

		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'tmb_filter_order',		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'tmb_filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		if ( $filter_order == 'u.username' )
		{
			$orderby 	= 'u.username ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= '' . $filter_order . ' ' . $filter_order_Dir . ' , u.username ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';

		$filter_state		= $mainframe->getUserStateFromRequest( $option . 'tmb_filter_state',		'filter_state',		'',				'word' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'tmb_filter_order',		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'tmb_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option . 'tmb_search',				'search',			'',				'string' );
		$search_mode		= $mainframe->getUserStateFromRequest( $option . 'tmb_search_mode',			'search_mode',		'',				'string' );
		$search				= JString::strtolower( $search );

		$where = array();
		$prediction_id = (int) $mainframe->getUserState( 'com_joomleague' . 'prediction_id' );
		if ( $prediction_id > 0 )
		{
			$where[] = 'tmb.prediction_id = ' . $prediction_id;
		}

		if ( $search )
		{
			$where[] = "LOWER(u.username) LIKE " . $this->_db->Quote( $search . '%' );
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'tmb.approved = 1';
			}
			elseif ($filter_state == 'U' )
				{
					$where[] = 'tmb.approved = 0';
				}
		}

		$where 	= ( count( $where ) ? ''. implode( ' AND ', $where ) : '' );

		return $where;
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
	
	function getPredictionProjectName($predictionID)
	{
	$mainframe			=& JFactory::getApplication();
	$option				= 'com_joomleague';
	
		$query="SELECT	ppj.name AS pjName
				  FROM #__joomleague_prediction_game AS ppj 
          where ppj.id = " . $predictionID;
				  

		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
	  
    //$mainframe->enqueueMessage(JText::_('<br />predictionID<pre>~' . print_r($predictionID,true) . '~</pre><br />'),'Notice');
    //$mainframe->enqueueMessage(JText::_('<br />result<pre>~' . print_r($result,true) . '~</pre><br />'),'Notice');
    
		return $result;
	}
	
	function getPredictionMembers($prediction_id)
	{
	$query="	SELECT	pm.user_id AS value,
							u.name AS text
					FROM #__joomleague_prediction_member AS pm
					LEFT JOIN #__users AS u ON	u.id = pm.user_id
					WHERE	prediction_id = " . (int) $prediction_id;
	
    $this->_db->setQuery($query);
	$results = $this->_db->loadObjectList();
    return $results;				
	}
	
	function getJLUsers($prediction_id)
	{
	//$not_in = array();
	$query="	SELECT	pm.user_id AS value
					FROM #__joomleague_prediction_member AS pm
					LEFT JOIN #__users AS u ON	u.id = pm.user_id
					WHERE	prediction_id = " . (int) $prediction_id;
	$this->_db->setQuery($query);
    
    if ( $predresult = $this->_db->loadResultArray() )
    {
    $query = "	SELECT	id AS value,
							name AS text
					FROM #__users
          where id not in (" . implode(",", $this->_db->loadResultArray() ) .")
					ORDER by name";
    }
    else
    {
    $query = "	SELECT	id AS value,
							name AS text
					FROM #__users
					ORDER by name";
    }   
		

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
	
	function save_memberlist()
	{
	$mainframe			=& JFactory::getApplication();
	$option				= 'com_joomleague';
	
  $post	= JRequest::get('post');
	$cid	= JRequest::getVar('cid', array(0), 'post', 'array');
  $prediction_id = (int) $cid[0];
  //echo '<br />save_memberlist post<pre>~' . print_r($post,true) . '~</pre><br />';
  
  //$mainframe->enqueueMessage(JText::_('<br />save_memberlist post<pre>~' . print_r($post,true) . '~</pre><br />'),'Notice');
  //$mainframe->enqueueMessage(JText::_('<br />prediction id<pre>~' . print_r($prediction_id,true) . '~</pre><br />'),'Notice');
  
  
  foreach ( $post['prediction_members'] as $key => $value )
  {
  //$mainframe->enqueueMessage(JText::_('<br />memberlist id<pre>~' . print_r($value,true) . '~</pre><br />'),'Notice');
  //$table = 'predictionmember';
  $table = 'predictionentry';
  $rowproject =& JTable::getInstance( $table, 'Table' );
  //$rowproject->load( $value );
  $rowproject->prediction_id = $prediction_id;
  $rowproject->user_id = $value;
  $rowproject->registerDate = JHTML::date(time(),'%Y-%m-%d %H:%M:%S');
  $rowproject->approved = 1;
  if ( !$rowproject->store() )
  {
  //echo 'project -> '.$value. ' nicht gesichert <br>';
  }
  else
  {
  //echo 'project -> '.$value. ' gesichert <br>';
  }
        
  }
  
  }
	

}
?>