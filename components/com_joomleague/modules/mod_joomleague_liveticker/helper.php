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
	
    function getListCommentary($list)
    {
    $db		=& JFactory::getDBO();    
    $matches = array();
    
    foreach ( $list as $row )    
    {
    //$matches[] = $row->match_id;    
        
    //$selmatchcomm = implode(',',$matches);
    $query = "SELECT *  
    FROM #__joomleague_match_commentary
    WHERE match_id = ".$row->match_id." 
    ORDER BY event_time DESC";
    $db->setQuery($query);
	$rows = $db->loadObjectList();
    
    if ( $rows )
    {
    $matches[$row->match_id] = $rows;    
    }
    
    }
    //echo 'getListCommentary rows -> <pre>'.print_r($rows,true).'</pre><br>';
    return $matches;    
    }
        
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
		
    
        
        $query = "SELECT jl.id,
        jl.name,
        jl.game_regular_time,
        jl.halftime,
        jl.fav_team,
        
        jco.alpha2,
        jm.id as match_id,
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
        
	}
}
