<?php
/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.model');

/**
 * Joomleague Component Import Model
 *
 * @package Joomla
 * @subpackage Joomleague
 * @since		1.5
 */
class JoomleagueModelImport extends JModel
{
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * return __joomleague_persons table fields name
	 *
	 * @return array
	 */
	function getTableFields($table)
	{
		$tables = array ($table);
		$tablesfields = $this->_db->getTableFields($tables);

		return array_keys($tablesfields[$table]);
	}

	/**
	 * import data corresponding to fieldsname into the database table
	 *
	 * @param array $fieldsname
	 * @param array $data the records
	 * @param boolean $replace replace if id already exists
	 * @param string $table tableclassname e.g Person
	 * @return array ['added', 'updated', 'exists', 'errormsg'] number of records inserted, updated, already exists and a possible db error msg
	 */
	function import($fieldsname, $data, $replace = true, $table)
	{
		$ignore = array ();
		if (!$replace)
		{
			$ignore[] = 'id';
		}
		$rec = array ('added'=>0, 'updated'=>0, 'exists' =>0, 'errormsg'=>'');
		// parse each row
		foreach ($data AS $row)
		{
			$values = array ();
			// parse each specified field and retrieve corresponding value for the record
			foreach ($fieldsname AS $k=>$field)
			{
				$values[$field] = $row[$k];
			}

			$object = & JTable::getInstance($table, 'Table');

			//print_r($values);exit;
			$object->bind($values, $ignore);

			// Make sure the data is valid
			if (!$object->check())
			{
				$this->setError($object->getError());
				$rec['errormsg'] = JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR_CHECK').$object->getError();
				continue ;
			}

			// Store it in the db
			if ($replace)
			{
				// We want to keep id from database so first we try to insert into database. if it fails,
				// it means the record already exists, we can use store().
				if (!$object->insertIgnore())
				{
					if (!$object->store())
					{
						//echo JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR_STORE').$this->_db->getErrorMsg()."\n";
						$rec['exists']++;
						continue ;
					}
					else
					{
						$rec['updated']++;
					}
				}
				else
				{
					$rec['added']++;
				}
			}
			else
			{
				if (!$object->store())
				{
					//show last error message
					//$rec['errormsg'] =  JText::_('COM_JOOMLEAGUE_GLOBAL_ERROR_STORE').$this->_db->getErrorMsg()."<br \>\n";
					$rec['exists']++;
					continue ;
				}
				else
				{
					$rec['added']++;
				}
			}
		}
		return $rec;
	}
}
?>
