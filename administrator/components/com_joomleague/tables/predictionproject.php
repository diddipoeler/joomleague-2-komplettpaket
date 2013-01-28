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
 * PredictionProject Table class
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class TablePredictionProject extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $prediction_id;
	var $project_id;

	var $mode;
	var $overview;

	var $points_tipp;
	var $points_tipp_joker;
	var $points_tipp_champ;
	var $points_correct_result;
	var $points_correct_result_joker;
	var $points_correct_diff;
	var $points_correct_diff_joker;
	var $points_correct_draw;
	var $points_correct_draw_joker;
	var $points_correct_tendence;
	var $points_correct_tendence_joker;

	var $joker;
	var $joker_limit;
	var $champ;
	//var $tip_admin;								// will not be used in future

	var $published;
	//var $ordering;

	var $checked_out;
	var $checked_out_time;
  var $league_champ;
  

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db)
	{
		parent::__construct( '#__joomleague_prediction_project', 'id', $db );
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
		if ( ! ( $this->prediction_id && $this->project_id ) )
		{
			$this->setError( JText::_( 'CHECK FAILED' ) );
			return false;
		}
		return true;
	}

}
?>