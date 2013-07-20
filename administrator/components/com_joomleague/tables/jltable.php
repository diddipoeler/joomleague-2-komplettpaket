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
defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Joomleague JLTable Table class
 *
 * @package	Joomleague
 * @since 1.50a
 */
class JLTable extends JTable {

	function bind($array, $ignore = '') {
		if (key_exists('extended', $array) && is_array($array['extended'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['extended']);
			$array['extended'] = $registry->toString('ini');
		}
		return parent :: bind($array, $ignore);
	}

	/**
	 * try to insert first, update if fails
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function insertIgnore($updateNulls = false) {
		$k = $this->_tbl_key;

		$ret = $this->_insertIgnoreObject($this->_tbl, $this, $this->_tbl_key);
		if (!$ret) {
			$this->setError(get_class($this) . '::store failed - ' . $this->getDbo()->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Inserts a row into a table based on an objects properties, ignore if already exists
	 *
	 * @access  public
	 * @param string  The name of the table
	 * @param object  An object whose properties match table fields
	 * @param string  The name of the primary key. If provided the object property is updated.
	 * @return int number of affected row
	 */
	function _insertIgnoreObject($table, & $object, $keyName = NULL) {
		$fmtsql = 'INSERT IGNORE INTO ' . $this->getDbo()->nameQuote($table) . ' ( %s ) VALUES ( %s ) ';
		$fields = array ();
		foreach (get_object_vars($object) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') {
				// internal field
				continue;
			}
			$fields[] = $this->getDbo()->nameQuote($k);
			$values[] = $this->getDbo()->isQuoted($k) ? $this->getDbo()->Quote($v) : (int) $v;
		}
		$this->getDbo()->setQuery(sprintf($fmtsql, implode(",", $fields), implode(",", $values)));
		if (!$this->getDbo()->query()) {
			return false;
		}
		$id = $this->getDbo()->insertid();
		if ($keyName && $id) {
			$object-> $keyName = $id;
		}
		return $this->getDbo()->getAffectedRows();
	}
    
    /**
	 * Method to determine if a row is checked out and therefore uneditable by
	 * a user. If the row is checked out by the same user, then it is considered
	 * not checked out -- as the user can still edit it.
	 *
	 * @param   integer  $with     The userid to preform the match with, if an item is checked
	 * out by this user the function will return false.
	 * @param   integer  $against  The userid to perform the match against when the function
	 * is used as a static function.
	 *
	 * @return  boolean  True if checked out.
	 *
	 * @link    http://docs.joomla.org/JTable/isCheckedOut
	 * @since   11.1
	 * @todo    This either needs to be static or not.
	 */
	public static function _isCheckedOut($with = 0, $against = null)
	{
		// Handle the non-static case.
		if (isset($this) && ($this instanceof JTable) && is_null($against))
		{
			$against = $this->get('checked_out');
		}
	
		// The item is not checked out or is checked out by the same user.
		if (!$against || ($against == $with))
		{
			return false;
		}
	
		$db = JFactory::getDBO();
		$db->setQuery('SELECT COUNT(userid)' . 
						' FROM ' . $db->quoteName('#__session') . 
						' WHERE ' . $db->quoteName('userid') . ' = ' . (int) $against);
		$checkedOut = (boolean) $db->loadResult();
	
		// If a session exists for the user then it is checked out.
		return $checkedOut;
	}


}
?>