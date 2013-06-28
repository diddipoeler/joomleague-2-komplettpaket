<?php defined('_JEXEC') or die('Restricted access');

/* JoomLeague League Management and Prediction Game for Joomla!
* Copyright (C) 2007  Robert Moss
*
* Homepage: http://www.JoomLeague.net
* Support: htt://www.JoomLeague.net/forum/
*
* This file is part of JoomLeague.
*
* JoomLeague is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* Please note that the GPL states that any headers in files and
* Copyright notices as well as credits in headers, source files
* and output (screens, prints, etc.) can not be removed.
* You can extend them with your own credits, though...
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/copyleft/gpl.html.
*/

jimport('joomla.application.component.model');
jimport('joomla.utilities.array');
jimport('joomla.utilities.arrayhelper');

//require_once( JLG_PATH_SITE . DS . 'extensions' . DS . 'rankingalltime' . DS. 'helpers' . DS . 'rankingalltime.php' );

// require_once (JLG_PATH_SITE . DS . 'models' . DS . 'project.php');

$maxImportTime = 480;
error_reporting(0);
if ((int)ini_get('max_execution_time') < $maxImportTime) {
    @set_time_limit($maxImportTime);
}

class JoomleagueModelRankingAllTime extends JModel
{

    var $teams = array();
    var $_teams = array();
    var $_matches = array();
    var $alltimepoints = '';
    var $debug_info = false;
    var $projectid = 0;
    var $leagueid = 0;
    
    /**
     * ranking parameters
     * @var array
     */
    var $_params = null;
    /**
     * criteria for ranking order
     * @var array
     */
    var $_criteria = null;

    function __construct()
    {
        $this->alltimepoints = JRequest::getVar("points", 0);
        
        $menu = &JMenu::getInstance('site');
        $item = $menu->getActive();
        $params = &$menu->getParams($item->id);

        //$menu = &JSite::getMenu();
        $show_debug_info = JComponentHelper::getParams('com_joomleague')->get('show_debug_info',
            0);
        if ($show_debug_info) {
            $this->debug_info = true;
        } else {
            $this->debug_info = false;
        }
        
        if ( $item->id )
        {
        // diddipoeler
        // menueeintrag vorhanden    

$registry = new JRegistry();
$registry->loadArray($params);
//$newparams = $registry->toString('ini');
$newparams = $registry->toArray();
//echo "<b>menue newparams</b><pre>" . print_r($newparams, true) . "</pre>";  
foreach ($newparams['data'] as $key => $value ) {
            
            $this->_params[$key] = $value;
        }
        
        }
        else
        {
        $strXmlFile = JLG_PATH_SITE. DS.'settings'.DS.'default'.DS.'rankingalltime.xml';    
        // get the JForm object
        $form = &JForm::getInstance('jlattform', $strXmlFile);
        //echo "<b>menue form</b><pre>" . print_r($form, true) . "</pre>";
        foreach($form->getFieldset($fieldset->name) as $field)
        {
//         echo ' -> '. $field->name.'<br>';
//         echo ' -> '. $field->type.'<br>';
//         echo ' -> '. $field->input.'<br>';
        $this->_params[$field->name] = $field->value;
        
        }
        
        
        /*
        $registry = new JRegistry();
$registry->loadArray($strXmlFile);
//$newparams = $registry->toString('ini');
$newparams = $registry->toArray();

//echo "<b>menue newparams</b><pre>" . print_r($newparams, true) . "</pre>";  

foreach ($newparams['data'] as $key => $value ) {
            
            $this->_params[$key] = $value;
        }
        */
            
        }
        
        
        parent::__construct();

    }
    
    function getAllTeamsIndexedByPtid($project_ids)
    {
        $mainframe =& JFactory::getApplication();
        $result = $this->getAllTeams($project_ids);
        
        $count_teams = count($result);
    $mainframe->enqueueMessage(JText::_('Wir verarbeiten '.$count_teams.' Vereine !'),'');

        if (count($result)) {
            foreach ($result as $r) {
                $this->teams[$r->team_id] = $r;
                $this->teams[$r->team_id]->cnt_matches = 0;
                $this->teams[$r->team_id]->sum_points = $r->points_finally;
                $this->teams[$r->team_id]->neg_points = $r->neg_points_finally;

                $this->teams[$r->team_id]->cnt_won_home = 0;
                $this->teams[$r->team_id]->cnt_draw_home = 0;
                $this->teams[$r->team_id]->cnt_lost_home = 0;

                $this->teams[$r->team_id]->cnt_won = 0;
                $this->teams[$r->team_id]->cnt_draw = 0;
                $this->teams[$r->team_id]->cnt_lost = 0;

                $this->teams[$r->team_id]->sum_team1_result = 0;
                $this->teams[$r->team_id]->sum_team2_result = 0;
                $this->teams[$r->team_id]->sum_away_for = 0;
                $this->teams[$r->team_id]->diff_team_results = 0;

                $this->teams[$r->team_id]->round = 0;
                $this->teams[$r->team_id]->rank = 0;

            }
        }
       

        return $this->teams;
    }
    
