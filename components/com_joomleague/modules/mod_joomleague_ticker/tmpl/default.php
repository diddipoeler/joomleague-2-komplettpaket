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
	
$showdate = $params->def("showdate");
$showproject = $params->def("showproject");
$teamformat = $params->def("teamformat");
$teamseparator = $params->def("team_separator");
$resultseparator = $params->def("result_separator");
$outline = $params->def("outline");
$urlformat = $params->def("urlformat");
$itemid = $params->def("itemid");
$refresh = $params->def("refresh");
$minute = $params->def("minute");
$height = $params->def("height");
$width  = $params->def("width");

if($showproject) 
	$height +=20;
if($showdate)
	$height +=20;
if($teamformat>3){
	$height += ($outline==0 ? 50 : 10);
}

$url = JUri::base();

if ($refresh == 1)
{
	$textdiv = "<script type=\"text/javascript\" language=\"javascript\">
			var reloadTimer = null;
			window.onload = function()
			{
			    setReloadTime($minute);
		    }
			function setReloadTime(secs)
				{
				    if (arguments.length == 1) {
			        if (reloadTimer) clearTimeout(reloadTimer);
			        reloadTimer = setTimeout(\"setReloadTime()\", Math.ceil(parseFloat(secs) * 1000));
			    }
		    else {
		        location.reload();
		    }
			}
		 </script> 
<div align=\"center\"><a href=\"javascript:location.reload();\"><img src=\"modules/mod_joomleague_ticker/css/icon_refresh.gif\" border=\"0\" title=\"Refresh\">&nbsp;&nbsp;&nbsp;&nbsp;<b>Refresh</b></a></div><br>";
}
else {$textdiv = "";} 
$id = 1;
$idstring = '';
$idstring = $id.$params->get( 'moduleclass_sfx' ) ;
foreach ($matches as $match)
{
	$idstring = $id.$params->get( 'moduleclass_sfx' ) ;
	if ($mode == 'T')
	{
		$textdiv .= '<div id="'.$idstring.'" class="textdiv">';
	}

	$report_link = JoomleagueHelperRoute::getMatchReportRoute( $match->project_id, $match->match_id);

	// Decide what kind of link method to be added to the teamnames
	switch ($urlformat)
	{
		case 0:
			$urlfronthome = "";
			$urlbackhome = "";
			$urlfrontaway = "";
			$urlbackaway = "";
			break;
		case 1:
			$result_link = JoomleagueHelperRoute::getResultsRoute( $match->project_id, $match->roundid, $match->division_id);
			$urlfronthome = '<a href="'.$result_link.'">';
			$urlbackhome = "</a>";
			$urlfrontaway = '<a href="'.$result_link.'">';
			$urlbackaway = "</a>";
			break;
		case 2:
			$urlfronthome = '<a href="'.$report_link.'">';
			$urlbackhome = "</a>";
			$urlfrontaway = '<a href="'.$report_link.'">';
			$urlbackaway = "</a>";
			break;
		case 3:
			$plan_link = JoomleagueHelperRoute::getTeamPlanRoute( $match->project_id, $match->team1_id);
			$urlfronthome = '<a href="'.$plan_link.'">';
			$urlbackhome = "</a>";
			$urlfrontaway = '<a href="'.$plan_link.'">';
			$urlbackaway = "</a>";
			break;
	}

	// Decide how team name/icon will be shown
	switch ($teamformat)
	{
		//Long name
		case 0:
			$home_name = $urlfronthome.$match->home_name.$urlbackhome;
			$away_name = $urlfrontaway.$match->away_name.$urlbackaway;
			break;
		//Middle name
		case 1:
			$home_name = $urlfronthome.$match->home_middlename.$urlbackhome;
			$away_name = $urlfrontaway.$match->away_middlename.$urlbackaway;
			break;
		//Short name
		case 2:
			$home_name = $urlfronthome.$match->home_shortname.$urlbackhome;
			$away_name = $urlfrontaway.$match->away_shortname.$urlbackaway;
			break;
		//Icon
		case 3:
			$home_name = $urlfronthome.'<img src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>'.$urlbackhome;
			$away_name = $urlfrontaway.'<img src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/>'.$urlbackaway;
			break;
		//Icon + Long name (same line)
		case 4:
			if($outline==1)
				$home_name = $urlfronthome.$match->home_name.'&nbsp;<img style="vertical-align:middle" src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>'.$urlbackhome;
			else
				$home_name = $urlfronthome.'<img style="vertical-align:middle" src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>&nbsp;'.$match->home_name.$urlbackhome;
			$away_name = $urlfrontaway.'<img style="vertical-align:middle" src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/>&nbsp;'.$match->away_name.$urlbackaway;
			break;
		//Icon + Middle name (same line)
		case 5:
			if($outline==1)
				$home_name = $urlfronthome.$match->home_middlename.'&nbsp;<img style="vertical-align:middle" src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>'.$urlbackhome;
			else
				$home_name = $urlfronthome.'<img style="vertical-align:middle" src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>&nbsp;'.$match->home_middlename.$urlbackhome;
			$away_name = $urlfrontaway.'<img style="vertical-align:middle" src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/>&nbsp;'.$match->away_middlename.$urlbackaway;
			break;
		//Icon + Short name (same line)
		case 6:
			if($outline==1)
				$home_name = $urlfronthome.$match->home_shortname.'&nbsp;<img style="vertical-align:middle" src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>'.$urlbackhome;
			else
				$home_name = $urlfronthome.'<img style="vertical-align:middle" src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/>&nbsp;'.$match->home_shortname.$urlbackhome;
			$away_name = $urlfrontaway.'<img style="vertical-align:middle" src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/>&nbsp;'.$match->away_shortname.$urlbackaway;
			break;
		//Icon + Long name (2 lines)			
		case 7:
			$home_name = $urlfronthome.'<div style="width:100%"><center><img src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/></center></div><div><center>'.$match->home_name."</center></div>".$urlbackhome;
			$away_name = $urlfrontaway.'<div style="width:100%"><center><img src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/></center></div><div><center>'.$match->away_name."</center></div>".$urlbackaway;
			break;
		//Icon + middle name (2 lines)			
		case 8:
			$home_name = $urlfronthome.'<div style="width:100%"><center><img src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/></center></div><div><center>'.$match->home_middlename."</center></div>".$urlbackhome;
			$away_name = $urlfrontaway.'<div style="width:100%"><center><img src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/></center></div><div><center>'.$match->away_middlename."</center></div>".$urlbackaway;
			break;
		//Icon + short name (2 lines)			
		case 9:
			$home_name = $urlfronthome.'<div style="width:100%"><center><img src="'.$url.$match->home_icon.'" alt="'.$match->home_name.'" title="'.$match->home_name.'" border="0"/></center></div><div><center>'.$match->home_shortname."</center></div>".$urlbackhome;
			$away_name = $urlfrontaway.'<div style="width:100%"><center><img src="'.$url.$match->away_icon.'" alt="'.$match->away_name.'" title="'.$match->away_name.'" border="0"/></center></div><div><center>'.$match->away_shortname."</center></div>".$urlbackaway;
			break;
	}

	// decide how the teams/results will be shown
	$urlres = '<a href="'.$report_link.'">';
	$urlresb = "</a>";
	
	switch ($outline)
	{
		// team/result above each other
		case 0:
			$textdiv .= "<div class='qslide'>";
			if ($showproject == 1)
			{
				$textdiv .= '<div class="tckproject">' . $match->project_name . '</div>';
			}
			if ($showdate == 1)
			{
				$textdiv .= '<div class="minute" align="center">' . $date[$id] . '</div>';
			}
			$textdiv .= '</br><div style="text-align:left">';
			$textdiv .= '<div style="float:left; width:80%; overflow:auto; font-weight: bold">' . $home_name. '</div>';
			$textdiv .= '<div style="float:left; text-align:right; width:20%; font-weight: bold">' . $urlres. $match->team1_result. $urlresb . '</div>';
			$textdiv .= '<div style="float:left; width:80%; font-weight: bold"><center>' . $teamseparator . '</center></div><div style="width:20%;"></div>';
			$textdiv .= '<div style="float:left; width:80%; overflow:auto; font-weight: bold">' . $away_name . '</div>';
			$textdiv .= '<div style="float:left; text-align:right; width:20%; font-weight: bold">' . $urlres. $match->team2_result. $urlresb . '</div>';
			$textdiv .= '</div></div>';
			break;

		// Results between the two team names/icons
		case 1:
			$textdiv .= '<div class="qslide">';
			if ($showproject == 1)
			{
				$textdiv .= '<div class="tckproject"><p>' . $match->project_name . '</p></div>';
			}
			if ($showdate == 1)
			{
				$textdiv .= '<div class="tckdate"><p>' . $date[$id] . '</p></div>';
			}
			$textdiv.='<div class = "tckrow">';
			$textdiv .= '<span class="tckteamleft">' . $home_name. '</span>';
			$textdiv .= '<span class="tckresult">' . $urlres. $match->team1_result . $urlresb. '</span>';
			$textdiv .= '<span class="tckresult">' . $teamseparator . '</span>';
			$textdiv .= '<span class="tckresult">'  . $urlres. $match->team2_result. $urlresb . '</span>';
			$textdiv .= '<span class="tckteamright">'  . $away_name . '</span>';
			$textdiv .= '</div></div>';
			break;

			// Results right to the two team names/icons
		case 2:
			$textdiv .= "<div class='qslide'>";
			if ($showproject == 1)
			{
				$textdiv .= '<div class="tckproject">' . $match->project_name . '</div>';
			}
			if ($showdate == 1)
			{
				$textdiv .= '<div class="minute" align="center">' . $date[$id] . '</div>';
			}
			$textdiv .= '<div class="tckrow"><span class="tckteamleft2">' . $home_name. '</span>';
			$textdiv .= '<span class="tckresult2">' . $teamseparator . '</span>';
			$textdiv .= '<span class="tckteamright2">' . $away_name . '</span>';
			$textdiv .= '<span class="tckresult2">'. $urlres. $match->team1_result . $resultseparator. $match->team2_result . $urlresb. '</span>';
			$textdiv .= '</div></div>';
			break;
	}

	if ($mode == 'T')
	{
		$textdiv .= "</div>";
	}
	$id++;
}

switch ($mode)
{
	// ticker mode template
	case 'T':
		echo $textdiv;
		break;
			
	// list mode template
	case 'L':
		?>
	<div id="qscroller<?php echo $params->get( 'moduleclass_sfx' ); ?>" ><?php echo $textdiv; ?></div>
		<?php
		break;

	// Vertical scroll mode	template
	case 'V':
	?>
	<div id="qscroller<?php echo $params->get( 'moduleclass_sfx' ); ?>" style="width:100%;height:<?php echo $height; ?>px"></div>
	<div class="hide"><?php echo $textdiv; ?></div>
	<?php
	break;

	// Horizontal scroll mode template
	case 'H':
	?>
	<div id="qscroller<?php echo $params->get( 'moduleclass_sfx' ); ?>" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px"></div>
	<div class="hide"><?php echo $textdiv; ?></div>

	<?php
	break;
}
