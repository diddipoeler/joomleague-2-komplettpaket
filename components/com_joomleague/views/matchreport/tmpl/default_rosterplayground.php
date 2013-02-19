<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( $this->show_debug_info )
{
echo 'this->formation1<br /><pre>~' . print_r($this->formation1,true) . '~</pre><br />';
echo 'this->formation2<br /><pre>~' . print_r($this->formation2,true) . '~</pre><br />';
echo 'this->extended2<br /><pre>~' . print_r($this->extended2,true) . '~</pre><br />';

echo 'this->schemahome<br /><pre>~' . print_r($this->schemahome,true) . '~</pre><br />';
echo 'this->schemaaway<br /><pre>~' . print_r($this->schemaaway,true) . '~</pre><br />';

echo 'this->matchplayerpositions<br /><pre>~' . print_r($this->matchplayerpositions,true) . '~</pre><br />';
echo 'this->matchplayers<br /><pre>~' . print_r($this->matchplayers,true) . '~</pre><br />';
echo 'this->match<br /><pre>~' . print_r($this->match,true) . '~</pre><br />';

echo 'this->overallconfig<br /><pre>~' . print_r($this->overallconfig,true) . '~</pre><br />';
echo 'this->config<br /><pre>~' . print_r($this->config,true) . '~</pre><br />';
}



$favteams1 = explode(",",$this->project->fav_team);
$favteams = array();

for ($a=0; $a < sizeof($favteams1);$a++ )
{
$favteams[$favteams1[$a]] = $favteams1[$a];
}


?>

<div class="flash">
<table align="center" style="width: 100% ;" border="0">
<tr>
<td colspan="5" align="center">
<?php


// diddipoeler schema der mannschaften
$schemahome = $this->formation1;
$schemaguest = $this->formation2;

$backgroundimage = 'media/com_joomleague/rosterground/spielfeld_578x1050.png';

list($width, $height, $type, $attr) = getimagesize($backgroundimage);

echo "<div style=\"background-image:url('".$backgroundimage."');background-position:left;position:relative;height:".$height."px;width:".$width."px;\">";

