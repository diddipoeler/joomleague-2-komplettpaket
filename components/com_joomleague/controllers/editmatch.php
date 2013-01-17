<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * Joomleague Component editmatch Controller
 *
 * @author	JoomLeague Team
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueControllerEditMatch extends JLGController
{

	function display()
	{
		/*
		// Get the view name from the query string
		$viewName = JRequest::getVar( 'view', 'editmatch' );

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
		$sr = $this->getModel( 'editmatch', 'JoomleagueModel' );
		$sr->set( '_name', 'editmatch' );
		if (!JError::isError( $sr ) )
		{
			$view->setModel ( $sr );
		}
		*/
		//$view->display();
	}

	function cancel()
	{
		$mid = JRequest::getInt( 'mid', 0 );

		$match = & JTable::getInstance( 'Match', 'Table' );
		$match->load( $mid );
		$match->checkin( $mid );
	}

	function load()
	{
		$mid = JRequest::getInt( 'mid', 0 );

		$match = & JTable::getInstance( 'Match', 'Table' );
		$match->load( $mid );
		$match->checkout( $user->id );

		$this->display();
	}

	function savematch()
	{
		JRequest::checkToken() or jexit( JText::_( 'COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN' ) );

		$msg				= '';
		$post				= JRequest::get( 'post' );
		$summary			= JRequest::getVar( 'summary', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['summary']	= $summary;
		$preview			= JRequest::getVar( 'preview', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['preview']	= $preview;

		$project_id	= JRequest::getInt( 'p', '', 'post', 'int' );
		$round_id	= JRequest::getInt( 'rid', '', 'post', 'int' );
		$match_id	= JRequest::getInt( 'mid', '', 'post', 'int' );
		if ($post['alt_decision'] == 0)
		{
			$post['team1_result_decision'] = null;
			$post['team2_result_decision'] = null;
		}
		$model = $this->getModel( 'editmatch' );

		$user = JFactory::getUser();

		$isAllowed = (($model->isAllowed()) || ($model->isMatchAdmin( $this->match->id, $user->id )));
		if ( !$isAllowed )
		{
			$link = JoomleagueHelperRoute::getResultsRoute( $project_id );

			jexit( JText::_( 'You are not allowed to change matchdata. What are you doing here???' ) );
			$this->setRedirect( $link, JText::_( $msg ) );
		}
		#$post['match_date' . $cid[$x]] = JoomleagueHelper::convertDate( $post['match_date' . $cid[$x]], 0 );
		if ( $model->savedetails( $post ) )
		{
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$project_id);
			$cache->clean();

			$msg = 'Changes on selected match were saved';
		}
		else
		{
			$msg = 'Error while saving changes on selected match';
		}
		$link = JoomleagueHelperRoute::getEditMatchRoute( $project_id, $match_id );
		$this->setRedirect( $link,JText::_( $msg ) );
	}

}
?>