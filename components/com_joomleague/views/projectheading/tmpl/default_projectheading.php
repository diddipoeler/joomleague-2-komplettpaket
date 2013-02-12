<?php defined( '_JEXEC' ) or die( 'Restricted access' );

if ( $this->show_debug_info )
{
echo 'this->overallconfig<br /><pre>~' . print_r($this->overallconfig,true) . '~</pre><br />';
echo 'this->project<br /><pre>~' . print_r($this->project,true) . '~</pre><br />';
}

$nbcols = 2;
if ( $this->overallconfig['show_project_sporttype_picture'] ) { $nbcols++; }
if ( $this->overallconfig['show_project_kunena_link'] ) { $nbcols++; }
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
          if ( $this->overallconfig['show_project_sporttype_picture'] == 1 )
					{
						?>
						<td>
						<?php
						echo JoomleagueHelper::getPictureThumb($this->project->sport_type_picture,
																$this->project->sport_type_name,
																$this->overallconfig['picture_width'],
																$this->overallconfig['picture_height'], 
																2);
						?>
						</td>
					<?php	
			    	}	
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
                    
                    <td class="buttonheading" align="right">
					<?php
					if ( $this->overallconfig['show_project_kunena_link'] == 1 && $this->project->sb_catid )
                    {
                    $link = JoomleagueHelperRoute::getKunenaRoute( $this->project->sb_catid );
						$imgTitle = JText::_($this->project->name.' Forum');
						$desc = JHTML::image('media/com_joomleague/jl_images/kunena.logo.png', $imgTitle, array('title' => $imgTitle,'width' => '100' ));
						echo '&nbsp;';
						echo JHTML::link($link, $desc);    
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