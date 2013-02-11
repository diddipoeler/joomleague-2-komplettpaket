<?php defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

jimport( 'joomla.application.component.view');

class JoomleagueViewjlallprojectrounds extends JLGView
{
	function display( $tpl = null )
	{
		
    // Get a refrence of the page instance in joomla
		$document = & JFactory::getDocument();
		$uri = &JFactory::getURI();		
				
		$model = & $this->getModel();
		$config	= $model->getTemplateConfig($this->getName());
		//$config	= $model->getAllRoundsParams();
    $project = $model->getProject();
		
		$this->assignRef('project', $project);
		
//     $mdlProject = JModel::getInstance("Project", "JoomleagueModel");
		
    $this->assignRef('projectid', $this->project->id );
    
    $this->assignRef('projectmatches', $model->getProjectMatches() );
    
    $this->assignRef('rounds',			$model->getRounds());
    
    $this->assignRef('overallconfig',	$model->getOverallConfig());
 		$this->assignRef('config',			array_merge($this->overallconfig, $config));
		//$this->assignRef('config', $model->getAllRoundsParams());
    	
//     echo '<br />getRounds<pre>~'.print_r($this->rounds,true).'~</pre><br />';
    
    $this->assignRef('favteams',		$model->getFavTeams($this->projectid));
//     echo '<br />getFavTeams<pre>~'.print_r($this->favteams,true).'~</pre><br />';
    
		$this->assignRef('projectteamid',		$model->getProjectTeamID($this->favteams));
		
    $this->assignRef('content',			$model->getRoundsColumn($this->rounds,$this->config));
        
		$this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info', 0));
		    
    

		parent::display( $tpl );
	}
}
?>