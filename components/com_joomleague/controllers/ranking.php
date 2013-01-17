<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerRanking extends JoomleagueController
{
    function display( )
    {
        $this->showprojectheading();
        $this->showranking();
        $this->showbackbutton();
        $this->showfooter();
    }

    function showranking( )
    {
        // Get the view name from the query string
        $viewName = JRequest::getVar( "view", "ranking" );

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
        $sr = $this->getModel( "ranking", "JoomleagueModel" );
        $sr->set( "_name", "ranking" );
        if (!JError::isError( $sr ) )
        {
            $view->setModel ( $sr );
        }

        $view->display();
    }
}
