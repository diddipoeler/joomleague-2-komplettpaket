<?php
/**
 * @version		$Id$
 * @package		JoomLeague 1.5
 * @copyright	Copyright (C) JoomLeague. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * provides html code snippets for the views
 * @author julienV
 */
class JoomleagueHelperHtml {


	/**
	 * Return formated match time
	 *
	 * @param object $game
	 * @param array $config
	 * @param array $overallconfig
	 * @param object $project
	 * @return string html
	 */
	public static function showMatchTime(&$game,&$config,&$overallconfig,&$project)
	// overallconfig could be deleted here and replaced below by config as both array were merged in view.html.php
	{
		$output='';

		if (!isset($overallconfig['time_format'])) {
			$overallconfig['time_format']='H:i';
		}
		$timeSuffix=JText::_('COM_JOOMLEAGUE_GLOBAL_CLOCK');
		if ($timeSuffix=='COM_JOOMLEAGUE_GLOBAL_CLOCK') {
			$timeSuffix='%1$s&nbsp;h';
		}

		if (strtotime($game->match_date))
		{
			$matchTime = JHTML::date($game->match_date,$overallconfig['time_format'],'UTC');

			if ($config['show_time_suffix'] == 1)
			{
				$output .= sprintf($timeSuffix,$matchTime);
			}
			else
			{
				$output .= $matchTime;
			}
				
			$config['mark_now_playing']=(isset($config['mark_now_playing'])) ? $config['mark_now_playing'] : 0;

			if ($config['mark_now_playing'])
			{
				$thistime=mktime();
				$time_to_ellapse=($project->halftime * ($project->game_parts - 1)) + $project->game_regular_time;
				if ($project->allow_add_time == 1 && ($game->team1_result == $game->team2_result))
				{
					$time_to_ellapse += $project->add_time;
				}
				$time_to_ellapse=$time_to_ellapse * 60;
				$mydate=preg_split("/-| |:/",$game->match_date);
				$match_stamp=mktime($mydate[3],$mydate[4],$mydate[5],$mydate[1],$mydate[2],$mydate[0]);
				if ($thistime >= $match_stamp && $match_stamp + $time_to_ellapse >= $thistime)
				{
					$match_begin=$output.' ';
					$title=str_replace('%STARTTIME%',$match_begin,trim(htmlspecialchars($config['mark_now_playing_alt_text'])));
					$title=str_replace('%ACTUALTIME%',self::mark_now_playing($thistime,$match_stamp,$config,$project),$title);
					$styletext='';
					if (isset($config['mark_now_playing_blink']) && $config['mark_now_playing_blink'])
					{
						$styletext=' style="text-decoration:blink"';
					}
					$output='<b><i><acronym title="'.$title.'"'.$styletext.'>';
					$output .= $config['mark_now_playing_text'];
					$output .= '</acronym></i></b>';
				}
			}
		}
		else
		{
			$matchTime='--&nbsp;:&nbsp;--';
			if ($config['show_time_suffix'])
			{
				$output .= sprintf($timeSuffix,$matchTime);
			}
			else
			{
				$output .= $matchTime;
			}
		}

		return $output;
	}

	/**
	 * prints teams names and divisions...
	 *
	 * @param object $hometeam
	 * @param object $guestteam
	 * @param array $config
	 * @return string html
	 */
	public function showDivisonRemark(&$hometeam,&$guestteam,&$config)
	{
		$output='';
		if ($config['switch_home_guest']) {
			$tmpteam =& $hometeam; $hometeam =& $guestteam; $guestteam =& $tmpteam;
		}
		if	((isset($hometeam) && $hometeam->division_id > 0) && (isset($guestteam) && $guestteam->division_id > 0))
		{
			//TO BE FIXED: Where is spacer defined???
			if (! isset($config['spacer'])) {
				$config['spacer']='/';
			}

			$nametype='division_'.$config['show_division_name'];

			if ($config['show_division_link'])
			{
				$link=JoomleagueHelperRoute::getRankingRoute($this->project->id,null,null,null,0,$hometeam->division_id);
				$output .= JHTML::link($link,$hometeam->$nametype);
			}
			else
			{
				$output .= $hometeam->$nametype;
			}

			if ($hometeam->division_id != $guestteam->division_id)
			{
				$output .= $config['spacer'];

				if ($config['show_division_link'] == 1)
				{
					$link=JoomleagueHelperRoute::getRankingRoute($this->project->id,null,null,null,0,$guestteam->division_id);
					$output .= JHTML::link($link,$guestteam->$nametype);
				}
				else
				{
					$output .= $guestteam->$nametype;
				}
			}
		}
		else
		{
			$output .= '&nbsp;';
		}
		return $output;
	}

