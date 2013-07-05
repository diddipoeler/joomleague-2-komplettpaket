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
 * Prediction Game Table class
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class TablePredictionGroup extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $name;

	/* alias for nice sef urls */
	var $alias;

	var $auto_approve;
	var $only_favteams;
	var $admin_tipp;
	var $master_template;
	var $sub_template_id;
	var $extension;
	var $notify_to;

	var $published;

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
		parent::__construct( '#__joomleague_prediction_groups', 'id', $db );
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
		if ( trim( $this->name ) == '' )
		{
			$this->setError( JText::_( 'CHECK FAILED - Empty name of prediction game' ) );
			return false;
		}

		$alias = JFilterOutput::stringURLSafe( $this->name );
		if ( empty( $this->alias ) || $this->alias === $alias )
		{
			$this->alias = $alias;
		}

		return true;
	}

}
?>