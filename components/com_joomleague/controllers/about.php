<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerAbout extends JLGController
{
	function display( )
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( "view", "about" );

		// Get the view
		$view = & $this->getView( $viewName );

		// Get the joomleague model
		$jl = $this->getModel( "joomleague", "JoomleagueModel" );
		$jl->set( "_name", "joomleague" );
		if (!JError::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}

		// Get the model
		$vr = $this->getModel( "version", "JoomleagueModel" );
		$vr->set( "_name", "version" );
		if (!JError::isError( $vr ) )
		{
			$view->setModel ( $vr );
		}

		// Display view
		// $this->showprojectheading(); No heading -> specific heading
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}
}
