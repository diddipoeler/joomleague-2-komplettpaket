<?php defined( '_JEXEC' ) or die( 'Restricted access' );

 echo JHTML::_('select.genericlist', $this->matchdaysoptions, 'select-round', 'onchange="joomleague_changedoc(this);" style="float:right;"', 'value', 'text', $this->currenturl);
?>