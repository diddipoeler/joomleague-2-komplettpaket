<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerMatrix extends JoomleagueController
{
    function display( )
    {
        $this->showprojectheading();
        $this->showmatrix();
        $this->showbackbutton();
        $this->showfooter();
    }

    function showmatrix( )
    {
        // Get the view name from the query string
        $viewName = JRequest::getVar( "view", "matrix" );

        // Get the view
        $view = & $this->getView( $viewName );

        // Get the joomleague model
        $jl = $this->getModel( "joomleague", "JoomleagueModel" );
        $jl->set( "_name", "joomleague" );
        if (!JError::isError( $jl ) )
        {
            $view->setModel ( $jl );
        }

        // Get the joomleague model
        $sm = $this->getModel( "matrix", "JoomleagueModel" );
        $sm->set( "_name", "matrix" );
        if (!JError::isError( $sm ) )
        {
            $view->setModel ( $sm );
        }

        $view->display();
    }
}
