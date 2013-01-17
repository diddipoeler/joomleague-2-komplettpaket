<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class JoomleagueViewTreetonode extends JLGView
{
	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document= JFactory::getDocument();

		$model = $this->getModel();
		$project = $model->getProject();
	//no treeko !!!
		$config = $model->getTemplateConfig('tree');
		
		$this->assignRef( 'project', $model->getProject() );
		$this->assignRef( 'overallconfig', $model->getOverallConfig() );
		$this->assignRef( 'config', $config );
		$this->assignRef( 'node', $model->getTreetonode() );
		$this->assignRef( 'roundname', $model->getRoundName() );
		$this->assignRef( 'model', $model );
		
		// Set page title
		///TODO: treeto name, no project name
		$document->setTitle(JText :: _('COM_JOOMLEAGUE_TREETO_PAGE_TITLE') . " - " . $this->project->name);
		
		parent::display( $tpl );
	}
}
?>