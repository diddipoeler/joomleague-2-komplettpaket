<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<script>
function findSWF(movieName) {
	if (navigator.appName.indexOf("Microsoft")!= -1) {
		return window[movieName];
	} else {
		return document[movieName];
	}
}
</script>
<?php
foreach ($this->divisions as $division)
{
	$chart = 'chartdata_'.$division->id;
	if(empty($this->$chart)) continue;
	if(empty($this->allteams) || count($this->allteams)==0) continue;
		?>
	<table width="100%" class="contentpaneopen">
	<tr>
		<td class="contentheading"><?php echo $division->name; ?></td>
	</tr>
	<tr>
	<td style="text-align: right">
		<?php echo JText::_('COM_JOOMLEAGUE_CURVE_TEAMS').' '.$division->name; ?>
		<?php echo $this->team1select[$division->id]; ?>
		<?php echo $this->team2select[$division->id]; ?>
		
		<form name="curveform<?php echo $division->id; ?>" method="get"
			id="curveform<?php echo $division->id; ?>">
			
			<input type="hidden" name="view" value="curve" />
			<input type="hidden" name="p" value="<?php echo $this->project->id; ?>" />  
			<input type="hidden" name="tid1" value="" /> 
			<input type="hidden" name="tid2" value="" />  
			<input type="hidden" name="division" value="<?php echo $division->id; ?>" /> 
			<input type="submit" style="font-size: 9px;" class="inputbox"
				value="<?php echo JText::_('COM_JOOMLEAGUE_CURVE_GO'); ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</td>
	</tr>
	</table>
<script type="text/javascript">
function get_curve_chart_<?php echo $division->id; ?>() {
	var data_curve_chart_<?php echo $division->id; ?> = <?php $chart = 'chartdata_'.$division->id; 
	echo $this->$chart->toString(); ?>;
	return JSON.stringify(data_curve_chart_<?php echo $division->id; ?>);
}
swfobject.embedSWF("<?php echo JURI::base().'components/com_joomleague/assets/classes/open-flash-chart/open-flash-chart.swf'; ?>", 
		"curve_chart_<?php echo $division->id; ?>", "100%", "400", "9.0.0", false, 
		{"loading": "loading <?php echo $division->name; ?>","get-data": "get_curve_chart_<?php echo $division->id; ?>", "wmode" : "transparent"} );

function reload_curve_chart_<?php echo $division->id; ?>() {
	var tmp = findSWF("curve_chart_<?php echo $division->id; ?>");
	var baseurl = '<?php echo JURI::base() ?>/';
	var reloadstring = 'index.php?option=com_joomleague&format=raw&view=curve&p=<?php echo $this->project->slug?>&division=<?php echo $division->id;?>'+
	'&tid1='+document.getElementById('tid1_<?php echo $division->id; ?>').options[document.getElementById('tid1_<?php echo $division->id; ?>').selectedIndex].value+
	'&tid2='+document.getElementById('tid2_<?php echo $division->id; ?>').options[document.getElementById('tid2_<?php echo $division->id; ?>').selectedIndex].value;
	document.forms['curveform<?php echo $division->id; ?>'].tid1.value = document.getElementById('tid1_<?php echo $division->id; ?>').options[document.getElementById('tid1_<?php echo $division->id; ?>').selectedIndex].value;
	document.forms['curveform<?php echo $division->id; ?>'].tid2.value = document.getElementById('tid2_<?php echo $division->id; ?>').options[document.getElementById('tid2_<?php echo $division->id; ?>').selectedIndex].value;
	x = tmp.reload(baseurl+reloadstring);
}
</script>

<div id="curve_chart_<?php echo $division->id; ?>"></div>
<?php 
}
?>
