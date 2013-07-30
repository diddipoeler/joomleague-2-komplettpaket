<?php defined('_JEXEC') or die('Restricted access');
$backimage = JURI::root().'images/com_joomleague/database/person_playground/COM_JOOMLEAGUE_P_GOALKEEPER.png';
$hauptimage = JURI::root().'images/com_joomleague/database/person_playground/hauptposition.png';
$nebenimage = JURI::root().'images/com_joomleague/database/person_playground/nebenposition.png';

$positions['hp1'] = "Torwart";
$positions['hp2'] = "Libero";
$positions['hp3l'] = "Innenverteidiger Links";
$positions['hp3r'] = "Innenverteidiger Rechts";
$positions['hp4'] = "Linker Verteidiger";
$positions['hp5'] = "Rechter Verteidiger";
$positions['hp6'] = "Defensives Mittelfeld";
$positions['hp7'] = "Zentrales Mittelfeld";
$positions['hp8'] = "Rechtes Mittelfeld";
$positions['hp9'] = "Linkes Mittelfeld";
$positions['hp10'] = "Offensives Mittelfeld";
$positions['hp11m'] = "Linksaußen";
$positions['hp11s'] = "offensiver Linksaußen";
$positions['hp12m'] = "Rechtsaußen";
$positions['hp12s'] = "offensiver Rechtsaußen";
$positions['hp13'] = "Hängende Spitze";
$positions['hp14'] = "Mittelstürmer";

foreach ($this->extended->getFieldsets() as $fieldset)
{
	?>
	<fieldset class="adminform">
	<legend><?php echo JText::_($fieldset->name); ?></legend>
    <table>
	<?php
	$fields = $this->extended->getFieldset($fieldset->name);
	
	if(!count($fields)) {
		echo JText::_('COM_JOOMLEAGUE_GLOBAL_NO_PARAMS');
	}
	
	foreach ($fields as $field)
	{
	   echo '<tr>';
       echo '<td>';
		echo $field->label;
        echo '</td>';
        echo '<td>';
       	echo $field->input;
        echo '</td>';
        if (preg_match("/COM_JOOMLEAGUE_EXT_PERSON_POSITION/i", $field->name)) 
        {
   echo '<td>';
   ?>

   
<div style="position:relative;width:265px;height:170px;background-image:url(<?PHP echo $backimage;?>);background-repeat:no-repeat;">
<?PHP

foreach ($positions as $key => $value)
{
?>    
<img src="<?PHP echo $hauptimage;?>" class="<?PHP echo $key;?>" alt="<?PHP echo $value; ?>" title="<?PHP echo $value; ?>" />    
<?PHP
}

?>
</div>

<?PHP
//   echo "Es wurde eine Übereinstimmung gefunden.";
   echo '</td>';
}
        elseif (preg_match("/COM_JOOMLEAGUE_EXT_PERSON_PARENT_POSITIONS/i", $field->name)) 
        {
   echo '<td>';
   ?>

   
<div style="position:relative;width:265px;height:170px;background-image:url(<?PHP echo $backimage;?>);background-repeat:no-repeat;">
<?PHP

foreach ($positions as $key => $value)
{
?>    
<img src="<?PHP echo $nebenimage;?>" class="<?PHP echo $key;?>" alt="<?PHP echo $value; ?>" title="<?PHP echo $value; ?>" />    
<?PHP
}

?>
</div>

<?PHP
//   echo "Es wurde eine Übereinstimmung gefunden.";
   echo '</td>';
}
else
{
    echo '<td>';
    echo '</td>';
}
        echo '</tr>';
	}
	?>
    </table>
	</fieldset>
	<?php
}
?>
