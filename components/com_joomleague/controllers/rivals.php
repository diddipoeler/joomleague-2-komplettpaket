<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class JoomleagueControllerRivals extends JoomleagueController
{
	function display( )
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( 'view', 'rivals' );

		// Get the view
		$view =& $this->getView( $viewName );

		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if (!JError::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}

		// Get the joomleague model
		$sr = $this->getModel( 'rivals', 'JoomleagueModel' );
		$sr->set( '_name', 'rivals' );
		if ( !JError::isError( $sr ) )
		{
			$view->setModel ( $sr );
		}

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

}
?>