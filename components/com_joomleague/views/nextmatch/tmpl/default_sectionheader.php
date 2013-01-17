<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<!-- START: Contentheading -->
<table width="100%" class="contentpaneopen">
	<tr>
		<td class="contentheading"><?php
		echo JHTML::date($this->match->match_date, JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_GAMES_DATE' ) ). " ".
		JoomleagueHelperHtml::showMatchTime($this->match, $this->config, $this->overallconfig, $this->project); 
		?></td>
	</tr>
</table>

<!-- END: Contentheading -->
