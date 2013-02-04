<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

jimport('joomla.utilities.array');
jimport('joomla.utilities.arrayhelper') ;


//require_once( JLG_PATH_SITE . DS . 'extensions' .DS . 'jlallprojectrounds' .DS . 'helpers' . DS . 'jlallprojectrounds.php' );
require_once( JLG_PATH_SITE . DS . 'models' . DS .'project.php' );

class JoomleagueModeljlallprojectrounds extends JoomleagueModelProject
{
	var $projectid = 0;
	var $project_ids = 0;
	var $project_ids_array = array();
	var $round = 0;
	var $rounds = array(0);
// 	var $part = 0;
// 	var $type = 0;
// 	var $from = 0;
// 	var $to = 0;
// 	var $divLevel = 0;
	var $ProjectTeams = array();
	var $previousRanking = array();
// 	var $homeRank = array();
// 	var $awayRank = array();
	var $colors = array();
	var $result = array();
 	
  var $projectteam_id = 0;
 	var $matchid = 0;
 	
 	var $_playersevents = array();
  /**
     * parameters
     * @var array
     */
    var $_params = null;
    
	function __construct( )
	{
		
    $this->projectid = JRequest::getInt( "p", 0 );

    $menu = &JMenu::getInstance('site');
        $item = $menu->getActive();
        $params = &$menu->getParams($item->id);
       $registry = new JRegistry();
$registry->loadArray($params);
//$newparams = $registry->toString('ini');
$newparams = $registry->toArray();
//echo "<b>menue newparams</b><pre>" . print_r($newparams, true) . "</pre>";  
foreach ($newparams['data'] as $key => $value ) {
            
            $this->_params[$key] = $value;
        }


// 		$this->round = JRequest::getInt( "r", $this->current_round);
// 		$this->part  = JRequest::getInt( "part", 0);
// 		$this->from  = JRequest::getInt( 'from', $this->round );
// 		$this->to	 = JRequest::getInt( 'to', $this->round);
// 		$this->type  = JRequest::getInt( 'type', 0 );
// 		$this->last  = JRequest::getInt( 'last', 0 );

// 		$this->selDivision = JRequest::getInt( 'division', 0 );

		parent::__construct( );
	}

//   function getProjectID()
//   {
//   echo 'project ->'.$this->projectid.'<br>';
//   }
  
