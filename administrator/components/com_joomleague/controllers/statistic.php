<?php
/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

/**
 * Joomleague Component Event Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerStatistic extends JoomleagueController
{

	protected $view_list = 'statistics';
	
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
		$this->registerTask('fulldelete','remove');
	}

	function display()
	{
		$option = JRequest::getCmd('option');
		$mainframe 		= JFactory::getApplication();
		$sports_type	= JRequest::getInt('filter_sports_type',0);
		$mainframe->setUserState($option.'.statistics.filter_sports_type',$sports_type);
		switch($this->getTask())
		{
			case 'add'	 :
			{
				JRequest::setVar('hidemainmenu', 0);
				JRequest::setVar('layout', 'form' );
				JRequest::setVar('view'  , 'statistic');
				JRequest::setVar('edit', false);

				// Checkout the object
				$model = $this->getModel('statistic');
				$model->checkout();
			} break;

			case 'edit'	:
			{
				JRequest::setVar('hidemainmenu', 0);
				JRequest::setVar('layout', 'form' );
				JRequest::setVar('view'  , 'statistic');
				JRequest::setVar('edit', true);

				// Checkout the club
				$model = $this->getModel('statistic');
				$model->checkout();
			} break;

		}

		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		
		$model = $this->getModel('statistic');

		if ($return_id = $model->store($post))
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_STAT_CTRL_STAT_SAVED');
		}
		else
		{
			$msg = JText::_('COM_JOOMLEAGUE_ADMIN_STAT_CTRL_ERROR_SAVE') . $model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		if ($this->getTask() == 'save')
		{
			$link = 'index.php?option=com_joomleague&view=statistics&task=statistic.display';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=statistic.edit&id=' . $return_id;
		}

		$this->setRedirect($link, $msg);
	}

	function remove()
	{

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));
		}

		$model = $this->getModel('statistic');

		if ($this->getTask() == 'fulldelete')
		{
			if(!$model->fulldelete($cid)) {
				$this->setRedirect('index.php?option=com_joomleague&view=statistics&task=statistic.display', $model->getError(), 'error');
				return;
			}
		}
		else if(!$model->delete($cid)) {
			$this->setRedirect('index.php?option=com_joomleague&view=statistics&task=statistic.display', $model->getError(), 'error');
			return;
		}
		$msg = JText::_('COM_JOOMLEAGUE_ADMIN_STAT_CTRL_DELETED');
		$this->setRedirect('index.php?option=com_joomleague&view=statistics&task=statistic.display', $msg);
	}

	function cancel()
	{
		// Checkin the event
		$model = $this->getModel('statistic');
		$model->checkin();

		$this->setRedirect('index.php?option=com_joomleague&view=statistics&task=statistic.display');
	}

	function import()
	{
		JRequest::setVar('view','import');
		JRequest::setVar('table','statistic');
		parent::display();
	}
	
	function export()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'));}
		$model = $this->getModel("statistic");
		$model->export($cid, "statistic", "Statistic");
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
	function getModel($name = 'Statistic', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
?>