	/**
	 * Shows matchday title
	 *
	 * @param string $title
	 * @param int $current_round
	 * @param array $config
	 * @param int $mode
	 * @return string html
	 */
	function showMatchdaysTitle($title,$current_round,&$config,$mode=0)
	{
		$projectid=JRequest::getInt('p',0);
		$joomleague =& JTable::getInstance('Project','Table');
		$joomleague->load($projectid);

		echo ($title != '') ? $title.' - ' : $title;
		if ($current_round > 0)
		{
			$thisround =& JTable::getInstance('Round','Table');
			$thisround->load($current_round);

			if ($config['type_section_heading'] == 1 && $thisround->name != '')
			{
				if ($mode == 1)
				{
					$link=JoomleagueHelperRoute::getRankingRoute($projectid,$thisround->id);
					echo JHTML::link($link,$thisround->name);
				}
				else
				{
					echo $thisround->name;
				}
			}
			elseif ($thisround->id > 0)
			{
				echo ' - '.$thisround->id.'. '.JText::_('COM_JOOMLEAGUE_RESULTS_MATCHDAY').'&nbsp;';
			}

			if ($config['show_rounds_dates'] == 1)
			{
				echo " (";
				if (! strstr($thisround->round_date_first,"0000-00-00"))
				{
					echo JHTML::date($thisround->round_date_first, 'COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE');
				}
				if (($thisround->round_date_last != $thisround->round_date_first) &&
				(! strstr($thisround->round_date_last,"0000-00-00")))
				{
					echo " - ".JHTML::date($thisround->round_date_last, 'COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE');
				}
				echo ")";
			}
		}
	}

	function getRoundSelectNavigation($form)
	{
		$rounds = $this->get('RoundOptions');
		$division = JRequest::getInt('division',0);

		if($form){
			$currenturl=JoomleagueHelperRoute::getResultsRoute($this->project->slug, $this->roundid, $division);
			$options=array();
			foreach ($rounds as $r)
			{
				$link=JoomleagueHelperRoute::getResultsRoute($this->project->slug, $r->value, $division);
				$options[]=JHTML::_('select.option', $link, $r->text);
			}
		} else {
			$currenturl=JoomleagueHelperRoute::getResultsRoute($this->project->slug, $this->roundid, $division);
			$options=array();
			foreach ($rounds as $r)
			{
				$link=JoomleagueHelperRoute::getResultsRoute($this->project->slug, $r->value, $division);
				$options[]=JHTML::_('select.option', $link, $r->text);
			}
		}
		return JHTML::_('select.genericlist',$options,'select-round','onchange="joomleague_changedoc(this);"','value','text',$currenturl);
	}

