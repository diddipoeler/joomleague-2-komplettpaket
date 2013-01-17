<?php defined( '_JEXEC' ) or die( 'Restricted access' ); 


jimport('joomla.html.pane');
?>

<!-- START: game stats -->
<?php
if (!empty($this->matchplayerpositions ))
{
	$hasMatchPlayerStats = false;
	$hasMatchStaffStats = false;
	foreach ( $this->matchplayerpositions as $pos )
	{
		if(isset($this->stats[$pos->position_id]) && count($this->stats[$pos->position_id])>0) {
			foreach ($this->stats[$pos->position_id] as $stat) {
				if ($stat->showInSingleMatchReports() && $stat->showInMatchReport()) {
					$hasMatchPlayerStats = true;
					break;
				}
			}
		}
	}	
	foreach ( $this->matchstaffpositions as $pos )
	{
		if(isset($this->stats[$pos->position_id]) && count($this->stats[$pos->position_id])>0) {
			foreach ($this->stats[$pos->position_id] as $stat) {
				if ($stat->showInSingleMatchReports() && $stat->showInMatchReport()) {
					$hasMatchStaffStats = true;
				}
			}
		}
	}
	if($hasMatchPlayerStats || $hasMatchStaffStats) :
	?>

	<h2><?php echo JText::_('COM_JOOMLEAGUE_MATCHREPORT_STATISTICS'); ?></h2>
	
		<?php
		$pane =& JPane::getInstance('tabs',array('startOffset'=>0));
		echo $pane->startPane('pane');
		echo $pane->startPanel($this->team1->name,'panel1');
		echo $this->loadTemplate('stats_home');
		echo $pane->endPanel();
		
		echo $pane->startPanel($this->team2->name,'panel2');
		echo $this->loadTemplate('stats_away');
		echo $pane->endPanel();
		
		echo $pane->endPane();

	endif;
}
?>
<!-- END of game stats -->
