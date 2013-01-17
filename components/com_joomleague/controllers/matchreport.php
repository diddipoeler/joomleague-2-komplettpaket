<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerMatchReport extends JoomleagueController
{
    function display( )
    {
        // Get the view name from the query string
        $viewName = JRequest::getVar( "view", "matchreport" );

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
        $sr = $this->getModel( "matchreport", "JoomleagueModel" );
        $sr->set( "_name", "matchreport" );
        if (!JError::isError( $sr ) )
        {
            $view->setModel ( $sr );
        }

        $this->showprojectheading();
        if ( JRequest::getInt( 'mid', 0 ) == 0 ) 
        {
            $view->showreports();
        }
        else
        {
            $view->display();
        }
        $this->showbackbutton();
        $this->showfooter();
    }
}
