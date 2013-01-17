<?php
class JoomleagueConnector extends JLCalendar{
	//var $database = JFactory::getDBO();
	var $xparams;
	var $prefix;

	function getEntries ( &$caldates, &$params, &$matches )
	{
		$m = array();
		$b = array();
		$this->xparams = $params;
		$this->prefix = $params->prefix;
		if($this->xparams->get('joomleague_use_favteams', 0) == 1)
		{
			$this->favteams = JoomleagueConnector::getFavs();
		}
		if ($this->xparams->get('jlmatches', 0) == 1)
		{
			$rows = JoomleagueConnector::getMatches($caldates);
			$m = JoomleagueConnector::formatMatches($rows, $matches);
		}
		if ($this->xparams->get('jlbirthdays', 1) == 1)
		{
			$birthdays = JoomleagueConnector::getBirthdays (  $caldates, $this->params, $this->matches  );
			$b = JoomleagueConnector::formatBirthdays($birthdays, $matches, $caldates);
		}


		return array_merge($m, $b);
	}

	function getFavs()
	{


		$query = "SELECT id, fav_team FROM #__joomleague_project
      where fav_team != '' ";

		$projectid		= $this->xparams->get('project_ids') ;

		if ($projectid)
		{
			$projectids = (is_array($projectid)) ? implode(",", $projectid) : $projectid;

			$query .= " AND id IN(".$projectids.")";
		}

		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$database = JFactory::getDBO();
		$database->setQuery($query);
		$fav=$database->loadObjectList();


		// echo '<pre>';
		// print_r($fav);
		// echo '</pre>';
		//	exit(0);
		return $fav;
		//	return implode(',', $fav);
	}

