<?php
/**
* @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
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
jimport('joomla.filesystem.file');

/**
 * Joomleague Component Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerPosition extends JoomleagueController
{

	protected $view_list = 'positions';
	
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
		$mainframe 		= JFactory::getApplication();
		$sports_type	= JRequest::getInt('filter_sports_type',0);
		$mainframe->setUserState($option.'.positions.filter_sports_type',$sports_type);
		
		switch($this->getTask())
		{
			case 'add':
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','position');
				JRequest::setVar('edit',false);
				// Checkout the project
				$model=$this->getModel('position');
				$model->checkout();
			} break;
			case 'edit':
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','position');
				JRequest::setVar('edit',true);
				// Checkout the project
				$model=$this->getModel('position');
				$model->checkout();
			} break;
		}
		parent::display();
	}

	// save position in cid and save/update also the events associated with the saved position
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		$model=$this->getModel('position');
		if ($model->store($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_CTRL_SAVED');
			$model2=$this->getModel('position_eventtype');
			if ($post['eventschanges_check']==1)
			{
				if ($post['id']==0){$post['id']=$model2->getDbo()->insertid();}
				$model2->store($post);
			}
			$modelstat=$this->getModel('positionstatistic');
			if ($post['statschanges_check']==1)
			{
				if ($post['id']==0){$post['id']=$modelstat->getDbo()->insertid();}
				$modelstat->store($post);
			}
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_CTRL_ERROR_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=positions';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=position.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	// remove the positions in cid and remove also the events associated with the deleted positions
	function remove()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		$msg='';
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_CTRL_SELECT_TO_DELETE'));}
		$model=$this->getModel('position');
		if(!$model->delete($cid))
		{
			$msg = $model->getError();
			$this->setRedirect('index.php?option=com_joomleague&view=positions&task=position.display',$msg);
			return;
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_CTRL_DELETED');
		}
		$model2=$this->getModel('position_eventtype');
		$model2->delete($cid);
		if(!$model2->delete($cid))
		{
			$msg = $model2->getError();
			$this->setRedirect('index.php?option=com_joomleague&view=positions&task=position.display',$msg);
			return;
		}
		$this->setRedirect('index.php?option=com_joomleague&view=positions&position.display',$msg);
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('position');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=positions&task=position.display');
	}

	// save the checked rows inside the positions list
	function saveshort()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		$model	= $this->getModel('position');
		if ($model->storeshort($cid,$post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_CTRL_POSITIONS_UPDATED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_CTRL_ERROR_UPDATING_POS').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=positions&task=position.display';
		$this->setRedirect($link,$msg);
	}

	function import()
	{
		JRequest::setVar('view','import');
		JRequest::setVar('table','position');
		parent::display();
	}

	function export()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'));}
		$model = $this->getModel("position");
		$model->export($cid, "position", "Position");
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
	function getModel($name = 'Position', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

}
?>