    function getAllTeams($project_ids)
    {

        $query = ' SELECT	tl.id AS projectteamid,	tl.division_id, ' .
            ' tl.standard_playground,	tl.admin,	tl.start_points, ' .
            'tl.points_finally,tl.neg_points_finally,tl.matches_finally,tl.won_finally,tl.draws_finally,tl.lost_finally,tl.homegoals_finally,tl.guestgoals_finally,tl.diffgoals_finally,' .
            ' tl.is_in_score, tl.info,	tl.team_id,	tl.checked_out,	tl.checked_out_time, ' .
            ' tl.picture, tl.project_id, ' .
            ' t.id, t.name, t.short_name, t.middle_name,	t.notes, t.club_id, ' .
            ' u.username, u.email, ' .
            ' c.email as club_email, c.logo_small,c.logo_middle,c.logo_big,	c.country, c.website, ' .
            ' d.name AS division_name,	d.shortname AS division_shortname, d.parent_id AS parent_division_id, ' .
            ' plg.name AS playground_name,	plg.short_name AS playground_short_name, ' .
            ' CASE WHEN CHAR_LENGTH( p.alias ) THEN CONCAT_WS( \':\', p.id, p.alias ) ELSE p.id END AS project_slug, ' .
            ' CASE WHEN CHAR_LENGTH( t.alias ) THEN CONCAT_WS( \':\', t.id, t.alias ) ELSE t.id END AS team_slug, ' .
            ' CASE WHEN CHAR_LENGTH( d.alias ) THEN CONCAT_WS( \':\', d.id, d.alias ) ELSE d.id END AS division_slug, ' .
            ' CASE WHEN CHAR_LENGTH( c.alias ) THEN CONCAT_WS( \':\', c.id, c.alias ) ELSE c.id END AS club_slug ' .
            ' FROM #__joomleague_project_team tl ' .
            ' LEFT JOIN #__joomleague_team t ON tl.team_id = t.id ' .
            ' LEFT JOIN #__users u ON tl.admin = u.id ' .
            ' LEFT JOIN #__joomleague_club c ON t.club_id = c.id ' .
            ' LEFT JOIN #__joomleague_division d ON d.id = tl.division_id ' .
            ' LEFT JOIN #__joomleague_playground plg ON plg.id = tl.standard_playground ' .
            ' LEFT JOIN #__joomleague_project AS p ON p.id = tl.project_id ' .
            ' WHERE tl.project_id IN (' . $project_ids . ') group by tl.team_id';

        $this->_db->setQuery($query);
        $this->_teams = $this->_db->loadObjectList();

        return $this->_teams;

    }
    
    function getAllMatches($projects)
    {
        $mainframe =& JFactory::getApplication();
    $query = ' SELECT m.id, '
		. ' m.projectteam1_id, '
		. ' m.projectteam2_id, '
		. ' m.team1_result AS home_score, '
		. ' m.team2_result AS away_score, '
		. ' m.team1_bonus AS home_bonus, '
		. ' m.team2_bonus AS away_bonus, '
		. ' m.team1_legs AS l1, '
		. ' m.team2_legs AS l2, '
		. ' m.match_result_type AS match_result_type, '
		. ' m.alt_decision as decision, '
		. ' m.team1_result_decision AS home_score_decision, '
		. ' m.team2_result_decision AS away_score_decision, '
		. ' m.team1_result_ot AS home_score_ot, '
		. ' m.team2_result_ot AS away_score_ot, '
		. ' m.team1_result_so AS home_score_so, '
		. ' m.team2_result_so AS away_score_so, '
		. ' t1.id AS team1_id, '
		. ' t2.id AS team2_id, '
		. ' r.id as roundid, m.team_won, r.roundcode '
		. ' FROM #__joomleague_match m '
		. ' INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id = pt1.id '
		. ' INNER JOIN #__joomleague_team t1 ON pt1.team_id = t1.id ' 
		. ' INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id = pt2.id '
		. ' INNER JOIN #__joomleague_team t2 ON pt2.team_id = t2.id ' 
		. ' INNER JOIN #__joomleague_round AS r ON m.round_id = r.id '
		. ' WHERE ((m.team1_result IS NOT NULL AND m.team2_result IS NOT NULL) '
		. ' OR (m.alt_decision=1)) '
		. ' AND m.published = 1 '
    . ' AND r.published = 1 '
		. ' AND pt1.project_id IN ('.$projects.')'
		. ' AND (m.cancel IS NULL OR m.cancel = 0) '
		. ' AND m.projectteam1_id>0 AND m.projectteam2_id>0 ';
    
    $this->_db->setQuery($query);
    $res = $this->_db->loadObjectList();        
    $this->_matches = $res;
    
    $count_matches = count($res);
    $mainframe->enqueueMessage(JText::_('Wir verarbeiten '.$count_matches.' Spiele !'),'');
           
    return $res;    
    }
    
