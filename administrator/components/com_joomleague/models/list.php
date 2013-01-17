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

/**
 * Joomleague Component Admin List Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	Joomleague
 * @since	0.1
 */
class JoomleagueModelList extends JModel
{
	 /**
	 * list data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/* current project id */
	var $_project_id = 0;
	var $_identifier = "";

	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		parent::__construct();
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();

		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest(	'global.list.limit', 
														'limit', 
														$mainframe->getCfg('list_limit'), 
														'int' );
		$this->setState('limit', $limit );
		$limitstart	= $mainframe->getUserStateFromRequest(	$option.'.'.$this->_identifier.'.limitstart',
															'limitstart', 
															0, 'int' );
		$this->setState('limitstart', $limitstart);
		$this->_project_id = $mainframe->getUserState( $option . 'project', 0 );
	}

	/**
	 * Method to get List data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if ( empty($this->_data) )
		{
			$query = $this->_buildQuery();
			if ( !$this->_data = $this->_getList(	$query, $this->getState( 'limitstart' ), 
													$this->getState( 'limit' ) ) )
			{
				echo $this->_db->getErrorMsg();
			}
		}
		return $this->_data;
	}

	/**
	 * Method to get the total number of items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_total ) )
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount( $query );
		}
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the list
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport( 'joomla.html.pagination' );
			$this->_pagination = new JPagination(	$this->getTotal(), 
													$this->getState( 'limitstart' ), 
													$this->getState( 'limit' ) );
		}
		return $this->_pagination;
	}
	
	function getIdentifier() {
		return $this->_identifier;
	}
	
	function publish($pks=array(),$value=1)
	{
		$user 		= JFactory::getUser();
		$table		= $this->getTable();
		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}
		
		return true;
	}
}
?>