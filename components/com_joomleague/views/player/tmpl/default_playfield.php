<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table>
<tr>
<td width="50%">
<h2><?php echo '&nbsp;' . JText::_( 'JL_PERSON_PLAYFIELD' ); ?></h2>

<?php
					
$backimage = 'images/com_joomleague/database/person_playground/' . $this->teamPlayer->position_name . '.png'; 					
$hauptimage = 'images/com_joomleague/database/person_playground/hauptposition.png';
$nebenimage = 'images/com_joomleague/database/person_playground/nebenposition.png';

switch ( $this->teamPlayer->position_name )
{
case 'JL_P_GOALKEEPER':
case 'Torwart':
case 'Torhüter':
case 'TorhÃ¼ter':
$image_class = 'hp1';
break;
case 'JL_P_DEFENDER':
case 'Abwehr':
$image_class = 'hp3l';
break;
case 'JL_P_MIDFIELDER':
case 'Mittelfeld':
$image_class = 'hp6';
break;
case 'JL_P_FORWARD':
case 'Sturm':
case 'Stürmer':
case 'StÃ¼rmer':
$image_class = 'hp14';
break;
}

?>
<div style="position:relative;height:170px;background-image:url(<?PHP echo $backimage;?>);background-repeat:no-repeat;">
<img src="<?PHP echo $hauptimage;?>" class="<?PHP echo $image_class;?>" alt="<?PHP echo $this->teamPlayer->position_name; ?>" title="<?PHP echo $this->teamPlayer->position_name; ?>" />

<?PHP


if ( isset($this->person_positions) )
{

if ( is_array($this->person_positions) )
{
foreach ( $this->person_positions as $key => $value)
{
?>
<img src="<?PHP echo $nebenimage;?>" class="<?PHP echo $value;?>" alt="Nebenposition" title="Nebenposition" />
<?PHP
}
}
else
{
?>
<img src="<?PHP echo $nebenimage;?>" class="<?PHP echo $this->person_positions;?>" alt="Nebenposition" title="Nebenposition" />
<?PHP
}

}
?>
</div>

</td>
</tr>
</table>