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

/**
 * Joomleague Person Table class
 *
 * @package		Joomleague
 * @since 1.50a
 */
class TablePerson extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db)
	{
		parent::__construct( '#__joomleague_person', 'id', $db );
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
		if ( empty( $this->firstname ) && empty( $this->lastname ) )
		{
			$this->setError( JText::_( 'ERROR FIRSTNAME OR LASTNAME REQUIRED' ) );
			return false;
		}
		$parts = array( trim( $this->firstname ), trim( $this->lastname ) );
		$alias = JFilterOutput::stringURLSafe( implode( ' ', $parts ) );
	
		// setting alias
		if ( empty( $this->alias ) )
		{
			$this->alias = $alias;
		}
		else {
			$this->alias = JFilterOutput::stringURLSafe( $this->alias ); // make sure the user didn't modify it to something illegal...
		}
		//should check name unicity
		return true;
	}

}
?>