	/**
	 * display match playground
	 *
	 * @param object $game
	 */
	function showMatchPlayground(&$game)
	{
		if (($this->config['show_playground'] || $this->config['show_playground_alert']) && isset($game->playground_id))
		{
			if (empty($game->playground_id)){
				$game->playground_id=$this->teams[$game->projectteam1_id]->standard_playground;
			}
			if (empty($game->playground_id))
			{
				$cinfo =& JTable::getInstance('Club','Table');
				$cinfo->load($this->teams[$game->projectteam1_id]->club_id);
				$game->playground_id=$cinfo->standard_playground;
				$this->teams[$game->projectteam1_id]->standard_playground=$cinfo->standard_playground;
			}

			if (!$this->config['show_playground'] && $this->config['show_playground_alert'])
			{
				if ($this->teams[$game->projectteam1_id]->standard_playground==$game->playground_id)
				{
					echo '-';
					return '';
				}
			}

			$boldStart	= '';
			$boldEnd	= '';
			$toolTipTitle	= JText::_('COM_JOOMLEAGUE_PLAYGROUND_MATCH');
			$toolTipText	= '';
			$playgroundID	= $this->teams[$game->projectteam1_id]->standard_playground;

			if (($this->config['show_playground_alert']) && ($this->teams[$game->projectteam1_id]->standard_playground!=$game->playground_id))
			{
				$boldStart		= '<b style="color:red; ">';
				$boldEnd		= '</b>';
				$toolTipTitle	= JText::_('COM_JOOMLEAGUE_PLAYGROUND_NEW');
				$playgroundID	= $this->teams[$game->projectteam1_id]->standard_playground;
			}

			$pginfo =& JTable::getInstance('Playground','Table');
			$pginfo->load($game->playground_id);

			$toolTipText	.= $pginfo->name.'&lt;br /&gt;';
			$toolTipText	.= $pginfo->address.'&lt;br /&gt;';
			$toolTipText	.= $pginfo->zipcode.' '.$pginfo->city. '&lt;br /&gt;';

			$link=JoomleagueHelperRoute::getPlaygroundRoute($this->project->id,$game->playground_id);
			$playgroundName=($this->config['show_playground_name'] == 'name') ? $pginfo->name : $pginfo->short_name;
			?>
<span class='hasTip'
	title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'> 
	<?php	echo JHTML::link($link,$boldStart.$playgroundName.$boldEnd); ?> </span>

	<?php
		}
	}

	/**
	 * mark currently playing game
	 *
	 * @param int $thistime
	 * @param int $match_stamp
	 * @param array $config
	 * @param object $project
	 * @return string
	 */
	function mark_now_playing($thistime,$match_stamp,&$config,&$project)
	{
		$whichpart=1;
		$gone_since_begin=intval(($thistime - $match_stamp)/60);
		$parts_time=intval($project->game_regular_time / $project->game_parts);
		if ($project->allow_add_time) {
			$overtime=1;
		}else{$overtime=0;
		}
		$temptext=JText::_('COM_JOOMLEAGUE_RESULTS_LIVE_WRONG');
		for ($temp_count=1; $temp_count <= $project->game_parts+$overtime; $temp_count++)
		{
			$this_part_start=(($temp_count-1) * ($project->halftime + $parts_time));
			$this_part_end=$this_part_start + $parts_time;
			$next_part_start=$this_part_end + $project->halftime;
			if ($gone_since_begin >= $this_part_start && $gone_since_begin <= $this_part_end)
			{
				$temptext=str_replace('%PART%',$temp_count,trim(htmlspecialchars($config['mark_now_playing_alt_actual_time'])));
				$temptext=str_replace('%MINUTE%',($gone_since_begin+1 - ($temp_count-1)*$project->halftime),$temptext);
				break;
			}
			elseif ($gone_since_begin > $this_part_end && $gone_since_begin < $next_part_start)
			{
				$temptext=str_replace('%PART%',$temp_count,trim(htmlspecialchars($config['mark_now_playing_alt_actual_break'])));
				break;
			}
		}
		return $temptext;
	}

