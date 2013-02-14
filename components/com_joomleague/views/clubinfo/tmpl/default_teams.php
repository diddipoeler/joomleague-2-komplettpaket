<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="no-column">
	<div class="contentpaneopen">
		<div class="contentheading">
			<?php echo JText::_('COM_JOOMLEAGUE_CLUBINFO_TEAMS'); ?>
		</div>
	</div>
	<div class="left-column-teamlist">
	<?php
	$params=array();
	$params['width']="30";
	
		foreach ( $this->teams as $team )
		{
			if ( $team->team_name )
			{
				//$link = JoomleagueHelperRoute::getProjectTeamInfoRoute( $team->pid, $team->ptid );
                $link = JoomleagueHelperRoute::getTeamInfoRoute( $team->pid, $team->id );
				?>
				<span class="clubinfo_team_item">
					<?php
					//echo JHTML::link( $link, $team->team_name );
						echo JHTML::image($team->trikot_home, $team->team_name, $params).JHTML::link( $link, $team->team_name );
						echo "&nbsp;";
						if ( $team->team_shortcut ) { echo "(" . $team->team_shortcut . ")"; }
					?>
				</span>
				<span class="clubinfo_team_value">
				<?php
				if ( $team->team_description )
				{
					echo $team->team_description;
				}
				else
				{
					echo "&nbsp;";
				}
				?>
				</span>
				<?php
			}
		}
	?>
	</div>
</div>