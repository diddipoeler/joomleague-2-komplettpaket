<?php

/**
 * @copyright	Copyright (C) 2006-2009 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
* Season Table class
*
* @package		Joomleague
* @since 0.1
*/
class Tablejlextassociation extends JLTable {
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $name;
	var $short_name;
	var $middle_name;
	

	/**
	 * alias for nice sef urls
	 * @var string
	 */
	var $alias;

	var $country;
	
	/**
	 * stores extended info
	 *
	 * @var string
	 */
	var $extended;	

	var $ordering;

	var $checked_out;
	var $checked_out_time;
  var $website;
  var $assocflag;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db)
	{
		parent :: __construct( '#__joomleague_associations', 'id', $db );
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
		$alias = JFilterOutput::stringURLSafe($this->name);

		if ( empty( $this->alias ) || $this->alias === $alias )
		{
			$this->alias = $alias;
		}
		//should check name unicity
		return true;
	}
}
?>