	/**
	 * return thumb up/down image url if team won/loss
	 *
	 * @param object $game
	 * @param int $projectteam_id
	 * @param array attributes
	 * @return string image html code
	 */
	public function getThumbUpDownImg($game, $projectteam_id, $attributes = null)
	{
		$res = JoomleagueHelper::getTeamMatchResult($game, $projectteam_id);
		if ($res === false) {
			return false;
		}
		
		if ($res == 0) 
		{
			$img = 'media/com_joomleague/jl_images/draw.png';
			$alt = JText::_('COM_JOOMLEAGUE_DRAW');
			$title = $alt;
		}
		else if ($res < 0) 
		{
			$img = 'media/com_joomleague/jl_images/thumbs_down.png';
			$alt = JText::_('COM_JOOMLEAGUE_LOST');
			$title = $alt;
		}
		else 
		{
			$img = 'media/com_joomleague/jl_images/thumbs_up.png';
			$alt = JText::_('COM_JOOMLEAGUE_WON');
			$title = $alt;
		}
		
		// default title attribute, if not specified in passed attributes
		$def_attribs = array('title' => $title);
		if ($attributes) {
			$attributes = array_merge($def_attribs, $attributes);
		}
		else {
			$attributes = $def_attribs;
		}
		
		return JHTML::image($img, $alt, $attributes);	
	}
	/**
	* return thumb up/down image as link with score as title
	*
	* @param object $game
	* @param int $projectteam_id
	* @param array attributes
	* @return string linked image html code
	*/
	public function getThumbScore($game, $projectteam_id, $attributes = null)
	{		
		if (!$img = self::getThumbUpDownImg($game, $projectteam_id, $attributes = null)) {
			return false;
		}
		$txt = $teams[$game->projectteam1_id]->name.' - '.$teams[$game->projectteam2_id]->name.' '.$game->team1_result.' - '. $game->team2_result;
		
		$attribs = array('title' => $txt);
		if (is_array($attributes)) {
			$attribs = array_merge($attributes, $attribs);
		}
		$url = JRoute::_(JoomleagueHelperRoute::getMatchReportRoute($game->project_slug, $game->slug));
		return JHTML::link($url, $img);
	}
	
	/**
	* return up/down image for ranking
	*
	* @param object $team (rank)
	* @param object $previous (rank)
	* @param int $ptid
	* @return string image html code
	*/	
	function getLastRankImg($team,$previous,$ptid,$attributes = null)
	{
		if ( isset( $previous[$ptid]->rank ) )
		{
			$imgsrc = JURI::root() . 'media/com_joomleague/jl_images/';
			if ( ( $team->rank == $previous[$ptid]->rank ) || ( $previous[$ptid]->rank == "" ) )
			{
				$imgsrc .= "same.png";
				$alt	 = JText::_('COM_JOOMLEAGUE_RANKING_SAME');
				$title	 = $alt;
			}
			elseif ( $team->rank < $previous[$ptid]->rank )
			{
				$imgsrc .= "up.png";
				$alt	 = JText::_('COM_JOOMLEAGUE_RANKING_UP');
				$title	 = $alt;
			}
			elseif ( $team->rank > $previous[$ptid]->rank )
			{
				$imgsrc .= "down.png";
				$alt	 = JText::_('COM_JOOMLEAGUE_RANKING_DOWN');
				$title	 = $alt;
			}
			
		$def_attribs = array('title' => $title);
		if ($attributes) {
			$attributes = array_merge($def_attribs, $attributes);
		}
		else {
			$attributes = $def_attribs;
		}
		return JHTML::image($imgsrc,$alt,$attributes);
		}
		
	}

    public static function printColumnHeadingSortAllTimeRanking( $columnTitle, $paramName, $config = null, $default="DESC" )
	{
		$output = "";
		$img='';
		if ( $config['column_sorting'] || $config == null)
		{
			$params = array(
					"option" => "com_joomleague",
					"view"   => JRequest::getVar("view", "rankingalltime"),
					"p" => JRequest::getInt( "p", 0 ),
                    "l" => JRequest::getInt( "l", 0 ),
					"r" => JRequest::getInt( "r", 0 ),
                    "points" => JRequest::getVar( "points", "" ),
					"type" => JRequest::getVar( "type", "" ) );
	
			if ( JRequest::getVar( 'order', '' ) == $paramName )
			{
				$params["order"] = $paramName;
				$params["dir"] = ( JRequest::getVar( 'dir', '') == 'ASC' ) ? 'DESC' : 'ASC';
				$imgname = 'sort'.(JRequest::getVar( 'dir', '') == 'ASC' ? "02" :"01" ).'.gif';
				$img = JHTML::image(
										'media/com_joomleague/jl_images/' . $imgname,
				$params["dir"] );
			}
			else
			{
				$params["order"] = $paramName;
				$params["dir"] = $default;
			}
			$query = JURI::buildQuery( $params );
			echo JHTML::link(
			JRoute::_( "index.php?".$query ),
			JText::_($columnTitle),
			array( "class" => "jl_rankingheader" ) ).$img;
		}
		else
		{
			echo JText::_($columnTitle);
		}
	}
    
