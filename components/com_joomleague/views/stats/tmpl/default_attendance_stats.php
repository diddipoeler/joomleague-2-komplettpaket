<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div id="jl_stats">

<div class="jl_substats">
<table cellspacing="0" border="0" width="100%">
	<tr class="sectiontableheader">
		<th colspan="2"><?php	echo JText::_('COM_JOOMLEAGUE_STATS_ATTENDANCE'); ?></th>
	</tr>

	<tr class="sectiontableentry1">
		<td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_STATS_ATTENDANCE_TOTAL');?>:</td>
		<td class="statvalue"><?php echo $this->totals->sumspectators;?></td>
	</tr>
	<tr class="sectiontableentry2">
		<td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_STATS_ATTENDANCE_PER_MATCH');?>:</td>
		<td class="statvalue"><?php echo round (($this->totals->sumspectators / $this->totals->attendedmatches),2);?>
		</td>
	</tr>
	<tr class="sectiontableentry1">
		<td class="statlabel"><b><?php echo JText::_('COM_JOOMLEAGUE_STATS_ATTENDANCE_BEST_AVG');?>:</b>
		<br />
		<?php echo $this->bestavgteam;?></td>
		<td class="statvalue"><?php echo $this->bestavg;?></td>
	</tr>
	<tr class="sectiontableentry2">
		<td class="statlabel"><b><?php echo JText::_('COM_JOOMLEAGUE_STATS_ATTENDANCE_WORST_AVG');?>:</b>
		<br />
		<?php echo $this->worstavgteam;?></td>
		<td class="statvalue"><?php echo $this->worstavg;?></td>
	</tr>
</table>
</div>

</div>
