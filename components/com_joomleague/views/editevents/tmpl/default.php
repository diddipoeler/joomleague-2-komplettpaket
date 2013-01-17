<?php defined('_JEXEC') or die('Restricted access');
if (!$this->isAllowed)
{
	JFactory::getApplication()->redirect(str_ireplace('&view=editevents','&view=results',JFactory::getURI()->toString()),JText::_('ALERTNOTAUTH'));
}
jimport('joomla.html.pane');

// load javascripts
$document = JFactory::getDocument();
JHTML::_('behavior.mootools');
$version = urlencode(JoomleagueHelper::getVersion());
$document->addScript(JURI::base().'administrator/components/com_joomleague/assets/js/editevents2.js?v='.$version);
$document->addScript(JURI::base().'administrator/components/com_joomleague/assets/js/startinglineup2.js?v='.$version);

?>
<script type="text/javascript">
<!--
	// url for ajax
	var baseajaxurl='<?php echo JURI::base().'index.php?option=com_joomleague'; ?>';
	var matchid=<?php echo $this->matchid; ?>;
	// We need to setup some text variables for translation
	var str_delete="<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_DELETE'); ?>";
//-->
</script>
<?php // Don't remove following <div id='ajaxresponse'></div> as it is neede for ajax changings ?>
<div id='ajaxresponse'></div>
<div id="editevents">
	<h2 style='text-align:center;'>
		<?php
		echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_TITLE');
		echo '<br />';
		echo JText::sprintf('COM_JOOMLEAGUE_EDIT_EVENTS_MATCH_TEAMS',$this->hometeam->name,$this->awayteam->name);
		?>
	</h2>
	<form name="startingSquadsForm" id="startingSquadsForm" method="post">
		<?php
		$paneStartOffset=9;
		$pane =& JPane::getInstance('Tabs',array('startOffset'=>$paneStartOffset));
					//startOffset: The default tab to start with.
					//onActive: Another function to use when making a tab active (??)
					//onBackground: Another function to use when making a tab dissapear (??)

		echo $pane->startPane('pane');

			echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_HOME_ROSTER'),'panel_0');
				echo $pane->startPane('pane1');

					echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_HOME_SUBST'),'panel_1');
						echo $this->loadTemplate('edit_home_substitution');
					echo $pane->endPanel();

					echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_HOME_PLAYER'),'panel_2');
						echo $this->loadTemplate('edit_home_player');
					echo $pane->endPanel();

					echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_HOME_STAFF'),'panel_3');
						echo $this->loadTemplate('edit_home_staff');
					echo $pane->endPanel();

				echo $pane->endPane();
			echo $pane->endPanel();

			echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_AWAY_ROSTER'),'panel_4');
				echo $pane->startPane('pane2');

					echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_AWAY_SUBST'),'panel_5');
						echo $this->loadTemplate('edit_away_substitution');
					echo $pane->endPanel();

					echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_AWAY_PLAYER'),'panel_6');
						echo $this->loadTemplate('edit_away_player');
					echo $pane->endPanel();

					echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_AWAY_STAFF'),'panel_7');
						echo $this->loadTemplate('edit_away_staff');
					echo $pane->endPanel();

				echo $pane->endPane();
			echo $pane->endPanel();

			echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_REFEREES'),'panel_8');
				echo $this->loadTemplate('edit_referees');
			echo $pane->endPanel();

			echo $pane->startPanel(JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EDIT_EVENTS'),'panel_9');
				echo $this->loadTemplate('edit_events');
			echo $pane->endPanel();

		echo $pane->endPane();
		?>

		<input type="hidden" name="p" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->match->round_id; ?>" />
		<input type="hidden" name="mid" value="<?php echo $this->match->id; ?>" />
		<input type="hidden" name="projectteam1_id" value="<?php echo $this->projectteam1_id; ?>" />
		<input type="hidden" name="projectteam2_id" value="<?php echo $this->projectteam2_id; ?>" />
		<input type="hidden" name="option" value="com_joomleague" />
		<input type="hidden" name="task" value="editevents.save" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid',1,'get','int'); ?>" />
		<input type="hidden" name="changes_check" value="0" id="changes_check" />

		<?php echo JHTML::_('form.token')."\n"; ?>
	</form>
</div>