    function getAllTimeRanking()
    {
    $arr = explode(",",$this->alltimepoints);
    
    foreach ((array)$this->_matches as $match)
		{
		$resultType = $match->match_result_type;
		$decision = $match->decision;
		if ($decision == 0)
			{
				$home_score=$match->home_score;
				$away_score=$match->away_score;
				$leg1=$match->l1;
				$leg2=$match->l2;
			}
			else
			{
				$home_score=$match->home_score_decision;
				$away_score=$match->away_score_decision;
				$leg1=0;
				$leg2=0;
			}
            
		$homeId = $match->team1_id;
		$awayId = $match->team2_id;
    $home = &$this->teams[$homeId];
    $away = &$this->teams[$awayId];
    
    $home->cnt_matches++;
		$away->cnt_matches++;
    
    $win_points  = (isset($arr[0])) ? $arr[0] : 3;
			$draw_points = (isset($arr[1])) ? $arr[1] : 1;
			$loss_points = (isset($arr[2])) ? $arr[2] : 0;
    
    if ( $loss_points )
    {
    $shownegpoints = 1;
    }
    else
    {
    $shownegpoints = 0;
    }
    
    
			$home_ot = $match->home_score_ot;
			$away_ot = $match->away_score_ot;			
			$home_so = $match->home_score_so;
			$away_so = $match->away_score_so;
		
    if ($decision!=1) {
				if( $home_score > $away_score )
				{
					switch ($resultType) 
					{
					case 0: 
						$home->cnt_won++; 
						$home->cnt_won_home++; 
						
						$away->cnt_lost++; 
						$away->cnt_lost_away++;	
						break;
					case 1: 
						$home->cnt_wot++; 
						$home->cnt_wot_home++; 
						$home->cnt_won++; 
						$home->cnt_won_home++; 
						
						$away->cnt_lot++; 
						$away->cnt_lot_away++; 
						//When LOT, LOT=1 but No LOSS Count(Hockey)
						//$away->cnt_lost++; 
						//$away->cnt_lost_home++; 						
						break;
					case 2: 
						$home->cnt_wso++; 
						$home->cnt_wso_home++;
						$home->cnt_won++; 
						$home->cnt_won_home++; 
						
						$away->cnt_lso++; 
						$away->cnt_lso_away++; 
						$away->cnt_lot++; 
						$away->cnt_lot_away++; 		
						//When LSO ,LSO=1 and LOT=1 but No LOSS Count (Hockey)
						//$away->cnt_lost++; 
						//$away->cnt_lost_home++; 							
						break;
					}				
					
					$home->sum_points += $win_points; //home_score can't be null...						
					$away->sum_points += ( $decision == 0 || isset($away_score) ? $loss_points : 0);

					if ( $shownegpoints == 1 )
					{
						$home->neg_points += $loss_points;
						$away->neg_points += ( $decision == 0 || isset($away_score) ? $win_points : 0);
					}		
				}
				else if ( $home_score == $away_score )
				{
					switch ($resultType) 
					{
					case 0: 				
						$home->cnt_draw++;
						$home->cnt_draw_home++;

						$away->cnt_draw++;
						$away->cnt_draw_away++;
					break;	
					case 1: 	
						if ( $home_ot > $away_ot)
						{
							$home->cnt_won++;
							$home->cnt_won_home++;
							$home->cnt_wot++; 
							$home->cnt_wot_home++;							

							$away->cnt_lost++;
							$away->cnt_lost_away++;
							$away->cnt_lot++; 
							$away->cnt_lot_away++;							
						}
						if ( $home_ot < $away_ot)
						{
							$away->cnt_won++;
							$away->cnt_won_home++;
							$away->cnt_wot++; 
							$away->cnt_wot_home++;							

							$home->cnt_lost++;
							$home->cnt_lost_away++;
							$home->cnt_lot++; 
							$home->cnt_lot_away++;							
						}						
					break;						
					case 2: 	
						if ( $home_so > $away_so)
						{
							$home->cnt_won++;
							$home->cnt_won_home++;
							$home->cnt_wso++; 
							$home->cnt_wso_home++;							

							$away->cnt_lost++;
							$away->cnt_lost_away++;
							$away->cnt_lso++; 
							$away->cnt_lso_away++;							
						}
						if ( $home_so < $away_so)
						{
							$away->cnt_won++;
							$away->cnt_won_home++;
							$away->cnt_wso++; 
							$away->cnt_wso_home++;							

							$home->cnt_lost++;
							$home->cnt_lost_away++;
							$home->cnt_lso++; 
							$home->cnt_lso_away++;							
						}						
					break;					
					}
					$home->sum_points += ( $decision == 0 || isset($home_score) ? $draw_points : 0);
					$away->sum_points += ( $decision == 0 || isset($away_score) ? $draw_points : 0);

					if ($shownegpoints==1)
					{
						$home->neg_points += ( $decision == 0 || isset($home_score) ? ($win_points-$draw_points): 0); // bug fixed, timoline 250709
						$away->neg_points += ( $decision == 0 || isset($away_score) ? ($win_points-$draw_points) : 0);// ex. for soccer, your loss = 2 points not 1 point
					}
				}
				else if ( $home_score < $away_score )
				{
					switch ($resultType) {
					case 0:   
						$home->cnt_lost++; 
						$home->cnt_lost_home++;
						
						$away->cnt_won++; 
						$away->cnt_won_away++; 
						break;
					case 1:   
						$home->cnt_lot++; 
						$home->cnt_lot_home++; 
						//When LOT, LOT=1 but No LOSS Count(Hockey)
						//$home->cnt_lost++; 
						//$home->cnt_lost_home++; 
						
						$away->cnt_wot++; 
						$away->cnt_wot_away++; 
						$away->cnt_won++; 
						$away->cnt_won_away++; 						
						break;
					case 2:   
						$home->cnt_lso++; 
						$home->cnt_lso_home++; 
						$home->cnt_lot++; 
						$home->cnt_lot_home++; 
						//When LSO ,LSO=1 and LOT=1 but No LOSS Count (Hockey)
						//$home->cnt_lost++; 
						//$home->cnt_lost_home++; 	
						
						$away->cnt_wso++; 
						$away->cnt_wso_away++;
						$away->cnt_won++; 
						$away->cnt_won_away++; 						
						break;
					}					
									
					$home->sum_points += ( $decision == 0 || isset($home_score) ? $loss_points : 0);			
					$away->sum_points += $win_points;

					if ( $shownegpoints==1)
					{
						$home->neg_points += ( $decision == 0 || isset($home_score) ? $win_points : 0);
						$away->neg_points += $loss_points;
					}
				}
			} else {
				if ($shownegpoints==1)
				{
					$home->neg_points += $loss_points;
					$away->neg_points += $loss_points;
				}
				//Final Win/Loss Decision
				if($match->team_won==0) {
					$home->cnt_lost++;
					$away->cnt_lost++; 
				//record a won on the home team
				} else if($match->team_won==1) {
					$home->cnt_won++;
					$away->cnt_lost++;
					$home->sum_points += $win_points;
					$away->cnt_lost_home++;					
				//record a won on the away team
				} else if($match->team_won==2) {
					$away->cnt_won++;
					$home->cnt_lost++;
					$away->sum_points += $win_points;
					$home->cnt_lost_home++;
					//record a loss on both teams
				} else if($match->team_won==3) {
					$home->cnt_lost++;
					$away->cnt_lost++;
					$away->cnt_lost_home++;
					$home->cnt_lost_home++;
					//record a won on both teams
				} else if($match->team_won==4) {
					$home->cnt_won++;
					$away->cnt_won++;
					$home->sum_points += $win_points;
					$away->sum_points += $win_points;
						
				}
			}
			/*winpoints*/
			$home->winpoints=$win_points;
			
			/* bonus points */
			$home->sum_points += $match->home_bonus;
			$home->bonus_points += $match->home_bonus;

			$away->sum_points += $match->away_bonus;
			$away->bonus_points += $match->away_bonus;

			/* goals for/against/diff */
			$home->sum_team1_result += $home_score;
			$home->sum_team2_result += $away_score;
			$home->diff_team_results = $home->sum_team1_result - $home->sum_team2_result;
			$home->sum_team1_legs   += $leg1;
			$home->sum_team2_legs   += $leg2;
			$home->diff_team_legs    = $home->sum_team1_legs - $home->sum_team2_legs;

			$away->sum_team1_result += $away_score;
			$away->sum_team2_result += $home_score;
			$away->diff_team_results = $away->sum_team1_result - $away->sum_team2_result;
			$away->sum_team1_legs   += $leg2;
			$away->sum_team2_legs   += $leg1;
			$away->diff_team_legs    = $away->sum_team1_legs - $away->sum_team2_legs;

			$away->sum_away_for += $away_score;	
    
    
    }
    
    return $this->teams;
     
    }    
    
