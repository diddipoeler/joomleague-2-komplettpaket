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
$database = JFactory::getDBO();
$clubs = array();
$crew = array();

if(!function_exists('jl_birthday_sort'))
{
	// snippet taken from http://de3.php.net/manual/en/function.uasort.php
	function jl_birthday_sort ($array, $arguments = array(), $keys = true) {
		$code = "\$result=0;";
		foreach ($arguments as $argument) {
			$field = substr($argument, 2, strlen($argument));
			$type = $argument[0];
			$order = $argument[1];
			$code .= "if (!Is_Numeric(\$result) || \$result == 0) ";
			if (strtolower($type) == "n") $code .= $order == "-" ? "\$result = (intval(\$a['{$field}']) > intval(\$b['{$field}']) ? -1 : (intval(\$a['{$field}']) < intval(\$b['{$field}']) ? 1 : 0));" : "\$result = (intval(\$a['{$field}']) > intval(\$b['{$field}']) ? 1 : (intval(\$a['{$field}']) < intval(\$b['{$field}']) ? -1 : 0));";
			else $code .= $order == "-" ? "\$result = strcoll(\$a['{$field}'], \$b['{$field}']) * -1;" : "\$result = strcoll(\$a['{$field}'], \$b['{$field}']);";
		}
		$code .= "return \$result;";
		$compare = create_function('$a, $b', $code);
		if ($keys) uasort($array, $compare);
		else usort($array, $compare);
		return $array;
	}
}

$birthdaytext='';

// get club info, we have to make a function for this
$dateformat = "DATE_FORMAT(c.founded,'%Y-%m-%d') AS date_of_birth";


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
			WHERE ( c.founded != '0000-00-00' AND c.founded_year != '0000' ) 
            OR ( c.founded != '0000-00-00' AND c.founded_year = '0000' ) 
            OR ( c.founded = '0000-00-00' AND c.founded_year != '0000' ) ";
			
	$query .= " GROUP BY c.id ";

	$query .= " ORDER BY days_to_birthday ASC ";

	if ($params->get('limit') > 0) $query .= " LIMIT " . $params->get('limit');

	$database->setQuery($query);
	//echo("<hr>".$database->getQuery($query));
	$clubs = $database->loadAssocList();




?>