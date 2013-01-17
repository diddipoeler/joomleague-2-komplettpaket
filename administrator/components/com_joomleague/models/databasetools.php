<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Joomleague Component DatabaseTools Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */

class JoomleagueModelDatabaseTools extends JModel
{
	function optimize()
	{
		$query="SHOW TABLES LIKE '%_joomleague%'";
		$this->_db->setQuery($query);
		$results=$this->_db->loadResultArray();
		foreach ($results as $result)
		{
			$query='OPTIMIZE TABLE `'.$result.'`'; $this->_db->setQuery($query);
		}		
		
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	return true;
	}

	function repair()
	{
		$query="SHOW TABLES LIKE '%_joomleague%'";
		$this->_db->setQuery($query);
		$results=$this->_db->loadResultArray();
		foreach ($results as $result)
		{
			$query='REPAIR TABLE `'.$result.'`'; $this->_db->setQuery($query);
		}		
		
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	return true;
	}

}
?>