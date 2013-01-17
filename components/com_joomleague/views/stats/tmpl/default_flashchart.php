<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<!-- Flash Statistik Start -->
<script	type="text/javascript" src="<?php echo JURI::base().'components/com_joomleague/assets/js/json2.js'; ?>"></script>
<script	type="text/javascript" src="<?php echo JURI::base().'components/com_joomleague/assets/js/swfobject.js'; ?>"></script>
<script type="text/javascript">
	function get_stats_chart() {
		var data_stats_chart = <?php echo $this->chartdata->toPrettyString(); ?>;
		return JSON.stringify(data_stats_chart);
	}
	swfobject.embedSWF("<?php echo JURI::base().'components/com_joomleague/assets/classes/open-flash-chart/open-flash-chart.swf'; ?>", 
			"stats_chart", "100%", "200", "9.0.0", false, {"get-data": "get_stats_chart"} );
</script>


<table cellspacing="0" border="0" width="100%">
	<tr class="sectiontableheader">
		<th colspan="2"><?php	echo JText::_('COM_JOOMLEAGUE_STATS_GOALS_STATISTIC'); ?></th>
	</tr>
</table>

<div id="stats_chart"></div>
<!-- Flash Statistik END -->

