<?PHP

echo '<pre>',print_r($this->csv, true),'</pre>';

?>

<table id="<?php echo $dcsv['tableid']; ?>" class="table_from_csv_sortable<? if ($dcsv['sortable'] == false){ echo '_not';} ?>" width="<?php echo $dcsv['tablewidth']; ?>" border="<?php echo $dcsv['border']; ?>" cellspacing="<?php echo $dcsv['cellspacing']; ?>" cellpadding="<?php echo $dcsv['cellpadding']; ?>" bgcolor="<?php echo $dcsv['tablebgcolor']; ?>">
	<tr>
		<?php 
		
		$i=1;
		foreach ($this->csv->titles as $value): ?>
		<th class="col<?php echo $i;?>"><?php echo $value; ?></th>
		<?php 
		$i++;
		endforeach; ?>
	</tr>
	<?php foreach ($this->csv->data as $key => $row): ?>
	<tr>
		<?php foreach ($row as $value): ?>
		<td><?php echo $value; ?></td>
		<?php endforeach; ?>
	</tr>
	<?php endforeach; ?>
</table>