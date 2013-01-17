<?php defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/*
 *
 *
 */
class JoomleagueController extends JLGController
{
	public function display($cachable = false, $urlparams = false)
	{
		$this->showprojectheading( $cachable );
//		$this->showbackbutton();
//		$this->showfooter();
//		parent::display();
	}

	//--------------------------------------------------------------------------
	// Generic functionality.
	//--------------------------------------------------------------------------

	function showprojectheading( $cachable = false )
	{
//		$document = JFactory::getDocument();
//
//		// Get the view name from the query string
//		$viewName = JRequest::getVar( "view", "projectheading" );
//	  	$viewType = $document->getType();
//
//		// Get the view
//		//$view = & $this->getView($viewName);
//		$view = & $this->getView($viewName, $viewType);
//
//		// Get the project model
//		$jl = $this->getModel("project");
//
//		if (!JError::isError($jl) )
//		{
//			$view->setModel($jl, false);
//		}

		//$view->display();
		parent::display();
	}

	function showbackbutton( )
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( 'view', 'backbutton' );

		// Get the view
		$view =& $this->getView( $viewName );

		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if (!JError::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}

		$view->display();
	}

	function showfooter( )
	{
		// Get the view name from the query string
		//$viewName = JRequest::getVar( "view", "footer" );

		// Get the view
		//$view = & $this->getView( $viewName );

		// Get the joomleague model
		//$version = $this->getModel( "version", "JoomleagueModel" );
		//$version->set( "_name", "version" );
		/*$footer = $this->getModel( "footer", "JoomleagueModel" );
		$footer->set( "_name", "footer" );*/
		/*if (!JError::isError( $version ) )
		{
			$view->setModel ( $version );
		}*/
		/*if (!JError::isError( $footer ) )
		{
			$view->setModel ( $footer );
		}

		$view->display();*/
		parent::display();
	}

	//--------------------------------------------------------------------------
	//
	//--------------------------------------------------------------------------

}
?>