    function getColors($configcolors='')
	{
		$s=substr($configcolors,0,-1);

		$arr1=array();
		if(trim($s) != "")
		{
			$arr1=explode(";",$s);
		}

		$colors=array();

		$colors[0]["from"]="";
		$colors[0]["to"]="";
		$colors[0]["color"]="";
		$colors[0]["description"]="";

		for($i=0; $i < count($arr1); $i++)
		{
			$arr2=explode(",",$arr1[$i]);
			if(count($arr2) != 4)
			{
				break;
			}

			$colors[$i]["from"]=$arr2[0];
			$colors[$i]["to"]=$arr2[1];
			$colors[$i]["color"]=$arr2[2];
			$colors[$i]["description"]=$arr2[3];
		}
		return $colors;
	}
    
    function getAllProject()
    {
        $mainframe =& JFactory::getApplication();
        $league = JRequest::getInt("l", 0);

        if (!$league) {
            $projekt = JRequest::getInt("p", 0);
            $query = 'select league_id 
  from #__joomleague_project
  where id = ' . $projekt . ' order by name ';
            $this->_db->setQuery($query);
            $league = $this->_db->loadResult();

        }

        $query = 'select id 
  from #__joomleague_project
  where league_id = ' . $league . ' order by name ';
        $this->_db->setQuery($query);
        //$result = $this->_db->loadObjectList();
        $result = $this->_db->loadResultArray();
        $this->project_ids = implode(",", $result);
        $this->project_ids_array = $result;
        
        $count_project = count($result);
    $mainframe->enqueueMessage(JText::_('Wir verarbeiten '.$count_project.' Projekte/Saisons !'),'');
    
        return $result;

    }

    function getAllTimeParams()
    {
        return $this->_params;
    }
    