  function getProjectMatches()
  {
  
  $result=array();

		$query_SELECT='
		SELECT	m.*,
			DATE_FORMAT(m.time_present,"%H:%i") time_present,
			playground.name AS playground_name,
			r.name as round_name,
			r.roundcode as roundcode,
			t1.name as home_name,
			t1.short_name as home_short_name,
			t1.middle_name as home_middle_name,
			t2.name as away_name,
			t2.short_name as away_short_name,
			t2.middle_name as away_middle_name,
			playground.short_name AS playground_short_name,
			tt1.project_id, d1.name as divhome, d2.name as divaway,
			CASE WHEN CHAR_LENGTH(t1.alias) AND CHAR_LENGTH(t2.alias) THEN CONCAT_WS(\':\',m.id,CONCAT_WS("_",t1.alias,t2.alias)) ELSE m.id END AS slug ';

		$query_FROM='
		FROM #__joomleague_match AS m
			INNER JOIN #__joomleague_round AS r ON m.round_id=r.id
			LEFT JOIN #__joomleague_project_team AS tt1 ON m.projectteam1_id=tt1.id
			LEFT JOIN #__joomleague_project_team AS tt2 ON m.projectteam2_id=tt2.id
			LEFT JOIN #__joomleague_team AS t1 ON t1.id=tt1.team_id
			LEFT JOIN #__joomleague_team AS t2 ON t2.id=tt2.team_id
			LEFT JOIN #__joomleague_division AS d1 ON tt1.division_id=d1.id
			LEFT JOIN #__joomleague_division AS d2 ON tt2.division_id=d2.id
			LEFT JOIN #__joomleague_playground AS playground ON playground.id=m.playground_id';

		$query_WHERE	= ' WHERE m.published=1 
							   
							  AND r.project_id='.(int)$this->projectid;
		$query_END		= ' GROUP BY m.id ORDER BY r.roundcode ASC,m.match_date ASC,m.match_number';
				
		$query=$query_SELECT.$query_FROM.$query_WHERE.$query_END;
		
		
		$this->_db->setQuery($query);
    if (!$result = $this->_db->loadObjectList()) 
    {
			JError::raiseWarning(0, $this->_db->getErrorMsg());
		}

// 		echo '<br /><pre>~'.print_r(count($result),true).'~</pre><br />';
// 		echo '<br /><pre>~'.print_r($result,true).'~</pre><br />';
		$this->result = $result;
		return $result;
		
  }
  
  function getProjectTeamID($favteams)
  {
  
  foreach ( $favteams as $key => $value )
  {
  $query = '	SELECT id
FROM #__joomleague_project_team
WHERE project_id = ' . $this->projectid .' and team_id = '.$value;

$this->_db->setQuery( $query );
$this->ProjectTeams[$value] = $this->_db->loadResult();
  
  }

//   echo '<br />ProjectTeams<pre>~'.print_r($this->ProjectTeams,true).'~</pre><br />';
  
  return $this->ProjectTeams;
  }
  
  function getSubstitutes()
	{
	$projectteamplayer = array();
		$query=' SELECT	mp.in_out_time,
						mp.teamplayer_id,
						pt.team_id,
						pt.id AS ptid,
						tp.person_id,
						tp.jerseynumber,
						tp2.person_id AS out_person_id,
						mp.in_for,
						p2.id AS out_ptid,
						p.firstname,
						p.nickname,
						p.lastname,
						pos.name AS in_position,
						pos2.name AS out_position,
						p2.firstname AS out_firstname,
						p2.nickname AS out_nickname,
						p2.lastname AS out_lastname,
						ppos.id AS pposid1,
						ppos2.id AS pposid2,
						CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,
						CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug
					FROM #__joomleague_match_player AS mp
						LEFT JOIN #__joomleague_team_player AS tp ON mp.teamplayer_id=tp.id
						LEFT JOIN #__joomleague_project_team AS pt ON tp.projectteam_id=pt.id
						LEFT JOIN #__joomleague_person AS p ON tp.person_id=p.id
						  AND p.published = 1
						LEFT JOIN #__joomleague_team_player AS tp2 ON mp.in_for=tp2.id
						LEFT JOIN #__joomleague_person AS p2 ON tp2.person_id=p2.id
						  AND p2.published = 1
						LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id
						LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id
						LEFT JOIN #__joomleague_match_player AS mp2 ON mp.match_id=mp2.match_id and mp.in_for=mp2.teamplayer_id
						LEFT JOIN #__joomleague_project_position AS ppos2 ON ppos2.id=mp2.project_position_id
						LEFT JOIN #__joomleague_position AS pos2 ON ppos2.position_id=pos2.id
						INNER JOIN #__joomleague_team AS t ON t.id=pt.team_id
					WHERE mp.match_id = '.(int)$this->matchid.'
            AND pt.id = '.$this->projectteam_id .' 
					  AND mp.came_in > 0
					  GROUP BY mp.in_out_time+mp.teamplayer_id+pt.team_id 
					ORDER by (mp.in_out_time+0)';
		$this->_db->setQuery($query);
		//echo($this->_db->getQuery());
		$result = $this->_db->loadObjectList();
		//return $result;
		
    foreach ( $result as $row )
		{
    $projectteamplayer[] = $row->firstname.' '.$row->lastname.' ('.$row->in_out_time.')';
    }
		return $projectteamplayer;
		
	}
	
	function getPlayersEvents()
	{
	$playersevents = array();	
//			$match=&$this->getMatch();

			$query=' SELECT ev.*,
      p.firstname,
			p.nickname,
			p.lastname,
			et.name as etname,
			et.icon as eticon      
      FROM #__joomleague_match_event as ev '
      .' INNER JOIN	#__joomleague_eventtype AS et 
      ON et.id = ev.event_type_id'
            .' INNER JOIN	#__joomleague_team_player AS tp ON tp.id = ev.teamplayer_id '
            .' INNER JOIN	#__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
            .' INNER JOIN #__joomleague_person AS p ON tp.person_id = p.id'
			      .' WHERE ev.match_id = ' . (int)$this->matchid
            .' AND ev.projectteam_id = ' . $this->projectteam_id;
			$this->_db->setQuery($query);
			$res = $this->_db->loadObjectList();

		foreach ( $res as $row )
		{
    $playersevents[] = JHTML::_( 'image', $row->eticon, JText::_($row->eticon ), NULL ) . JText::_($row->etname).' '.$row->notice.' '.$row->firstname.' '.$row->lastname.' ('.$row->event_time.')';
    }
    
// 			$this->_playersevents = $events;

		return $playersevents;

	}
	
  function getMatchPlayers()
	{
	$projectteamplayer = array();
	
		$query=' SELECT	pt.id,'
		      .' tp.person_id,'
		      .' p.firstname,'
		      .' p.nickname,'
		      .' p.lastname,'
		      .' tp.jerseynumber,'
		      .' ppos.position_id,'
		      .' ppos.id AS pposid,'
		      .' pt.team_id,'
		      .' pt.id as ptid,'
		      .' mp.teamplayer_id,'
		      .' mp.out,'
		      .' mp.in_out_time,'
		      .' tp.picture,'
			  .' p.picture AS ppic,'
		      .' CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,'
		      .' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug '
		      .' FROM #__joomleague_match_player AS mp '
		      .' INNER JOIN	#__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id '
		      .' INNER JOIN	#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id '
		      .' INNER JOIN	#__joomleague_team AS t ON t.id=pt.team_id '
		      .' INNER JOIN	#__joomleague_person AS p ON tp.person_id=p.id '
		      .' LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id '
		      .' LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id '
		      .' WHERE mp.match_id='.(int)$this->matchid
		      .' AND mp.came_in=0 '
		      .' AND pt.id='.$this->projectteam_id
		      .' AND p.published = 1 '
		      .' ORDER BY mp.ordering, tp.jerseynumber, p.lastname ';
		$this->_db->setQuery($query);
		$matchplayers = $this->_db->loadObjectList();
		
// 		echo '<br />matchplayers<pre>~'.print_r($matchplayers,true).'~</pre><br />';
		
		foreach ( $matchplayers as $row )
		{
        
    $query = '	SELECT	in_out_time
					FROM #__joomleague_match_player
					WHERE match_id = ' . (int)$this->matchid . '
					AND in_for = ' . (int)$row->teamplayer_id;

		$this->_db->setQuery( $query );
		$row->in_out_time = $this->_db->loadResult();
      
    
    
    if ( $row->in_out_time )
    {
    $projectteamplayer[] = $row->firstname.' '.$row->lastname.' ('.$row->in_out_time.')';
    }
    else
    {
    $projectteamplayer[] = $row->firstname.' '.$row->lastname;
    }
    
    }
		
    return $projectteamplayer;
		
	}
	
	function getAllRoundsParams()
    {
        return $this->_params;
    }
    
  function getRoundsColumn($rounds,$config)
  {
  
  //$countrows = count($rounds) %2;
  //echo 'countrows -> '.$countrows.'<br>';
  
  if ( count($rounds) %2 ) 
  { 
//   echo "Zahl ist ungrade<br>"; 
  } 
  else 
  { 
//   echo "Zahl ist gerade<br>";
  $countrows = count($rounds) / 2;
//   echo 'wir haben '.$countrows.' spieltage pro spalte<br>';
  }

  if ( $config['show_columns'] == 1 )
  {
  }
  else
  {
  $countrows = count($rounds);
  }
  
  $lfdnumber = 0;
  $htmlcontent = array();
  $content = '<table width="100%" border="4" rules="none">';
  
  for($a=0; $a < $countrows;$a++)
  {
  
  if ( $config['show_columns'] == 1 )
  {
  // zwei spalten
  $secondcolumn = $a + $countrows;
  
  $htmlcontent[$a]['header'] = '';
  $htmlcontent[$a]['first'] = '<table width="100%" border="0" rules="rows">';
  $htmlcontent[$a]['second'] = '<table width="100%" border="0" rules="rows">';
  $htmlcontent[$a]['header'] = '<thead><tr><th colspan="" >'.$rounds[$a]->name.'</th><th colspan="" >'.$rounds[$secondcolumn]->name.'</th></tr></thead>';

  
  $roundcode = $a + 1;
  $secondroundcode = $a + 1 + $countrows;
 
  foreach ( $this->result as $match )
  {
    
  if ( (int)$match->roundcode === (int)$roundcode )
  {
  
  $htmlcontent[$a]['first'] .= '<tr><td>'.$match->home_name.'</td>';
  $htmlcontent[$a]['first'] .= '<td>'.$match->team1_result.'</td>';
  $htmlcontent[$a]['first'] .= '<td>'.$match->team2_result.'</td>';
  $htmlcontent[$a]['first'] .= '<td>'.$match->away_name.'</td></tr>';
  
  foreach ( $this->ProjectTeams as $key => $value )
  {
  
  if ( (int)$match->projectteam1_id === (int)$value || (int)$match->projectteam2_id === (int)$value )
  {
  
  if ( $config['show_firstroster'] )
  {
  $htmlcontent[$a]['firstroster'] = '<b>'.JText::_('JL_MATCHREPORT_STARTING_LINE-UP').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['firstroster'] .= implode(",",$this->getMatchPlayers());
  $htmlcontent[$a]['firstroster'] .= '';
  }
  if ( $config['show_firstsubst'] )
  {
  $htmlcontent[$a]['firstsubst'] = '<b>'.JText::_('JL_MATCHREPORT_SUBSTITUTES').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['firstsubst'] .= implode(",",$this->getSubstitutes());
  $htmlcontent[$a]['firstsubst'] .= '';
  }
  if ( $config['show_firstevents'] )
  {
  $htmlcontent[$a]['firstevents'] = '<b>'.JText::_('JL_MATCHREPORT_EVENTS').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['firstevents'] .= implode(",",$this->getPlayersEvents());
  $htmlcontent[$a]['firstevents'] .= '';
  }
  
  }
  
  }
  
  }
  
  if ( (int)$match->roundcode === (int)$secondroundcode )
  {
  
  $htmlcontent[$a]['second'] .= '<tr><td>'.$match->home_name.'</td>';
  $htmlcontent[$a]['second'] .= '<td>'.$match->team1_result.'</td>';
  $htmlcontent[$a]['second'] .= '<td>'.$match->team2_result.'</td>';
  $htmlcontent[$a]['second'] .= '<td>'.$match->away_name.'</td></tr>';
  
  foreach ( $this->ProjectTeams as $key => $value )
  {
  
  if ( (int)$match->projectteam1_id === (int)$value || (int)$match->projectteam2_id === (int)$value )
  {
  if ( $config['show_secondroster'] )
  {
  $htmlcontent[$a]['secondroster'] = '<b>'.JText::_('JL_MATCHREPORT_STARTING_LINE-UP').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['secondroster'] .= implode(",",$this->getMatchPlayers());
  $htmlcontent[$a]['secondroster'] .= '';
  }
  if ( $config['show_secondsubst'] )
  {
  $htmlcontent[$a]['secondsubst'] = '<b>'.JText::_('JL_MATCHREPORT_SUBSTITUTES').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['secondsubst'] .= implode(",",$this->getSubstitutes());
  $htmlcontent[$a]['secondsubst'] .= '';
  }
  if ( $config['show_secondevents'] )
  {
  $htmlcontent[$a]['secondevents'] = '<b>'.JText::_('JL_MATCHREPORT_EVENTS').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['secondevents'] .= implode(",",$this->getPlayersEvents());
  $htmlcontent[$a]['secondevents'] .= '';
  }
  }
  
  }
    
  }
      
  }
  
  $htmlcontent[$a]['first'] .= '</table>';
  $htmlcontent[$a]['second'] .= '</table>';
  }
  else
  {
  // nur eine spalte
  $htmlcontent[$a]['header'] = '';
  $htmlcontent[$a]['first'] = '<table width="100%" border="0" rules="rows">';
  $htmlcontent[$a]['header'] = '<thead><tr><th colspan="" >'.$rounds[$a]->name.'</th></tr></thead>';
  $roundcode = $a + 1;
  
  foreach ( $this->result as $match )
  {
    
  if ( (int)$match->roundcode === (int)$roundcode )
  {
  
  $htmlcontent[$a]['first'] .= '<tr><td width="45%">'.$match->home_name.'</td>';
  $htmlcontent[$a]['first'] .= '<td width="5%">'.$match->team1_result.'</td>';
  $htmlcontent[$a]['first'] .= '<td width="5%">'.$match->team2_result.'</td>';
  $htmlcontent[$a]['first'] .= '<td width="45%">'.$match->away_name.'</td></tr>';
  
  foreach ( $this->ProjectTeams as $key => $value )
  {
  
  if ( (int)$match->projectteam1_id === (int)$value || (int)$match->projectteam2_id === (int)$value )
  {
  
  
  $htmlcontent[$a]['firstroster'] = '<b>'.JText::_('JL_MATCHREPORT_STARTING_LINE-UP').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['firstroster'] .= implode(",",$this->getMatchPlayers());
  $htmlcontent[$a]['firstroster'] .= '';
  
  $htmlcontent[$a]['firstsubst'] = '<b>'.JText::_('JL_MATCHREPORT_SUBSTITUTES').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['firstsubst'] .= implode(",",$this->getSubstitutes());
  $htmlcontent[$a]['firstsubst'] .= '';
  
  $htmlcontent[$a]['firstevents'] = '<b>'.JText::_('JL_MATCHREPORT_EVENTS').' : </b>';
  $this->matchid = $match->id;
  $this->projectteam_id = $value;
  $htmlcontent[$a]['firstevents'] .= implode(",",$this->getPlayersEvents());
  $htmlcontent[$a]['firstevents'] .= '';
  
  
  }
  
  }
  
  }
  
  }
  
  $htmlcontent[$a]['first'] .= '</table>';
  }
    
  }
  
  if ( $htmlcontent )
  {
  foreach ( $htmlcontent as $key => $value )
  {
  
  if ( $config['show_columns'] == 1 )
  {
  $content .= $value['header'];
  $content .= '<tr><td>'.$value['first'].'</td>';
  $content .= '<td>'.$value['second'].'</td></tr>';
  
  if (array_key_exists('firstroster', $value)) {
  $content .= '<tr><td>'.$value['firstroster'].'</td>';
  }
  if (array_key_exists('secondroster', $value)) {
  $content .= '<td>'.$value['secondroster'].'</td></tr>';
  }
  if (array_key_exists('firstsubst', $value)) {
  $content .= '<tr><td>'.$value['firstsubst'].'</td>';
  }
  if (array_key_exists('secondsubst', $value)) {
  $content .= '<td>'.$value['secondsubst'].'</td></tr>';
  }
  if (array_key_exists('firstevents', $value)) {
  $content .= '<tr><td>'.$value['firstevents'].'</td>';
  }
  if (array_key_exists('secondevents', $value)) {
  $content .= '<td>'.$value['secondevents'].'</td></tr>';
  }
  
  }
  else
  {
  $content .= $value['header'];
  $content .= '<tr><td>'.$value['first'].'</td></tr>';
  
  
  if (array_key_exists('firstroster', $value)) {
  $content .= '<tr><td>'.$value['firstroster'].'</td></tr>';
  }
  
  if (array_key_exists('firstsubst', $value)) {
  $content .= '<tr><td>'.$value['firstsubst'].'</td></tr>';
  }
  
  if (array_key_exists('firstevents', $value)) {
  $content .= '<tr><td>'.$value['firstevents'].'</td></tr>';
  }
  
  }
  
  }
  
  }
   
  $content .= '</table>';
  
  return $content;
  }
  	                              	
}
?>