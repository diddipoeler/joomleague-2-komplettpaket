<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerCurve extends JoomleagueController
{
    function display( )
    {
        // Get the view name from the query string
        $viewName = JRequest::getVar( "view", "curve" );

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
        $sr = $this->getModel( "curve", "JoomleagueModel" );
        $sr->set( "_name", "curve" );
        if (!JError::isError( $sr ) )
        {
            $view->setModel ( $sr );
        }
        
        $this->showprojectheading();
        $view->display();
        $this->showbackbutton();
        $this->showfooter();
    }
        
    function chartdata()
    {
        $viewName = JRequest::getVar( "view", "curve" );

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
        $sr = $this->getModel( "curve", "JoomleagueModel" );
        $sr->set( "_name", "curve" );
        if (!JError::isError( $sr ) )
        {
            $view->setModel ( $sr );
        }
        
        $view->setLayout( "chartdata" );
        $view->display();
    }
}
