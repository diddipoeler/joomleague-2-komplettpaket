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

// Include library dependencies
jimport( 'joomla.filter.input' );

/**
 * PredictionAdmins Table class
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class TablePredictionAdmin extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $prediction_id;	// field contains the id of a record of the JoomLeague Table #__joomleague_prediction_game
	var $user_id;		// field contains the id of a record of the Joomla Table #__user
	//var $project_id;	// This field "$project_id" will not be used in future anymore as there is already
						// a table prediction_project where a relation between the pedictionID and projectID
						//  is declared

	var $checked_out;
	var $checked_out_time;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db)
	{
		parent::__construct( '#__joomleague_prediction_admin', 'id', $db );
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{
		if ( ! ( $this->prediction_id && $this->user_id ) )
		{
			$this->setError( JText::_( 'CHECK FAILED' ) );
			return false;
		}
		return true;
	}

}
?>