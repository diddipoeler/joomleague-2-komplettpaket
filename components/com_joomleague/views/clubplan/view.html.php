<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JoomleagueViewClubPlan extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$uri = JFactory::getURI();
		$model = $this->getModel();
		$project =& $model->getProject();
		$config=$model->getTemplateConfig($this->getName());
		$this->assignRef('project',$project);
		$this->assignRef('overallconfig',$model->getOverallConfig());
		$this->assignRef('config',$config);
		$this->assignRef('showclubconfig',$showclubconfig);
		$this->assignRef('favteams',$model->getFavTeams());
		$this->assignRef('club',$model->getClub());
		switch ($config['type_matches']) {
			case 0 : case 4 : // all matches
				$this->assignRef('allmatches',$model->getAllMatches($config['MatchesOrderBy']));
				break;
			case 1 : // home matches
				$this->assignRef('homematches',$model->getHomeMatches($config['MatchesOrderBy']));
				break;
			case 2 : // away matches
				$this->assignRef('awaymatches',$model->getAwayMatches($config['MatchesOrderBy']));
				break;
			default: // home+away matches
				$this->assignRef('homematches',$model->getHomeMatches($config['MatchesOrderBy']));
				$this->assignRef('awaymatches',$model->getAwayMatches($config['MatchesOrderBy']));
				break;
		}
		$this->assignRef('startdate',$model->getStartDate());
		$this->assignRef('enddate',$model->getEndDate());
		$this->assignRef('teams',$model->getTeams());
		$this->assignRef('model',$model);
		$this->assign('action',$uri->toString());

		// Set page title
		$pageTitle=JText::_('COM_JOOMLEAGUE_CLUBPLAN_TITLE');
		if (isset($this->club)){
			$pageTitle .= ': '.$this->club->name;
		}
		$document->setTitle($pageTitle);
		
		$this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );

		//build feed links
		$project_id=(!empty($this->project->id)) ? '&p='.$this->project->id : '';
		$club_id=(!empty($this->club->id)) ? '&cid='.$this->club->id : '';
		$rssVar=(!empty($this->club->id)) ? $club_id : $project_id;

		//$feed='index.php?option=com_joomleague&view=clubplan&cid='.$this->club->id.'&format=feed';
		$feed='index.php?option=com_joomleague&view=clubplan'.$rssVar.'&format=feed';
		$rss=array('type' => 'application/rss+xml','title' => JText::_('COM_JOOMLEAGUE_CLUBPLAN_RSSFEED'));

		// add the links
		$document->addHeadLink(JRoute::_($feed.'&type=rss'),'alternate','rel',$rss);
		parent::display($tpl);
	}

}
?>
