<?php defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'pagination.php');

jimport('joomla.application.component.view');

class JoomleagueViewRoster extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$model = $this->getModel();
		$config=$model->getTemplateConfig($this->getName());

		$this->assignRef('project',$model->getProject());
		$this->assignRef('overallconfig',$model->getOverallConfig());
		//$this->assignRef('staffconfig',$model->getTemplateConfig('teamstaff'));
		$this->assignRef('config',$config);
		$this->assignRef('projectteam',$model->getProjectTeam());
        
        $type = JRequest::getVar("type", 0);
        if ( !$type )
        {
            $type = $this->config['show_players_layout'];
        }
        $this->assignRef('type',$type);
        
        $this->config['show_players_layout'] = $type;
		if ($this->projectteam)
		{
			$this->assignRef('team',$model->getTeam());
			$this->assignRef('rows',$model->getTeamPlayers());
			// events
			if ($this->config['show_events_stats'])
			{
				$this->assignRef('positioneventtypes',$model->getPositionEventTypes());
				$this->assignRef('playereventstats',$model->getPlayerEventStats());
			}
			//stats
			if ($this->config['show_stats'])
			{
				$this->assignRef('stats',$model->getProjectStats());
				$this->assignRef('playerstats',$model->getRosterStats());
			}

			$this->assignRef('stafflist',$model->getStaffList());

			// Set page title
			$document->setTitle(JText::sprintf('COM_JOOMLEAGUE_ROSTER_TITLE',$this->team->name));
		}
		else
		{
			// Set page title
			$document->setTitle(JText::sprintf('COM_JOOMLEAGUE_ROSTER_TITLE', "Project team does not exist"));
		}

    $document->addScript( JURI::base(true).'/components/com_joomleague/assets/js/highslide.js');
		$document->addStyleSheet( JURI::base(true) . '/components/com_joomleague/assets/css/highslide/highslide.css' );
    
    $js = "hs.graphicsDir = '".JURI::base(true) . "/components/com_joomleague/assets/css/highslide/graphics/"."';\n";
    $js .= "hs.outlineType = 'rounded-white';\n";
    $js .= "
    hs.lang = {
   cssDirection:     'ltr',
   loadingText :     'Lade...',
   loadingTitle :    'Klick zum Abbrechen',
   focusTitle :      'Klick um nach vorn zu bringen',
   fullExpandTitle : 'Zur Originalgr&ouml;&szlig;e erweitern',
   fullExpandText :  'Vollbild',
   creditsText :     '',
   creditsTitle :    '',
   previousText :    'Voriges',
   previousTitle :   'Voriges (Pfeiltaste links)',
   nextText :        'N&auml;chstes',
   nextTitle :       'N&auml;chstes (Pfeiltaste rechts)',
   moveTitle :       'Verschieben',
   moveText :        'Verschieben',
   closeText :       'Schlie&szlig;en',
   closeTitle :      'Schlie&szlig;en (Esc)',
   resizeTitle :     'Gr&ouml;&szlig;e wiederherstellen',
   playText :        'Abspielen',
   playTitle :       'Slideshow abspielen (Leertaste)',
   pauseText :       'Pause',
   pauseTitle :      'Pausiere Slideshow (Leertaste)',
   number :          'Bild %1/%2',
   restoreTitle :    'Klick um das Bild zu schlie&szlig;en, klick und ziehe um zu verschieben. Benutze Pfeiltasten für vor und zurück.'
};

    
    \n";
    
    $document->addScriptDeclaration( $js );
    
    // select roster view
    $opp_arr = array ();
    $opp_arr[] = JHTML :: _('select.option', "player_standard", JText :: _('COM_JOOMLEAGUE_FES_ROSTER_PARAM_OPTION1_PLAYER_STANDARD'));
	$opp_arr[] = JHTML :: _('select.option', "player_card", JText :: _('COM_JOOMLEAGUE_FES_ROSTER_PARAM_OPTION2_PLAYER_CARD'));
	$opp_arr[] = JHTML :: _('select.option', "player_johncage", JText :: _('COM_JOOMLEAGUE_FES_ROSTER_PARAM_OPTION3_PLAYER_CARD'));

	$lists['type'] = $opp_arr;
	$this->assignRef('lists', $lists);

$this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );

		parent::display($tpl);
	}

}
?>