if ( $this->config['roster_playground_calculation'] == 0 )
{
// positionen aus der rostertabelle benutzen
?>

<table class="taktischeaufstellung" summary="Taktische Aufstellung">
<tr>

</tr>
<tr>
<td>

<?PHP
// die logos
?>

<div style="position:absolute; width:103px; left:0px; top:0px; text-align:center;">
<a href="<?php echo $this->team1_club->logo_big;?>" alt="<?php echo $this->team1_club->name;?>" title="<?php echo $this->team1_club->name;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_team_picture_width']; ?>px;" src="<?PHP echo $this->team1_club->logo_big; ?>" alt="" /><br />
</a>
</div>
<div style="position:absolute; width:103px; left:0px; top:950px; text-align:center;">
<a href="<?php echo $this->team2_club->logo_big;?>" alt="<?php echo $this->team2_club->name;?>" title="<?php echo $this->team2_club->name;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_team_picture_width']; ?>px;" src="<?PHP echo $this->team2_club->logo_big; ?>" alt="" /><br />
</a>
</div>

<?PHP
// hometeam
$testlauf = 0;
foreach ($this->matchplayerpositions as $pos)
		{
			$personCount=0;
			foreach ($this->matchplayers as $player)
			{
				if ($player->pposid == $pos->pposid)
				{
					$personCount++;
				}
			}

if ($personCount > 0)
{

foreach ($this->matchplayers as $player)
{

if ( $player->pposid == $pos->pposid && $player->ptid == $this->match->projectteam1_id )
{

$picture = $player->ppic;
if ( !file_exists( $picture ) )
{
$picture = JoomleagueHelper::getDefaultPlaceholder("player");
}

?>

<div style="position:absolute; width:103px; left:<?PHP echo $this->schemahome[$schemahome][$testlauf]['heim']['links']; ?>px; top:<?PHP echo $this->schemahome[$schemahome][$testlauf]['heim']['oben']; ?>px; text-align:center;">
<a href="<?php echo $picture;?>" alt="<?php echo $player->lastname;?>" title="<?php echo $player->lastname;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_player_picture_width']; ?>px; " src="<?PHP echo $picture; ?>" alt="" /><br />
</a>
<font color="white"><a class="link" href=""><?PHP echo $player->lastname." ".$player->firstname; ?></a></font>
</div>
                                      
<?PHP
$testlauf++;
}

}

}

}

// guestteam
$testlauf = 0;
foreach ($this->matchplayerpositions as $pos)
		{
			$personCount=0;
			foreach ($this->matchplayers as $player)
			{
				if ($player->pposid == $pos->pposid)
				{
					$personCount++;
				}
			}

if ($personCount > 0)
{			

foreach ($this->matchplayers as $player)
{

if ( $player->pposid == $pos->pposid && $player->ptid == $this->match->projectteam2_id )
{

$picture = $player->ppic;
if ( !file_exists( $picture ) )
{
$picture = JoomleagueHelper::getDefaultPlaceholder("player");
}

?>

<div style="position:absolute; width:103px; left:<?PHP echo $this->schemaaway[$schemaguest][$testlauf]['gast']['links']; ?>px; top:<?PHP echo $this->schemaaway[$schemaguest][$testlauf]['gast']['oben']; ?>px; text-align:center;">
<a href="<?php echo $picture;?>" alt="<?php echo $player->lastname;?>" title="<?php echo $player->lastname;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_player_picture_width']; ?>px;" src="<?PHP echo $picture; ?>" alt="" /><br />
</a>
<font color="white"><a class="link" href=""><?PHP echo $player->lastname." ".$player->firstname; ?></a></font>
</div>
                                      
<?PHP
$testlauf++;
}

}

}

}	
?>

</td>
</tr>
</table>

<?PHP 
}
else
{
// automatische berechnung
$witdhplayground = 578;

?>
<table class="taktischeaufstellung" summary="Taktische Aufstellung">
<tr>
</tr>
<tr>
<td>
<?PHP
// die logos
?>

<div style="position:absolute; width:103px; left:0px; top:0px; text-align:center;">
<a href="<?php echo $this->team1_club->logo_big;?>" alt="<?php echo $this->team1_club->name;?>" title="<?php echo $this->team1_club->name;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_team_picture_width']; ?>px;" src="<?PHP echo $this->team1_club->logo_big; ?>" alt="" /><br />
</a>
</div>
<div style="position:absolute; width:103px; left:0px; top:950px; text-align:center;">
<a href="<?php echo $this->team2_club->logo_big;?>" alt="<?php echo $this->team2_club->name;?>" title="<?php echo $this->team2_club->name;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_team_picture_width']; ?>px;" src="<?PHP echo $this->team2_club->logo_big; ?>" alt="" /><br />
</a>
</div>

<?PHP
// hometeam
$testlauf = 0;
$personCount = array();
foreach ($this->matchplayerpositions as $pos)
		{
			switch ($testlauf)
      {
      case 0:
      $pos->startposition = $this->config['roster_playground_start_position_home_goalkeeper'];
      break;
      case 1:
      $pos->startposition = $this->config['roster_playground_start_position_home_defender'];
      break;
      case 2:
      $pos->startposition = $this->config['roster_playground_start_position_home_midfield'];
      break;
      case 3:
      $pos->startposition = $this->config['roster_playground_start_position_home_forward'];
      break;
      }
			foreach ($this->matchplayers as $player)
			{
				if ($player->pposid == $pos->pposid && $player->ptid == $this->match->projectteam1_id)
				{
					$personCount[$pos->pposid] = $personCount[$pos->pposid] + 1;
				}
			}
      $testlauf++;
     }

foreach ($this->matchplayerpositions as $pos)
{
$testlauf = 0;
if ( $personCount[$pos->pposid] > 0 )
{

/*
$startleft = ( $witdhplayground - ( $personCount[$pos->pposid] * $this->config['roster_playground_player_picture_width'] )
+ ( ( $personCount[$pos->pposid] - 1 ) * $this->config['roster_playground_player_picture_spacing'] ) ) / 2;     
*/
$startleft = ( $personCount[$pos->pposid] * $this->config['roster_playground_player_picture_width'] );
$startleft += ( $personCount[$pos->pposid] ) * $this->config['roster_playground_player_picture_spacing'];
$startleft = ( $witdhplayground - $startleft ) / 2;
foreach ($this->matchplayers as $player)
{

if ( $player->pposid == $pos->pposid && $player->ptid == $this->match->projectteam1_id )
{

$picture = $player->ppic;
if ( !file_exists( $picture ) )
{
$picture = JoomleagueHelper::getDefaultPlaceholder("player");
}

switch ($testlauf)
      {
      case 0:
      $left = $startleft;
      break;
      default:
      $left += $this->config['roster_playground_player_picture_width'];
      $left += $this->config['roster_playground_player_picture_spacing'];
      break;
      }

$top = $pos->startposition;

?>

<div style="position:absolute; width:103px; left:<?PHP echo $left; ?>px; top:<?PHP echo $top; ?>px; text-align:center;">
<a href="<?php echo $picture;?>" alt="<?php echo $player->lastname;?>" title="<?php echo $player->lastname;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_player_picture_width']; ?>px; " src="<?PHP echo $picture; ?>" alt="" />
</a>
<br />
<font color="white"><a class="link" href=""><?PHP echo $player->lastname." ".$player->firstname; ?></a></font>
</div>
                                      
<?PHP
$testlauf++;
}

}

}

}

// away team
$testlauf = 0;
$personCount = array();
foreach ($this->matchplayerpositions as $pos)
		{
			switch ($testlauf)
      {
      case 0:
      $pos->startposition = $this->config['roster_playground_start_position_away_goalkeeper'];
      break;
      case 1:
      $pos->startposition = $this->config['roster_playground_start_position_away_defender'];
      break;
      case 2:
      $pos->startposition = $this->config['roster_playground_start_position_away_midfield'];
      break;
      case 3:
      $pos->startposition = $this->config['roster_playground_start_position_away_forward'];
      break;
      }
			foreach ($this->matchplayers as $player)
			{
				if ($player->pposid == $pos->pposid && $player->ptid == $this->match->projectteam2_id)
				{
					$personCount[$pos->pposid] = $personCount[$pos->pposid] + 1;
				}
			}
      $testlauf++;
     }

foreach ($this->matchplayerpositions as $pos)
{
$testlauf = 0;
if ( $personCount[$pos->pposid] > 0 )
{

/*
$startleft = ( $witdhplayground - ( $personCount[$pos->pposid] * $this->config['roster_playground_player_picture_width'] )
+ ( ( $personCount[$pos->pposid] - 1 ) * $this->config['roster_playground_player_picture_spacing'] ) ) / 2;     
*/
$startleft = ( $personCount[$pos->pposid] * $this->config['roster_playground_player_picture_width'] );
$startleft += ( $personCount[$pos->pposid] ) * $this->config['roster_playground_player_picture_spacing'];
$startleft = ( $witdhplayground - $startleft ) / 2;
foreach ($this->matchplayers as $player)
{

if ( $player->pposid == $pos->pposid && $player->ptid == $this->match->projectteam2_id )
{

$picture = $player->ppic;
if ( !file_exists( $picture ) )
{
$picture = JoomleagueHelper::getDefaultPlaceholder("player");
}

switch ($testlauf)
      {
      case 0:
      $left = $startleft;
      break;
      default:
      $left += $this->config['roster_playground_player_picture_width'];
      $left += $this->config['roster_playground_player_picture_spacing'];
      break;
      }

$top = $pos->startposition;

?>

<div style="position:absolute; width:103px; left:<?PHP echo $left; ?>px; top:<?PHP echo $top; ?>px; text-align:center;">
<a href="<?php echo $picture;?>" alt="<?php echo $player->lastname;?>" title="<?php echo $player->lastname;?>" class="highslide" onclick="return hs.expand(this)">
<img class="bild_s" style="width:<?PHP echo $this->config['roster_playground_player_picture_width']; ?>px; " src="<?PHP echo $picture; ?>" alt="" /><br />
</a>
<font color="white"><a class="link" href=""><?PHP echo $player->lastname." ".$player->firstname; ?></a></font>
</div>
                                      
<?PHP
$testlauf++;
}

}

}

}




?>
</td>
</tr>
</table>
<?PHP 
}                               
echo "</div>";

/*
echo 'this->matchplayerpositions<br /><pre>~' . print_r($this->matchplayerpositions,true) . '~</pre><br />';
echo 'this->personCount<br /><pre>~' . print_r($personCount,true) . '~</pre><br />';
echo 'this->matchplayers<br /><pre>~' . print_r($this->matchplayers,true) . '~</pre><br />';
*/

?>
</td>
</tr>
</table>
</div>