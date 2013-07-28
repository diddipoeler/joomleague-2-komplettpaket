<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class JoomleagueViewTeamInfo extends JLGView
{
	function display( $tpl = null )
	{
		// Get a reference of the page instance in joomla
		$document	= JFactory::getDocument();
		$model		= $this->getModel();
		$config		= $model->getTemplateConfig( $this->getName() );
		$project	= $model->getProject();
		$this->assignRef( 'project', $project );
		$isEditor = $model->hasEditPermission('projectteam.edit');

		if ( isset($this->project->id) )
		{
			$overallconfig = $model->getOverallConfig();
			$this->assignRef( 'overallconfig',  $overallconfig);
			$this->assignRef( 'config', $config );
			$team = $model->getTeamByProject();
			$this->assignRef( 'team',  $team );
			$club = $model->getClub() ;
			$this->assignRef( 'club', $club);
			$seasons = $model->getSeasons( $config );
			$this->assignRef( 'seasons', $seasons );
			$this->assignRef('showediticon', $isEditor);
			$this->assignRef('projectteamid', $model->projectteamid);
            $this->assignRef('teamid', $model->teamid);
            
            $trainingData = $model->getTrainigData($this->project->id);
			$this->assignRef( 'trainingData', $trainingData );

			$daysOfWeek=array(
				1 => JText::_('COM_JOOMLEAGUE_GLOBAL_MONDAY'),
				2 => JText::_('COM_JOOMLEAGUE_GLOBAL_TUESDAY'),
				3 => JText::_('COM_JOOMLEAGUE_GLOBAL_WEDNESDAY'),
				4 => JText::_('COM_JOOMLEAGUE_GLOBAL_THURSDAY'),
				5 => JText::_('COM_JOOMLEAGUE_GLOBAL_FRIDAY'),
				6 => JText::_('COM_JOOMLEAGUE_GLOBAL_SATURDAY'),
				7 => JText::_('COM_JOOMLEAGUE_GLOBAL_SUNDAY')
			);
			$this->assignRef( 'daysOfWeek', $daysOfWeek );
            
      
      if ( $this->team->merge_clubs )
      {
      $merge_clubs = $model->getMergeClubs( $this->team->merge_clubs );
			$this->assignRef( 'merge_clubs', $merge_clubs );
      }
      
            
            if ($this->config['show_history_leagues']==1)
	{
            $this->assignRef( 'leaguerankoverview', $model->getLeagueRankOverview( $this->seasons ) );
			$this->assignRef( 'leaguerankoverviewdetail', $model->getLeagueRankOverviewDetail( $this->seasons ) );
}

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
    	
		$extended = $this->getExtended($team->teamextended, 'team');
		$this->assignRef( 'extended', $extended );
    $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
    $this->assign('use_joomlaworks', JComponentHelper::getParams('com_joomleague')->get('use_joomlaworks',0) );
    
		// Set page title
		$pageTitle = JText::_( 'COM_JOOMLEAGUE_TEAMINFO_PAGE_TITLE' );
		if ( isset( $this->team ) )
		{
			$pageTitle .= ': ' . $this->team->tname;
		}
		$document->setTitle( $pageTitle );

		parent::display( $tpl );
	}
}
?>