    function getCurrentRanking()
    {
        $newranking = array();

        /*
        echo 'models function getCurrentRanking this->teams<pre>';
        print_r($this->teams);
        echo '</pre>';
        */

        foreach ($this->teams as $key) {
            $new = new JLGRankingalltimeTeam(0);
            $new->cnt_matches = $key->cnt_matches;
            $new->sum_points = $key->sum_points;
            $new->neg_points = $key->neg_points;

            $new->cnt_won_home = $key->cnt_won_home;
            $new->cnt_draw_home = $key->cnt_draw_home;
            $new->cnt_lost_home = $key->cnt_lost_home;

            $new->cnt_won = $key->cnt_won;
            $new->cnt_draw = $key->cnt_draw;
            $new->cnt_lost = $key->cnt_lost;

            $new->sum_team1_result = $key->sum_team1_result;
            $new->sum_team2_result = $key->sum_team2_result;
            $new->sum_away_for = $key->sum_away_for;
            $new->diff_team_results = $key->diff_team_results;


            $new->_is_in_score = $key->is_in_score;
            $new->_teamid = $key->team_id;
            $new->_name = $key->name;

            $new->_ptid = $key->projectteamid;
            $new->_pid = $key->project_id;

            $newranking[0][$key->team_id] = $new;

        }

        //$this->_sortRanking($newranking[0]);
        $newranking[0] = $this->_sortRanking($newranking[0]);

        $oldpoints = 0;
        $rank = 0;
        foreach ($newranking[0] as $teamid => $row) {

            //    echo 'rank -> '.$rank.'oldpoints -> '.$oldpoints.' teampoints -> '.$row->sum_points.'<br>';
            if ($oldpoints == $row->sum_points) {
                $row->rank = $rank;
                $oldpoints = $row->sum_points;
            } else {
                $rank++;
                $row->rank = $rank;
                $oldpoints = $row->sum_points;

            }


        }

        if ($this->debug_info) {
            $this->dump_header("models function getCurrentRanking");
            $this->dump_variable("newranking", $newranking);
        }


        // return $this->teams;
        return $newranking;

    }
    
    
    
    
    function _sortRanking(&$ranking)
    {


        $order = JRequest::getVar('order', '');
        $order_dir = JRequest::getVar('dir', 'DESC');

        if (!$order) {
            $order_dir = 'DESC';
            $sortarray = array();
            

            foreach ($this->_getRankingCriteria() as $c) {

                if ($this->debug_info) {
                    $this->dump_header("models function _sortRanking");
                    $this->dump_variable("c", $c);
                }

                switch ($c) {
                    case '_cmpPoints':
                        $sortarray[sum_points] = SORT_DESC;
                        break;
                    case '_cmpPLAYED':
                        $sortarray[cnt_matches] = SORT_DESC;
                        break;
                    case '_cmpDiff':
                        $sortarray[diff_team_results] = SORT_DESC;
                        break;
                    case '_cmpFor':
                        $sortarray[sum_team1_result] = SORT_DESC;
                        break;
                    case '_cmpPlayedasc':
                        $sortarray[cnt_matches] = SORT_ASC;
                        break;
                }
                //uasort( $ranking, array("JoomleagueModelRankingalltime",$c ));

            }

            if ($this->debug_info) {
                $this->dump_header("models function _sortRanking");
                $this->dump_variable("sortarray", $sortarray);
            }

            foreach ($ranking as $row) {
                $arr2[$row->_teamid] = JArrayHelper::fromObject($row);
            }
            //$arr2 = $this->array_msort($arr2, array('sum_points'=>SORT_DESC,  'diff_team_results'=>SORT_DESC ) );
            //$sortarray2 = implode (",", $sortarray);
            //$arr2 = $this->array_msort($arr2, array($sortarray2) );
            $arr2 = $this->array_msort($arr2, $sortarray);

            if ($this->debug_info) {
                $this->dump_header("models function _sortRanking");
                $this->dump_variable("sortarray2", $sortarray2);
            }

            unset($ranking);

            foreach ($arr2 as $key => $row) {
                $ranking2[$key] = JArrayHelper::toObject($row, 'JLGRankingTeam');
                $ranking[$key] = JArrayHelper::toObject($row, 'JLGRankingTeam');
            }

            if ($this->debug_info) {

                $this->dump_header("models function _sortRanking");
                $this->dump_variable("arr2", $arr2);


                $this->dump_header("models function _sortRanking");
                $this->dump_variable("ranking2", $ranking2);

                $this->dump_header("models function _sortRanking");
                $this->dump_variable("ranking", $ranking);


            }


        } else //     if ( !$order_dir)
        {
            //     $order_dir = 'DESC';
            //     }
            switch ($order) {
                case 'played':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "playedCmp"));
                    break;
                case 'name':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "teamNameCmp"));
                    break;
                case 'rank':
                    break;
                case 'won':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "wonCmp"));
                    break;
                case 'draw':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "drawCmp"));
                    break;
                case 'loss':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "lossCmp"));
                    break;
                case 'winpct':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "winpctCmp"));
                    break;
                case 'quot':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "quotCmp"));
                    break;
                case 'goalsp':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "goalspCmp"));
                    break;
                case 'goalsfor':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "goalsforCmp"));
                    break;
                case 'goalsagainst':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "goalsagainstCmp"));
                    break;
                case 'legsdiff':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "legsdiffCmp"));
                    break;
                case 'legsratio':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "legsratioCmp"));
                    break;
                case 'diff':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "diffCmp"));
                    break;
                case 'points':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "pointsCmp"));
                    break;
                case 'start':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "startCmp"));
                    break;
                case 'bonus':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "bonusCmp"));
                    break;
                case 'negpoints':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "negpointsCmp"));
                    break;
                case 'pointsratio':
                    uasort($ranking, array("JoomleagueModelRankingalltime", "pointsratioCmp"));
                    break;

                default:
                    if (method_exists($this, $order . 'Cmp')) {
                        uasort($ranking, array($this, $order . 'Cmp'));
                    }
                    break;
            }

            if ($order_dir == 'DESC') {
                $ranking = array_reverse($ranking, true);
            }

        }

        return $ranking;
        
    }

    function playedCmp(&$a, &$b)
    {
        $res = $a->cnt_matches - $b->cnt_matches;
        return $res;
    }

    function teamNameCmp(&$a, &$b)
    {
        return strcasecmp($a->_name, $b->_name);
    }

    function wonCmp(&$a, &$b)
    {
        $res = $a->cnt_won - $b->cnt_won;
        return $res;
    }

    function drawCmp(&$a, &$b)
    {
        $res = ($a->cnt_draw - $b->cnt_draw);
        return $res;
    }

    function lossCmp(&$a, &$b)
    {
        $res = ($a->cnt_lost - $b->cnt_lost);
        return $res;
    }

    function winpctCmp(&$a, &$b)
    {
        $pct_a = $a->cnt_won / ($a->cnt_won + $a->cnt_lost + $a->cnt_draw);
        $pct_b = $b->cnt_won / ($b->cnt_won + $b->cnt_lost + $b->cnt_draw);
        $res = ($pct_a < $pct_b);
        return $res;
    }

    function quotCmp(&$a, &$b)
    {
        $pct_a = $a->cnt_won / ($a->cnt_won + $a->cnt_lost + $a->cnt_draw);
        $pct_b = $b->cnt_won / ($b->cnt_won + $b->cnt_lost + $b->cnt_draw);
        $res = ($pct_a < $pct_b);
        return $res;
    }

    function goalspCmp(&$a, &$b)
    {
        $res = ($a->sum_team1_result - $b->sum_team1_result);
        return $res;
    }

    function goalsforCmp(&$a, &$b)
    {
        $res = ($a->sum_team1_result - $b->sum_team1_result);
        return $res;
    }

    function goalsagainstCmp(&$a, &$b)
    {
        $res = ($a->sum_team2_result - $b->sum_team2_result);
        return $res;
    }

    function legsdiffCmp(&$a, &$b)
    {
        $res = ($a->diff_team_legs - $b->diff_team_legs);
        return $res;
    }

    function legsratioCmp(&$a, &$b)
    {
        $res = ($a->legsRatio - $b->legsRatio);
        return $res;
    }

    function diffCmp(&$a, &$b)
    {
        $res = ($a->diff_team_results - $b->diff_team_results);
        return $res;
    }

    function pointsCmp(&$a, &$b)
    {
        $res = ($a->getPoints() - $b->getPoints());
        return $res;
    }

    function startCmp(&$a, &$b)
    {
        $res = ($a->team->start_points * $b->team->start_points);
        return $res;
    }

    function bonusCmp(&$a, &$b)
    {
        $res = ($a->bonus_points - $b->bonus_points);
        return $res;
    }

    function negpointsCmp(&$a, &$b)
    {
        $res = ($a->neg_points - $b->neg_points);
        return $res;
    }

    function pointsratioCmp(&$a, &$b)
    {
        $res = ($a->pointsRatio - $b->pointsRatio);
        return $res;
    }

    function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $params = array();
        foreach ($cols as $col => $order) {
            $params[] = &$colarr[$col];
            $params = array_merge($params, (array )$order);
        }
        call_user_func_array('array_multisort', $params);
        $ret = array();
        $keys = array();
        $first = true;
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                if ($first) {
                    $keys[$k] = substr($k, 1);
                }
                $k = $keys[$k];
                if (!isset($ret[$k]))
                    $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
            $first = false;
        }
        return $ret;

    }

