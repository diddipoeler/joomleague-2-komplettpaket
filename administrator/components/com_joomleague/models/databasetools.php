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
    
    function picturepath()
	{
	$arrQueries = array();
		
		$query = "update #__joomleague_club set logo_big = replace(logo_big, 'media/com_joomleague/clubs/large', 'images/com_joomleague/database/clubs/large')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_club set logo_middle = replace(logo_middle, 'media/com_joomleague/clubs/medium', 'images/com_joomleague/database/clubs/medium')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_club set logo_small = replace(logo_small, 'media/com_joomleague/clubs/small', 'images/com_joomleague/database/clubs/small')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_eventtype set icon = replace(icon, 'media/com_joomleague/event_icons', 'images/com_joomleague/database/events')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_person set picture = replace(picture, 'media/com_joomleague/persons', 'images/com_joomleague/database/persons')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_team_player set picture = replace(picture, 'media/com_joomleague/persons', 'images/com_joomleague/database/persons')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_project set picture = replace(picture, 'media/com_joomleague/projects', 'images/com_joomleague/database/projects')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_playground set picture = replace(picture, 'media/com_joomleague/playgrounds', 'images/com_joomleague/database/playgrounds')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_sports_type set icon = replace(icon, 'media/com_joomleague/sportstypes', 'images/com_joomleague/database/sport_types')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_team set picture = replace(picture, 'media/com_joomleague/teams', 'images/com_joomleague/database/teams')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_project_team set picture = replace(picture, 'media/com_joomleague/teams', 'images/com_joomleague/database/teams')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_statistic set icon = replace(icon, 'media/com_joomleague/statistics', 'images/com_joomleague/database/statistics')";
		array_push($arrQueries, $query);
        
        $query="SHOW TABLES LIKE '%_joomleague%'";
			
		$this->_db->setQuery($query);
		$results = $this->_db->loadColumn();
		if(is_array($results)) {
			echo JText::_('Database Tables Picture Path Migration');
			foreach ($arrQueries as $key=>$value) {
				$this->_db->setQuery($value);
				if (!$this->_db->query())
				{
					echo '-> '.JText::_('Failed').'! <br>';
					$this->setError($this->_db->getErrorMsg());
					echo $this->_db->getErrorMsg();
					//return false;
				} else {
					//echo "-> done !<br>";		
				}
				
			}
			echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
            return true;
		} else {
			echo JText::_('No Picture Path Migration neccessary!'); 
            return false;
		}	
	//return true;
	}

}
?>