<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class JoomleagueControllerTreetonode extends JoomleagueController
{
	function display( )
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( 'view', 'treetonode' );

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
		$sm = $this->getModel( 'treetonode', 'JoomleagueModel' );
		$sm->set( '_name', 'treetonode' );
		if ( !JError::isError( $sm ) )
		{
			$view->setModel ( $sm );
		}

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

}
?>