	public static function printColumnHeadingSort( $columnTitle, $paramName, $config = null, $default="DESC" )
	{
		$output = "";
		$img='';
		if ( $config['column_sorting'] || $config == null)
		{
			$params = array(
					"option" => "com_joomleague",
					"view"   => JRequest::getVar("view", "ranking"),
					"p" => JRequest::getInt( "p", 0 ),
					"r" => JRequest::getInt( "r", 0 ),
					"type" => JRequest::getVar( "type", "" ) );
	
			if ( JRequest::getVar( 'order', '' ) == $paramName )
			{
				$params["order"] = $paramName;
				$params["dir"] = ( JRequest::getVar( 'dir', '') == 'ASC' ) ? 'DESC' : 'ASC';
				$imgname = 'sort'.(JRequest::getVar( 'dir', '') == 'ASC' ? "02" :"01" ).'.gif';
				$img = JHTML::image(
										'media/com_joomleague/jl_images/' . $imgname,
				$params["dir"] );
			}
			else
			{
				$params["order"] = $paramName;
				$params["dir"] = $default;
			}
			$query = JURI::buildQuery( $params );
			echo JHTML::link(
			JRoute::_( "index.php?".$query ),
			JText::_($columnTitle),
			array( "class" => "jl_rankingheader" ) ).$img;
		}
		else
		{
			echo JText::_($columnTitle);
		}
	}
	
	public static function nextLastPages( $url, $text, $maxentries, $limitstart = 0, $limit = 10 )
	{
		$latestlimitstart = 0;
		if ( intval( $limitstart - $limit ) > 0 )
		{
			$latestlimitstart = intval( $limitstart - $limit );
		}
		$nextlimitstart = 0;
		if ( ( $limitstart + $limit ) < $maxentries )
		{
			$nextlimitstart = $limitstart + $limit;
		}
		$lastlimitstart = ( $maxentries - ( $maxentries % $limit ) );
		if ( ( $maxentries % $limit ) == 0 )
		{
			$lastlimitstart = ( $maxentries - ( $maxentries % $limit ) - $limit );
		}
	
		echo '<center>';
		echo '<table style="width: 50%; align: center;" cellspacing="0" cellpadding="0" border="0">';
		echo '<tr>';
		echo '<td style="width: 10%; text-align: left;" nowrap="nowrap">';
		if ( $limitstart > 0 )
		{
			$query = JURI::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => 0 ) );
			echo JHTML::link( $url.$query, '&lt;&lt;&lt;' );
			echo '&nbsp;&nbsp;&nbsp';
			$query = JURI::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => $latestlimitstart ) );
			echo JHTML::link( $url.$query, '&lt;&lt;' );
			echo '&nbsp;&nbsp;&nbsp;';
		}
		echo '</td>';
		echo '<td style="text-align: center;" nowrap="nowrap">';
		$players_to = $maxentries;
		if ( ( $limitstart + $limit ) < $maxentries )
		{
			$players_to = ( $limitstart + $limit );
		}
		echo sprintf( $text, $maxentries, ($limitstart+1).' - '.$players_to );
		echo '</td>';
		echo '<td style="width: 10%; text-align: right;" nowrap="nowrap">';
		if ( $nextlimitstart > 0 )
		{
			echo '&nbsp;&nbsp;&nbsp;';
			$query = JURI::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => $nextlimitstart ) );
			echo JHTML::link( $url.$query, '&gt;&gt;' );
			echo '&nbsp;&nbsp;&nbsp';
			$query = JURI::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => $lastlimitstart ) );
			echo JHTML::link( $url.$query, '&gt;&gt;&gt;' );
		}
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</center>';
	}
	
}
