<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JoomleagueViewEventsRanking extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();

		// read the config-data from template file
		$model = $this->getModel();
		$config=$model->getTemplateConfig($this->getName());

		$this->assignRef('project', $model->getProject());
		$this->assignRef('division', $model->getDivision());
		$this->assignRef('matchid', $model->matchid);
		$this->assignRef('overallconfig', $model->getOverallConfig());
		$this->assignRef('config', $config);
		$this->assignRef('teamid', $model->getTeamId());
		$this->assignRef('teams', $model->getTeamsIndexedById());
		$this->assignRef('favteams', $model->getFavTeams());
		$this->assignRef('eventtypes', $model->getEventTypes());
		$this->assignRef('limit', $model->getLimit());
		$this->assignRef('limitstart', $model->getLimitStart());
		$this->assignRef('pagination', $this->get('Pagination'));
		$this->assignRef('eventranking', $model->getEventRankings($this->limit));
		$this->assign( 'multiple_events', count($this->eventtypes) > 1 );

		if ( $this->multiple_events )
		{
			$title = JText::_( 'COM_JOOMLEAGUE_EVENTSRANKING_TITLE' );
		}
		else
		{
			// Next query will result in an array with exactly 1 statistic id
			$evid = array_keys($this->eventtypes);

			// Selected one valid eventtype, so show its name
			$title = JText::_($this->eventtypes[$evid[0]]->name);
		}

		// Set page title
		$title = JText::sprintf('COM_JOOMLEAGUE_EVENTSRANKING_PAGE_TITLE', $title);
		$document->setTitle($title);
		$this->assign('title', $title);
		parent::display($tpl);
	}

}
?>
