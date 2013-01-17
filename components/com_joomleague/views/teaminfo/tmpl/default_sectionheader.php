<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- START: Contentheading -->
<div class="contentpaneopen">
	<div class="contentheading">
		<?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_PAGE_TITLE') . " - " . $this->team->tname;
		if ( $this->showediticon )
		{
			$link = JoomleagueHelperRoute::getProjectTeamInfoRoute( $this->project->id, $this->projectteamid, 'projectteam.edit' );
			$desc = JHTML::image(
					"media/com_joomleague/jl_images/edit.png",
					JText::_( 'COM_JOOMLEAGUE_PROJECTTEAM_EDIT' ),
					array( "title" => JText::_( "COM_JOOMLEAGUE_PROJECTTEAMEDIT" ) )
			);
			echo " ";
			echo JHTML::_('link', $link, $desc );
		} else {
			echo "no permission";
		}
		?>
	</div>
</div>
<!-- END: Contentheading -->