	function getBirthdays ( $caldates, $ordering='ASC' )
	{
		$teamCondition = '';
		$clubCondition = '';
		$favCondition = '';
		$limitingcondition = '';
		$limitingconditions = array();

		$database = JFactory::getDBO();

		$customteam = JRequest::getVar('jlcteam',0,'default','POST');
		$teamid		= $this->xparams->get('team_ids') ;


		if($customteam != 0)
		{
			$limitingconditions[] = "( m.projectteam1_id = ".$customteam." OR m.projectteam2_id = ".$customteam.")";
		}

		if ($teamid && $customteam == 0)
		{

			$teamids = (is_array($teamid)) ? implode(",", $teamid) : $teamid;
			if($teamids > 0) 	{

				$limitingconditions[] = "pt.team_id IN (".$teamids.")";
					
			}
		}



		if($this->xparams->get('joomleague_use_favteams', 0) == 1 && $customteam == 0)
		{
			foreach ($this->favteams as $projectfavs)
			{
				$favConds[] = "(pt.team_id IN (". $projectfavs->fav_team.") AND p.id =".$projectfavs->id.")";

			}

			$limitingconditions[] = implode(' OR ', $favConds);
				
		}


		// new insert for user select a club
		$clubid		= $this->xparams->get('club_ids') ;

		if ($clubid && $customteam == 0)
		{

			$clubids = (is_array($clubid)) ? implode(",", $clubid) : $clubid;
			if($clubids > 0) 	$limitingconditions[] = "team.club_id IN (".$clubids.")";

		}



		if (count($limitingconditions) > 0)
		{
			$limitingcondition .=' AND (';
			$limitingcondition .= implode(' OR ', $limitingconditions);
			$limitingcondition .=')';
		}


		$query="SELECT p.id, p.firstname, p.lastname, p.picture, p.country,
                     DATE_FORMAT(p.birthday, '%m-%d') AS month_day,
                     YEAR( CURRENT_DATE( ) ) as year,
                     DATE_FORMAT('".$caldates['start']."', '%Y') - YEAR( p.birthday ) AS age,
                     DATE_FORMAT(p.birthday,'%Y-%m-%d') AS date_of_birth,
                     pt.project_id as project_id,
                     'showPlayer' AS func_to_call,
                     'pid' AS id_to_append,
                     team.short_name, team.id as teamid
              FROM #__joomleague_person AS p
              inner JOIN #__joomleague_team_player AS tp ON (p.id = tp.person_id)
              inner JOIN #__joomleague_project_team AS pt ON (pt.id = tp.projectteam_id)
              inner JOIN #__joomleague_team AS team ON (team.id = pt.team_id )
              inner JOIN #__joomleague_club AS club ON (club.id = team.club_id )
              WHERE p.published = 1 AND p.birthday != '0000-00-00' and DATE_FORMAT(p.birthday, '%m') = DATE_FORMAT('".$caldates['start']."', '%m')";

		$projectid		= $this->xparams->get('project_ids') ;


		if ($projectid)
		{
			$projectids = (is_array($projectid)) ? implode(",", $projectid) : $projectid;
			if($projectids > 0) 	$query .= " AND (pt.project_id IN (".$projectids.") )";

		}

		$query .= $limitingcondition;
		$query .= "  GROUP BY p.id ORDER BY p.birthday";
		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$database->setQuery($query);
		//echo($database->getQuery());

		$players=$database->loadObjectList();

		return $players;
	}
	function formatBirthdays( $rows, &$matches, $dates )
	{
		$newrows = array();
		$year = substr($dates['start'], 0, 4);


		foreach ($rows AS $key => $row)
		{
			$newrows[$key]['type'] = 'jlb';
			$newrows[$key]['homepic'] = '';
			$newrows[$key]['awaypic'] = '';
			$newrows[$key]['date'] = $year.'-'.$row->month_day;
			$newrows[$key]['age'] = '('.$row->age.')';
			$newrows[$key]['headingtitle'] = $this->xparams->get('birthday_text', 'Birthday');
			$newrows[$key]['name'] = '';

			if ($row->picture != '' AND file_exists(JPATH_BASE.DS.$row->picture))
			{
				$linkit = 1;
				$newrows[$key]['name'] = '<img src="'.JURI::root(true).'/'.$row->picture.'" alt="Picture" style="height:40px; vertical-align:middle;margin:0 5px;" />';

				//echo $newrows[$key]['name'].'</br>';
			}
			$newrows[$key]['name'] .= parent::jl_utf8_convert ($row->firstname, 'iso-8859-1', 'utf-8').' ';
			$newrows[$key]['name'] .= parent::jl_utf8_convert ($row->lastname, 'iso-8859-1', 'utf-8').' - '.parent::jl_utf8_convert ($row->short_name, 'iso-8859-1', 'utf-8');
			//$newrows[$key]['name'] .= ' ('..')';
			$newrows[$key]['matchcode'] = 0;
			$newrows[$key]['project_id'] = $row->project_id;

			// new insert for link to player profile
			//$newrows[$key]['link'] = 'index.php?option=com_joomleague&view=player&p='.$row->project_id.'&pid='.$row->id;
			$newrows[$key]['link'] = JoomleagueHelperRoute::getPlayerRoute( $row->project_id, $row->teamid, $row->id);


			$matches[] = $newrows[$key];
		}
		return $newrows;
	}


