<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class JoomleagueViewTeamStats extends JLGView
{
	function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document= JFactory::getDocument();

		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());

		$tableconfig = $model->getTemplateConfig( "ranking" );
		$eventsconfig = $model->getTemplateConfig( "eventsranking" );
		$flashconfig = $model->getTemplateConfig( "flash" );

		$this->assignRef( 'project', $model->getProject() );
		if ( isset( $this->project ) )
		{
			$this->assignRef( 'overallconfig', $model->getOverallConfig() );
			if ( !isset( $this->overallconfig['seperator'] ) )
			{
				$this->overallconfig['seperator'] = ":";
			}
			$this->assignRef( 'config', $config );

			$this->assignRef( 'tableconfig', $tableconfig );
			$this->assignRef( 'eventsconfig', $eventsconfig );
			$this->assignRef( 'actualround', $model->getCurrentRound() );
			$this->assignRef( 'team', $model->getTeam() );
			$this->assignRef( 'highest_home', $model->getHighestHome( ) );
			$this->assignRef( 'highest_away', $model->getHighestAway( ) );
			$this->assignRef( 'highestdef_home', $model->getHighestDefHome( ) );
			$this->assignRef( 'highestdef_away', $model->getHighestDefAway( ) );
			$this->assignRef( 'totalshome', $model->getSeasonTotalsHome( ) );
			$this->assignRef( 'totalsaway', $model->getSeasonTotalsAway( ) );
			$this->assignRef( 'matchdaytotals', $model->getMatchDayTotals( ) );
			$this->assignRef( 'totalrounds', $model->getTotalRounds( ) );
			$this->assignRef( 'totalattendance', $model->getTotalAttendance() );
			$this->assignRef( 'bestattendance', $model->getBestAttendance() );
			$this->assignRef( 'worstattendance', $model->getWorstAttendance() );
			$this->assignRef( 'averageattendance', $model->getAverageAttendance() );
			$this->assignRef( 'chart_url', $model->getChartURL( ) );
			$this->assignRef( 'nogoals_against', $model->getNoGoalsAgainst( ) );
			$this->assignRef( 'logo', $model->getLogo( ) );
			$this->assignRef( 'results',  $model->getResults());

			$this->_setChartdata(array_merge($flashconfig, $config));
		}
		// Set page title
		$pageTitle = JText::_( 'COM_JOOMLEAGUE_TEAMSTATS_PAGE_TITLE' );
		if ( isset( $this->team ) )
		{
			$pageTitle .= ': ' . $this->team->name;
		}
		$document->setTitle( $pageTitle );

		parent::display( $tpl );
	}

	/**
	 * assign the chartdata object for open flash chart library
	 * @param $config
	 * @return unknown_type
	 */
	function _setChartdata($config)
	{
		require_once( JLG_PATH_SITE.DS."assets".DS."classes".DS."open-flash-chart".DS."open-flash-chart.php" );

		$data = $this->get('ChartData');

		// Calculate Values for Chart Object
		$forSum = array();
		$againstSum = array();
		$matchDayGoalsCount = array();
		$matchDayGoalsCount[] = 0;
		$round_labels = array();

		$matchDayGoalsCountMax = 0;
		foreach( $data as $rw )
		{
			if (!$rw->goalsfor) $rw->goalsfor = 0;
			if (!$rw->goalsagainst) $rw->goalsagainst = 0;
			$forSum[]     = intval($rw->goalsfor);
			$againstSum[] = intval($rw->goalsagainst);

			// check, if both results are missing and avoid drawing the flatline of "0" goals for not played games yet
			if ((!$rw->goalsfor) && (!$rw->goalsagainst))
			{
				$matchDayGoalsCount[] = 0;
			}
			else
			{
				$matchDayGoalsCount[] = intval($rw->goalsfor + $rw->goalsagainst);
			}
			$round_labels[] = $rw->roundcode;
		}
		
		$chart = new open_flash_chart();
		//$chart->set_title( $title );
		$chart->set_bg_colour($config['bg_colour']);

		$barfor = new $config['bartype_1']();
		$barfor->set_values( $forSum );
		$barfor->set_tooltip( JText::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_FOR'). ": #val#" );
		$barfor->set_colour( $config['bar1'] );
		$barfor->set_on_show(new bar_on_show($config['animation_1'], $config['cascade_1'], $config['delay_1']));
		$barfor->set_key(JText::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_FOR'), 12);

		$baragainst = new $config['bartype_2']();
		$baragainst->set_values( $againstSum );
		$baragainst->set_tooltip(   JText::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_AGAINST'). ": #val#" );
		$baragainst->set_colour( $config['bar2'] );
		$baragainst->set_on_show(new bar_on_show($config['animation_2'], $config['cascade_2'], $config['delay_2']));
		$baragainst->set_key(JText::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_AGAINST'), 12);

		$chart->add_element($barfor);
		$chart->add_element($baragainst);

		// total
		$d = new $config['dotstyle_3']();
		$d->size((int)$config['line3_dot_strength']);
		$d->halo_size(1);
		$d->colour($config['line3']);
		$d->tooltip(JText::_('COM_JOOMLEAGUE_TEAMSTATS_TOTAL2').' #val#');

		$line = new line();
		$line->set_default_dot_style($d);
		$line->set_values(array_slice( $matchDayGoalsCount,1) );
		$line->set_width( (int) $config['line3_strength'] );
		$line->set_key(JText::_('COM_JOOMLEAGUE_TEAMSTATS_TOTAL'), 12);
		$line->set_colour( $config['line3'] );
		$line->on_show(new line_on_show($config['l_animation_3'], $config['l_cascade_3'], $config['l_delay_3']));
		$chart->add_element($line);		
		
		$x = new x_axis();
		$x->set_colours($config['x_axis_colour'], $config['x_axis_colour_inner']);
		$x->set_labels_from_array($round_labels);
		$chart->set_x_axis( $x );
		$x_legend = new x_legend( JText::_('COM_JOOMLEAGUE_TEAMSTATS_ROUNDS') );
		$x_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_x_legend( $x_legend );

		$y = new y_axis();
		$y->set_range( 0, max($matchDayGoalsCount)+2, $config['y_axis_steps']);
		$y->set_colours($config['y_axis_colour'], $config['y_axis_colour_inner']);
		$chart->set_y_axis( $y );
		$y_legend = new y_legend( JText::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS') );
		$y_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_y_legend( $y_legend );

		$this->assignRef( 'chartdata',  $chart);
	}
}
?>