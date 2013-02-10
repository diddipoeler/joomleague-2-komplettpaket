<?php
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');
require_once( JLG_PATH_SITE . DS . 'models' . DS . 'matrix.php' );
require_once( JLG_PATH_SITE . DS . 'models' . DS . 'results.php' );
require_once( JLG_PATH_SITE . DS . 'views' . DS . 'results' . DS . 'view.html.php' );

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.html.pane');
class JoomleagueViewResultsmatrix extends JoomleagueViewResults  {

	function display($tpl = null)
	{
		JHTML::_('behavior.mootools');
		$mainframe = JFactory::getApplication();
		$params = &$mainframe->getParams();
		// get a reference of the page instance in joomla
		$document = JFactory :: getDocument();
		$uri = JFactory :: getURI();
		// add the css files
		$version = urlencode(JoomleagueHelper::getVersion());
		$css		= 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);
		// add the matrix model
		$matrixmodel = new JoomleagueModelMatrix();
		// add the matrix config file
		$matrixconfig = $matrixmodel->getTemplateConfig('matrix');

		// add the results model
		$resultsmodel	= new JoomleagueModelResults();
		$project = $resultsmodel->getProject();
		
		// add some javascript
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript( JURI::base(true).'/components/com_joomleague/assets/js/results.js?v='.$version );
		// add the results config file
		$resultsconfig = $resultsmodel->getTemplateConfig('results');
		
		$mdlRound = JModel::getInstance("Round", "JoomleagueModel");
		$roundcode = $mdlRound->getRoundcode($resultsmodel->roundid);
		$rounds = JoomleagueHelper::getRoundsOptions($project->id, 'ASC', true);
		
		
		if (!isset($resultsconfig['switch_home_guest'])){$resultsconfig['switch_home_guest']=0;}
		if (!isset($resultsconfig['show_dnp_teams_icons'])){$resultsconfig['show_dnp_teams_icons']=0;}
		if (!isset($resultsconfig['show_results_ranking'])){$resultsconfig['show_results_ranking']=0;}
		$resultsconfig['show_matchday_dropdown']=0;
		// merge the 2 config files
		$config = array_merge($matrixconfig, $resultsconfig);

		$this->assignRef('project', 		$resultsmodel->getProject());
		$this->assignRef('overallconfig',	$resultsmodel->getOverallConfig());
		$this->assignRef('config',			array_merge($this->overallconfig, $config));
		$this->assignRef('tableconfig',		$matrixconfig);
		$this->assignRef('params', 			$params);
		$this->assignRef('showediticon',	$resultsmodel->getShowEditIcon());
		$this->assignRef('division',		$resultsmodel->getDivision());

		$this->assignRef( 'divisionid', $matrixmodel->getDivisionID() );
		$this->assignRef( 'division', $matrixmodel->getDivision() );
		$this->assignRef( 'teams', $matrixmodel->getTeamsIndexedByPtid( $matrixmodel->getDivisionID() ) );
		$this->assignRef( 'results', $matrixmodel->getMatrixResults( $matrixmodel->getProject()->id ) );
		$this->assignRef( 'favteams', $matrixmodel->getFavTeams() );

		$this->assignRef('matches',			$resultsmodel->getMatches());
		$this->assignRef('round',			$resultsmodel->roundid);
		$this->assignRef('roundid',			$resultsmodel->roundid);
		$this->assignRef('roundcode',		$roundcode);
		
		$options = $this->getRoundSelectNavigation($rounds);

		$this->assignRef('matchdaysoptions',$options);
		$this->assignRef('currenturl', 		JoomleagueHelperRoute::getResultsMatrixRoute($resultsmodel->getProject()->slug, $this->roundid));
		$this->assignRef('rounds',			$resultsmodel->getRounds());
		$this->assignRef('favteams',		$resultsmodel->getFavTeams($this->project));
		$this->assignRef('projectevents',	$resultsmodel->getProjectEvents());
		$this->assignRef('model',			$resultsmodel);
		$this->assignRef('isAllowed',		$resultsmodel->isAllowed());

		$this->assign('action', $uri->toString());
        
        $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );

		// Set page title
		$pageTitle = ($this->params->get('what_to_show_first', 0) == 0)
		? JText::_('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE').' & ' . JText :: _('COM_JOOMLEAGUE_MATRIX_PAGE_TITLE')
		: JText::_('COM_JOOMLEAGUE_MATRIX_PAGE_TITLE').' & ' . JText :: _('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE');
		if ( isset( $this->project->name ) )
		{
			$pageTitle .= ' - ' . $this->project->name;
		}
		$document->setTitle($pageTitle);
		/*
		 //build feed links
		 $feed = 'index.php?option=com_joomleague&view=results&p='.$this->project->id.'&format=feed';
		 $rss = array('type' => 'application/rss+xml', 'title' => JText::_('COM_JOOMLEAGUE_RESULTS_RSSFEED'));

		 // add the links
		 $document->addHeadLink(JRoute::_($feed.'&type=rss'), 'alternate', 'rel', $rss);
		 */
		JLGView::display($tpl);
	}

	function getRoundSelectNavigation(&$rounds)
	{
		$options = array();
		foreach ($rounds as $r)
		{
			$link = JoomleagueHelperRoute::getResultsMatrixRoute($this->project->slug, $r->value);
			$options[] = JHTML::_('select.option', $link, $r->text);
		}
		return $options;
	}
}
?>