	function formatMatches( $rows, &$matches )
	{
		$newrows = array();
		$teamnames = $this->xparams->get('team_names', 'short_name');
		$teams = JoomleagueConnector::getTeamsFromMatches( $rows );
		$teams[0]->name = $teams[0]->$teamnames = $teams[0]->logo_small = $teams[0]->logo_middle = $teams[0]->logo_big =  '';

		/*
		 echo 'function formatMatches array teams<br>';
		 echo '<pre>';
		 print_r($teams);
		 echo '</pre>';
		 */

		/*
		 echo 'function formatMatches array rows<br>';
		 echo '<pre>';
		 print_r($rows);
		 echo '</pre>';
		 */

		/*
		 echo 'function formatMatches array matches<br>';
		 echo '<pre>';
		 print_r($matches);
		 echo '</pre>';
		 */

		foreach ($rows AS $key => $row) {
			$newrows[$key]['type'] = 'jlm';
			//$newrows[$key]['homepic'] = JoomleagueConnector::buildImage($teams[$row->matchpart1]);
			//$newrows[$key]['awaypic'] = JoomleagueConnector::buildImage($teams[$row->matchpart2]);
			$newrows[$key]['homepic'] = JoomleagueConnector::buildImage($teams[$row->projectteam1_id]);
			$newrows[$key]['awaypic'] = JoomleagueConnector::buildImage($teams[$row->projectteam2_id]);

			$newrows[$key]['date'] = $row->match_date;
			//$newrows[$key]['result'] = (!is_null($row->matchpart1_result)) ? $row->matchpart1_result . ':' . $row->matchpart2_result : '-:-';
			$newrows[$key]['result'] = (!is_null($row->team1_result)) ? $row->team1_result . ':' . $row->team2_result : '-:-';
			$newrows[$key]['headingtitle'] = parent::jl_utf8_convert ($row->name.'-'.$row->roundname, 'iso-8859-1', 'utf-8');
			//$newrows[$key]['homename'] = JoomleagueConnector::formatTeamName($teams[$row->matchpart1]);
			//$newrows[$key]['awayname'] = JoomleagueConnector::formatTeamName($teams[$row->matchpart2]);
			$newrows[$key]['homename'] = JoomleagueConnector::formatTeamName($teams[$row->projectteam1_id]);
			$newrows[$key]['awayname'] = JoomleagueConnector::formatTeamName($teams[$row->projectteam2_id]);
			$newrows[$key]['matchcode'] = $row->matchcode;
			$newrows[$key]['project_id'] = $row->project_id;

			// insert matchdetaillinks
			//$newrows[$key]['link'] = 'index.php?option=com_joomleague&view=nextmatch&p='.$row->project_id.'&mid='.$row->matchcode;
			$newrows[$key]['link'] = JoomleagueHelperRoute::getNextMatchRoute( $row->project_id, $row->matchcode);
			$matches[] = $newrows[$key];
			//parent::addTeam($row->matchpart1, parent::jl_utf8_convert ($teams[$row->matchpart1]->name, 'iso-8859-1', 'utf-8'), $newrows[$key]['homepic']);
			//parent::addTeam($row->matchpart2, parent::jl_utf8_convert ($teams[$row->matchpart2]->name, 'iso-8859-1', 'utf-8'),$newrows[$key]['awaypic']);
			parent::addTeam($row->projectteam1_id, parent::jl_utf8_convert ($teams[$row->projectteam1_id]->name, 'iso-8859-1', 'utf-8'), $newrows[$key]['homepic']);
			parent::addTeam($row->projectteam2_id, parent::jl_utf8_convert ($teams[$row->projectteam2_id]->name, 'iso-8859-1', 'utf-8'),$newrows[$key]['awaypic']);

			/*
			 echo 'function formatMatches projectteam1_id<br>';
			 echo $row->projectteam1_id.'-'.$teams[$row->projectteam1_id]->name.'<br>';
			 echo $row->projectteam2_id.'-'.$teams[$row->projectteam2_id]->name.'<br>';
			 */

		}
		return $newrows;
	}

	function formatTeamName($team)
	{
		$teamnames = $this->xparams->get('team_names', 'short_name');
		switch ($teamnames)
		{
			case '-':
				return '';
				break;
			case 'short_name':
				$teamname = '<acronym title="'.parent::jl_utf8_convert ($team->name, 'iso-8859-1', 'utf-8').'">'
				.parent::jl_utf8_convert ($team->short_name, 'iso-8859-1', 'utf-8')
				.'</acronym>';
				break;
			default:
				if (!isset($team->$teamnames) OR (is_null($team->$teamnames) OR trim($team->$teamnames)=='')) {
					$teamname = parent::jl_utf8_convert ($team->name, 'iso-8859-1', 'utf-8');
				}
				else {
					$teamname = parent::jl_utf8_convert ($team->$teamnames, 'iso-8859-1', 'utf-8');
				}
				break;
		}
		return $teamname;
	}

	function buildImage($team)
	{
		$image = $this->xparams->get('team_logos', 'logo_small');
		if ($image == '-') { return ''; }
		$logo = '';

		if ($team->$image != '' && file_exists(JPATH_BASE.'/'.$team->$image))
		{
			$h = $this->xparams->get('logo_height', 20);
			$logo = '<img src="'.JURI::root(true).'/'.$team->$image.'" alt="'
			.parent::jl_utf8_convert ($team->short_name, 'iso-8859-1', 'utf-8').'" title="'
			.parent::jl_utf8_convert ($team->name, 'iso-8859-1', 'utf-8').'"';
			if ($h > 0) {
				$logo .= ' style="height:'.$h.'px;"';
			}
			$logo .= ' />';
		}
		return $logo;
	}

