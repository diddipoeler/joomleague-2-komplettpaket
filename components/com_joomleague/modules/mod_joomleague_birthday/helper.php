<?php
/**
 * @version $Id$
 * @package Joomleague
 * @subpackage pl_birthday
 * @copyright Copyright (C) 2009  JoomLeague
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
 */

// no direct access

defined('_JEXEC') or die('Restricted access');
$database = JFactory::getDBO();
$players = array();
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

$usedp = $params->get('projects','0');
$p = (is_array($usedp)) ? implode(",", $usedp) : $usedp;
$usedteams = "";
// get favorite team(s), we have to make a function for this
if ($params->get('use_fav')==1)
{
	$query='SELECT fav_team FROM #__joomleague_project';
	if ($p!='' && $p>0) $query.= ' WHERE id IN ('.$p.')';

	$database->setQuery($query);
	$temp=$database->loadResultArray();

	if (count($temp)>0)
	{
		$usedteams=join(',', array_filter($temp));
	}
}
else
{
	$usedteams = $params->get('teams');
}

$birthdaytext='';

// get player info, we have to make a function for this
$dateformat = "DATE_FORMAT(p.birthday,'%Y-%m-%d') AS date_of_birth";

if ($params->get('use_which') <= 1)
{
	$query="SELECT p.id, p.birthday, p.firstname, p.nickname, p.lastname,
			p.picture AS default_picture, p.country, 
			DATE_FORMAT(p.birthday, '%m-%d')AS daymonth,
			YEAR( CURRENT_DATE( ) ) as year,

			(YEAR( CURRENT_DATE( ) ) - YEAR( p.birthday ) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') > DATE_FORMAT(p.birthday, '%m.%d'), 1, 0)) AS age,

			$dateformat, tp.picture,

			(TO_DAYS(DATE_ADD(p.birthday, INTERVAL
			(YEAR(CURDATE()) - YEAR(p.birthday) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') >
			DATE_FORMAT(p.birthday, '%m.%d'), 1, 0))
			YEAR)) - TO_DAYS( CURDATE())+0) AS days_to_birthday,

			'person' AS func_to_call, '' project_id, '' team_id,
			'pid' AS id_to_append, 1 AS type,

			pt.team_id, pt.project_id

			FROM #__joomleague_person p 
			INNER JOIN #__joomleague_team_player tp ON tp.person_id = p.id 
			INNER JOIN #__joomleague_project_team pt ON pt.id = tp.projectteam_id 
			WHERE p.published = 1 AND p.birthday != '0000-00-00'";
			
	if ($usedteams!='') $query .= " AND pt.team_id IN (".$usedteams.") ";

	if ($p!='' && $p>0) $query .= " AND pt.project_id IN (".$p.") ";

	$query .= " GROUP BY p.id ";

	$maxDays = $params->get('maxdays');
	if ((strlen($maxDays) > 0) && (intval($maxDays) >= 0))
	{
		$query .= " HAVING days_to_birthday <= " . intval($maxDays);
	}

	$query .= " ORDER BY days_to_birthday ASC ";

	if ($params->get('limit') > 0) $query .= " LIMIT " . $params->get('limit');

	$database->setQuery($query);
	//echo("<hr>".$database->getQuery($query));
	$players=$database->loadAssocList();
}

//get staff info, we have to make a function for this
if ($params->get('use_which') == 2 || $params->get('use_which') == 0)
{
	$query="SELECT p.id, p.birthday, p.firstname, p.nickname, p.lastname,
			p.picture AS default_picture, p.country, 
			DATE_FORMAT(p.birthday, '%m-%d')AS daymonth,
			YEAR( CURRENT_DATE( ) ) as year,

			(YEAR( CURRENT_DATE( ) ) - YEAR( p.birthday ) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') > DATE_FORMAT(p.birthday, '%m.%d'), 1, 0)) AS age,

			$dateformat, ts.picture,

			(TO_DAYS(DATE_ADD(p.birthday, INTERVAL
			(YEAR(CURDATE()) - YEAR(p.birthday) +
			IF(DATE_FORMAT(CURDATE(), '%m.%d') >
			DATE_FORMAT(p.birthday, '%m.%d'), 1, 0))
			YEAR)) - TO_DAYS( CURDATE())+0) AS days_to_birthday,

			'staff' AS func_to_call, '' project_id, '' team_id,
			'tsid' AS id_to_append, 2 AS type, 

			pt.team_id, pt.project_id

			FROM #__joomleague_person p
			INNER JOIN #__joomleague_team_staff ts ON ts.person_id = p.id 
			INNER JOIN #__joomleague_project_team pt ON pt.id = ts.projectteam_id 
			WHERE p.published = 1 AND p.birthday != '0000-00-00'";

	// Exclude players from the staff query to avoid duplicate persons (if a person is both player and staff)
	if(count($players) > 0)
	{
		$ids = "0";
		foreach ($players AS $player)
		{
			$ids .= "," . $player['id'];
		}
		$query .= " AND p.id NOT IN (" . $ids . ") ";
	}

	if ($usedteams!='') $query .= " AND pt.team_id IN (".$usedteams.") ";

	if ($p!='' && $p>0) $query .= " AND pt.project_id IN (".$p.") ";

	$query .= " GROUP BY p.id ";

	$maxDays = $params->get('maxdays');
	if ((strlen($maxDays) > 0) && (intval($maxDays) >= 0))
	{
		$query .= " HAVING days_to_birthday <= " . intval($maxDays);
	}

	$query .= " ORDER BY days_to_birthday ASC";

	if ($params->get('limit') > 0) $query .= " LIMIT " . $params->get('limit');

	$database->setQuery($query);
	//echo("<hr>".$database->getQuery($query));
	$crew=$database->loadAssocList();
}
?>