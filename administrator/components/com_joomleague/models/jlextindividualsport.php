<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
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
require_once(JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component Match Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueModeljlextindividualsport extends JoomleagueModelItem
{

	/**
	 * Method to load content matchday data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query=' SELECT	m.*,
							CASE m.time_present 
							when NULL then NULL
							else DATE_FORMAT(m.time_present, "%H:%i")
							END AS time_present,
							t1.name AS hometeam, t1.id AS t1id, 
							t2.name as awayteam, t2.id AS t2id,
							pt1.project_id,
							m.extended as matchextended
						FROM #__joomleague_match_single AS m
						INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id
						INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
						INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id
						INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
						WHERE m.id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the match data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$match=new stdClass();
			$match->id						= 0;
			$match->round_id				= null;
			$match->match_number			= null;
			$match->projectteam1_id			= null;
			$match->projectteam2_id			= null;
			$match->playground_id			= null;
			
			$match->match_id			= null;
			$match->teamplayer1_id			= null;
			$match->teamplayer2_id			= null;
			
			
			$match->match_date				= null;
			$match->time_present			= null;
			$match->team1_result			= null;
			$match->team2_result			= null;
			$match->team1_bonus				= null;
			$match->team2_bonus				= null;
			$match->team1_legs				= null;
			$match->team2_legs				= null;
			$match->team1_result_split		= null;
			$match->team2_result_split		= null;
			$match->match_result_type		= 0;
			$match->team1_result_ot			= null;
			$match->team2_result_ot			= null;
			$match->team1_result_so			= null;
			$match->team2_result_so			= null;
			$match->alt_decision			= null;
			$match->decision_info			= null;
			$match->team1_result_decision	= null;
			$match->team2_result_decision	= null;
			$match->match_state				= null;
			$match->count_result			= null;
			$match->crowd					= null;
			$match->summary					= null;
			$match->show_report				= null;
			$match->preview					= null;
			$match->match_result_detail		= null;
			$match->new_matchid				= null;
			$match->extended				= null;
			$match->published				= 0;
			$match->checked_out				= 0;
			$match->checked_out_time		= 0;
			$match->old_match_id			= 0;
			$match->new_match_id			= 0;
			$match->team_won 				= 0;
			$match->modified				= null;
			$match->modified_by				= null;
			$this->_data				= $match;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to remove matches and match_persons of only one project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */

	function deleteOne($project_id)
	{
		if ($project_id > 0)
		{
			$query='SELECT DISTINCT
						  #__joomleague_match_1.id AS match_id
						FROM
						  #__joomleague_project_team
						  INNER JOIN #__joomleague_match_single #__joomleague_match_1 ON #__joomleague_project_team.id=#__joomleague_match_1.projectteam2_id
						  INNER JOIN #__joomleague_project_team #__joomleague_project_team_1 ON #__joomleague_project_team_1.id=#__joomleague_match_1.projectteam1_id
						WHERE
						  #__joomleague_project_team.project_id='.(int) $project_id;
			$this->_db->setQuery($query);
			if ($results=$this->_db->loadAssocList())
			{
				foreach($results as $result)
				{
					$query='DELETE FROM #__joomleague_match_statistic WHERE match_id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					$query='DELETE FROM #__joomleague_match_staff_statistic WHERE match_id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					$query='DELETE FROM #__joomleague_match_staff WHERE match_id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					$query='DELETE FROM #__joomleague_match_event WHERE match_id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					$query='DELETE FROM #__joomleague_match_referee WHERE match_id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					$query='DELETE FROM #__joomleague_match_player WHERE match_id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
					$query='DELETE FROM #__joomleague_match_single WHERE id='.(int) $result['match_id'];
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Method to remove a matchday
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete($cid=array())
	{
		$result=false;
		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids=implode(',',$cid);
			$query="DELETE FROM #__joomleague_match_single WHERE id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			/*
			$query="DELETE FROM #__joomleague_match_statistic WHERE match_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$query="DELETE FROM #__joomleague_match_staff_statistic WHERE match_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$query="DELETE FROM #__joomleague_match_staff WHERE match_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$query="DELETE FROM #__joomleague_match_event WHERE match_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$query="DELETE FROM #__joomleague_match_referee WHERE match_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$query="DELETE FROM #__joomleague_match_player WHERE match_id IN ($cids)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			*/
		}
		return true;
	}

	function save_team_to($cid,$post)
	{
		$result=true;
		$t1_r=$post['team1_result'.$cid];
		$t2_r=$post['team2_result'.$cid];
		$t1_rso=$post['team1_result_so'.$cid];
		$t2_rso=$post['team2_result_so'.$cid];
		//TODO leg, team_won, ...
		if($t1_r == $t2_r)
		{
			if($t1_rso == $t2_rso)
			{
				$projectteam_id=0;
			}
			elseif($t1_rso > $t2_rso)
			{
				$projectteam_id=$post['projectteam1_id'.$cid];
			}
			elseif($t1_rso < $t2_rso)
			{
				$projectteam_id=$post['projectteam2_id'.$cid];
			}
		}
		else if ($t1_r > $t2_r)
		{
			$projectteam_id=$post['projectteam1_id'.$cid];
		}
		else
		{
			$projectteam_id=$post['projectteam2_id'.$cid];
		}
		$query = ' UPDATE #__joomleague_treeto_node AS ttn ';
		$query .=	' SET ttn.team_id = ' . $projectteam_id;
		$query .=	' , ttn.is_lock = ' . 1;
		$query .=	' WHERE ttn.id=(SELECT ttm.node_id FROM #__joomleague_treeto_match AS ttm ';
		$query .=	' WHERE ttm.match_id = ' . $cid . ')';
		$query .=	' AND (ttn.bestof = 1) '; //only for Bestof=1, if B>1 :: setNodeWinner
	//	$query .=	' AND (tt.global_fake = 0) '; //
		$query .=	';';

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result=false;
		}

		return $result;
	}

  /**
* obj2Array#
* converts simpleXml object to array
*
* Variables: $o['obj']: simplexml object
*
* @return
*
*/
function obj2Array($obj)
{
	$arr=(array)$obj;
	if (empty($arr))
	{
		$arr='';
	}
	else
	{
		foreach ($arr as $key=>$value)
		{
			if (!is_scalar($value))
			{
				$arr[$key]=$this->obj2Array($value);
			}
		}
	}
	return $arr;
}

	// function save_array changed for date per match and period results
	// Gucky 2007/05/25
	function save_array($cid=null,$post=null,$zusatz=false,$project_id)
	{
		$option='com_joomleague';
		$mainframe	=& JFactory::getApplication();
		$datatable[0]='#__joomleague_match_single';
		$fields = $this->_db->getTableFields($datatable);
		
    $sporttype = $mainframe->getUserState( $option . 'sporttype' );
    $defaultvalues = array();
    $game_parts = $mainframe->getUserState( $option . 'game_parts' );
    //$game_parts = $game_parts - 1;
     
//     $mainframe->enqueueMessage(JText::_('save_array-resultsplit: '.print_r($post,true) ),'');
//     $mainframe->enqueueMessage(JText::_('save_array-resultsplit team 1: '.print_r($post['team1_result_split'.$cid],true) ),'Notice');
//     $mainframe->enqueueMessage(JText::_('save_array-resultsplit team 2: '.print_r($post['team2_result_split'.$cid],true) ),'Notice');

// in abhängigkeit von der sportart wird das ergebnis gespeichert    
switch(strtolower($sporttype))
{
case 'kegeln':

$xmldir = JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'extensions'.DS.'jlextindividualsport'.DS.'admin'.DS.'assets'.DS.'extended';
$file = 'kegelnresults.xml';
$defaultconfig=array ();
$out=simplexml_load_file($xmldir.DS.$file,'SimpleXMLElement',LIBXML_NOCDATA);
$temp='';
$arr = $this->obj2Array($out);
$outName=JText::_($out->name[0]);

if (isset($arr["params"]["param"]))
					{
						foreach ($arr["params"]["param"] as $param)
						{
							//$temp .= $param["@attributes"]["name"]."=".$param["@attributes"]["default"]."\n";
							$defaultconfig[$param["@attributes"]["name"]]=$param["@attributes"]["default"];
						}
					}

					
$post['team1_result'.$cid] = 0;
$post['team2_result'.$cid] = 0;


// alles auf null setzen
$defaultconfig['JL_EXT_KEGELN_DATA_HOME_VOLLE'] = 0;
$defaultconfig['JL_EXT_KEGELN_DATA_AWAY_VOLLE'] = 0;
$defaultconfig['JL_EXT_KEGELN_DATA_HOME_ABR'] = 0;
$defaultconfig['JL_EXT_KEGELN_DATA_AWAY_ABR'] = 0;
$defaultconfig['JL_EXT_KEGELN_DATA_HOME_FW'] = 0;
$defaultconfig['JL_EXT_KEGELN_DATA_AWAY_FW'] = 0;


for ($a=0; $a < 2; $a++)
{
$post['team1_result'.$cid] = $post['team1_result'.$cid] + $post['team1_result_split'.$cid][$a];
$post['team2_result'.$cid] = $post['team2_result'.$cid] + $post['team2_result_split'.$cid][$a];
}


for ($a=0; $a < $game_parts; $a++)
{

switch ($a)
{
case 0:
$defaultconfig['JL_EXT_KEGELN_DATA_HOME_VOLLE'] = $post['team1_result_split'.$cid][$a];
$defaultconfig['JL_EXT_KEGELN_DATA_AWAY_VOLLE'] = $post['team2_result_split'.$cid][$a];
break;
case 1:
$defaultconfig['JL_EXT_KEGELN_DATA_HOME_ABR'] = $post['team1_result_split'.$cid][$a];
$defaultconfig['JL_EXT_KEGELN_DATA_AWAY_ABR'] = $post['team2_result_split'.$cid][$a];
break;
case 2:
$defaultconfig['JL_EXT_KEGELN_DATA_HOME_FW'] = $post['team1_result_split'.$cid][$a];
$defaultconfig['JL_EXT_KEGELN_DATA_AWAY_FW'] = $post['team2_result_split'.$cid][$a];
break;
}

}

foreach ( $defaultconfig as $key => $value )
{
$defaultvalues[] = $key . '=' . $value;
//$temp .= $key."=".$value."\n";
}
$temp = implode( "\n", $defaultvalues );

$query="UPDATE #__joomleague_match_single SET `extended`='".$temp."' WHERE id=".$cid;
$this->_db->setQuery($query);
if (!$this->_db->query())
		{
$mainframe->enqueueMessage(JText::_('save_array - defaultconfig: '.print_r($this->_db->getErrorMsg(),true) ),'Error');			
		}


break;

case 'tennis':
$xmldir = JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'extensions'.DS.'jlextindividualsport'.DS.'admin'.DS.'assets'.DS.'extended';
$file = 'tennisresults.xml';
$defaultconfig=array ();
$out=simplexml_load_file($xmldir.DS.$file,'SimpleXMLElement',LIBXML_NOCDATA);
$temp='';
$arr = $this->obj2Array($out);
$outName=JText::_($out->name[0]);

if (isset($arr["params"]["param"]))
					{
						foreach ($arr["params"]["param"] as $param)
						{
							$temp .= $param["@attributes"]["name"]."=".$param["@attributes"]["default"]."\n";
							$defaultconfig[$param["@attributes"]["name"]]=$param["@attributes"]["default"];
						}
					}
					
//$mainframe->enqueueMessage(JText::_('save_array - temp: '.print_r($temp,true) ),'');					
//$mainframe->enqueueMessage(JText::_('save_array - defaultconfig: '.print_r($defaultconfig,true) ),'');

// alles auf null setzen
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_MATCHES'] = 0;
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_MATCHES'] = 0;
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_SETS'] = 0;
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_SETS'] = 0;
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_POINTS'] = 0;
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_POINTS'] = 0;

for ($a=0; $a < $game_parts; $a++)
{
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_MATCHES'] = $defaultconfig['JL_EXT_TENNIS_DATA_HOME_MATCHES'] + $post['team1_result_split'.$cid][$a];
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_MATCHES'] = $defaultconfig['JL_EXT_TENNIS_DATA_AWAY_MATCHES'] + $post['team2_result_split'.$cid][$a];

// erst die sätze
if ( $post['team1_result_split'.$cid][$a] > $post['team2_result_split'.$cid][$a] )
{
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_SETS']++;
}
elseif ( $post['team1_result_split'.$cid][$a] < $post['team2_result_split'.$cid][$a] )
{
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_SETS']++;
}

}
// zum schluss die punkte
if ( $defaultconfig['JL_EXT_TENNIS_DATA_HOME_SETS'] > $defaultconfig['JL_EXT_TENNIS_DATA_AWAY_SETS'] )
{
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_POINTS']++;
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_POINTS'] = 0;
}
elseif ( $defaultconfig['JL_EXT_TENNIS_DATA_HOME_SETS'] < $defaultconfig['JL_EXT_TENNIS_DATA_AWAY_SETS'] )
{
$defaultconfig['JL_EXT_TENNIS_DATA_AWAY_POINTS']++;
$defaultconfig['JL_EXT_TENNIS_DATA_HOME_POINTS'] = 0;
}
$post['team1_result'.$cid] = $defaultconfig['JL_EXT_TENNIS_DATA_HOME_POINTS'];
$post['team2_result'.$cid] = $defaultconfig['JL_EXT_TENNIS_DATA_AWAY_POINTS'];

//$mainframe->enqueueMessage(JText::_('save_array - defaultconfig: '.print_r($defaultconfig,true) ),'Notice');

// $temp='';
// foreach ( $defaultconfig as $key => $value )
// {
// $temp .= $key."=".$value."\n";
// }

foreach ( $defaultconfig as $key => $value )
{
$defaultvalues[] = $key . '=' . $value;
//$temp .= $key."=".$value."\n";
}
$temp = implode( "\n", $defaultvalues );


$query="UPDATE #__joomleague_match_single SET `extended`='".$temp."' WHERE id=".$cid;
$this->_db->setQuery($query);
if (!$this->_db->query())
		{
$mainframe->enqueueMessage(JText::_('save_array - defaultconfig: '.print_r($this->_db->getErrorMsg(),true) ),'Error');			
		}
		
break;
}
		
		foreach($fields as $field)
		{
			$query='';
			$datafield=array_keys($field);
			if ($zusatz){$fieldzusatz=$cid;}
			foreach ($datafield as $keys)
			{
				if (isset($post[$keys.$fieldzusatz]))
				{
					$result=$post[$keys.$fieldzusatz];
					if ($keys=='match_date')
					{
						$result .= ' '.$post['match_time'.$fieldzusatz];
					}
					if ($keys=='team1_result_split' || $keys=='team2_result_split' || $keys=='homeroster' || $keys=='awayroster')
					{
						$result=trim(join(';',$result));
					}
					if ($keys=='alt_decision' && $post[$keys.$fieldzusatz]==0)
					{
						$query .= ",team1_result_decision=NULL,team2_result_decision=NULL,decision_info='',team_won=0";
					}
					if ($keys=='team1_result_decision' && strtoupper($post[$keys.$fieldzusatz])=='X' && $post['alt_decision'.$fieldzusatz]==1)
					{
						$result='';
					}
					if ($keys=='team2_result_decision' && strtoupper($post[$keys.$fieldzusatz])=='X' && $post['alt_decision'.$fieldzusatz]==1)
					{
						$result='';
					}
					if (!is_numeric($result) || ($keys == 'match_number'))
					{
						$vorzeichen="'";
					}
					else
					{
						$vorzeichen='';
					}
					if (strstr(	"crowd,formation1,formation2,homeroster,awayroster,show_report,team1_result,
								team1_bonus,team1_legs,team2_result,team2_bonus,team2_legs,
								team1_result_decision,team2_result_decision,team1_result_split,
								team2_result_split,team1_result_ot,team2_result_ot,
								team1_result_so,team2_result_so,team_won,",$keys.',') &&
					$result == '' && isset($post[$keys.$fieldzusatz]))
					{
						$result='NULL';
						$vorzeichen='';
					}
					if ($keys=='crowd' && $post['crowd'.$fieldzusatz]=='')
					{
						$result='0';
					}
					if ($result!='' || $keys=='summary' || $keys=='match_result_detail')
					{
						if ($query){$query .= ',';}
						$query .= $keys.'='.$vorzeichen.$result.$vorzeichen;
					}
					
					if ($result=='' && $keys=='time_present')
					{
						if ($query){$query .= ',';}
						$query .= $keys.'=null';
					}
					
					if ($result=='' && $keys=='match_number')
					{
						if ($query){$query .= ',';}
						$query .= $keys.'=null';
					}
				}
			}
		}
		$user =& JFactory::getUser();
		$query='UPDATE #__joomleague_match_single SET '.$query.',`modified`=NOW(),`modified_by`='.$user->id.' WHERE id='.$cid;
		$this->_db->setQuery($query);
		$this->_db->query($query);
		
	
		
		// now for ko mode
		$project=&$this->getProject();
		if ($project->project_type == 'TOURNAMENT_MODE')
		{
			return $this->save_team_to($cid,$post);
		}

		return true;
	}

	/**
	 * Method to return a playground/venue array (id,text)
		*
		* @access	public
		* @return	array
		* @since 0.1
		*/
	/*
  function getPlaygrounds()
	{
		$query='SELECT id AS value, name AS text FROM #__joomleague_playground ORDER BY text ASC ';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}
  */
  
	/**
	 * Method to return teams and match data
		*
		* @access	public
		* @return	array
		* @since 0.1
		*/
	function getMatchTeams()
	{
		$query='	SELECT	mc.*,'
		.' t1.name AS team1,'
		.' t2.name AS team2,'
		.' u.name AS editor '
		.' FROM #__joomleague_match_single AS mc '
		.' INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=mc.projectteam1_id '
		.' INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id '
		.' INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=mc.projectteam2_id '
		.' INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id '
		.' LEFT JOIN #__users u ON u.id=mc.checked_out '
		.' WHERE mc.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		return	$this->_db->loadObject();
	}

	/**
	 * returns starters player id for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of player ids
	 */
	function getRoster($team_id,$project_position_id=0)
	{
		$query='SELECT	mp.teamplayer_id AS value,'
		.' pl.firstname,'
		.' pl.nickname,'
		.' pl.lastname,'
		.' tpl.jerseynumber'		
		.' FROM #__joomleague_match_player AS mp '
		.' INNER JOIN #__joomleague_team_player AS tpl ON tpl.id=mp.teamplayer_id '
		.' INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id '
		.' INNER JOIN #__joomleague_project_position as ppos ON ppos.id = tpl.project_position_id '
		.' WHERE mp.match_id='.(int) $this->_id
		.' AND tpl.projectteam_id='.$team_id
		.' AND mp.came_in=0 '
		.' AND pl.published = 1 ';
		if ($project_position_id > 0)
		{
			$query .= " AND mp.project_position_id='".$project_position_id."' ";
		}
		$query .= " ORDER BY ppos.position_id, mp.ordering ASC";
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList('value');
		return $result;
	}


	/**
	 * returns players who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchPlayers($projectteam_id,$project_position_id=0)
	{
		$query='	SELECT	mp.teamplayer_id AS tpid,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							mp.project_position_id,
							ppos.position_id,
							ppos.id AS pposid,
							tpl.projectteam_id
					FROM #__joomleague_match_player AS mp
					INNER JOIN #__joomleague_team_player AS tpl ON tpl.id=mp.teamplayer_id
					INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
					INNER JOIN #__joomleague_project_position as ppos ON ppos.id = mp.project_position_id
					WHERE	mp.match_id='.(int) $this->_id.' AND
					pl.published = 1 AND
					tpl.projectteam_id='.$projectteam_id;
		if ($project_position_id > 0)
		{
			$query .= " AND mp.project_position_id='$position_id'";
		}
		$query .= " ORDER BY mp.project_position_id, mp.ordering,
					tpl.jerseynumber, pl.lastname, pl.firstname ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('tpid');
	}

	/**
	 * returns players who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchStaffs($projectteam_id,$project_position_id=0)
	{
		$query='	SELECT	mp.team_staff_id,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							mp.project_position_id,
							tpl.projectteam_id,
							ppos.position_id,
							ppos.id AS pposid
					FROM #__joomleague_match_staff AS mp
					INNER JOIN #__joomleague_team_staff AS tpl ON tpl.id=mp.team_staff_id
					INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
					INNER JOIN #__joomleague_project_position as ppos ON ppos.id = mp.project_position_id
					WHERE mp.match_id='.(int) $this->_id.' 
					AND pl.published = 1 
					AND tpl.projectteam_id='.$projectteam_id;
		if ($project_position_id > 0)
		{
			$query .= " AND mp.project_position_id='".$project_position_id."'";
		}
		$query .= " ORDER BY mp.project_position_id, mp.ordering,
					pl.lastname, pl.firstname ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('team_staff_id');
	}

	/**
	 * returns starters referees id for the specified team
	 *
	 * @param int $project_position_id
	 * @return array of referee ids
	 */
	function getRefereeRoster($project_position_id=0)
	{
		$query='	SELECT	pref.id AS value,
							pr.firstname,
							pr.nickname,
							pr.lastname
					FROM #__joomleague_match_referee AS mr
					LEFT JOIN #__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id
					 AND pref.published = 1
					LEFT JOIN #__joomleague_person AS pr ON pref.person_id=pr.id
					 AND pr.published = 1
					WHERE mr.match_id='.(int) $this->_id;
		if ($project_position_id > 0)
		{
			$query .= ' AND mr.project_position_id='.$project_position_id;
		}
		$query .= ' ORDER BY mr.project_position_id, mr.ordering ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('value');
	}

	/**
	 * Method to return the projects referees array
	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function getProjectReferees($already_sel=false, $project_id)
	{
		$query=' SELECT	pref.id AS value,'
		.' pl.firstname,'
		.' pl.nickname,'
		.' pl.lastname,'
		.' pl.info,'
		.' pos.name AS positionname '
		.' FROM #__joomleague_person AS pl '
		.' LEFT JOIN #__joomleague_project_referee AS pref ON pref.person_id=pl.id '
		.' AND pref.published=1 '
		.' LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=pref.project_position_id '
		.' LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id '
		.' WHERE pref.project_id='.$project_id
		.' AND pl.published = 1';
		if (is_array($already_sel) && count($already_sel) > 0)
		{
			$query .= " AND pref.id NOT IN (".implode(',',$already_sel).")";
		}
		$query .= " ORDER BY pl.lastname ASC ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('value');
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getTeamPlayers($projectteam_id,$filter=false)
	{
		$query='	SELECT	tpl.id AS value,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							tpl.projectteam_id,
							tpl.jerseynumber,
							pl.info,
							pos.name AS positionname,
							ppos.position_id,
							ppos.id AS pposid
					FROM #__joomleague_person AS pl
					INNER JOIN #__joomleague_team_player AS tpl ON tpl.person_id=pl.id
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=tpl.project_position_id
					INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE tpl.projectteam_id='. $this->_db->Quote($projectteam_id).
					'AND pl.published = 1';
		if (is_array($filter) && count($filter) > 0)
		{
			$query .= " AND tpl.id NOT IN (".implode(',',$filter).")";
		}
		$query .= " ORDER BY pos.ordering, pl.lastname ASC ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Method to return the team players array
	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	/*
  function getGhostPlayer()
	{
		$ghost=new JObject();
		$ghost->set('value',0);
		$ghost->set('tpid',0);
		$ghost->set('firstname','');
		$ghost->set('nickname','');
		$ghost->set('lastname',JText::_('JL_GLOBAL_UNKNOWN'));
		$ghost->set('info','');
		$ghost->set('positionname','');
		$ghost->set('project_position_id',0);
		return array($ghost);
	}
  */
  
  /*
	function getGhostPlayerbb($projectteam_id)
	{
		$ghost=new JObject();
		$ghost->set('value',0);
		$ghost->set('tpid',0);
		$ghost->set('firstname','');
		$ghost->set('nickname','');
		$ghost->set('lastname',JText::_('JL_GLOBAL_UNKNOWN'));
		$ghost->set('projectteam_id',$projectteam_id);
		$ghost->set('info','');
		$ghost->set('positionname','');
		$ghost->set('project_position_id',0);
		return array($ghost);
	}
  */

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getTeamStaffs($projectteam_id,$filter=false)
	{
		$query='	SELECT	ts.id AS value,
							pl.firstname,
							pl.nickname,
							pl.lastname,
							ts.projectteam_id,
							pl.info,
							pos.name AS positionname,
							ppos.position_id
					FROM #__joomleague_person AS pl
					INNER JOIN #__joomleague_team_staff AS ts ON ts.person_id=pl.id
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id
					INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					WHERE ts.projectteam_id='.$this->_db->Quote($projectteam_id).
					' AND pl.published = 1';
		if (is_array($filter) && count($filter) > 0)
		{
			$query .= " AND ts.id NOT IN (".implode(',',$filter).")";
		}
		$query .= " ORDER BY pl.lastname ASC ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Method to return the project positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 * @since 1.5
	 */
	function getProjectPositions($id=0)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query='	SELECT	pos.id AS value,
							pos.name AS text,
							ppos.id AS pposid
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pos.id
					WHERE ppos.project_id='.$project_id.' AND pos.persontype=1';
		if ($id > 0)
		{
			$query .= ' AND ppos.position_id='.$id;
		}
		$query .= ' ORDER BY pos.ordering';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList('value'))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * Method to return the project positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 * @since 1.5
	 */
	function getProjectPositionsOptions($id=0, $person_type=1)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query='	SELECT	ppos.id AS value,
							pos.name AS text,
							pos.id AS posid
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pos.id
					WHERE ppos.project_id='.$project_id.' 
					  AND pos.persontype='.$person_type;
		if ($id > 0)
		{
			$query .= ' AND ppos.position_id='.$id;
		}
		$query .= ' ORDER BY pos.ordering';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList('value'))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * Method to return the project staff positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 * @since 1.5
	 */
	function getProjectStaffPositions($id=0)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query='	SELECT	pos.id AS value,
							pos.name AS text,
							ppos.id AS pposid
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pos.id
					WHERE ppos.project_id='.$project_id.' AND pos.persontype=2';
		if ($id > 0)
		{
			$query .= ' AND ppos.position_id='.$id;
		}
		$query .= ' ORDER BY pos.ordering';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList('value'))
		{
			$this->setError($this->_db->getErrorMsg());
			return array();
		}
		return $result;
	}

	/**
	 * Method to return the projects referee positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 * @since 0.1
	 */
	function getProjectRefereePositions($id=0)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query='	SELECT	ppos.id AS value,
							pos.name AS text,
							ppos.id AS pposid
					FROM #__joomleague_position AS pos
					LEFT JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pos.id
					WHERE ppos.project_id='.$project_id.' 
					  AND pos.persontype=3';
		if ($id > 0)
		{
			$query .= ' AND ppos.position_id='.$id;
		}
		$query .= ' ORDER BY pos.ordering';
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList('value'))
		{
			$this->setError($this->_db->getErrorMsg());
			return array();
		}
		return $result;
	}

	/**
	 * Method to update starting lineup list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function updateRoster($data)
	{
	$mainframe	=& JFactory::getApplication();
	//$mainframe->enqueueMessage(JText::_('updateRoster data: '.print_r($data,true) ),'');
	
		$result = true;
		
    if ( isset($data['positions']) && isset($data['mid']) && isset($data['mid']) )
    {
    $positions = $data['positions'];
		$mid = $data['mid'];
		$team = $data['mid'];
		
		/*
    // we first remove the records of starter for this team and this game then add them again from updated data.
		$query='	DELETE	mp
					FROM #__joomleague_match_player AS mp
					INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id
					WHERE	came_in=0 AND
							mp.match_id='.$this->_db->Quote($mid).' AND
							tp.projectteam_id='.$this->_db->Quote($team);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result=false;
		}
		*/
		
		//$mainframe->enqueueMessage(JText::_('updateRoster positions: '.print_r($positions,true) ),'');
		
		foreach ($positions AS $project_position_id => $pos)
		{
		
    //$mainframe->enqueueMessage(JText::_('updateRoster pos: '.print_r($pos,true) ),'');
		//$mainframe->enqueueMessage(JText::_('updateRoster project_position_id: '.$project_position_id ),'');
		//$mainframe->enqueueMessage(JText::_('updateRoster data position: '.print_r($data['position'.$project_position_id],true) ),'');
		
			if ( isset($data['position'.$project_position_id]) )
			{
			
      //$mainframe->enqueueMessage(JText::_('updateRoster position_id: '.$project_position_id ),'');
			
				foreach ($data['position'.$project_position_id] AS $ordering => $player_id)
				{
				
				// we first remove the records of starter for this team and this game then add them again from updated data.
		$query='	DELETE	mp
					FROM #__joomleague_match_player AS mp
					INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id
					WHERE	came_in=0 AND
							mp.match_id='.$this->_db->Quote($mid).' AND
							tp.projectteam_id='.$this->_db->Quote($team).' AND
							tp.teamplayer_id='.$this->_db->Quote($player_id);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result=false;
		}
				
				
        //$mainframe->enqueueMessage(JText::_('updateRoster ordering: '.$ordering ),'');
				//$mainframe->enqueueMessage(JText::_('updateRoster player_id: '.$player_id ),'');
				
					$record =& JTable::getInstance('Matchplayer','Table');
					$record->match_id=$mid;
					$record->teamplayer_id=$player_id;
					$record->project_position_id=$pos->pposid;
					$record->came_in=0;
					$record->ordering=$ordering;
					if (!$record->check())
					{
						$this->setError($record->getError());
						$result=false;
					}
					if (!$record->store())
					{
						$this->setError($record->getError());
						$result=false;
					}
				}
			}
			
      
		}
		
		}
		return true;
	}

	/**
	 * Method to update starting lineup list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	/* 
	function updateStaff($data)
	{
		$result=true;
		$positions=$data['staffpositions'];
		$mid=$data['mid'];
		$team=$data['team'];
		// we first remove the records of starter for this team and this game,then add them again from updated data.
		$query='	DELETE mp
					FROM #__joomleague_match_staff AS mp
					INNER JOIN #__joomleague_team_staff AS tp ON tp.id=mp.team_staff_id
					WHERE	mp.match_id='.$this->_db->Quote($mid).' AND
							tp.projectteam_id='.$this->_db->Quote($team);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result=false;
		}
		foreach ($positions AS $project_position_id => $pos)
		{
			if (isset($data['staffposition'.$project_position_id]))
			{
				foreach ($data['staffposition'.$project_position_id] AS $ordering => $player_id)
				{
					$record =& JTable::getInstance('Matchstaff','Table');
					$record->match_id=$mid;
					$record->team_staff_id=$player_id;
					$record->project_position_id=$pos->pposid;
					$record->ordering=$ordering;
					if (!$record->check())
					{
						$this->setError($record->getError());
						$result=false;
					}
					if (!$record->store())
					{
						$this->setError($record->getError());
						$result=false;
					}
				}
			}
		}
		//die();
		return true;
	}
  */

	/**
	 * Method to update starting lineup list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	 
	 /*
	function updateReferees($post)
	{
		$mid=$post['mid'];
		$peid=array();
		$result=true;
		$positions=$post['positions'];
		$project_id=$post['project'];
		foreach ($positions AS $key=>$pos)
		{
			if (isset($post['position'.$key])) { $peid=array_merge((array) $post['position'.$key],$peid); }
		}
		if ($peid == null)
		{ // Delete all referees assigned to this match
			$query='DELETE FROM #__joomleague_match_referee WHERE match_id='.$post['mid'];
		}
		else
		{ // Delete all referees which are not selected anymore from this match
			JArrayHelper::toInteger($peid);
			$peids=implode(',',$peid);
			$query="	DELETE FROM #__joomleague_match_referee
						WHERE match_id=".$post['mid']." AND project_referee_id NOT IN (".$peids.")";
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query()) { $this->setError($this->_db->getErrorMsg()); $result=false; }
		foreach ($positions AS $key=>$pos)
		{
			if (isset($post['position'.$key]))
			{
				for ($x=0; $x < count($post['position'.$key]); $x++)
				{
					$project_referee_id=$post['position'.$key][$x];
					$query="	SELECT *
								FROM #__joomleague_match_referee
								WHERE match_id=$mid AND project_referee_id=$project_referee_id";

					$this->_db->setQuery($query);
					if ($result=$this->_db->loadResult())
					{
						$query="	UPDATE #__joomleague_match_referee
									SET project_position_id='$key',ordering= '$x'
									WHERE id='$result' AND match_id='$mid' AND project_referee_id='$project_referee_id'";
					}
					else
					{
						$query="	INSERT INTO #__joomleague_match_referee
										(match_id,project_referee_id, project_position_id, ordering) VALUES
										('$mid','$project_referee_id','$key','$x')";
					}
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError($this->_db->getErrorMsg());
						$result=false;
					}
				}
			}
		}
		return true;
	}
  */
  
	/**
	 * Method to return substitutions made by a team during a match
	 * if no team id is passed,all substitutions should be returned (to be done!!)
	 * @access	public
	 * @return	array of substitutions
	 *
	 */
	function getSubstitutions($tid=0)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$in_out=array();
		$query='SELECT	mp.*,'
		.' p1.firstname AS firstname, p1.nickname AS nickname, p1.lastname AS lastname,'
		.' p2.firstname AS out_firstname,p2.nickname AS out_nickname, p2.lastname AS out_lastname,'
		.' pos.name AS in_position, came_in'
		.' FROM #__joomleague_match_player AS mp '
		.' LEFT JOIN #__joomleague_team_player AS tp1 ON tp1.id=mp.teamplayer_id '
		.' LEFT JOIN #__joomleague_person AS p1 ON tp1.person_id=p1.id '
		.'   AND p1.published = 1'
		.' LEFT JOIN #__joomleague_team_player AS tp2 ON tp2.id=mp.in_for '
		.' LEFT JOIN #__joomleague_person AS p2 ON tp2.person_id=p2.id '
		.'   AND p2.published = 1'
		.' LEFT JOIN #__joomleague_project_position AS ppos ON mp.project_position_id=ppos.id '
		.' LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id '
		.' WHERE mp.match_id='.(int) $this->_id
		.'   AND came_in>0 '
		.'   AND tp1.projectteam_id='.$tid
		.' ORDER by (mp.in_out_time+0) ';
		$this->_db->setQuery($query);
		$in_out[$tid]=$this->_db->loadObjectList();
		return $in_out;
	}

	/**
	 * Save match details from modal window
	 *
	 * @param array $data
	 * @return boolean
	 */
	function savedetails($data)
	{
		if (!$data['id'])
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_MATCH_ID_IS_NULL'));
			return false;
		}
		$table =& $this->getTable();
		if (!$table->load($data['id']))
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_GAME_NOT_FOUND'));
			return false;
		}
		// Bind the form fields to the table row
		if (!$table->bind($data))
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_BINDING_FAILED'));
			return false;
		}
		if (!$table->check())
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_CHECK_FAILED'));
			return false;
		}
		if (!$table->store(true))
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_STORE_FAILED'));
			return false;
		}
		if($data["new_match_id"] >0) {
			$table =& $this->getTable();
			if (!$table->load($data['new_match_id']))
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_OLD_GAME_NOT_FOUND'));
				return false;
			}
			$newdata=array();
			$newdata["old_match_id"]=$data["id"];
			// Bind the form fields to the table row
			if (!$table->bind($newdata))
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_BINDING_FAILED'));
				return false;
			}
			if (!$table->check())
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_CHECK_FAILED'));
				return false;
			}
			if (!$table->store(true))
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_STORE_FAILED'));
				return false;
			}
		}
		if($data["old_match_id"] >0) {
			$table =& $this->getTable();
			if (!$table->load($data['old_match_id']))
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_OLD_GAME_NOT_FOUND'));
				return false;
			}
			$newdata=array();
			$newdata["new_match_id"]=$data["id"];
			// Bind the form fields to the table row
			if (!$table->bind($newdata))
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_BINDING_FAILED'));
				return false;
			}
			if (!$table->check())
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_CHECK_FAILED'));
				return false;
			}
			if (!$table->store(true))
			{
				$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_STORE_FAILED'));
				return false;
			}
		}
		return true;
	}

	/**
	 * save the submitted substitution
	 *
	 * @param array $data
	 * @return boolean
	 */
	function savesubstitution($data)
	{
		if (! ($data['matchid']))
		{
			$this->setError("in: " . $data['in'].
							", out: " . $data['out'].
							", matchid: " . $data['matchid'].
							", project_position_id: " . $data['project_position_id']);
			return false;
		}
		$player_in				= (int) $data['in'];
		$player_out				= (int) $data['out'];
		$match_id				= (int) $data['matchid'];
		$in_out_time			= $data['in_out_time'];
		$project_position_id 	= $data['project_position_id'];

		if ($data['project_position_id'] == 0 && $player_in>0)
		{
			// retrieve normal position of player getting in
			$query='	SELECT project_position_id '
			.' FROM #__joomleague_team_player AS pt '
			.' WHERE pt.player_id='.$this->_db->Quote($player_in)
			;
			$this->_db->setQuery($query);
			$project_position_id = $this->_db->loadResult();
		}
		if($player_in>0) {
			$in_player_record =& JTable::getInstance('Matchplayer','Table');
			$in_player_record->match_id				= $match_id;
			$in_player_record->came_in				= 1; //1=came in, 2=went out
			$in_player_record->teamplayer_id		= $player_in;
			$in_player_record->in_for				= ($player_out>0) ? $player_out : 0;
			$in_player_record->in_out_time			= $in_out_time;
			$in_player_record->project_position_id	= $project_position_id;
			if (!$in_player_record->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		if($player_out>0 && $player_in==0) {
			$out_player_record =& JTable::getInstance('Matchplayer','Table');
			$out_player_record->match_id			= $match_id;
			$out_player_record->came_in				= 2; //0=starting lineup
			$out_player_record->teamplayer_id		= $player_out;
			$out_player_record->in_out_time			= $in_out_time;
			$out_player_record->project_position_id	= $project_position_id;
			$out_player_record->out					= 1;
			if (!$out_player_record->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * remove specified subsitution
	 *
	 * @param int $substitution_id
	 * @return boolean
	 */
	/*
  function removeSubstitution($substitution_id)
	{
		// the subsitute isn't getting in so we delete the substitution
		$query="	DELETE
					FROM #__joomleague_match_player
					WHERE id=".$this->_db->Quote($substitution_id). "
					 OR id=".$this->_db->Quote($substitution_id + 1);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_ERROR_DELETING_SUBST'));
			return false;
		}
		return true;
	}
  */
  
	function saveevent($data,$project_id)
	{
		$object =& JTable::getInstance('MatchEvent','Table');
		$object->bind($data);
		if (!$object->check())
		{
			$this->setError(JText::_('JL_ADMIN_MATCH_MODEL_CHECK_FAILED'));
			return false;
		}
		if (!$object->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $object->id;
	}

	function saveeventbb($data,$project_id)
	{
		$object =& JTable::getInstance('MatchEvent','Table');
		$object->match_id=(int) $this->_id;
		// home players
		for ($x=0; $x < $data['total_h_players'] ; $x++)
		{
			for($e=1; $e < $data['tehp']+1; $e++)
			{
				if (isset($data['cid_h'.$x]) && ($data['event_sum_h_'.$x.'_'.$e]!="") )
				{
					$object->id 			= $data['event_id_h_'		.$x.'_'.$e];
					//$object->project_id 	= $data['project_id'		];
					$object->teamplayer_id 	= $data['player_id_h_'		.$x];
					$object->projectteam_id = $data['team_id_h_'		.$x];
					$object->event_type_id 	= $data['event_type_id_h_'	.$x.'_'.$e];
					$object->event_sum 		= ($data['event_sum_h_'		.$x.'_'.$e]=="") ? null : $data['event_sum_h_'		.$x.'_'.$e] ;
					$object->event_time		= ($data['event_time_h_'		.$x.'_'.$e]=="") ? null : $data['event_time_h_'		.$x.'_'.$e] ;
					$object->notice			= ($data['notice_h_'		.$x.'_'.$e]=="") ? null : $data['notice_h_'		.$x.'_'.$e] ;
					$object->notes			= "";
					if (!$object->store())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
			}
		}
		// away players
		for ($x=0; $x < $data['total_a_players'] ; $x++)
		{
			for($e=1; $e < $data['teap']+1; $e++)
			{
				if (isset($data['cid_a'.$x]) && $data['event_sum_a_'.$x.'_'.$e]!="")
				{
					$object->id 			= $data['event_id_a_'		.$x.'_'.$e];
					//$object->project_id 	= $data['project_id'		];
					$object->teamplayer_id 	= $data['player_id_a_'		.$x];
					$object->projectteam_id	= $data['team_id_a_'		.$x];
					$object->event_type_id 	= $data['event_type_id_a_'	.$x.'_'.$e];
					$object->event_sum 		= ($data['event_sum_a_'		.$x.'_'.$e]=="") ? null : $data['event_sum_a_'		.$x.'_'.$e];
					$object->event_time		= ($data['event_time_a_'		.$x.'_'.$e]=="") ? null : $data['event_time_a_'		.$x.'_'.$e];
					$object->notice			= ($data['notice_a_'		.$x.'_'.$e]=="") ? null : $data['notice_a_'		.$x.'_'.$e] ;
					$object->notes			= "";
					if (!$object->store())
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
			}
		}
		return true;
	}

	function savestats($data)
	{
		$match_id=$data['match_id'];
		if (isset($data['cid']))
		{
			// save all checked rows
			foreach ($data['teamplayer_id'] as $idx => $tpid)
			{
				$teamplayer_id  = $data['teamplayer_id'][$idx];
				$projectteam_id = $data['projectteam_id'][$idx];
				//clear previous data
				$query=' DELETE FROM #__joomleague_match_statistic '
				.' WHERE match_id='. $this->_db->Quote($match_id)
				.'   AND teamplayer_id='. $this->_db->Quote($teamplayer_id);
				$this->_db->setQuery($query);
				$res=$this->_db->query();
				foreach ($data as $key => $value)
				{
					if (preg_match('/^stat'.$teamplayer_id.'_([0-9]+)/',$key,$reg) && $value!="")
					{
						$statistic_id=$reg[1];
						$stat=&JTable::getInstance('Matchstatistic','Table');
						$stat->match_id       = $match_id;
						$stat->projectteam_id = $projectteam_id;
						$stat->teamplayer_id  = $teamplayer_id;
						$stat->statistic_id   = $statistic_id;
						$stat->value          = ($value=="") ? null : $value;
						if (!$stat->check())
						{
							//var_dump($stat);
							echo "stat check failed!"; die();
						}
						if (!$stat->store())
						{
							//var_dump($stat);
							echo "stat store failed!"; die();
						}
					}
				}
			}
		}
		//staff stats
		if (isset($data['staffcid']))
		{
			// save all checked rows
			foreach ($data['team_staff_id'] as $idx => $stid)
			{
				$team_staff_id = $data['team_staff_id'][$idx];
				$projectteam_id = $data['sprojectteam_id'][$idx];
				//clear previous data
				$query=' DELETE FROM #__joomleague_match_staff_statistic '
				.' WHERE match_id='. $this->_db->Quote($match_id)
				.'   AND team_staff_id='. $this->_db->Quote($team_staff_id);
				$this->_db->setQuery($query);
				$res=$this->_db->query();
				foreach ($data as $key => $value)
				{
					if (ereg('^staffstat'.$team_staff_id.'_([0-9]+)',$key,$reg) && $value!="")
					{
						$statistic_id=$reg[1];
						$stat=&JTable::getInstance('Matchstaffstatistic','Table');
						$stat->match_id      = $match_id;
						$stat->projectteam_id= $projectteam_id;
						$stat->team_staff_id = $team_staff_id;
						$stat->statistic_id  = $statistic_id;
						$stat->value= ($value=="") ? null : $value;
						if (!$stat->check())
						{
							//var_dump($stat);
							echo "stat check failed!"; die();
						}
						if (!$stat->store())
						{
							//var_dump($stat);
							echo "stat store failed!"; die();
						}
					}
				}
			}
		}
		return true;
	}

	function deleteevent($event_id)
	{
		$object =& JTable::getInstance('MatchEvent','Table');
		if (!$object->canDelete($event_id))
		{
			$this->setError('JL_ADMIN_MATCH_MODEL_ERROR_DELETE');
			return false;
		}
		if (!$object->delete($event_id))
		{
			$this->setError('JL_ADMIN_MATCH_MODEL_DELETE_FAILED');
			return false;
		}
		return true;
	}

	/**
	 * get match events
	 *
	 * @return array
	 */
	function getMatchEvents()
	{
		$query=' SELECT	me.*,'
		.' t.name AS team,'
		.' et.name AS event,'
		.' CONCAT(t1.firstname," \'",t1.nickname,"\' ",t1.lastname) AS player1,'
		.' CONCAT(t2.firstname," \'",t2.nickname,"\' ",t2.lastname) AS player2 '
		.' FROM #__joomleague_match_event AS me '
		.' LEFT JOIN #__joomleague_team_player AS tp1 ON tp1.id=me.teamplayer_id '
		.' LEFT JOIN #__joomleague_person AS t1 ON t1.id=tp1.person_id '
		.' AND t1.published = 1 '
		.' LEFT JOIN #__joomleague_project_team AS pt ON pt.id=me.projectteam_id '
		.' LEFT JOIN #__joomleague_team AS t ON t.id=pt.team_id '
		.' LEFT JOIN #__joomleague_eventtype AS et ON et.id=me.event_type_id '
		.' LEFT JOIN #__joomleague_team_player AS tp2 ON tp2.id=me.teamplayer_id2 '
		.' LEFT JOIN #__joomleague_person AS t2 ON t2.id=tp2.person_id '
		.' AND t2.published = 1 '
		.' WHERE me.match_id='.$this->_db->Quote((int) $this->_id)
		.' ORDER BY me.event_time ASC ';
		$this->_db->setQuery($query);
		return ($this->_db->loadObjectList());
	}

	function getMatchStatsInput()
	{
		$match =& $this->getData();
		$query='SELECT * FROM #__joomleague_match_statistic  WHERE match_id='.$this->_db->Quote($match->id);
		$this->_db->setQuery($query);
		$res=$this->_db->loadObjectList();
		$stats=array(	$match->projectteam1_id => array(),
		$match->projectteam2_id => array());
		foreach ($res as $stat)
		{
			@$stats[$stat->projectteam_id][$stat->teamplayer_id][$stat->statistic_id]=$stat->value;
		}
		return $stats;
	}

	function getMatchStaffStatsInput()
	{
		$match =& $this->getData();
		$query='SELECT * FROM #__joomleague_match_staff_statistic WHERE match_id='.$this->_db->Quote($match->id);
		$this->_db->setQuery($query);
		$res=$this->_db->loadObjectList();
		$stats=array($match->projectteam1_id => array(),$match->projectteam2_id => array());
		foreach ((array)$res as $stat)
		{
			@$stats[$stat->projectteam_id][$stat->team_staff_id][$stat->statistic_id]=$stat->value;
		}
		return $stats;
	}

	function getPlayerEventsbb($teamplayer_id=0,$event_type_id=0)
	{
		$ret=array();
		$record;
		$record->id='';
		$record->event_sum=0;
        $record->event_time="";
        $record->notice="";
		$record->teamplayer_id=$teamplayer_id;
		$record->event_type_id=$event_type_id;
		$ret[0]=$record;
		$query='	SELECT	me.projectteam_id,
							me.id,
							me.match_id,
							me.teamplayer_id,
							me.event_type_id,
							me.event_sum,
                            me.event_time,
                            me.notice
					FROM #__joomleague_match_event AS me
					WHERE me.match_id='.(int) $this->_id.'
					AND	  me.teamplayer_id='.(int) $teamplayer_id.'
					AND	  me.event_type_id=' .(int) $event_type_id.'
					ORDER BY me.teamplayer_id ASC ';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		if(count($result)>0)
		{
			return $result;
		}
		return $ret;
	}

	function getEventsOptions($project_id)
	{
		$query='	SELECT DISTINCT	et.id AS value,
									et.name AS text,
									et.icon AS icon
					FROM #__joomleague_match_single AS m
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.project_id='.$project_id.'
					INNER JOIN #__joomleague_position_eventtype AS pet ON pet.position_id=ppos.position_id
					INNER JOIN #__joomleague_eventtype AS et ON et.id=pet.eventtype_id
					WHERE m.id='.$this->_db->Quote($this->_id).'
                    AND et.published=1
					ORDER BY pet.ordering, et.ordering';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		foreach ($result as $event){$event->text=JText::_($event->text);}
		return $result;
	}

	/**
	 * Checkout disabled
	 * @access	public
	 * @see administrator/components/com_joomleague/models/JoomleagueModelItem#checkout($uid)
	 */
	function checkout($uid=null)
	{
		return true;
	}

	function getRoundMatches($round_id)
	{
		$query="SELECT * FROM #__joomleague_match_single WHERE round_id=$round_id ORDER by match_number";
		$this->_db->setQuery($query);
		return ($this->_db->loadObjectList());
	}

	function getInputStats()
	{
		require_once (JPATH_COMPONENT_ADMINISTRATOR .DS.'statistics'.DS.'base.php');
		$match =& $this->getData();
		$project_id=$match->project_id;
		$query=' SELECT stat.id,stat.name,stat.short,stat.class,stat.icon,stat.calculated,ppos.position_id AS posid'
		.' FROM #__joomleague_statistic AS stat '
		.' INNER JOIN #__joomleague_position_statistic AS ps ON ps.statistic_id=stat.id '
		.' INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=ps.position_id '
		.' WHERE ppos.project_id='.  $project_id
		.' ORDER BY stat.ordering, ps.ordering ';
		$this->_db->setQuery($query);
		$res=$this->_db->loadObjectList();
		$stats=array();
		foreach ($res as $k => $row)
		{
			$stat=&JLGStatistic::getInstance($row->class);
			$stat->bind($row);
			$stat->set('position_id',$row->posid);
			$stats[]=$stat;
		}
		return $stats;
	}

	function getMatchRelationsOptions($project_id,$exclududeMatchId=0)
	{
		$query="SELECT	m.id AS value,
							CONCAT('(',m.match_date,') - ',t1.name,' - ',t2.name) AS text
							FROM #__joomleague_match_single AS m
							INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id=pt1.id
							INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id=pt2.id
							INNER JOIN #__joomleague_team AS t1 ON pt1.team_id=t1.id
							INNER JOIN #__joomleague_team AS t2 ON pt2.team_id=t2.id
							WHERE pt1.project_id=".$this->_db->Quote($project_id)."
							AND m.id NOT in (".$exclududeMatchId.")
							AND m.published=1
							ORDER BY m.match_date DESC,t1.short_name";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getProjectRoundCodes($project_id)
	{
		$query="SELECT id,roundcode,round_date_first FROM #__joomleague_round
				WHERE project_id=".$project_id." 
				ORDER by roundcode,round_date_first ASC";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	
	function save_roster_match()
	{
  $option='com_joomleague';
  
  $datahome = array();
  $dataaway = array();
  $exporthome = array();
  $exportaway = array();
  
	$mainframe	=& JFactory::getApplication();
	$post=JRequest::get('post');
  $cid=JRequest::getVar('cid',array(),'post','array');
	JArrayHelper::toInteger($cid);
		
  $sporttype = $mainframe->getUserState( $option . 'sporttype' );
  
  //$mainframe->enqueueMessage(JText::_('save_roster_match-post: '.print_r($post,true) ),'');
  //$mainframe->enqueueMessage(JText::_('save_roster_match-cid: '.print_r($cid,true) ),'');
  
  $project_positions = $this->getProjectPositions(0);
  
  //$mainframe->enqueueMessage(JText::_('getProjectPositions: '.print_r($project_positions,true) ),'');
  
  foreach ( $project_positions as $key => $value )
  {
  //$project_position_id = $value->pposid;
  $project_position_id = $value->value;
  //$mainframe->enqueueMessage(JText::_('getProjectPositions project_position_id: '.$project_position_id ),'');
  }
      
  $match_id = $post['match_id'];
  
  for ($x=0; $x < count($cid); $x++)
	{
	$homeplayer = $post['teamplayer1_id'.$cid[$x]];	
	$awayplayer = $post['teamplayer2_id'.$cid[$x]];
	$hometeam = $post['projectteam1_id'];	
	$awayteam = $post['projectteam2_id'];
	
  $query = ' SELECT id
  FROM #__joomleague_match_player
  WHERE match_id='. $match_id .' AND teamplayer_id = '. $homeplayer;
  $this->_db->setQuery($query);	
  //$matchplayer_id = $this->_db->loadResult();
  if ( !$matchplayer_id = $this->_db->loadResult() )
  {
  $mainframe->enqueueMessage(JText::_('heimspieler: '.$homeplayer.' nicht vorhanden' ),'Error');
  
  $datahome['mid'] = $match_id;
	$datahome['team'] = $hometeam;        
  $datahome['positions'] = $project_positions;        
	//$temp = new stdClass();
	$temp = $homeplayer;
  $exporthome[] = $temp;
  //$datahome['position'.$project_position_id] = array_merge($export);
  //unset($export);				
  }
  
  $query = ' SELECT id
  FROM #__joomleague_match_player
  WHERE match_id='. $match_id .' AND teamplayer_id = '. $awayplayer;
  $this->_db->setQuery($query);	
  //$matchplayer_id = $this->_db->loadResult();
  if ( !$matchplayer_id = $this->_db->loadResult() )
  {
  $mainframe->enqueueMessage(JText::_('gastspieler: '.$awayplayer.' nicht vorhanden' ),'Error');
  
  $dataaway['mid'] = $match_id;
	$dataaway['team'] = $awayteam;
	$dataaway['positions'] = $project_positions;
  //$temp = new stdClass();
	$temp = $awayplayer;
  $exportaway[] = $temp;
  //$dataaway['position'.$project_position_id] = array_merge($export);        
  //unset($export);          
  }
  	
	}
	
	$datahome['position'.$project_position_id] = array_merge($exporthome);
  $dataaway['position'.$project_position_id] = array_merge($exportaway);
  unset($exporthome);
  unset($exportaway);
	// nur wenn auch nichts da ist
	if ( isset($datahome['position'.$project_position_id]) )
			{
			$this->updateRoster($datahome);
			}
	if ( isset($dataaway['position'.$project_position_id]) )
			{
			$this->updateRoster($dataaway);
			}
	
	
  
  
  
  }
  
	
	function save_round_match_tennis()
	{
  $option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
	$post=JRequest::get('post');
  $cid=JRequest::getVar('cid',array(),'post','array');
	JArrayHelper::toInteger($cid);
		
  $sporttype = $mainframe->getUserState( $option . 'sporttype' );
  $defaultvalues = array();
  
//   $mainframe->enqueueMessage(JText::_('save_round_match-post: '.print_r($post,true) ),'');
//   $mainframe->enqueueMessage(JText::_('save_round_match-cid: '.print_r($cid,true) ),'');
    
  $match_id = $post['match_id'];
  
  $query = ' SELECT m.team1_result, 
  m.team2_result,
  team1_result_split,
  team2_result_split,
  extended
  FROM #__joomleague_match_single AS m
	WHERE m.match_id='.(int) $match_id .' AND m.published = 1';
	$this->_db->setQuery($query);		
	$singlerows = $this->_db->loadObjectList();
    
  foreach ( $singlerows as $row )
  {
  $update['resulthome'] += $row->team1_result;
  $update['resultaway'] += $row->team2_result;
  
  $params=explode("\n",$row->extended);
  foreach($params AS $param)
	{
		list ($name,$value) = explode("=",$param);
		$configvalues[$name] += $value;
	}
    
  }
  
  //$mainframe->enqueueMessage(JText::_('save_array - configvalues: '.print_r($configvalues,true) ),'Notice');

  //$temp='';
foreach ( $configvalues as $key => $value )
{
$defaultvalues[] = $key . '=' . $value;
//$temp .= $key."=".$value."\n";
}
$temp = implode( "\n", $defaultvalues );

  $rowupdate =& JTable::getInstance('match', 'Table');
  $rowupdate->load( $match_id );
  $rowupdate->team1_result = $update['resulthome'];
  $rowupdate->team2_result = $update['resultaway'];
  $rowupdate->extended = $temp;
  if (!$rowupdate->store()) 
  {
	JError::raiseError(500, $rowupdate->getError() );
  }  
    
  }
  
  
  function save_round_match_kegeln()
	{
  $option = 'com_joomleague';
	$mainframe	=& JFactory::getApplication();
	$post = JRequest::get('post');
  $cid = JRequest::getVar('cid',array(),'post','array');
	JArrayHelper::toInteger($cid);
		
  $sporttype = $mainframe->getUserState( $option . 'sporttype' );
  $defaultvalues = array();
  
  //$mainframe->enqueueMessage(JText::_('save_round_match-post: '.print_r($post,true) ),'');
  //$mainframe->enqueueMessage(JText::_('save_round_match-cid: '.print_r($cid,true) ),'');
    
  $match_id = $post['match_id'];	

	$query = ' SELECT SUM(m.team1_result) AS resulthome, 
  SUM(m.team2_result) AS resultaway,
  team1_result_split,
  team2_result_split
  FROM #__joomleague_match_single AS m
	WHERE m.match_id='.(int) $match_id .' AND m.published = 1';
	$this->_db->setQuery($query);		
	$row = $this->_db->loadAssoc();

$row['team1_result_split'] = array();
$row['team2_result_split'] = array();

for ($x=0; $x < count($cid); $x++)
{

$tempteam1 = $post['team1_result_split'.$cid[$x]]; 
$tempteam2 = $post['team2_result_split'.$cid[$x]];

for ($a=0; $a < 3; $a++)
{
$row['team1_result_split'][$a] = $row['team1_result_split'][$a] + $tempteam1[$a];
$row['team2_result_split'][$a] = $row['team2_result_split'][$a] + $tempteam2[$a];
}

}
$row['team1_result_split'] = implode(";",$row['team1_result_split']);
$row['team2_result_split'] = implode(";",$row['team2_result_split']);


$query = ' SELECT 
  extended
  FROM #__joomleague_match_single AS m
	WHERE m.match_id='.(int) $match_id .' AND m.published = 1';
	$this->_db->setQuery($query);		
	$singlerows = $this->_db->loadObjectList();
  
  foreach ( $singlerows as $singlerow )
  {
  
  $params=explode("\n",$singlerow->extended);
  foreach($params AS $param)
	{
		list ($name,$value) = explode("=",$param);
		$configvalues[$name] += $value;
	}
    
  }

  //$mainframe->enqueueMessage(JText::_('save_round_match -configvalues: '.print_r($configvalues,true) ),'');
  

foreach ( $configvalues as $key => $value )
{
$defaultvalues[] = $key . '=' . $value;
}
$temp = implode( "\n", $defaultvalues );


  $rowupdate =& JTable::getInstance('match', 'Table');
  $rowupdate->load( $match_id );
  $rowupdate->team1_result = $row['resulthome'];
  $rowupdate->team2_result = $row['resultaway'];
  $rowupdate->team1_result_split = $row['team1_result_split'];
  $rowupdate->team2_result_split = $row['team2_result_split'];
  $rowupdate->extended = $temp;
  if (!$rowupdate->store()) 
  {
	JError::raiseError(500, $rowupdate->getError() );
  }  
		
// 	$mainframe->enqueueMessage(JText::_('save_round_match -> '.$match_id ),'Notice');
// 	$mainframe->enqueueMessage(JText::_('save_round_match -> '.$row['resulthome'] ),'Notice');
// 	$mainframe->enqueueMessage(JText::_('save_round_match -> '.$row['resultaway'] ),'Notice');

  }

  function save_events_match($event_config)
	{
  $option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
	$lfdnummer =0 ;
	$post=JRequest::get('post');
  $cid=JRequest::getVar('cid',array(),'post','array');
	JArrayHelper::toInteger($cid);
		
  $sporttype = $mainframe->getUserState( $option . 'sporttype' );
  $project_id = $mainframe->getUserState($option.'project');

  //$mainframe->enqueueMessage(JText::_('save_events_match-post: '.print_r($post,true) ),'');
  //$mainframe->enqueueMessage(JText::_('save_events_match-cid: '.print_r($cid,true) ),'');
  //$mainframe->enqueueMessage(JText::_('save_events_match-post: '.print_r($event_config,true) ),'');
  
  $match_id = $post['match_id'];
  
  for ($a=1; $a <= sizeof($event_config); $a++ )
  {
  $event_id = $event_config['event'.$a];
  //$mainframe->enqueueMessage(JText::_('save_events_match - event_id: '.$event_id),'');
  
for ($x=0; $x < count($cid); $x++)
{
$tempteam1 = $post['team1_result_split'.$cid[$x]]; 
$tempteam2 = $post['team2_result_split'.$cid[$x]];

//$mainframe->enqueueMessage(JText::_('save_events_match - event result home: '.$tempteam1[$lfdnummer]),'');
//$mainframe->enqueueMessage(JText::_('save_events_match - event result teamplayer1: '.$post['teamplayer1_id'.$cid[$x]]),'');

$query = ' SELECT id
  FROM #__joomleague_match_event
  WHERE match_id='. $this->_db->Quote($match_id) 
  .' AND teamplayer_id = '. $this->_db->Quote($post['teamplayer1_id'.$cid[$x]])
  .' AND event_type_id = '. $this->_db->Quote($event_id);
  $this->_db->setQuery($query);	
//  $matchplayer_id = $this->_db->loadResult();

if ($matchplayer_id = $this->_db->loadResult())
{
//$mainframe->enqueueMessage(JText::_('save_events_match - teamplayer1 gefunden: '.$post['teamplayer1_id'.$cid[$x]]),'Notice');
$data['id'] = $matchplayer_id;
}  
$data['match_id'] = $match_id;
$data['projectteam_id'] = $post['projectteam1_id'];
$data['teamplayer_id'] = $post['teamplayer1_id'.$cid[$x]];
$data['event_type_id'] = $event_id;
$data['event_sum'] = $tempteam1[$lfdnummer];

$this->saveevent($data,$project_id);

//$mainframe->enqueueMessage(JText::_('save_events_match - event result away: '.$tempteam2[$lfdnummer]),'');
//$mainframe->enqueueMessage(JText::_('save_events_match - event result teamplayer2: '.$post['teamplayer2_id'.$cid[$x]]),'');

$query = ' SELECT id
  FROM #__joomleague_match_event
  WHERE match_id='. $this->_db->Quote($match_id) 
  .' AND teamplayer_id = '. $this->_db->Quote($post['teamplayer2_id'.$cid[$x]])
  .' AND event_type_id = '. $this->_db->Quote($event_id);
  $this->_db->setQuery($query);	
  //$matchplayer_id = $this->_db->loadResult();

if ($matchplayer_id = $this->_db->loadResult())
{
//$mainframe->enqueueMessage(JText::_('save_events_match - teamplayer2 gefunden: '.$post['teamplayer2_id'.$cid[$x]]),'Notice');
$data['id'] = $matchplayer_id;
}  

$data['match_id'] = $post['match_id'];
$data['projectteam_id'] = $post['projectteam2_id'];
$data['teamplayer_id'] = $post['teamplayer2_id'.$cid[$x]];
$data['event_type_id'] = $event_id;
$data['event_sum'] = $tempteam2[$lfdnummer];

$this->saveevent($data,$project_id);

}
  $lfdnummer++;
  }

  

  
  }
  
  function getTemplateConfig ($template)
	{
	$option='com_joomleague';
	$mainframe	=& JFactory::getApplication();
		$result = '';
		$configvalues = array();
		$project = $mainframe->getUserState($option.'project',0);;

		// load template param associated to project, or to master template if none find.
		$query =	"	SELECT params
						FROM #__joomleague_template_config
						WHERE template = " . $this->_db->Quote($template) .
					"	AND project_id =" . (int) $project;
		$this->_db->setQuery($query);
		
    if (!$result=$this->_db->loadResult())
		{
			if ($project->master_template)
			{
				$query = '	SELECT	params
							FROM #__joomleague_template_config
							WHERE template = ' . $this->_db->Quote($template) . '
							AND project_id = ' . (int) $project;
				$this->_db->setQuery($query);
				if (!$result = $this->_db->loadResult())
				{
					JError::raiseWarning(	500,
											sprintf(JText::_('JL_ADMIN_PROJECT_MODEL_MISSING_MASTER_TEMPLATE'),
											$template));
					return array();
				}
			}
			else
			{
				JError::raiseWarning(	500,
										sprintf(JText::_('JL_ADMIN_PROJECT_MODEL_MISSING_TEMPLATE'),
										$template));
				return array();
			}
		}
		$params = explode("\n", trim($result));
		foreach ($params AS $param)
		{
			list($name, $value) = explode("=", $param);
			$configvalues[$name]=$value;
		}
		
		
		return $configvalues;
	}
  
}
?>
