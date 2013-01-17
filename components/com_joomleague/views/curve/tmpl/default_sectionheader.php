<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>


<!-- sectionheading e.g. ranking aso -->
	<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading"><a name="division<?php echo $this->divisions;?>"></a>
			
			<?php 
				echo JText::_('COM_JOOMLEAGUE_CURVE_TITLE');
				if ($this->division) {
					echo ' '.$this->division->name;
				}
			?>
			</td>
		</tr>
	</table>
<br/>	
<!-- sectionheading ends -->
	