<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>	
		<div class="clr"></div>
		<div class="jl_teamsubstats">
			<table width="50%" align="center" border="0" cellpadding="0" cellspacing="0">
				<tr class="sectiontableheader">
					<th colspan="2" class="le">
						<?php
						echo JText::_('COM_JOOMLEAGUE_TEAMSTATS_ATTENDANCE');
						?>
					</th>
				</tr>
				<tr class="sectiontableentry1">
					<td class="statlabel">
						<?php
						echo JText::_('COM_JOOMLEAGUE_TEAMSTATS_ATTENDANCE_TOTAL');
						?>:
					</td>
					<td class="statvalue">
						<?php
						echo $this->totalattendance;
						?>
					</td>
				</tr>
				<tr class="sectiontableentry2">
					<td class="statlabel">
						<?php
						echo JText::_('COM_JOOMLEAGUE_TEAMSTATS_ATTENDANCE_PER_MATCH');
						?>:
					</td>
					<td class="statvalue">
						<?php
						echo $this->averageattendance;
						?>
					</td>
				</tr>
				<tr class="sectiontableentry1">
					<td class="statlabel">
							<?php
							echo JText::_('COM_JOOMLEAGUE_TEAMSTATS_ATTENDANCE_BEST');
							?>:
					</td>
					<td class="statvalue">
						<?php
						echo $this->bestattendance;
						?>
					</td>
				</tr>
				<tr class="sectiontableentry2">
					<td class="statlabel">
							<?php
							echo JText::_('COM_JOOMLEAGUE_TEAMSTATS_ATTENDANCE_WORST');
							?>:
					</td>
					<td class="statvalue">
						<?php
						echo $this->worstattendance;
						?>
					</td>
				</tr>
			</table>
		</div>						

<div class="clr"></div>