	function getMatches($caldates, $ordering='ASC')
	{
		/*
		 echo 'function getMatches array caldates<br>';
		 echo '<pre>';
		 print_r($caldates);
		 echo '</pre>';
		 */
		$database = JFactory::getDBO();

		$teamCondition = '';
		$clubCondition = '';
		$favCondition = '';
		$limitingcondition = '';
		$limitingconditions = array();
		$favConds = array();

		$customteam = JRequest::getVar('jlcteam',0,'default','POST');



		$teamid		= $this->xparams->get('team_ids') ;

		if($customteam != 0)
		{
			$limitingconditions[] = "( m.projectteam1_id = ".$customteam." OR m.projectteam2_id = ".$customteam.")";
		}

		if ($teamid && $customteam == 0)
		{

			$teamids = (is_array($teamid)) ? implode(",", $teamid) : $teamid;
			if($teamids > 0) 	{

				$limitingconditions[] = "pt.team_id IN (".$teamids.")";
					
			}
		}

		$clubid		= $this->xparams->get('club_ids') ;

		if ($clubid && $customteam == 0)
		{

			$clubids = (is_array($clubid)) ? implode(",", $clubid) : $clubid;
			if($clubids > 0) 	$limitingconditions[] = "team.club_id IN (".$clubids.")";

		}

		if($this->xparams->get('joomleague_use_favteams', 0) == 1 && $customteam == 0)
		{
			foreach ($this->favteams as $projectfavs)
			{
				$favConds[] = "(pt.team_id IN (". $projectfavs->fav_team.") AND p.id =".$projectfavs->id.")";
			}
			if(!empty($favConds)){
			$limitingconditions[] = implode(' OR ', $favConds);
			}
		}

		if (count($limitingconditions) > 0)
		{
			$limitingcondition .=' AND (';
			$limitingcondition .= implode(' OR ', $limitingconditions);
			$limitingcondition .=')';
		}

		$limit = (isset($caldates['limitstart'])&&isset($caldates['limitend'])) ? ' LIMIT '.$caldates['limitstart'].', '.$caldates['limitend'] :'';


		$query = "SELECT  m.*,p.*,
                      DATE_FORMAT(match_date, '%Y-%m-%d') AS caldate,
                      r.roundcode, r.name AS roundname, r.round_date_first, r.round_date_last,
                      m.id as matchcode, p.id as project_id
              FROM #__joomleague_match m
              inner JOIN #__joomleague_round r ON r.id = m.round_id
              inner JOIN #__joomleague_project p ON p.id = r.project_id
              inner JOIN #__joomleague_project_team pt ON (pt.id = m.projectteam1_id OR pt.id = m.projectteam2_id)
              inner JOIN #__joomleague_team team ON team.id = pt.team_id
              inner JOIN #__joomleague_club club ON club.id = team.club_id 
               ";

		$where = " WHERE m.published = 1
               AND p.published = 1 ";
		if (isset($caldates['start'])) $where .= " AND m.match_date >= '".$caldates['start']."'";
		if (isset($caldates['end'])) $where .= " AND m.match_date <= '".$caldates['end']."'";
		if (isset($caldates['matchcode'])) $where .= " AND r.matchcode = '".$caldates['matchcode']."'";
		$projectid		= $this->xparams->get('project_ids') ;

		if ($projectid)
		{
			$projectids = (is_array($projectid)) ? implode(",", $projectid) : $projectid;
			if($projectids > 0) 	$where .= " AND p.id IN (".$projectids.")";
		}

		if(isset($caldates['resultsonly']) && $caldates['resultsonly']== 1) $where .= " AND m.team1_result IS NOT NULL";

		$where .= $limitingcondition;

		$where .= " GROUP BY m.id";
		$where .=" ORDER BY m.match_date ".$ordering;

		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$database->setQuery($query.$where.$limit);
		$result = $database->loadObjectList();

		return $result;
	}

