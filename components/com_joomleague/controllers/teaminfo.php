<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerTeamInfo extends JoomleagueController
{
    function display( )
    {
        // Get the view name from the query string
        $viewName = JRequest::getVar( "view", "teaminfo" );

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
        $sc = $this->getModel( "teaminfo", "JoomleagueModel" );
        $sc->set( "_name", "teaminfo" );
        if (!JError::isError( $sc ) )
        {
            $view->setModel ( $sc );
        }

        $this->showprojectheading();
        $view->display();
        $this->showbackbutton();
        $this->showfooter();
    }
}
