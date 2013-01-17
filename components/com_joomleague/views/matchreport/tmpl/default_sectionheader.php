<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<table class="contentpaneopen">
	<tr>
		<td class="contentheading"><?php
		$pageTitle = 'COM_JOOMLEAGUE_MATCHREPORT_TITLE';
		if ( isset( $this->round->name ) )
		{
			$matchDate = JoomleagueHelper::getTimestamp( $this->match->match_date, 1 );
			echo '&nbsp;' . JText::sprintf(	$pageTitle,
			$this->round->name,
			JHTML::date( $matchDate, JText::_( 'COM_JOOMLEAGUE_MATCHREPORT_GAMES_DATE' ) ),
			JoomleagueHelperHtml::showMatchTime($this->match, $this->config, $this->overallconfig, $this->project) );
		
		}
		else
		{
			echo '&nbsp;' . JText::sprintf( $pageTitle, '', '', '' );
		}
		?></td>
	</tr>
</table>