function _getRankingCriteria()
    {

        if (empty($this->_criteria)) {
            // get the values from ranking template setting
            $values = explode(',', $this->_params['ranking_order']);
            $crit = array();
            foreach ($values as $v) {
                $v = ucfirst(str_replace("jl_", "", strtolower(trim($v))));
                if (method_exists($this, '_cmp' . $v)) {
                    $crit[] = '_cmp' . $v;
                } else {
                    JError::raiseWarning(0, JText::_('JL_RANKING_NOT_VALID_CRITERIA') . ': ' . $v);
                }
            }
            // set a default criteria if empty
            if (!count($crit)) {
                $crit[] = '_cmpPoints';
            }
            $this->_criteria = $crit;
        }

        if ($this->debug_info) {
            $this->dump_header("models function _getRankingCriteria");
            $this->dump_variable("this->_criteria", $this->_criteria);
        }

        return $this->_criteria;
    }
    
    
/*****************************************************************************
    *
    * Compare functions (callbacks for uasort)
    *
    * You can add more criteria by just adding more _cmpXxxx functions, with Xxxx
    * being the name of your criteria to be set in ranking template setting
    *
    *****************************************************************************/

    /**
     * alphanumerical comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpAlpha($a, $b)
    {
        $res = strcasecmp($a->getName(), $b->getName());
        return $res;
    }

    /**
     * Point comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpPoints($a, $b)
    {
        $res = -($a->getPoints() - $b->getPoints());
        return (int)$res;
    }

    /**
     * Bonus points comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpBonus($a, $b)
    {
        $res = -($a->bonus_points - $b->bonus_points);
        return $res;
    }

    /**
     * Score difference comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpDiff($a, $b)
    {
        $res = -($a->diff_team_results - $b->diff_team_results);
        return $res;
    }

    /**
     * Score for comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpFor($a, $b)
    {
        $res = -($a->sum_team1_result - $b->sum_team1_result);
        return $res;
    }

    /**
     * Score against comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpAgainst($a, $b)
    {
        $res = ($a->sum_team2_result - $b->sum_team2_result);
        return $res;
    }

    /**
     * Scoring average comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpScoreAvg($a, $b)
    {
        $res = -($a->scoreAvg() - $b->scoreAvg());
        return $res;
    }

    /**
     * Scoring percentage comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpScorePct($a, $b)
    {
        $res = -($a->scorePct() - $b->scorePct());
        return $res;
    }


    /**
     * Winning percentage comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpWinpct($a, $b)
    {
        $res = -($a->winPct() - $b->winPct());
        if ($res != 0)
            $res = ($res >= 0 ? 1 : -1);
        return $res;
    }

    /**
     * Gameback comparison (US sports)
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpGb($a, $b)
    {
        $res = -(($a->cnt_won - $b->cnt_won) + ($b->cnt_lost - $a->cnt_lost));
        return $res;
    }

    /**
     * Score away comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpAwayfor($a, $b)
    {
        $res = -($a->sum_away_for - $b->sum_away_for);
        return $res;
    }

    /**
     * Head to Head points comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpH2h($a, $b)
    {
        $teams = $this->_geth2h();
        // we do not include start points in h2h comparison
        $res = -($teams[$a->_ptid]->getPoints(false) - $teams[$b->_ptid]->getPoints(false));
        return $res;
    }

    /**
     * Head to Head score difference comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpH2h_diff($a, $b)
    {
        $teams = $this->_geth2h();
        return $this->_cmpDiff($teams[$a->_ptid], $teams[$b->_ptid]);
    }

    /**
     * Head to Head score for comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpH2h_for($a, $b)
    {
        $teams = $this->_geth2h();
        return $this->_cmpFor($teams[$a->_ptid], $teams[$b->_ptid]);
    }

    /**
     * Head to Head scored away comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpH2h_away($a, $b)
    {
        $teams = $this->_geth2h();
        return $this->_cmpAwayfor($teams[$a->_ptid], $teams[$b->_ptid]);
    }

    /**
     * Legs diff comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpLegs_diff($a, $b)
    {
        $res = -($a->diff_team_legs - $b->diff_team_legs);
        return $res;
    }

    /**
     * Legs ratio comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpLegs_ratio($a, $b)
    {
        $res = -($a->legsRatio() - $b->legsRatio());
        if ($res != 0)
            $res = ($res >= 0 ? 1 : -1);
        return $res;
    }

    /**
     * Legs wins comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpLegs_win($a, $b)
    {
        $res = -($a->sum_team1_legs - $b->sum_team1_legs);
        return $res;
    }

    /**
     * Total wins comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpWins($a, $b)
    {
        $res = -($a->cnt_won - $b->cnt_won);
        return $res;
    }

    /**
     * Games played comparison, more games played, higher in rank
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpPlayed($a, $b)
    {
        $res = -($a->cnt_matches - $b->cnt_matches);
        return $res;
    }

    /**
     * Games played ASC comparison, less games played, higher in rank
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpPlayedasc($a, $b)
    {
        $res = -($this->_cmpPlayed($a, $b));
        return $res;
    }
    /**
     * Points ratio comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpPoints_ratio($a, $b)
    {
        $res = -($a->pointsRatio() - $b->pointsRatio());
        if ($res != 0)
            $res = ($res >= 0 ? 1 : -1);
        return $res;
    }
    /**
     * OT_wins comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpWOT($a, $b)
    {
        $res = -($a->cnt_wot - $b->cnt_wot);
        return $res;
    }

    /**
     * SO_wins comparison
     * @param JLGRankingTeam a
     * @param JLGRankingTeam b
     * @return int
     */
    function _cmpWSO($a, $b)
    {
        $res = -($a->cnt_wso - $b->cnt_wso);
        return $res;
    }
    
    
}