	function getTeamsFromMatches( &$games )
	{
		$database = JFactory::getDBO();
		//if ($my->id == 62) explain_array($games);
		//$project_id = $games[0]->project_id;

		/*
		 echo 'function getTeamsFromMatches <br>';
		 echo '<pre>';
		 print_r($games);
		 echo '</pre>';
		 */

		if ( !count ($games) ) return Array();
		foreach ( $games as $m )
		{
			//$teamsId[] = $m->matchpart1;
			//$teamsId[] = $m->matchpart2;
			$teamsId[] = $m->projectteam1_id;
			$teamsId[] = $m->projectteam2_id;
		}

		$listTeamId = implode( ",", array_unique($teamsId) );

		//echo 'function getTeamsFromMatches teamids<br>';
		//echo $listTeamId.'<br>';

		/*
		 $query = "SELECT tl.id AS teamtoolid, tl.division_id, tl.standard_playground, tl.admin, tl.start_points,
		 tl.info, tl.team_id, tl.checked_out, tl.checked_out_time, tl.picture, tl.project_id,
		 t.id, t.name, t.short_name, t.middle_name, t.description, t.club_id,
		 pg1.id AS tt_pg_id, pg1.name AS tt_pg_name, pg1.short_name AS tt_pg_short_name,
		 pg2.id AS club_pg_id, pg2.name AS club_pg_name, pg2.short_name AS club_pg_short_name,
		 u.username, u.email,
		 c.logo_small, c.logo_middle, c.logo_big, c.country,
		 CONCAT('images/joomleague/flags/', LOWER(ctry.countries_iso_code_2), '.png') AS logo_country,
		 d.name AS division_name, d.shortname AS division_shortname,
		 p.name AS project_name
		 FROM #__joomleague_teams t
		 LEFT JOIN #__joomleague_team_joomleague tl on tl.team_id = t.id ";
		 $query .= "LEFT JOIN #__users u on tl.admin = u.id
		 LEFT JOIN #__joomleague_clubs c on t.club_id = c.id
		 LEFT JOIN #__joomleague_countries ctry on ctry.countries_id = c.country
		 LEFT JOIN #__joomleague_divisions d on d.id = tl.division_id
		 LEFT JOIN #__joomleague p on p.id = tl.project_id
		 LEFT JOIN #__joomleague_playgrounds pg1 ON pg1.id = tl.standard_playground
		 LEFT JOIN #__joomleague_playgrounds pg2 ON pg2.id = c.standard_playground
		 WHERE t.id IN (".$listTeamId.") AND tl.project_id = p.id";
		 */

		$query = "SELECT tl.id AS teamtoolid, tl.division_id, tl.standard_playground, tl.admin, tl.start_points,
                     tl.info, tl.team_id, tl.checked_out, tl.checked_out_time, tl.picture, tl.project_id,
                     t.id, t.name, t.short_name, t.middle_name, t.info, t.club_id,
                     c.logo_small, c.logo_middle, c.logo_big, c.country,
                     p.name AS project_name
                FROM #__joomleague_team t 
                inner JOIN #__joomleague_project_team tl on tl.team_id = t.id
                inner JOIN #__joomleague_project p on p.id = tl.project_id
                inner JOIN #__joomleague_club c on t.club_id = c.id 

                WHERE tl.id IN (".$listTeamId.") AND tl.project_id = p.id";

		/*
		 $query .= "LEFT JOIN #__users u on tl.admin = u.id
		 LEFT JOIN #__joomleague_clubs c on t.club_id = c.id
		 LEFT JOIN #__joomleague_countries ctry on ctry.countries_id = c.country
		 LEFT JOIN #__joomleague_divisions d on d.id = tl.division_id
		 LEFT JOIN #__joomleague p on p.id = tl.project_id
		 LEFT JOIN #__joomleague_playgrounds pg1 ON pg1.id = tl.standard_playground
		 LEFT JOIN #__joomleague_playgrounds pg2 ON pg2.id = c.standard_playground
		 WHERE t.id IN (".$listTeamId.") AND tl.project_id = p.id";
		 */
			
		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$database->setQuery($query);
		//if ( !$result = $database->loadObjectList('team_id') ) $result = Array();
		if ( !$result = $database->loadObjectList('teamtoolid') ) $result = Array();

		/*
		 echo 'function getTeamsFromMatches result<br>';
		 echo '<pre>';
		 print_r($result);
		 echo '</pre>';
		 */

		return $result;
	}

	function build_url( &$row )
	{

	}

function getGlobalTeams ()
	{
		$teamnames = $this->xparams->get('team_names', 'short_name');
		$database = JFactory::getDBO();
		$query = "SELECT t.".$teamnames." AS name, t.id AS value
    FROM #__joomleague_teams t, #__joomleague p
    WHERE t.id IN(p.fav_team)";
		$database->setQuery($query);
		$result = $database->loadObjectList();
	}
}