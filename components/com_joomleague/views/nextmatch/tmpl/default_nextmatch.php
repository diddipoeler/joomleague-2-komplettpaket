<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<!-- Main START -->
<table width="96%" align="center" border="0" cellpadding="0"
	cellspacing="0">
	<?php
	if( $this->config['show_logo'] == 1 )
	{
		?>
	<tr class="nextmatch">
		<td class="teamlogo"><?php
			$pic = $this->config['show_picture'];
			echo JoomleagueHelper::getPictureThumb($this->teams[0]->$pic, 
								$this->teams[0]->name,
								$this->config['team_picture_width'],
								$this->config['team_picture_height'],1);
		?></td>
		<td class="vs">&nbsp;</td>
		<td class="teamlogo"><?php
			echo JoomleagueHelper::getPictureThumb($this->teams[1]->$pic, 
								$this->teams[1]->name,
								$this->config['team_picture_width'],
								$this->config['team_picture_height'],1);
		?></td>
	</tr>
	<?php
	}
	?>
	<tr class="nextmatch">
		<td class="team"><?php
		if ( !is_null ( $this->teams ) )
		{
			echo $this->teams[0]->name;
		}
		else
		{
			echo JText::_( "COM_JOOMLEAGUE_NEXTMATCH_UNKNOWNTEAM" );
		}
		?></td>
		<td class="vs"><?php
		echo JText::_( "COM_JOOMLEAGUE_NEXTMATCH_VS" );
		?></td>
		<td class="team"><?php
		if ( !is_null ( $this->teams ) )
		{
			echo $this->teams[1]->name;
		}
		else
		{
			echo JText::_( "COM_JOOMLEAGUE_NEXTMATCH_UNKNOWNTEAM" );
		}
		?></td>
	</tr>
</table>

	<?php 
        $report_link = JoomleagueHelperRoute::getMatchReportRoute( $this->project->id,$this->match->id);
					
        if(isset($this->match->team1_result) && isset($this->match->team2_result))
            { ?>
			<div class="notice">
			<?php 
                $text = JText::_( "COM_JOOMLEAGUE_NEXTMATCH_ALREADYPLAYED" );
                echo JHTML::link( $report_link, $text );
			?>
			</div>
			<?php 
            } ?>
                
<br />