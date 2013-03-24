<?php
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

jimport('joomla.application.component.view');

//require_once (JLG_PATH_ADMIN . DS . 'models' . DS . 'divisions.php');

class JoomleagueViewRankingAllTime extends JLGView
{

    function display($tpl = null)
    {
        // Get a refrence of the page instance in joomla
        $document = &JFactory::getDocument();
        $uri = &JFactory::getURI();
        //$this->project->id = JRequest::getInt('p', 0);

         $model = &$this->getModel();
//         $leagueallseasons = $modelallseasons->getLeagueSeasons();
//         $this->assignRef('allseasons', $leagueallseasons);
//         $this->assignRef('leaguename', $modelallseasons->getLeagueName());


//         $this->project->name = $this->leaguename;
//         $this->assignRef('alltimepoints', $modelallseasons->getAllTimePoints());
         $this->assignRef('projectids', $model->getAllProject());
         $project_ids = implode(",", $this->projectids);
         $this->assignRef('project_ids', $project_ids);
         $this->assignRef('teams', $model->getAllTeamsIndexedByPtid($project_ids));
         $this->assignRef('matches', $model->getAllMatches($project_ids));
         $this->assignRef('ranking', $model->getAllTimeRanking());

/*
echo 'views rankingalltime project_ids<pre>';
print_r($project_ids);
echo '</pre>';
*/

/*
echo 'views rankingalltime teams<pre>';
print_r($this->teams);
echo '</pre>';
*/

/*
echo 'views rankingalltime matches<pre>';
print_r($this->matches);
echo '</pre>';
*/

/*
echo 'views rankingalltime ranking<pre>';
print_r($this->ranking);
echo '</pre>';
*/

        /*
        foreach ($leagueallseasons as $allseasons) {

            JRequest::setVar('p', $allseasons->id);

            $model = JModel::getInstance("Ranking", "JoomleagueModel");
            $model->setProjectId($allseasons->id);
            $model->computeRanking();
            $this->assignRef('currentRanking' . $allseasons->id, $model->currentRanking);
            $ausgabe = 'currentRanking' . $allseasons->id;
            $modelallseasons->prepareRankingAllTime($this->$ausgabe);

            // echo 'views rankingalltime currentRanking<pre>';
            // print_r($this->$ausgabe);
            // echo '</pre>';

        }
        */

        
        $this->assignRef('tableconfig', $model->getAllTimeParams());
        $this->assignRef('config', $model->getAllTimeParams());
        $this->assignRef('currentRanking', $model->getCurrentRanking());
        $this->assign('action', $uri->toString());
        $this->assignRef('lists', $lists);
        $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info', 0));
/*        
        if (!isset($config['colors'])) {
            $config['colors'] = "";
        }
*/
        $this->assignRef('colors', $model->getColors($this->config['colors']));
        

        // Set page title
        $pageTitle = JText::_('JL_RANKING_PAGE_TITLE');
        
        $document->setTitle($pageTitle);

        parent::display($tpl);
    }
}
?>