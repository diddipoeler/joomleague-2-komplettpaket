<?php defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.html.pane');

if ( !$this->showediticon )
{
	JFactory::getApplication()->redirect( str_ireplace('&view=editmatch','&view=results',JFactory::getURI()->toString()), JText::_('ALERTNOTAUTH') );
}
?>
<!-- Main START -->
<div id="matchdetails">
	<h1>
		<?php
		echo JText::sprintf( 'COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS_TITLE', $this->team1->name , $this->team2->name );
		?>
	</h1>
<form name="adminForm" id="adminForm" method="post" action="index.php">
<?php
	$pane =& JPane::getInstance('tabs', array('startOffset'=>0));
	echo $pane->startPane( 'pane' );
	
	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_MATCHDETAILS'), 'panel1' );
	echo $this->loadTemplate('matchdetails');
	echo $pane->endPanel();

	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_SCOREDETAILS'), 'panel2' );
	echo $this->loadTemplate('scoredetails');
	echo $pane->endPanel();

	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_ALTDECISION'), 'panel3' );
	echo $this->loadTemplate('altdecision');
	echo $pane->endPanel();

	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_MATCHREPORT'), 'panel4' );
	echo $this->loadTemplate('matchreport');
	echo $pane->endPanel();

	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_MATCHPREVIEW'), 'panel5' );
	echo $this->loadTemplate('matchpreview');
	echo $pane->endPanel();

	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_MATCHRELATION'), 'panel6' );
	echo $this->loadTemplate('matchrelation');
	echo $pane->endPanel();
	
	echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_TABS_EXTENDED'), 'panel7' );
	echo $this->loadTemplate('matchextended');
	echo $pane->endPanel();	

	echo $pane->endPane();
	//echo '<br /><pre>~' . print_r( $this, true ) . '~</pre><br />';
?>
	<input type="hidden" name="p"					value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="rid"					value="<?php echo $this->match->round_id; ?>" />
	<input type="hidden" name="mid"					value="<?php echo $this->match->id; ?>" />
	<input type="hidden" name="option"				value="com_joomleague" />
	<input type="hidden" name="task"				value="editmatch.savematch" />
	<input type="hidden" name="Itemid"				value="<?php echo JRequest::getVar('Itemid', 1, 'get', 'int'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>