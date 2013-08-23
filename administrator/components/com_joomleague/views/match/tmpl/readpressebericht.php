<?PHP

if ( $this->matchnumber )
{

//echo '<pre>',print_r($this->csv, true),'</pre>';

?>
<table id="<?php echo $dcsv['tableid']; ?>" class="table_from_csv_sortable<? if ($dcsv['sortable'] == false){ echo '_not';} ?>" width="<?php echo $dcsv['tablewidth']; ?>" border="<?php echo $dcsv['border']; ?>" cellspacing="<?php echo $dcsv['cellspacing']; ?>" cellpadding="<?php echo $dcsv['cellpadding']; ?>" bgcolor="<?php echo $dcsv['tablebgcolor']; ?>">
<tr>	
<th class="">Spielnummer</th>	
<th class="">Vorname</th>
<th class="">Nachname</th>
<th class="">In der Datenbank ?</th>
<th class="">Projektteam zugeordnet ?</th>
</tr>	
	
		<?php foreach ($this->csvplayers as $value): ?>
		<tr>
        <td><?php echo $value->nummer; ?></td>
        <td><?php echo $value->firstname; ?></td>
        <td><?php echo $value->lastname; ?></td>
        <?PHP
        if ( $value->person_id )
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/ok.png',
														'','title= "'.''.'"').'</td>';
        }
        else
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/error.png',
														'','title= "'.''.'"').'</td>';
        }
        
        if ( $value->project_person_id )
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/ok.png',
														'','title= "'.''.'"').'</td>';
        }
        else
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/error.png',
														'','title= "'.''.'"').'</td>';
        }
        
        ?>
        </tr>
		<?php endforeach; ?>
	
</table>

<table id="<?php echo $dcsv['tableid']; ?>" class="table_from_csv_sortable<? if ($dcsv['sortable'] == false){ echo '_not';} ?>" width="<?php echo $dcsv['tablewidth']; ?>" border="<?php echo $dcsv['border']; ?>" cellspacing="<?php echo $dcsv['cellspacing']; ?>" cellpadding="<?php echo $dcsv['cellpadding']; ?>" bgcolor="<?php echo $dcsv['tablebgcolor']; ?>">
<tr>	
<th class="">Staff Position</th>	
<th class="">Vorname</th>
<th class="">Nachname</th>
<th class="">In der Datenbank ?</th>
<th class="">Projektteam zugeordnet ?</th>
</tr>	
	
		<?php foreach ($this->csvstaff as $value): ?>
		<tr>
        <td><?php echo $value->position; ?></td>
        <td><?php echo $value->firstname; ?></td>
        <td><?php echo $value->lastname; ?></td>
        <?PHP
        if ( $value->person_id )
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/ok.png',
														'','title= "'.''.'"').'</td>';
        }
        else
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/error.png',
														'','title= "'.''.'"').'</td>';
        }
        
        if ( $value->project_person_id )
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/ok.png',
														'','title= "'.''.'"').'</td>';
        }
        else
        {
            echo '<td>'.JHTML::_('image','administrator/components/com_joomleague/assets/images/error.png',
														'','title= "'.''.'"').'</td>';
        }
        
        ?>
        </tr>
		<?php endforeach; ?>
	
</table>





<table id="<?php echo $dcsv['tableid']; ?>" class="table_from_csv_sortable<? if ($dcsv['sortable'] == false){ echo '_not';} ?>" width="<?php echo $dcsv['tablewidth']; ?>" border="<?php echo $dcsv['border']; ?>" cellspacing="<?php echo $dcsv['cellspacing']; ?>" cellpadding="<?php echo $dcsv['cellpadding']; ?>" bgcolor="<?php echo $dcsv['tablebgcolor']; ?>">
<tr>	
<th class="">Spieler</th>	
<th class="">Minute</th>
<th class="">Rückennummer</th>
<th class="">für</th>
</tr>	
	
	
		<?php foreach ($this->csvinout as $value): ?>
		<tr>
        <td><?php echo $value->spieler; ?></td>
        <td><?php echo $value->in_out_time; ?></td>
        <td><?php echo $value->in; ?></td>
        <td><?php echo $value->out; ?></td>
		<?php endforeach; ?>
	
	
</table>

<table id="<?php echo $dcsv['tableid']; ?>" class="table_from_csv_sortable<? if ($dcsv['sortable'] == false){ echo '_not';} ?>" width="<?php echo $dcsv['tablewidth']; ?>" border="<?php echo $dcsv['border']; ?>" cellspacing="<?php echo $dcsv['cellspacing']; ?>" cellpadding="<?php echo $dcsv['cellpadding']; ?>" bgcolor="<?php echo $dcsv['tablebgcolor']; ?>">
<tr>	
<th class="">Spieler</th>	
<th class="">Minute</th>
<th class="">Karte</th>
<th class="">Rückennummer</th>
<th class="">Grund</th>
</tr>	
	
	
		<?php foreach ($this->csvcards as $value): ?>
		<tr>
        <td><?php echo $value->spieler; ?></td>
        <td><?php echo $value->event_time; ?></td>
        <td><?php echo $value->event_name; ?></td>
        <td><?php echo $value->spielernummer; ?></td>
        <td><?php echo $value->notice; ?></td>
		<?php endforeach; ?>
	
	
</table>



<?PHP
}


?>