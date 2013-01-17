<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

	<div class="contentpaneopen">
		<div class="contentheading">
			<?php
				echo JText::_( 'COM_JOOMLEAGUE_CLUBINFO_TITLE' ) . " " . $this->club->name;

	            if ( $this->showediticon )
	            {
	                $link = JoomleagueHelperRoute::getClubInfoRoute( $this->project->id, $this->club->id, "club.edit" );
	                $desc = JHTML::image(
	                                      "media/com_joomleague/jl_images/edit.png",
	                                      JText::_( 'COM_JOOMLEAGUE_CLUBINFO_EDIT' ),
	                                      array( "title" => JText::_( "COM_JOOMLEAGUE_CLUBINFO_EDIT" ) )
	                                   );
	                echo " ";
	                echo JHTML::_('link', $link, $desc );
	            }
			?>
		</div>
	</div>