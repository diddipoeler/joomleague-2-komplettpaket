<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<table class="contentpaneopen">
	<tr>
		<td class="contentheading">
			<?php
	echo JText::sprintf('COM_JOOMLEAGUE_PLAYER_INFORMATION', JoomleagueHelper::formatName(null, $this->person->firstname, $this->person->nickname, $this->person->lastname, $this->config["name_format"]));
	
	if ( $this->showediticon )
	{
		$link = JoomleagueHelperRoute::getPlayerRoute( $this->project->id, $this->teamPlayer->team_id, $this->person->id, 'person.edit' );
		$desc = JHTML::image(
			"media/com_joomleague/jl_images/edit.png",
			JText::_( 'COM_JOOMLEAGUE_PERSON_EDIT' ),
			array( "title" => JText::_( "COM_JOOMLEAGUE_PERSON_EDIT" ) )
		);
	    echo " ";
	    echo JHTML::_('link', $link, $desc );
	}

	if ( isset($this->teamPlayer->injury) && $this->teamPlayer->injury )
	{
		$imageTitle = JText::_( 'COM_JOOMLEAGUE_PERSON_INJURED' );
		echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
							$imageTitle,
							array( 'title' => $imageTitle ) );
	}

	if ( isset($this->teamPlayer->suspension) && $this->teamPlayer->suspension )
	{
		$imageTitle = JText::_( 'COM_JOOMLEAGUE_PERSON_SUSPENDED' );
		echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
							$imageTitle,
							array( 'title' => $imageTitle ) );
	}


	if ( isset($this->teamPlayer->away) && $this->teamPlayer->away )
	{
		$imageTitle = JText::_( 'COM_JOOMLEAGUE_PERSON_AWAY' );
		echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
							$imageTitle,
							array( 'title' => $imageTitle ) );
	}
			?>
		</td>
	</tr>
</table>