/**
 * Ranking team class
 * Support class for ranking helper
 */
class JLGRankingalltimeTeam
{

    // new for use_finally
    var $_use_finally = 0;
    var $_points_finally = 0;
    var $_neg_points_finally = 0;
    var $_matches_finally = 0;
    var $_won_finally = 0;
    var $_draws_finally = 0;
    var $_lost_finally = 0;
    var $_homegoals_finally = 0;
    var $_guestgoals_finally = 0;
    var $_diffgoals_finally = 0;

    // new for is_in_score
    var $_is_in_score = 0;

    /**
     * project team id
     * @var int
     */
    var $_ptid = 0;
    /**
     * team id
     * @var int
     */
    var $_teamid = 0;
    /**
     * division id
     * @var int
     */
    var $_divisionid = 0;
    /**
     * start point / penalty
     * @var int
     */
    var $_startpoints = 0;
    /**
     * team name
     * @var string
     */
    var $_name = null;

    var $cnt_matches = 0;
    var $cnt_won = 0;
    var $cnt_draw = 0;
    var $cnt_lost = 0;
    var $cnt_won_home = 0;
    var $cnt_draw_home = 0;
    var $cnt_lost_home = 0;
    var $sum_points = 0;
    var $neg_points = 0;
    var $bonus_points = 0;
    var $sum_team1_result = 0;
    var $sum_team2_result = 0;
    var $sum_away_for = 0;
    var $sum_team1_legs = 0;
    var $sum_team2_legs = 0;
    var $diff_team_results = 0;
    var $diff_team_legs = 0;
    var $round = 0;
    var $rank = 0;

