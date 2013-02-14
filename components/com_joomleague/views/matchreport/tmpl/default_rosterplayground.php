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
<img class="bild_s" style="width:90px;" src="<?PHP echo $this->team1_club->logo_big; ?>" alt="" /><br />
</div>
<div style="position:absolute; width:103px; left:0px; top:950px; text-align:center;">
<img class="bild_s" style="width:90px;" src="<?PHP echo $this->team2_club->logo_big; ?>" alt="" /><br />
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
<img class="bild_s" style="width:44px; height:52px;" src="<?PHP echo $picture; ?>" alt="" /><br />
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
<img class="bild_s" style="width:44px; height:52px;" src="<?PHP echo $picture; ?>" alt="" /><br />
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
echo "</div>";

?>
</td>
</tr>
</table>
</div>