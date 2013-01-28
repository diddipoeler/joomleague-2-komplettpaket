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
			$divisions = $model->getDivisions();
			$this->assignRef('divisions', $divisions);
		}
		
		if(!is_null($project)) {
			$this->assignRef( 'favteams', $model->getFavTeams() );
		}
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