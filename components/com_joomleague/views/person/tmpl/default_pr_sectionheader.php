<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<table width="100%" class="contentpaneopen">
	<tr>
		<td class="contentheading">
			<?php
			$titleStr = 'About %1$s %2$s as a Person';
			if ($this->showType == 1)
			{
				$titleStr = 'About %1$s %2$s as a Player';
			}
			elseif ($this->showType == 2)
			{
				$titleStr = 'About %1$s %2$s as a Staffmember';
			}
			elseif ($this->showType == 3)
			{
				$titleStr = 'About %1$s %2$s as a Referee';
			}
			elseif ($this->showType == 4)
			{
				$titleStr = 'About %1$s %2$s as a Club-Staffmember';
			}
			echo '&nbsp;' . JText::sprintf( $titleStr, $this->person->firstname, $this->person->lastname );

			if ( isset($this->inprojectinfo->injury) && $this->inprojectinfo->injury )
			{
				$imageTitle = JText::_( 'Injured' );
				echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
													$imageTitle,
													array( 'title' => $imageTitle ) );
			}

			if ( isset($this->inprojectinfo->suspension) && $this->inprojectinfo->suspension )
			{
				$imageTitle = JText::_( 'Suspended' );
				echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
													$imageTitle,
													array( 'title' => $imageTitle ) );
			}


			if ( isset($this->inprojectinfo->away) && $this->inprojectinfo->away )
			{
				$imageTitle = JText::_( 'Away' );
				echo "&nbsp;&nbsp;" . JHTML::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
													$imageTitle,
													array( 'title' => $imageTitle ) );
			}
			?>
		</td>
	</tr>
</table>
<br />