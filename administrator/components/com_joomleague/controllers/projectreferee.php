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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Joomleague Component Controller
 *
 * @author	Kurt Norgaz
 * @package	Joomleague
 * @since	1.5.02a
 */
class JoomleagueControllerProjectReferee extends JoomleagueController
{

	protected $view_list = 'projectreferees&task=projectreferee.display';
	
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add', 'display' );
		$this->registerTask( 'edit', 'display' );
		$this->registerTask( 'apply', 'save' );
	}

	function display()
	{
		$option = JRequest::getCmd('option');

		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$model		= $this->getModel ( 'projectreferees' );
		$viewType	= $document->getType();
		$view		= $this->getView  ( 'projectreferees', $viewType );
		$view->setModel( $model, true );  // true is for the default model;

		$projectws	= $this->getModel ( 'project' );
		$projectws->set('name','projectws');
		$projectws->setId( $mainframe->getUserState( $option . 'project', 0 ) );
		$view->setModel( $projectws );

		switch($this->getTask())
		{
			case 'add'	 :
				{
					JRequest::setVar( 'hidemainmenu', 0 );
					JRequest::setVar( 'layout', 'form' );
					JRequest::setVar( 'view', 'projectreferee' );
					JRequest::setVar( 'edit', false );

					$model = $this->getModel( 'projectreferee' );
					#$model->checkout();
				} break;

			case 'edit'	:
				{
					$model = $this->getModel ( 'projectreferee' );
					$viewType = $document->getType();
					$view = $this->getView  ( 'projectreferee', $viewType );
					$view->setModel( $model, true );  // true is for the default model;

					$projectws = $this->getModel ( 'project' );
					$projectws->set('name','projectws');
					$projectws->setId( $mainframe->getUserState( $option . 'project', 0 ) );
					$view->setModel( $projectws );

					JRequest::setVar( 'hidemainmenu', 0 );
					JRequest::setVar( 'layout', 'form' );
					JRequest::setVar( 'view', 'projectreferee' );
					JRequest::setVar( 'edit', true );

					// Checkout the project
					$model = $this->getModel( 'projectreferee' );
					#$model->checkout();
				} break;

		}
		parent::display();
	}

	function editlist()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$document = JFactory::getDocument();

		$model = $this->getModel ( 'projectreferees' );
		$viewType = $document->getType();
		$view = $this->getView  ( 'projectreferees', $viewType );
		$view->setModel( $model, true );  // true is for the default model;

		$projectws = $this->getModel ( 'project' );
		$projectws->set('name','projectws');
		$projectws->setId( $mainframe->getUserState( $option . 'project', 0 ) );
		$view->setModel( $projectws );

		$teamws->setId(  $mainframe->getUserState( $option . 'team', 0 ) );
		$view->setModel( $teamws );

		JRequest::setVar( 'hidemainmenu', 1 );
		JRequest::setVar( 'layout', 'editlist' );
		JRequest::setVar( 'view', 'projectreferees' );
		JRequest::setVar( 'edit', true );

		parent::display();
	}


	function save_projectrefereeslist()
	{
		$post 		= JRequest::get( 'post' );
		$cid 		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$project 	= JRequest::getVar( 'project', 'post' );
		$team_id 	= JRequest::getVar( 'team', 'post' );
		$post['id'] 		= (int) $cid[0];
		$post['project_id']	= (int) $project;
		$post['team_id']   	= (int) $team_id;
		$model = $this->getModel( 'projectreferees' );

		if ( $model->store( $post ) )
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_SAVED' );
		}
		else
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_ERROR_SAVE' ) . $model->getError();
		}

		$link = 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display';
		$this->setRedirect( $link, $msg );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN' );

		$post = JRequest::get( 'post' );

		// decription must be fetched without striping away html code
		$post['notes'] = JRequest::getVar( 'notes', 'none', 'post', 'STRING', JREQUEST_ALLOWHTML );
		$model = $this->getModel( 'projectreferee' );

		if ( $model->store( $post ) )
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_SAVED' );

		}
		else
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_ERROR_SAVE' ) . $model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ( $this->getTask() == 'save' )
		{
			$link = 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=projectreferee.edit&cid[]=' . $post['id'];
		}
		$this->setRedirect( $link, $msg );
	}

	// save the checked rows inside the project teams list
	function saveshort()
	{
		$post = JRequest::get( 'post' );
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cid );

		$model = $this->getModel( 'projectreferees' );
		/*
		for ( $x = 0; $x < count( $cid ); $x++ )
		{
			$post['birthday' . $cid[$x]] = JoomleagueHelper::convertDate( $post['birthday' . $cid[$x]], 0 );
		}
		*/
		#echo '<pre>'; print_r($post); echo '</pre>';
		#$model->storeshort( $cid, $post );

		if ( $model->saveshort( $cid, $post ) )
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_UPDATED' );
		}
		else
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_ERROR_UPDATED' ) . $model->getError();
		}

		$link = 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display';
		$this->setRedirect( $link, $msg );
	}

	function remove()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cid );

		if ( count( $cid ) < 1 )
		{
			JError::raiseError(500, JText::_( 'COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE' ) );
		}

		$model = $this->getModel( 'team' );

		if( !$model->delete($cid) )
		{
			echo "<script> alert('" . $model->getError( true ) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display' );
	}

	function cancel()
	{
		// Checkin the project
		$model = $this->getModel( 'projectreferee' );
		$model->checkin();

		$this->setRedirect( 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display');
	}

	function select()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();

		$mainframe->setUserState( $option . 'team', JRequest::getVar( 'team' ) );
		$this->setRedirect( 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display' );
	}

	function assign()
	{
		//redirect to ProjectReferees page, with a message
		$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_ASSIGN' );
		$this->setRedirect( 'index.php?option=com_joomleague&view=persons&task=person.display&layout=assignplayers&type=2&hidemainmenu=1', $msg );
	}

	function unassign()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cid );
		$model = $this->getModel( 'projectreferees' );
		$nDeleted = $model->unassign( $cid );
		if ( !$nDeleted )
		{
			$msg = JText::_( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_UNASSIGN' );
		}
		else
		{
			$msg = JText::sprintf( 'COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_UNASSIGNED', $nDeleted );
		}
		//redirect to projectreferee page, with a message
		$this->setRedirect( 'index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display', $msg );
	}
	
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'Projectreferee', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
}
?>