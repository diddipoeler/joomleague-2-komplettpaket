<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerAjax extends JoomleagueController
{
	function getprojectsoptions()
	{
		$app = &JFactory::getApplication();
		
		$season = Jrequest::getInt('s');
		$league = Jrequest::getInt('l');
		$ordering = Jrequest::getInt('o');
		
		$model = $this->getModel('ajax');
		
		$res = $model->getProjectsOptions($season, $league, $ordering);
		
		echo json_encode($res);
		
		$app->close();
	}
	
	function getroute()
	{
		$app = &JFactory::getApplication();
		
		$view = Jrequest::getCmd('view');
	
		switch ($view)
		{
			case "teaminfo":
				$link = JoomleagueHelperRoute::getTeamInfoRoute( JRequest::getVar('p'), JRequest::getVar('tid') );
				break;
				
			case "resultsranking":
				$link = JoomleagueHelperRoute::getResultsRankingRoute( JRequest::getVar('p') );
				break;
				
			case "rankingmatrix":
				$link = JoomleagueHelperRoute::getRankingMatrixRoute( JRequest::getVar('p') );
				break;
				
			case "resultsrankingmatrix":
				$link = JoomleagueHelperRoute::getResultsRankingMatrixRoute( JRequest::getVar('p') );
				break;
				
			case "teamplan":
				$link = JoomleagueHelperRoute::getTeamPlanRoute( JRequest::getVar('p'), JRequest::getVar('tid'), JRequest::getVar('division') );
				break;
				
			case "roster":
				$link = JoomleagueHelperRoute::getPlayersRoute( JRequest::getVar('p'), JRequest::getVar('tid') );
				break;
				
			case "eventsranking":				
				$link = JoomleagueHelperRoute::getEventsRankingRoute( JRequest::getVar('p'), JRequest::getVar('division') );
				break;
				
			case "curve":
				$link = JoomleagueHelperRoute::getCurveRoute( JRequest::getVar('p'),0,0, JRequest::getVar('division') );
				break;
				
			case "statsranking":
				$link = JoomleagueHelperRoute::getStatsRankingRoute( JRequest::getVar('p'), JRequest::getVar('division') );
				break;
								
			default:
			case "ranking":
				$link = JoomleagueHelperRoute::getRankingRoute( JRequest::getVar('p'),null,null,null,0,JRequest::getVar('division') );
		}
		
		echo json_encode($link);
		
		$app->close();
	}
}