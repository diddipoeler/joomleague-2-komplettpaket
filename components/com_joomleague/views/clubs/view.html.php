<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class JoomleagueViewClubs extends JLGView
{
	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document= JFactory::getDocument();

		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		$project = $model->getProject();
		$division = $model->getDivision() ;
		$overallconfig = $model->getOverallConfig();
		$clubs = $model->getClubs();
		$this->assignRef( 'project',  $project);
		$this->assignRef( 'division', $division);
		$this->assignRef( 'overallconfig', $overallconfig );
		$this->assignRef( 'config', $config );

		$this->assignRef( 'clubs', $clubs );

		// Set page title
		$pageTitle = JText::_( 'COM_JOOMLEAGUE_CLUBS_PAGE_TITLE' );
		if ( isset( $this->project ) )
		{
			$pageTitle .= ' - ' . $this->project->name;
			if ( isset( $this->division ) )
			{
				$pageTitle .= ' : ' . $this->division->name;
			}
		}
		$document->setTitle( $pageTitle );

		parent::display( $tpl );
	}
}
?>