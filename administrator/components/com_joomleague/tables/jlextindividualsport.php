<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
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
 * Match Table class
 *
 * @author Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class Tablejlextindividualsport  extends JLTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = NULL;

	/**
	 * round
	 *
	 * @var int
	 */
	var $round_id;

	/**
	 * matchnumber
	 *
	 * @var int
	 */
	var $match_number;

	/**
	 * home team id
	 *
	 * @var int
	 */
	var $projectteam1_id;

	/**
	 * away team id
	 *
	 * @var int
	 */
	var $projectteam2_id;

	/**
	 * playground id
	 *
	 * @var int
	 */
	var $playground_id;
	
	var $match_id;
	var $teamplayer1_id;
	var $teamplayer2_id;

	/**
	 * date
	 *
	 * @var unknown_type
	 */
	var $match_date;

	/**
	 * time_present
	 *
	 * @var varchar
	 */
	var $time_present;

	/**
	 * home score
	 *
	 * @var int
	 */
	var $team1_result;

	/**
	 * away score
	 *
	 * @var unknown_type
	 */
	var $team2_result;

	/**
	 * home bonus points
	 *
	 * @var int
	 */
	var $team1_bonus;

	/**
	 * away bonus points
	 *
	 * @var int
	 */
	var $team2_bonus;

	/**
	 * TODO: add comment !
	 * @var float
	 */
	var $team1_legs;

	/**
	 * TODO: add comment !
	 * @var float
	 */
	var $team2_legs;

	/**
	 * home score by period
	 *
	 * @var array
	 */
	var $team1_result_split;

	/**
	 * away score by period
	 *
	 * @var unknown_type
	 */
	var $team2_result_split;

	/**
	 * type of results (normal, overtime, shootouts...)
	 *
	 * @var int
	 */
	var $match_result_type;

	/**
	 * home overtime score
	 *
	 * @var int
	 */
	var $team1_result_ot;

	/**
	 * away overtime score
	 *
	 * @var int
	 */
	var $team2_result_ot;
	
	/**
	 * home shootout score
	 *
	 * @var int
	 */
	var $team1_result_so;

	/**
	 * away shootout score
	 *
	 * @var int
	 */
	var $team2_result_so;
	
	/**
	 * changed decision ?
	 *
	 * @var boolean
	 */
	var $alt_decision;

	/**
	 * reason to alter the result
	 *
	 * @var string
	 */
	var $decision_info;

	/**
	 * new home score
	 *
	 * @var int
	 */
	var $team1_result_decision;

	/**
	 * new away score
	 *
	 * @var int
	 */
	var $team2_result_decision;

	/**
	 * match cancelled
	 *
	 * @var string
	 */
	var $cancel;

	/**
	 * match cancelled reason
	 *
	 * @var string
	 */
	var $cancel_reason;

	/**
	 * count result in ranking ?
	 *
	 * @var boolean
	 */
	var $count_result;

	/**
	 * attendance
	 *
	 * @var int
	 */
	var $crowd;

	/**
	 * full summary
	 *
	 * @var string
	 */
	var $summary;

	/**
	 * show reports ?
	 *
	 * @var boolean
	 */
	var $show_report;

	/**
	 * playground id
	 *
	 * @var int
	 */
	var $preview;

	/**
	 * one line summary
	 *
	 * @var string
	 */
	var $match_result_detail;

	/**
	 * new match id if match was postponed or canceled
	 *
	 * @var int
	 */
	//var $new_match_id;

	/**
	 * stores extended info
	 *
	 * @var string
	 */
	var $extended;
	
	var $published;
	var $checked_out;
	var $checked_out_time;
	
	var $old_match_id;
	var $new_match_id;
	
	/**
	 * 
	 * which team get the won recorded
	 * @var int
	 */
	var $team_won;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db)
	{
		parent::__construct( '#__joomleague_match_single', 'id', $db );
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
		if (!is_numeric($this->team1_result_decision)) {
			$this->team1_result_decision = null;
		}
		if (!is_numeric($this->team2_result_decision)) {
			$this->team2_result_decision = null;
		}
		
		return true;
	}

}
?>