<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerClubInfo extends JoomleagueController
{
	function display( )
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( "view", "clubinfo" );

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
		$sc = $this->getModel( "clubinfo", "JoomleagueModel" );
		$sc->set( "_name", "clubinfo" );
		if (!JError::isError( $sc ) )
		{
			$view->setModel ( $sc );
		}

		// Get the Google map model
		$gm = $this->getModel( "googlemap", "JoomleagueModel" );
		$gm->set( "_name", "googlemap" );
		if (!JError::isError( $gm ) )
		{
			$view->setModel ( $gm );
		}

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	function edit( )
	{
		$viewName = JRequest::getVar( "view", "clubinfo" );

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
		$sc = $this->getModel( "clubinfo", "JoomleagueModel" );
		$sc->set( "_name", "clubinfo" );
		if (!JError::isError( $sc ) )
		{
			$view->setModel ( $sc );
		}

		$this->showprojectheading();
		$view->setLayout( "edit" );
		$view->edit();
		$this->showbackbutton();
		$this->showfooter();
	}

	function save( )
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN' );
		
		$cid = JRequest::getInt( "cid", 0 );
		$post = JRequest::get( 'post' );

		if( $cid > 0 )
		{
			$club = & JTable::getInstance( "Club", "Table" );
			$club->load( $cid );
			$club->bind( $post );
			$params =& JComponentHelper::getParams('com_joomleague');

			if ( ( $club->store() ) &&
			( $params->get('cfg_edit_club_info_update_notify') == "1" ) )
			{
				$db = JFactory::getDBO();
				$user = JFactory::getUser();

				$query = "SELECT email
                         FROM #__users 
                         WHERE usertype = 'Super Administrator' 
                            OR usertype = 'Administrator'";

				$db->setQuery( $query );

				$to = $db->loadResultArray();

				$subject = addslashes(
				sprintf(
				JText::_( "COM_JOOMLEAGUE_ADMIN_EDIT_CLUB_INFO_SUBJECT" ),
				$club->name ) );
				$message = addslashes(
				sprintf(
				JText::_( "COM_JOOMLEAGUE_ADMIN_EDIT_CLUB_INFO_MESSAGE" ),
				$user->name,
				$club->name ) );
				$message .= $this->_getShowClubInfoLink();

				JUtility::sendMail( '', '', $to, $subject, $message );
			}
		}
		$this->setRedirect( $this->_getShowClubInfoLink() );
	}

	function cancel( )
	{
		$this->setRedirect( $this->_getShowClubInfoLink() );
	}

	function _getShowClubInfoLink( )
	{
		$p = JRequest::getInt( "p", 0 );
		$cid = JRequest::getInt( "cid", 0 );
		$link = JoomleagueHelperRoute::getClubInfoRoute( $p, $cid );
		return $link;
	}
}
?>
