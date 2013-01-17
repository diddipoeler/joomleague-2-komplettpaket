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

		parent::display($tpl);
	}

}
?>