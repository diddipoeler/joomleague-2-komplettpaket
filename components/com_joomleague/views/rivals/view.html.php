<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class JoomleagueViewRivals extends JLGView
{
	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document	= JFactory::getDocument();

		$model	= $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		
		$this->assignRef( 'project', $model->getProject() );
		$this->assignRef( 'overallconfig', $model->getOverallConfig() );
		if ( !isset( $this->overallconfig['seperator'] ) )
		{
			$this->overallconfig['seperator'] = "-";
		}
		$this->assignRef( 'config', $config );
		$this->assignRef( 'opos', $model->getOponents() );
		$this->assignRef( 'team', $model->getTeam() );
		// Set page title
		$pageTitle=JText::_( 'COM_JOOMLEAGUE_RIVALS_PAGE_TITLE' );
		if ( isset( $this->team ) )
		{
			$pageTitle .= ': ' . $this->team->name;
		}
		$document->setTitle( $pageTitle );

		parent::display( $tpl );
	}
}
?>