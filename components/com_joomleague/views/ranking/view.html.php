<?php
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

jimport('joomla.application.component.view');

require_once (JLG_PATH_ADMIN .DS.'models'.DS.'divisions.php');

class JoomleagueViewRanking extends JLGView {
	
	function display($tpl = null) 
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory :: getDocument();
		$uri = JFactory :: getURI();

		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		$project = $model->getProject();
		$rounds = JoomleagueHelper::getRoundsOptions($project->id, 'ASC', true);
			
		$model->setProjectId($project->id);
		
		$this->assignRef('project', $project);
		$this->assignRef('overallconfig', $model->getOverallConfig());
		$this->assignRef('tableconfig', $config);
		$this->assignRef('config', $config);

		$model->computeRanking();

		$this->assignRef('round',     $model->round);
		$this->assignRef('part',      $model->part);
		$this->assignRef('rounds',    $rounds);
		$this->assignRef('divisions', $model->getDivisions());
		$this->assignRef('type',      $model->type);
		$this->assignRef('from',      $model->from);
		$this->assignRef('to',        $model->to);
		$this->assignRef('divLevel',  $model->divLevel);
		$this->assignRef('currentRanking',  $model->currentRanking);
		$this->assignRef('previousRanking', $model->previousRanking);
		$this->assignRef('homeRank',      $model->homeRank);
		$this->assignRef('awayRank',      $model->awayRank);
		$this->assignRef('current_round', $model->current_round);
		$this->assignRef('teams',			    $model->getTeamsIndexedByPtid());
		$this->assignRef('previousgames', $model->getPreviousGames());
		$this->assign('action', $uri->toString());

		$frommatchday[] = JHTML :: _('select.option', '0', JText :: _('COM_JOOMLEAGUE_RANKING_FROM_MATCHDAY'));
		$frommatchday = array_merge($frommatchday, $rounds);
		$lists['frommatchday'] = $frommatchday;
		$tomatchday[] = JHTML :: _('select.option', '0', JText :: _('COM_JOOMLEAGUE_RANKING_TO_MATCHDAY'));
		$tomatchday = array_merge($tomatchday, $rounds);
		$lists['tomatchday'] = $tomatchday;

		$opp_arr = array ();
		$opp_arr[] = JHTML :: _('select.option', "0", JText :: _('COM_JOOMLEAGUE_RANKING_FULL_RANKING'));
		$opp_arr[] = JHTML :: _('select.option', "1", JText :: _('COM_JOOMLEAGUE_RANKING_HOME_RANKING'));
		$opp_arr[] = JHTML :: _('select.option', "2", JText :: _('COM_JOOMLEAGUE_RANKING_AWAY_RANKING'));

		$lists['type'] = $opp_arr;
		$this->assignRef('lists', $lists);

		if (!isset ($config['colors'])) {
			$config['colors'] = "";
		}

		$this->assignRef('colors', $model->getColors($config['colors']));
		//$this->assignRef('result', $model->getTeamInfo());
		//		$this->assignRef( 'pageNav', $model->pagenav( "ranking", count( $rounds ), $sr->to ) );
		//		$this->assignRef( 'pageNav2', $model->pagenav2( "ranking", count( $rounds ), $sr->to ) );

		// Set page title
		$pageTitle = JText::_( 'COM_JOOMLEAGUE_RANKING_PAGE_TITLE' );
		if ( isset( $this->project->name ) )
		{
			$pageTitle .= ': ' . $this->project->name;
		}
		$document->setTitle( $pageTitle );
		
		parent :: display($tpl);
	}
		
}
?>
