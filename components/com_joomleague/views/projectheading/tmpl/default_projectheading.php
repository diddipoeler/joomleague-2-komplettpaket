<?php defined( '_JEXEC' ) or die( 'Restricted access' );

$nbcols = 2;
if ( $this->overallconfig['show_project_picture'] ) { $nbcols++; }
if ( $this->overallconfig['show_project_heading'] == 1 && $this->project)
{
	?>
	<div class="componentheading">
		<table class="contentpaneopen">
			<tbody>
				<?php
				if ( $this->overallconfig['show_project_country'] == 1 )
				{
					?>
				<tr class="contentheading">
					<td colspan="<?php echo $nbcols; ?>">
					<?php
					$country = $this->project->country;
					echo Countries::getCountryFlag($country) . ' ' . Countries::getCountryName($country);
					?>
					</td>
				</tr>
				<?php	
			   	}
				?>
				<tr class="contentheading">
					<?php	
			    	if ( $this->overallconfig['show_project_picture'] == 1 )
					{
						?>
						<td>
						<?php
						echo JoomleagueHelper::getPictureThumb($this->project->picture,
																$this->project->name,
																$this->overallconfig['picture_width'],
																$this->overallconfig['picture_height'], 
																2);
						?>
						</td>
					<?php	
			    	}
			    	?>
					<?php	
			    	if ( $this->overallconfig['show_project_text'] == 1 )
					{
						?>
				    	<td>
						<?php
						echo $this->project->name;
						if (isset( $this->division))
						{
							echo ' - ' . $this->division->name;
						}
						?>
						</td>
					<?php	
			    	}
			    	?>
					<td class="buttonheading" align="right">
					<?php
						if(JRequest::getVar('print') != 1) {
							$overallconfig = $this->overallconfig;
							echo JoomleagueHelper::printbutton(null, $overallconfig);
						}
					?>
					&nbsp;
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php 
} else {
	if ($this->overallconfig['show_print_button'] == 1) {
	?>
		<div class="componentheading">
			<table class="contentpaneopen">
				<tbody>
					<tr class="contentheading">
						<td class="buttonheading" align="right">
						<?php 
							if(JRequest::getVar('print') != 1) {
							  echo JoomleagueHelper::printbutton(null, $this->overallconfig);
							}
						?>
						&nbsp;
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php 
	}
}
?>