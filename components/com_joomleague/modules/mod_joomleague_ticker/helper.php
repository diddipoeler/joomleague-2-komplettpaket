<?php
/**
 * @version $Id$
 * @package Joomleague
 * @subpackage ticker
 * @copyright Copyright (C) 2009  JoomLeague
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
 */

// no direct access

defined('_JEXEC') or die('Restricted access');

class modJoomleagueTickerHelper
{

	function getMatches($numberofmatches, $projectid, $teamid, $selectiondate, $ordering = 'DESC', $round=0, $matchstatus, $bUseFavteams)
	{
		$result = array();
		$query_SELECT = ' SELECT matches.*, r.roundcode as roundcode, r.id as roundid, r.name as roundname, "dummy", '
		. ' jl.name AS project_name, '
		. ' t1.name AS home_name, '
		. ' t1.middle_name AS home_middlename, '
		. ' t1.short_name AS home_shortname, '
		. ' t2.name AS away_name, '
		. ' t2.middle_name AS away_middlename, '
		. ' t2.short_name AS away_shortname, '
		. ' c1.logo_small AS home_icon, '
		. ' c2.logo_small AS away_icon, '
		. ' t1.id AS team1_id, '
		. ' t2.id AS team2_id, '
		. ' jl.id AS project_id, '
		. ' matches.id AS match_id, '
		. ' pt1.division_id '
				;
		$query_FROM  = ' FROM #__joomleague_match AS matches '
		. ' INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id = matches.projectteam1_id '
		. ' INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id = matches.projectteam2_id '
		. ' INNER JOIN #__joomleague_team AS t1 ON pt1.team_id = t1.id '
		. ' INNER JOIN #__joomleague_team AS t2 ON pt2.team_id = t2.id '
		. ' INNER JOIN #__joomleague_round AS r ON matches.round_id = r.id '
		. ' INNER JOIN #__joomleague_project AS jl ON pt1.project_id = jl.id '
		. ' INNER JOIN #__joomleague_club AS c1 ON c1.id = t1.club_id '
		. ' INNER JOIN #__joomleague_club AS c2 ON c2.id = t2.club_id '
		;
			
		$query_WHERE = " WHERE matches.published=1 ";


		switch ($matchstatus)
		{
			case 0:
				$query_WHERE .= " AND matches.match_date >=  STR_TO_DATE('".$selectiondate."', '%Y-%m-%d-%H:%i') ";
				$query_WHERE .= " AND ( matches.match_date + INTERVAL (jl.game_regular_time+jl.halftime*(jl.game_parts-1)) MINUTE ) < NOW() ";
				break;
			case 1:
				$query_WHERE .= " AND ( matches.match_date BETWEEN  STR_TO_DATE('".$selectiondate."', '%Y-%m-%d-%H:%i') AND NOW() ) ";
					
				break;
			case 2:
				$query_WHERE .= " AND ( matches.match_date + INTERVAL (jl.game_regular_time+jl.halftime*(jl.game_parts-1)) MINUTE ) >= NOW() ";

					
				break;
			case 3:
				$query_WHERE .= " AND matches.match_date >= NOW() ";
				break;
			case 4:
				$query_WHERE .= " AND matches.match_date >= STR_TO_DATE('".$selectiondate."', '%Y-%m-%d-%H:%i')";
					
				break;
		}
		if ($round != 0 )
		{
			$query_WHERE .= " AND r.id = $round";
		}

		if ($projectid != -1 && $projectid != '')
		{
			if(count($projectid) >1 || is_array($projectid)) {
				$projects = implode(",", $projectid);
			} else {
				$projects = $projectid;
			}
			$query_WHERE .= " AND pt1.project_id IN (" . $projects. ")";
		}
		if ($teamid != -1 && $teamid != '' && is_array($teamid))
		{
			if($teamid[0]!="") {
				if(count($teamid) >1 || is_array($teamid)) {
					$teams = implode(",", $teamid);
				} else {
					$teams = $teamid;
				}
				$query_WHERE .= " AND (pt1.team_id IN (" . $teams . ")";
				$query_WHERE .= " OR pt2.team_id IN (" . $teams . "))";
			}
		}
		if($bUseFavteams) {
			$teams = modJoomleagueTickerHelper::getFavs($projectid);
			$query_WHERE .= " AND (pt1.team_id IN (" . $teams . ")";
			$query_WHERE .= " OR pt2.team_id IN (" . $teams . "))";
		}
		$query_END   = " ORDER BY matches.match_date $ordering, matches.match_number
								DESC LIMIT ".$numberofmatches;

		$database = JFactory::getDBO();
		$database->setQuery($query_SELECT.$query_FROM.$query_WHERE.$query_END);
		$result = $database->loadObjectList();
		return $result;
	}

	public static function getFavs($projectid) {
		$query = "SELECT fav_team FROM #__joomleague_project WHERE fav_team != ''";
		if ($projectid != '') {
			$projectids = explode( ',', $projectid );
			JArrayHelper::toInteger( $projectids );
			$query .= " AND id IN(".implode(', ', $projectids).")";
		}
		$database = JFactory::getDBO();
		$database->setQuery($query);
		$fav=$database->loadResultArray();
		return implode(',', $fav);
	}
	 

	function getCorrectDateFormat($format, $matchinfo, $offset=0)
	{
		$id = 1;
		//$now = gmmktime(gmdate("H"),gmdate("i")-$offset,gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y")); // adjust GMT by client's offset
		$now       = gmmktime()+$offset*60*60 ;

		//Second for the first time (46 * 60)
		$first_time = 2760;
		//Second for the second time (47 * 60)
		$second_time = 2820;
		//Pause in second (15*60)
		$pause = 900;

		if ($format == '')
		{
			$format = '%a., %d. %b. %Y';
		}
		$time = array();
		foreach ($matchinfo as $match)
		{
			$unix_date[$id] = strtotime($match->match_date);
			// is it future date or past date
			if($now > ($unix_date[$id] + $first_time + $second_time + $pause)) {
				$time1[$id] = JHTML::_('date', $match->match_date, $format);
				$time[$id] = $time1[$id];

			} elseif($now < ($unix_date[$id])) {
				$time[$id] = JHTML::_('date', $match->match_date, $format);
			}

			else {
				$difference[$id]  = $now - $unix_date[$id];
				if ( ($difference[$id] <= $first_time ) )
				{
					$difference1[$id] = floor($difference[$id]/60);$mi = "'";
				}

				elseif ( ($first_time + $pause) < $difference[$id])
				{
					$difference1[$id] = floor($difference[$id]/60) - 15;$mi = "'";
				}

				else {
					$difference1[$id] = "HT";
					$mi = "";
				}

				$time3[$id] = "<img src=\"modules/mod_joomleague_ticker/css/live_icon.jpg\" width=\"29\" border=\"0\" height=\"8\"><img src=\"modules/mod_joomleague_ticker/css/live.gif\" width=\"8\" border=\"0\" height=\"8\">&nbsp;";

				$time[$id] = $time3[$id].$difference1[$id].$mi;

			}
			$id++;
		}
		return $time;
	}


	function getSelectionDate($daysback, $dateTimeZone)
	{
		$timezone = new DateTimeZone($dateTimeZone);
		$utc = new DateTime();
		$offset = $timezone->getOffset($utc);
		$getdate = gmmktime(gmdate("H"),gmdate("i")-$offset,gmdate("s"),gmdate("m"),gmdate("d")-$daysback,gmdate("Y")); // adjust GMT by client's offset

		$selectiondate = gmdate("Y-m-d-H:i",$getdate);
		return $selectiondate;
	}
}

?>