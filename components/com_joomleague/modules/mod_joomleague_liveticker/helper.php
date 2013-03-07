<?php
/**
 * @package
 * @author
 * @link
 * @version
 * @copyright
 * @license
 */

defined('_JEXEC') or die('Restricted access');

class modTurtushoutHelper
{
	function getList(&$params, $limit)
    {
        // aktuelles datum
        $akt_datum = date("Y-m-d",time());
        $von = $akt_datum.' 00:00:00';
        $bis = $akt_datum.' 23:59:59';
        
//        echo $akt_datum.'<br>';
//        echo $von.'<br>';
//        echo $bis.'<br>';
		$db		=& JFactory::getDBO();
//		$query = 'SELECT * FROM #__turtushout ORDER BY id DESC ';
		
    /*
    $query = "SELECT jl.id,
        jl.name,
        jl.game_regular_time,
        jl.halftime,
        jl.fav_team,
        jco.countries_iso_code_2,
        jm.match_date,
        jm.matchpart1,
        jm.matchpart2,
        jm.matchpart1_result,
        jm.matchpart2_result,
        jt1.name as heim,
        jt1.short_name as heim_short_name,
        jt1.middle_name as heim_middle_name,
        jt2.name as gast,
        jt2.short_name as gast_short_name,
        jt2.middle_name as gast_middle_name,
        jc1.logo_big as wappenheim,
        jc2.logo_big as wappengast
        
        FROM #__joomleague as jl
        
        inner join #__joomleague_matches as jm
        on jm.project_id = jl.id
        
        inner join #__joomleague_teams as jt1
        on jt1.id = jm.matchpart1
        
        inner join #__joomleague_clubs as jc1
        on jc1.id = jt1.club_id
        
        inner join #__joomleague_teams as jt2
        on jt2.id = jm.matchpart2
        
        inner join #__joomleague_clubs as jc2
        on jc2.id = jt2.club_id
        
        inner join #__joomleague_leagues as jle
        on jle.id = jl.league_id
        
        left outer join #__joomleague_countries as jco
        on jco.countries_id = jle.country
        where jm.match_date >= '$von' and jm.match_date <= '$bis'

        ";
        */
        
        $query = "SELECT jl.id,
        jl.name,
        jl.game_regular_time,
        jl.halftime,
        jl.fav_team,
        
        jco.alpha2,
        
        jm.match_date,
        jm.projectteam1_id,
        jm.projectteam2_id,
        jm.team1_result,
        jm.team2_result,
        
        jt1.name as heim,
        jt1.short_name as heim_short_name,
        jt1.middle_name as heim_middle_name,
        
        jt2.name as gast,
        jt2.short_name as gast_short_name,
        jt2.middle_name as gast_middle_name,
        
        jc1.logo_big as wappenheim,
        jc2.logo_big as wappengast
        
        FROM #__joomleague_project as jl
        
        inner join #__joomleague_round as jr
        on jr.project_id = jl.id
        
        inner join #__joomleague_match as jm
        on jm.round_id = jr.id
        
        inner join #__joomleague_project_team as jpt1
        on jpt1.id = jm.projectteam1_id  
        inner join #__joomleague_team as jt1
        on jt1.id = jpt1.team_id
        inner join #__joomleague_club as jc1
        on jc1.id = jt1.club_id
        
        inner join #__joomleague_project_team as jpt2
        on jpt2.id = jm.projectteam2_id 
        inner join #__joomleague_team as jt2
        on jt2.id = jpt2.team_id
        inner join #__joomleague_club as jc2
        on jc2.id = jt2.club_id
        
        inner join #__joomleague_league as jle
        on jle.id = jl.league_id
        
        left outer join #__joomleague_countries as jco
        on jco.alpha3 = jle.country
        where jm.match_date >= '$von' and jm.match_date <= '$bis'

        ";
        
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
// 		echo 'query -> <pre>'.print_r($query,true).'</pre><br>';
// 		echo 'rows -> <pre>'.print_r($rows,true).'</pre><br>';
		
		if ($db->_errorMsg)
        {
//			 modTurtushoutHelper::install();
		}
		return $rows;
	}

	function shout($display_username, $display_title, $add_timeout)
    {
        /*
		$title  = $display_title ? mysql_real_escape_string(strip_tags(JRequest::getString('title'))) : "";
		$text   = mysql_real_escape_string(strip_tags(JRequest::getString('text')));
		if ($text)
        {
			$db		=& JFactory::getDBO();
			$user	=& JFactory::getUser();
			$userId = (int) $user->get('id');


			$datenow = new JDate();
			$created = $datenow->toMySQL();
			$created_by_alias = $userId ? $user->get('name'): ($display_username ? JRequest::getString('created_by_alias') : "");

			$userIP =  isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: "";

			$query = "SELECT created FROM  #__turtushout WHERE ";
			if ($userId)
            {
				$query .= "created_by=$userId ";
			}
            else
            {
				$query .= " ip='$userIP' ";
 			}
 			$datenow->setOffset(-$add_timeout / 60 / 60);
 			$query .= " AND '" . $datenow->toMySQL(true) . "' <= created ";
 			$db->setQuery($query);
 			$last_time = $db->loadResult();

 			if ($last_time)
             {
				return "Wait before submiting new message..";
 				exit;
 			}

			$query  = "INSERT INTO  #__turtushout (`title`, `text`, `created`, `created_by`, `created_by_alias`, `ip`) VALUES (";
			$query .= "'$title', '$text', '$created', $userId, '$created_by_alias', '$userIP')";

			$db->setQuery($query);
			$db->query();
			return $db->_errorMsg;
		}
        else
        {
			return "Set some text!";
		}
		*/
	}


	function delete()
    {
        /*
		$sid  = JRequest::getInt('sid');
		if ($sid)
        {
			$db		=& JFactory::getDBO();
			$query  = "DELETE FROM   #__turtushout WHERE id=$sid";
			$db->setQuery($query);
			$db->query();
			return $db->_errorMsg;
		}
        else
        {
			return "Select message!";
		}
		*/
	}

	function install()
    {
        /*
		$db		=& JFactory::getDBO();
		$query = "CREATE TABLE IF NOT EXISTS #__turtushout (
			`id` int(16) NOT NULL auto_increment,
			`title` varchar(255) default NULL,
			`text` text,
			`created` datetime default NULL,
			`created_by` int(11) default NULL,
			`created_by_alias` varchar(255) default NULL,
			`ip` varchar(255) default NULL,
			PRIMARY KEY  (`id`)
		) ; ";
		$db->setQuery($query);
		$db->query();
		$query = "INSERT INTO `#__turtushout` (`id`, `title`, `text`, `created`, `created_by`, `created_by_alias`) VALUES
			(1, 'Thank you for using Turtushoutbox!', 'You could download this and other modules on site <a href=http://www.turtus.org.ua>http://www.turtus.org.ua</a>', '2008-03-25 20:45:43', 0, 'Ksu');";

		$db->setQuery($query);
		$db->query();
		*/
	}
}
