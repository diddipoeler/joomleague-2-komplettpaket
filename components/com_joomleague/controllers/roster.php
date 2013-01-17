<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class JoomleagueControllerRoster extends JoomleagueController
{
	function display( )
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( 'view', 'roster' );

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
		$sr = $this->getModel( 'roster', 'JoomleagueModel' );
		$sr->set( '_name', 'roster' );
		if ( !JError::isError( $sr ) )
		{
			$view->setModel ( $sr );
		}

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	function favplayers()
	{
		$db  = JFactory::getDBO();
		$jlm = $this->getModel( 'project', 'JoomleagueModel' );
		$jl = $jlm->getProject();

		$favteam = explode( ',', $jl->fav_team );
		if ( count( $favteam ) == 1 )
		{
			$teamid = $favteam[0];
			$query = 'SELECT id
					  FROM #__joomleague_project_team tt
					  WHERE tt.project_id = ' . $jl->id . '
					  AND tt.team_id = ' . $teamid;

			$db->setQuery( $query );
			$projectteamid = $db->loadResult();

			JRequest::setVar( 'ttid', $projectteamid );
		}

		$this->display();
	}

}
?>