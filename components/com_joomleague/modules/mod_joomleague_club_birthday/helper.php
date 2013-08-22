<?php
/**
 * @version $Id$
 * @package Joomleague
 * @subpackage club_birthday
 * @copyright Copyright (C) 2013  fussballineuropa
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
 */

// no direct access

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.utilities.arrayhelper' );



$clubs = array();
$crew = array();

class modJoomleagueClubBirthdayHelper
{
    
function jl_birthday_sort ($array, $sort) 
{

/**
	 * Utility function to sort an array of objects on a given field
	 *
	 * @param   array  &$a             An array of objects
	 * @param   mixed  $k              The key (string) or a array of key to sort on
	 * @param   mixed  $direction      Direction (integer) or an array of direction to sort in [1 = Ascending] [-1 = Descending]
	 * @param   mixed  $caseSensitive  Boolean or array of booleans to let sort occur case sensitive or insensitive
	 * @param   mixed  $locale         Boolean or array of booleans to let sort occur using the locale language or not
	 *
	 * @return  array  The sorted array of objects
	 *
	 * @since   11.1
	 */
     		
		$res = JArrayHelper::sortObjects($array,'age',$sort);
        return $res;
	}    

function getClubs($limit)
	{
$birthdaytext='';
$database = JFactory::getDBO();
// get club info, we have to make a function for this
$dateformat = "DATE_FORMAT(c.founded,'%Y-%m-%d') AS date_of_birth";

/*
SELECT c.id, c.founded, c.name, c.alias, c.founded_year, 
			c.logo_big AS picture, c.country, 
			DATE_FORMAT(c.founded, '%m-%d')AS daymonth,
			YEAR( CURRENT_DATE( ) ) as year,
			(YEAR( CURRENT_DATE( ) ) - YEAR( c.founded ) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') > DATE_FORMAT(c.founded, '%m.%d'), 1, 0)) AS age,
            YEAR( CURRENT_DATE( ) ) - c.founded_year as age_year,
			DATE_FORMAT(c.founded,'%Y-%m-%d') AS date_of_birth, 
			(TO_DAYS(DATE_ADD(c.founded, INTERVAL
			(YEAR(CURDATE()) - YEAR(c.founded) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') >
			DATE_FORMAT(c.founded, '%m.%d'), 1, 0))
			YEAR)) - TO_DAYS( CURDATE())+0) AS days_to_birthday,
            pt.project_id

			FROM b05ce_joomleague_club c 
            INNER JOIN b05ce_joomleague_team as t 
            ON t.club_id = c.id
            INNER JOIN b05ce_joomleague_project_team as pt 
            ON pt.team_id = t.id  
			WHERE ( c.founded != '0000-00-00' OR c.founded_year != '0000' )
            GROUP BY c.id
            ORDER BY days_to_birthday ASC
            
*/
	$query="SELECT c.id, c.founded, c.name, c.alias, c.founded_year, 
			c.logo_big AS picture, c.country, 
			DATE_FORMAT(c.founded, '%m-%d')AS daymonth,
			YEAR( CURRENT_DATE( ) ) as year,

			(YEAR( CURRENT_DATE( ) ) - YEAR( c.founded ) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') > DATE_FORMAT(c.founded, '%m.%d'), 1, 0)) AS age,
            YEAR( CURRENT_DATE( ) ) - c.founded_year as age_year,

			$dateformat, 

			(TO_DAYS(DATE_ADD(c.founded, INTERVAL
			(YEAR(CURDATE()) - YEAR(c.founded) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') >
			DATE_FORMAT(c.founded, '%m.%d'), 1, 0))
			YEAR)) - TO_DAYS( CURDATE())+0) AS days_to_birthday,
            pt.project_id

			FROM #__joomleague_club c 
            INNER JOIN #__joomleague_team as t 
            ON t.club_id = c.id
            INNER JOIN #__joomleague_project_team as pt 
            ON pt.team_id = t.id  
			WHERE ( c.founded != '0000-00-00' OR c.founded_year != '0000' ) 
             ";
			
	$query .= " GROUP BY c.id ";

	$query .= " ORDER BY days_to_birthday ASC ";

	$query .= " LIMIT " . $limit;

	$database->setQuery($query);
	//echo("<hr>".$database->getQuery($query));
	//$clubs = $database->loadAssocList();
    $result = $database->loadObjectList();
	return $result;
}

}


?>