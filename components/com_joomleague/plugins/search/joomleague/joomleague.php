<?php
/**
 * @package		JoomLeague
 * @subpackage	plg_search_joomleague
 * @copyright	Copyright (c)2010-2013 JoomLeague Developers
 * @license		GNU General Public License version 2, or later
 * @since		1.5.0a
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.plugin.plugin' );
require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'helpers'.DS.'countries.php' );

class plgSearchJoomleague extends JPlugin
{

	public function plgSearchJoomleague(&$subject, $params)
	{
		parent::__construct($subject, $params);
		// load language file for frontend
		JPlugin::loadLanguage( 'plg_joomleague_search', JPATH_ADMINISTRATOR );
	}

	function onContentSearchAreas()
	{
		static $areas = array(
				'joomleague' => 'JoomLeague'
		);
		return $areas;
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		// load plugin params info
		$plugin			=& JPluginHelper::getPlugin('search', 'joomleague');
		$search_clubs 		= $this->params->def( 'search_clubs', 		1 );
		$search_teams 		= $this->params->def( 'search_teams', 		1 );
		$search_players 	= $this->params->def( 'search_players', 	1 );
		$search_playgrounds	= $this->params->def( 'search_playgrounds', 1 );
		$search_staffs	 	= $this->params->def( 'search_staffs',	 	1 );
		$search_referees	= $this->params->def( 'search_referees',	1 );
        $search_projects	= $this->params->def( 'search_projects',	1 );
		$text = trim( $text );
		if ($text == '') {
			return array();
		}

		$wheres = array();

		switch ($phrase)
		{

			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				$wheresteam = array();
				$wheresperson = array();
				$wheresplayground = array();
                $wheresproject = array();

				if ( $search_clubs )
				{
					foreach ($words as $word)
					{
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
						$wheres2 	= array();
						$wheres2[] 	= 'c.name LIKE '.$word;
						$wheres2[] 	= 'c.alias LIKE '.$word;
						$wheres2[] 	= 'c.location LIKE '.$word;
                        $wheres2[] 	= 'c.unique_id LIKE '.$word;

						$wheres[] 	= implode( ' OR ', $wheres2 );
					}
				}


				if ( $search_teams )
				{
					foreach ($words as $word)
					{
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
						$wheres2 	= array();
						$wheres2[] 	= 't.name LIKE '.$word;
						$wheresteam[] 	= implode( ' OR ', $wheres2 );
					}
				}

				if ( $search_players || $search_referees || $search_staffs)
				{
					foreach ($words as $word)
					{
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
						$wheres2 	= array();
						$wheres2[] 	= 'pe.firstname LIKE '.$word;
						$wheres2[] 	= 'pe.lastname LIKE '.$word;
						$wheres2[] 	= 'pe.nickname LIKE '.$word;
						$wheresperson[] 	= implode( ' OR ', $wheres2 );
					}
				}

				if ( $search_playgrounds )
				{
					foreach ($words as $word)
					{
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
						$wheres2 	= array();
						$wheres2[] 	= 'pl.name LIKE '.$word;
						$wheres2[] 	= 'pl.city LIKE '.$word;


						$wheresplayground[] 	= implode( ' OR ', $wheres2 );
					}
				}
                
                if ( $search_projects )
				{
					foreach ($words as $word)
					{
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
						$wheres2 	= array();
						$wheres2[] 	= 'pro.name LIKE '.$word;
						$wheres2[] 	= 'pro.staffel_id LIKE '.$word;


						$wheresproject[] 	= implode( ' OR ', $wheres2 );
					}
				}


				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				$whereteam = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheresteam ) . ')';
				$wheresperson = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheresperson ) . ')';
				$wheresplayground = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheresplayground ) . ')';
                $wheresproject = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheresproject ) . ')';
				break;


		}


		$rows = array();

		if ( $search_clubs )
		{
			$query = "SELECT 'Club' as section, c.name AS title,"
			." c.founded AS created,"
			." c.country,"
			." c.logo_big AS picture,"
			." CONCAT( 'Address: ',c.address,' ',c.zipcode,' ',c.location,' Phone: ',c.phone,' Fax: ',c.fax,' E-Mail: ',c.email,' Vereinsnummer: ',c.unique_id ) AS text,"
			." pt.project_id AS project,"
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=clubinfo&cid=', c.id,'&p=', pt.project_id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_club AS c"
			." LEFT JOIN #__joomleague_team AS t"
			." ON c.id = t.club_id"
			." LEFT JOIN #__joomleague_project_team AS pt"
			." ON pt.team_id = t.id"
			." WHERE ( ".$where." ) "
			." GROUP BY c.name ORDER BY c.name";

			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;
		}

		if ( $search_teams )
		{
			$query = "SELECT 'Team' as section, t.name AS title,"
			." t.checked_out_time AS created,"
			//." t.notes AS text,"
            ." CONCAT( 'Teamart:',t.info , ' Notes:', t.notes ) AS text,"
			." pt.project_id AS project, "
			." pt.picture AS picture, "
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=teaminfo&tid=', t.id,'&p=', pt.project_id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_team AS t"
			." LEFT JOIN #__joomleague_project_team AS pt"
			." ON pt.team_id = t.id"
			." WHERE ( ".$whereteam." ) "
			." GROUP BY t.name ORDER BY t.name";

			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;
		}


		if ( $search_players )
		{

			$query = "SELECT 'Person' as section, REPLACE(CONCAT(pe.firstname, ' \'', pe.nickname, '\' ' , pe.lastname ),'\'\'','') AS title,"
			." pe.birthday AS created,"
			." pe.country,"
			." pe.picture AS picture, "
			." CONCAT( 'Birthday:',pe.birthday , ' Notes:', pe.notes ) AS text,"
			." pt.project_id AS project,"
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=player&pid=', pe.id,'&p=', pt.project_id, '&tid=', pt.team_id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_person AS pe"
			." LEFT JOIN #__joomleague_team_player AS tp"
			." ON tp.person_id = pe.id"
			." LEFT JOIN #__joomleague_project_team AS pt"
			." ON pt.id = tp.projectteam_id"
			." WHERE ( ".$wheresperson." ) "
			." AND pe.published = '1' "
			." GROUP BY pe.lastname, pe.firstname, pe.nickname ORDER BY pe.lastname,pe.firstname,pe.nickname";

			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;

		}

		if ( $search_staffs )
		{

			$query = "SELECT 'Staff' as section, REPLACE(CONCAT(pe.firstname, ' \'', pe.nickname, '\' ' , pe.lastname ),'\'\'','') AS title,"
			." pe.birthday AS created,"
			." pe.country,"
			." pe.picture AS picture, "
			." CONCAT( 'Birthday:',pe.birthday , ' Notes:', pe.notes ) AS text,"
			." pt.project_id AS project,"
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=staff&pid=', pe.id,'&p=', pt.project_id, '&tid=', pt.team_id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_person AS pe"
			." LEFT JOIN #__joomleague_team_staff AS ts"
			." ON ts.person_id = pe.id"
			." LEFT JOIN #__joomleague_project_team AS pt"
			." ON pt.id = tp.projectteam_id"
			." WHERE ( ".$wheresperson." ) "
			." AND pe.published = '1' "
			." GROUP BY pe.lastname, pe.firstname, pe.nickname ORDER BY pe.lastname,pe.firstname,pe.nickname";

			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;

		}

		if ( $search_referees )
		{

			$query = "SELECT 'Referee' as section, REPLACE(CONCAT(pe.firstname, ' \'', pe.nickname, '\' ' , pe.lastname ),'\'\'','') AS title,"
			." pe.birthday AS created,"
			." pe.country,"
			." pe.picture AS picture, "
			." CONCAT( 'Birthday:', pe.birthday, ' Notes:', pe.notes ) AS text,"
			." pr.project_id AS project,"
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=referee&pid=', pe.id,'&p=', pr.project_id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_person AS pe"
			." LEFT JOIN #__joomleague_project_referee AS pr"
			." ON pr.person_id = pe.id"
			." WHERE ( ".$wheresperson." ) "
			." AND pe.published = '1' "
			." GROUP BY pe.lastname, pe.firstname, pe.nickname ORDER BY pe.lastname,pe.firstname,pe.nickname";
			
			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;

		}

		if ( $search_playgrounds )
		{

			$query = "SELECT 'Playground' as section, pl.name AS title,"
			." pl.checked_out_time AS created,"
			." pl.country,"
			." pl.picture AS picture, "
			." pl.notes AS text,"
			." r.project_id AS project,"
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=playground&pgid=', pl.id,'&p=', r.project_id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_playground AS pl"
			." LEFT JOIN #__joomleague_club AS c"
			." ON c.id = pl.club_id"
			." LEFT JOIN #__joomleague_match AS m"
			." ON m.playground_id = pl.id"
			." LEFT JOIN #__joomleague_round AS r"
			." ON m.round_id = r.id"
			." WHERE ( ".$wheresplayground." ) "
			." GROUP BY pl.name ORDER BY pl.name ";

			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;
		}
        
        if ( $search_projects )
		{
        $query = "SELECT 'Project' as section, pro.name AS title,"
			." pro.checked_out_time AS created,"
			." l.country,"
			." pro.picture AS picture, "
			." CONCAT( pro.name, ' Staffel-ID (', pro.staffel_id, ')' ) AS text,"
			." pro.id AS project,"
			." CONCAT( 'index.php?option=com_joomleague"
			."&view=ranking&type=', '0','&p=', pro.id ) AS href,"
			." '2' AS browsernav"
			." FROM #__joomleague_project AS pro"
			." LEFT JOIN #__joomleague_league AS l"
			." ON l.id = pro.league_id"
			." WHERE ( ".$wheresproject." ) "
			." GROUP BY pro.name ORDER BY pro.name ";

			$db->setQuery( $query );
			$list = $db->loadObjectList();
			$rows[] = $list;
        }

		$results = array();

		if(count($rows))
		{
		  foreach($rows as $row)
			{
			if ( $row )
			{
			foreach($row as $output )
			{
			//echo 'country<pre>'.print_r($output->country,true).'</pre><br>';
			//echo 'picture<pre>'.print_r($output->picture,true).'</pre><br>';
			if ( $output->country)
			{
			$flag = Countries::getCountryFlag($output->country);
			$output->flag = $flag;
			$output->text = $flag.' '.$output->text ;
			}
      if ( $output->picture )
			{
			$output->text = '<p><img style="float: left;" src="'.$output->picture.'" alt="" width="50" height="" >'.$output->text.'</p>';
			}
			}
			}
			}
			
			foreach($rows as $row)
			{
			// diddipoeler
			// testausgabe
			//echo '<pre>'.print_r($row,true).'</pre><br>';
				$results = array_merge($results, (array) $row);
			}
		}
		return $results;
	}
}
?>