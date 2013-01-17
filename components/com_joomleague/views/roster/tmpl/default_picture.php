<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php
	// Show team-picture if defined.
	if ( ( $this->config['show_team_logo'] == 1 ) )
	{
		?>
		<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center">
					<?php

					$picture = $this->projectteam->picture;
					if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("team") ))
					{
						$picture = $this->team->picture;
					}
					if ( !file_exists( $picture ) )
					{
						$picture = JoomleagueHelper::getDefaultPlaceholder("team");
					}					
					$imgTitle = JText::sprintf( 'COM_JOOMLEAGUE_ROSTER_PICTURE_TEAM', $this->team->name );
					echo JoomleagueHelper::getPictureThumb($picture, $imgTitle,
															$this->config['team_picture_width'],
															$this->config['team_picture_height']);
					?>
				</td>
			</tr>
		</table>
	<?php
	}
	?>