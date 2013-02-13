<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class JoomleagueViewMatrix extends JLGView
{
	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document= JFactory::getDocument();

		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		$project =& $model->getProject();
		
		$this->assignRef( 'model', $model);
		$this->assignRef( 'project', $project);
		$this->assignRef( 'overallconfig', $model->getOverallConfig() );

		$this->assignRef( 'config', $config );

		$this->assignRef( 'divisionid', $model->getDivisionID() );
		$this->assignRef( 'roundid', $model->getRoundID() );
		$this->assignRef( 'division', $model->getDivision() );
		$this->assignRef( 'round', $model->getRound() );
		$this->assignRef( 'teams', $model->getTeamsIndexedByPtid( $model->getDivisionID() ) );
		$this->assignRef( 'results', $model->getMatrixResults( $model->projectid ) );
		
		if ($project->project_type == 'DIVISIONS_LEAGUE' && !$this->divisionid )
		{
			$ranking_reason = array();
      $divisions = $model->getDivisions();
			$this->assignRef('divisions', $divisions);
			
			foreach ( $this->results as $result ) 
      {
      foreach ( $this->teams as $teams ) 
        {
        
        if ( $result->division_id )
        {

        if ( ($result->projectteam1_id == $teams->projectteamid) || ($result->projectteam2_id == $teams->projectteamid) ) 
        {
        $teams->division_id = $result->division_id;
        
        if ( $teams->start_points )
        {
        
        if ( $teams->start_points < 0 )
        {
        $color = "red";
        }
        else
        {
        $color = "green";
        }
        
        $ranking_reason[$result->division_id][$teams->name] = '<font color="'.$color.'">'.$teams->name.': '.$teams->start_points.' Punkte Grund: '.$teams->reason.'</font>';
        }
        
        }

        }
        
        }
  
      }
      
    foreach ( $this->divisions as $row )
		{
		if ( isset($ranking_reason[$row->id]) )
		{
    $row->notes = implode(", ",$ranking_reason[$row->id]);
    }
    
    }
    
		}
		
		if(!is_null($project)) {
			$this->assignRef( 'favteams', $model->getFavTeams() );
		}
    
    $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
		
    // Set page title
		$pageTitle = JText::_( 'COM_JOOMLEAGUE_MATRIX_PAGE_TITLE' );
		if ( isset( $project->name ) )
		{
			$pageTitle .= ': ' . $project->name;
		}
		$document->setTitle( $pageTitle );
		
		parent::display( $tpl );
	}
}
?>