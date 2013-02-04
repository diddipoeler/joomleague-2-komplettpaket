<?php
/**
* @copyright	Copyright (C) 2007 JoomLeague.net. All rights reserved.
* @license		GNU/GPL,see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License,and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
* short_name 15	middle_name 25	alias 75 utf8_general_ci
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
//require_once (JPATH_COMPONENT.DS.'controllers'.DS.'joomleague.php');

/**
 * Joomleague Component League Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerJlextassociations extends JoomleagueController
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
		switch ($this->getTask())
		{
			case 'add'	 :
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','jlextassociation');
				JRequest::setVar('edit',false);
				// Checkout the project
				$model=$this->getModel('jlextassociation');
				$model->checkout();
			} break;
			case 'edit'	:
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','jlextassociation');
				JRequest::setVar('edit',true);
				// Checkout the project
				$model=$this->getModel('jlextassociation');
				$model->checkout();
			} break;
		}
		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		$model=$this->getModel('jlextassociation');
		if ($model->store($post))
		{
			$msg=JText::_('JL_ADMIN_LEAGUE_CTRL_SAVED');
		}
		else
		{
			$msg=JText::_('JL_ADMIN_LEAGUE_CTRL_ERROR_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=jlextassociations';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=jlextassociation.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_GLOBAL_SELECT_TO_DELETE'));}
		$model=$this->getModel('jlextassociation');
		if (!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			return;
		}
		else
		{
			$msg=JText::_('JL_ADMIN_LEAGUE_CTRL_DELETED');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=jlextassociations',$msg);
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('jlextassociation');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=jlextassociations');
	}

	function orderup()
	{
		$model=$this->getModel('jlextassociation');
		$model->move(-1);
		$this->setRedirect('index.php?option=com_joomleague&view=jlextassociations');
	}

	function orderdown()
	{
		$model=$this->getModel('jlextassociation');
		$model->move(1);
		$this->setRedirect('index.php?option=com_joomleague&view=jlextassociations');
	}

	function saveorder()
	{
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		$order=JRequest::getVar('order',array(),'post','array');
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		$model=$this->getModel('jlextassociation');
		$model->saveorder($cid,$order);
		$msg=JText::_('JL_GLOBAL_NEW_ORDERING_SAVED');
		$this->setRedirect('index.php?option=com_joomleague&view=jlextassociations',$msg);
	}

	function import()
	{
		JRequest::setVar('view','import');
		JRequest::setVar('table','associations');
		parent::display();
	}
	
	function export()
	{
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('JL_GLOBAL_SELECT_TO_EXPORT'));}
		$model = $this->getModel("jlextassociation");
		$model->export($cid, "associations", "Associations");
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
	function getModel($name = 'jlextassociation', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
}
?>