    /**
     * contructor requires ptid
     * @param int $ptid
     */
    function JLGRankingTeam($ptid)
    {
        $this->setPtid($ptid);
    }

    // new for is_in_score
    function setis_in_score($val)
    {
        $this->_is_in_score = (int)$val;
    }

    // new for use finally
    function setuse_finally($val)
    {
        $this->_use_finally = (int)$val;
    }
    function setpoints_finally($val)
    {
        $this->_points_finally = (int)$val;
    }
    function setneg_points_finally($val)
    {
        $this->_neg_points_finally = (int)$val;
    }
    function setmatches_finally($val)
    {
        $this->_matches_finally = (int)$val;
    }
    function setwon_finally($val)
    {
        $this->_won_finally = (int)$val;
    }
    function setdraws_finally($val)
    {
        $this->_draws_finally = (int)$val;
    }
    function setlost_finally($val)
    {
        $this->_lost_finally = (int)$val;
    }
    function sethomegoals_finally($val)
    {
        $this->_homegoals_finally = (int)$val;
    }
    function setguestgoals_finally($val)
    {
        $this->_guestgoals_finally = (int)$val;
    }
    function setdiffgoals_finally($val)
    {
        $this->_diffgoals_finally = (int)$val;
    }

    /**
     * set project team id
     * @param int ptid
     */
    function setPtid($ptid)
    {
        $this->_ptid = (int)$ptid;
    }

    /**
     * set team id
     * @param int id
     */
    function setTeamid($id)
    {
        $this->_teamid = (int)$id;
    }

    /**
     * returns project team id
     * @return int id
     */
    function getPtid()
    {
        return $this->_ptid;
    }

    /**
     * returns team id
     * @return int id
     */
    function getTeamid()
    {
        return $this->_teamid;
    }

    /**
     * set team division id
     * @param int val
     */
    function setDivisionid($val)
    {
        $this->_divisionid = (int)$val;
    }

    /**
     * return team division id
     * @return int id
     */
    function getDivisionid()
    {
        return $this->_divisionid;
    }

    /**
     * set team start points
     * @param int val
     */
    function setStartpoints($val)
    {
        $this->_startpoints = $val;
    }

    /**
     * set team neg points
     * @param int val
     */
    function setNegpoints($val)
    {
        $this->neg_points = $val;
    }

    /**
     * set team name
     * @param string val
     */
    function setName($val)
    {
        $this->_name = $val;
    }

    /**
     * return winning percentage
     *
     * @return float
     */
    function winPct()
    {
        if ($this->cnt_won + $this->cnt_lost + $this->cnt_draw == 0) {
            return 0;
        } else {
            return ($this->cnt_won / ($this->cnt_won + $this->cnt_lost + $this->cnt_draw)) *
                100;
        }
    }


    /**
     * return scoring average
     *
     * @return float
     */
    function goalAvg()
    {
        if ($this->sum_team2_result == 0) {
            return $this->sum_team1_result / 1;
        } else {
            return $this->sum_team1_result / $this->sum_team2_result;
        }
    }

    /**
     * return scoring percentage
     *
     * @return float
     */
    function goalPct()
    {
        $result = $this->goalAvg() * 100;
        return $result;
    }


    /**
     * return leg ratio
     *
     * @return float
     */
    function legsRatio()
    {
        if ($this->sum_team2_legs == 0) {
            return $this->sum_team1_legs / 1;
        } else {
            return $this->sum_team1_legs / $this->sum_team2_legs;
        }
    }

    /**
     * return points ratio
     *
     * @return float
     */
    function pointsRatio()
    {
        if ($this->neg_points == 0) {
            // we do not include start points
            return $this->getPoints(false) / 1;
        } else {
            // we do not include start points
            return $this->getPoints(false) / $this->neg_points;
        }
    }

    /**
     * return points quot
     *
     * @return float
     */
    function pointsQuot()
    {
        if ($this->cnt_matches == 0) {
            // we do not include start points
            return $this->getPoints(false) / 1;
        } else {
            // we do not include start points
            return $this->getPoints(false) / $this->cnt_matches;
        }
    }


    function getName()
    {
        return $this->_name;
    }

    /**
     * return points total
     *
     * @param boolean include start points, default true
     */
    function getPoints($include_start = true)
    {
        if ($include_start) {
            return $this->sum_points + $this->_startpoints;
        } else {
            return $this->sum_points;
        }
    }


}


?>