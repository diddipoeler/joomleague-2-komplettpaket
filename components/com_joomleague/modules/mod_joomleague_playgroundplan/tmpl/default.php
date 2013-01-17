<?php

/**
 * @version	 $Id: default.php zeta65$
 * @package	 Joomla
 * @subpackage  Joomleague playgroundplan module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die('Restricted access');


$teamformat=$params->get('teamformat', 'name');
$dateformat= $params->get('dateformat');
$timeformat= $params->get('timeformat');
$mode=$params->get('mode',0);
$textdiv ="";
$n=1;
//if ($mode == 0)
echo '<div id="modjlplaygroundplan'.$mode.'">';
//else if ($mode == 1)
#modjlplaygroundplan1
foreach ($list as $match)
 	{
	if ($mode == 0)
		{
		$textdiv .= '<div class="qslidejl">';
		}
if ($mode == 1)
{
$odd=$n&1;
		$textdiv .= '<div id="jlplaygroundplanis'.$odd.'" class="jlplaygroundplantextdivlist">';
		}
$n++;

if ($params->get ('show_playground_name',0)) 
{
 $textdiv.= '<div class="jlplplaneplname"> ';
if ($match->playground_id !="")
 {
 $playgroundname=  $match->playground_name;
$playground_id=$match->playground_id;
 }
 else if ($match->team_playground_id !="")
{
 $playgroundname = $match->team_playground_name;
$playground_id=$match->team_playground_id;
}
elseif ($match->club_playground_id !="")
{
 $playgroundname =  $match->club_playground_name;
$playground_id=$match->club_playground_id;
} 

if( $params->get('show_playground_link'))
{
  $link = JoomleagueHelperRoute::getPlaygroundRoute( $match->project_id, $playground_id );
		     $playgroundname= JHTML::link($link, JText::sprintf( '%1$s', $playgroundname ) );
		}
		else
		{
			$playgroundname= JText::sprintf( '%1$s', $playgroundname);
		
}
$textdiv.= $playgroundname.'</div>';
 }
$textdiv .= '<div class="jlplplanedate">';
 $textdiv .= JHTML::date( $match->match_date,$dateformat );
$textdiv .= " ".JText::_('JL_START_TIME')." ";
 $textdiv .= JHTML::date( $match->match_date,$timeformat );
 $textdiv.= '</div>';
if ($params->get ('show_project_name',0)) 
{
$textdiv .= '<div class="jlplplaneleaguename">';

$textdiv .= $match->project_name;
 $textdiv.= '</div>';
}
if ($params->get ('show_league_name',0)) 
{
$textdiv .= '<div class="jlplplaneleaguename">';

$textdiv .= $match->league_name;
 $textdiv.= '</div>';
}
$textdiv .= '<div>';
 $textdiv .= '<div class="jlplplanetname">';
if( $params->get('show_club_logo'))
{
$team1logo= modJLGPlaygroundplanHelper::getTeamLogo($match->team1);
$textdiv .= '<p>'.JHTML::image( $team1logo,"").'</p>';
}
$textdiv .= '<p>'.modJLGPlaygroundplanHelper::getTeams($match->team1,$teamformat).'</p>';
 $textdiv.= '</div>';
$textdiv .= '<div class="jlplplanetnamesep"> - </div>';
 $textdiv .= '<div class="jlplplanetname">';
if( $params->get('show_club_logo'))
{
$team2logo= modJLGPlaygroundplanHelper::getTeamLogo($match->team2);
$textdiv .= '<p>'.JHTML::image( $team2logo,"").'</p>';
}
$textdiv .= '<p>'.modJLGPlaygroundplanHelper::getTeams($match->team2,$teamformat).'</p>';
 $textdiv.= '</div>';
 $textdiv.= '</div>';
 $textdiv.= '<div style="clear:both"></div>';
 $textdiv.= '</div>';

}

 echo $textdiv;

echo '</div>';