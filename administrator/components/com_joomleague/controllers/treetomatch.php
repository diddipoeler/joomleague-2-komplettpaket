<?php
/**
* @copyright	Copyright (C) 2007 Joomteam.de. All rights reserved.
* @license		GNU/GPL,see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License,and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Joomleague Component Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerTreetomatch extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	function display()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

	 	$model=$this->getModel('treetomatchs');
		$viewType=$document->getType();
		$view=$this->getView('treetomatchs',$viewType);
		$view->setModel($model,true);	// true is for the default model;

		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws);
		if ( $nid = JRequest::getVar( 'nid', null, '', 'array' ) )
		{
			$mainframe->setUserState( $option . 'node_id', $nid[0] );
		}
		$nodews = $this->getModel( 'treetonode' );
		$nodews->set('name','nodews');
		$nodews->setId( $mainframe->getUserState( $option.'node_id') );
		$view->setModel( $nodews );
		
		switch($this->getTask())
		{
			case 'edit'	:
			{
				$model=$this->getModel('treetomatch');
				$viewType=$document->getType();
				$view=$this->getView('treetomatch',$viewType);
				$view->setModel($model,true);	// true is for the default model;

				$projectws=$this->getModel('project');
				$projectws->set('name','projectws');
				$projectws->setId($mainframe->getUserState($option.'project',0));
				$view->setModel($projectws);

				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','treetomatch');
				JRequest::setVar('edit',true);

				$model=$this->getModel('treetomatch');
				$model->checkout();
			} break;

			case 'matchadd':
			{
				JRequest::setVar('matchadd',true);
			} break;

		}
		parent::display();
	}

	function editlist()
	{
		$option = JRequest::getCmd('option');

		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$model		= $this->getModel ('treetomatchs');
		$viewType	= $document->getType();
		$view		= $this->getView  ('treetomatchs', $viewType);
		$view->setModel($model, true);  // true is for the default model;

		$projectws = $this->getModel ('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option . 'project', 0));
		$view->setModel($projectws);
		
		if ( $nid = JRequest::getVar( 'nid', null, '', 'array' ) )
		{
			$mainframe->setUserState( $option . 'node_id', $nid[0] );
		}
		$nodews = $this->getModel( 'treetonode' );
		$nodews->set('name','nodews');
		$nodews->setId( $mainframe->getUserState( $option.'node_id') );
		$view->setModel( $nodews );
		
		JRequest::setVar('hidemainmenu', 0);
		JRequest::setVar('layout', 'editlist' );
		JRequest::setVar('view', 'treetomatchs');
		JRequest::setVar('edit', true);

		// Checkout the project
		//	$model = $this->getModel('treetomatchs');

		parent::display();
	}

	function save_matcheslist()
	{
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('treetomatchs');

		if ($model->store($post))
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_CTRL_SAVED');
		}
		else
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_CTRL_ERROR_SAVE') . $model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		//$model->checkin();
		$link = 'index.php?option=com_joomleague&view=treetonodes&controller=treetonode';
		$this->setRedirect($link, $msg);
	}

	function publish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_PUBLISH'));}
		$model=$this->getModel('treetomatch');
		if (!$model->publish($cid,1)){echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";}
		$this->setRedirect('index.php?option=com_joomleague&controller=treetomatch&view=treetomatchs');
	}

	function unpublish()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_UNPUBLISH'));}
		$model=$this->getModel('treetomatch');
		if (!$model->publish($cid,0)){echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";}
		$this->setRedirect('index.php?option=com_joomleague&controller=treetomatch&view=treetomatchs');
	}
}
?>