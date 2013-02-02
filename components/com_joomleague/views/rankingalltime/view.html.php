<?php
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

jimport('joomla.application.component.view');

require_once (JLG_PATH_ADMIN .DS.'models'.DS.'divisions.php');

class JoomleagueViewRankingAllTime extends JLGView {
	
	function display($tpl = null) 
	{
		// Get a refrence of the page instance in joomla
		$document = & JFactory :: getDocument();
		$uri = & JFactory :: getURI();

		
		 
// 		$model = JModel::getInstance("Ranking", "JoomleagueModel");
// 		
//     $config = $model->getTemplateConfig($this->getName());
// 		$project = $model->getProject();
// 		
//     $mdlRound = JModel::getInstance("Rounds", "JoomleagueModel");
//     $rounds = $mdlRound->getRoundsOptions($project->id);

    $modelallseasons = & $this->getModel();
    $leagueallseasons = $modelallseasons->getLeagueSeasons();
		$this->assignRef('allseasons',    $leagueallseasons);
		$this->assignRef('leaguename',    $modelallseasons->getLeagueName());
    $this->project->name = $this->leaguename;
    $this->assignRef( 'alltimepoints', $modelallseasons->getAllTimePoints() );
    $this->assignRef( 'projectids', $modelallseasons->getAllProject() );
    $project_ids = implode (",", $this->projectids);
		$this->assignRef( 'project_ids', $project_ids );
    $this->assignRef('teams',			    $modelallseasons->getAllTeamsIndexedByPtid($project_ids));
    
// echo 'views rankingalltime teams<pre>';
// print_r($this->teams);
// echo '</pre>';
    
    foreach ( $leagueallseasons as $allseasons )
{

//JRequest::setVar('p', $allseasons->id, 'post');
JRequest::setVar( 'p', $allseasons->id );
// $post = JRequest::get('post');
// echo '<pre>'.print_r($post, true).'</pre>';


    $model = JModel::getInstance("Ranking", "JoomleagueModel");
		
    //$config = $model->getTemplateConfig($this->getName());
    
		//$project = $model->getProject();

		/*
    $mdlRound = JModel::getInstance("Rounds", "JoomleagueModel");
    $config = $model->getTemplateConfig($this->getName());
		$project = $model->getProject();
    $rounds = $mdlRound->getRoundsOptions($allseasons->id);
    */
    
    $model->setProjectId($allseasons->id);
    
    /*
    $this->assignRef('project', $project);
		$this->assignRef('overallconfig', $model->getOverallConfig());
		$this->assignRef('tableconfig'.$allseasons->id, $config);
		$this->assignRef('tableconfig', $config);
		$this->assignRef('config', $config);
    */
    
    $model->computeRanking();
    
    /*
    $this->assignRef('round'.$allseasons->id,     $model->round);
		$this->assignRef('part'.$allseasons->id,      $model->part);
		$this->assignRef('rounds'.$allseasons->id,    $rounds);
		$this->assignRef('divisions'.$allseasons->id, $model->getDivisions());
		*/
		
		/*
		//$model->type = 3;
    $this->assignRef('type',      $model->type);
		$this->assignRef('from'.$allseasons->id,      $model->from);
		$this->assignRef('to'.$allseasons->id,        $model->to);
		$this->assignRef('divLevel'.$allseasons->id,  $model->divLevel);
		*/
		
		$this->assignRef('currentRanking'.$allseasons->id,  $model->currentRanking);
		
    //$this->assignRef('previousRanking'.$allseasons->id, $model->previousRanking);
		
    //JRequest::setVar( 'type', 1 );
    //$model->computeRanking();
		//$this->assignRef('homeRank'.$allseasons->id,      $model->homeRank);
		//$this->assignRef('homeRank'.$allseasons->id,      $model->currentRanking);

		//JRequest::setVar( 'type', 2 );
		//$model->computeRanking();
		//$this->assignRef('awayRank'.$allseasons->id,      $model->awayRank);
		
		//$this->assignRef('current_round'.$allseasons->id, $model->current_round);
		
		//$this->assignRef('teams'.$allseasons->id,			    $model->getTeamsIndexedByPtid());

    $ausgabe = 'currentRanking'.$allseasons->id;    
    $modelallseasons->prepareRankingAllTime($this->$ausgabe);
    
// echo 'views rankingalltime currentRanking<pre>';
// print_r($this->$ausgabe);
// echo '</pre>';    
    
}	
		$this->assignRef('tableconfig', $modelallseasons->getAllTimeParams() );
		$this->assignRef('config', $modelallseasons->getAllTimeParams() );
    $this->assignRef('currentRanking', $modelallseasons->getCurrentRanking() );     
    
		$this->assign('action', $uri->toString());

    /*
		$frommatchday[] = JHTML :: _('select.option', '0', JText :: _('JL_RANKING_FROM_MATCHDAY'));
		$frommatchday = array_merge($frommatchday, $rounds);
		$lists['frommatchday'] = $frommatchday;
		$tomatchday[] = JHTML :: _('select.option', '0', JText :: _('JL_RANKING_TO_MATCHDAY'));
		$tomatchday = array_merge($tomatchday, $rounds);
		$lists['tomatchday'] = $tomatchday;

		$opp_arr = array ();
		$opp_arr[] = JHTML :: _('select.option', "0", JText :: _('JL_RANKING_FULL_RANKING'));
		$opp_arr[] = JHTML :: _('select.option', "1", JText :: _('JL_RANKING_HOME_RANKING'));
		$opp_arr[] = JHTML :: _('select.option', "2", JText :: _('JL_RANKING_AWAY_RANKING'));
    */
    
		//$lists['type'] = $opp_arr;
		
		$this->assignRef('lists', $lists);

		if (!isset ($config['colors'])) {
			$config['colors'] = "";
		}

		//$this->assignRef('colors', $this->config['colors'] );
		$this->assignRef('colors', $model->getColors($this->config['colors']));
		
		//$this->assignRef('result', $model->getTeamInfo());
		//		$this->assignRef( 'pageNav', $model->pagenav( "ranking", count( $rounds ), $sr->to ) );
		//		$this->assignRef( 'pageNav2', $model->pagenav2( "ranking", count( $rounds ), $sr->to ) );

		// Set page title
		$pageTitle = JText::_( 'JL_RANKING_PAGE_TITLE' );
		if ( isset( $this->project->name ) )
		{
			$pageTitle .= ': ' . $this->project->name;
		}
		$document->setTitle( $pageTitle );

		parent :: display($tpl);
	}
}
?>