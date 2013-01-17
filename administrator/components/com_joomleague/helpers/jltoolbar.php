<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.toolbar');

class JLToolBarHelper extends JToolBarHelper {

	public static function addNew($task = 'add', $alt = 'JTOOLBAR_NEW', $check = false)
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::addNew($task, $alt, $check);
		}
	}

	public static function addNewX($task = 'add', $alt = 'JTOOLBAR_NEW')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::addNew($task, $alt);
		}
	}

	public static function publish($task = 'publish', $alt = 'JTOOLBAR_PUBLISH', $check = false)
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::publish($task, $alt, $check);
		}
	}

	public static function publishList($task = 'publish', $alt = 'JTOOLBAR_PUBLISH')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::publishList($task, $alt);
		}
	}

	public static function unpublish($task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH', $check = false)
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::unpublish($task, $alt, $check);
		}
	}

	public static function unpublishList($task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::unpublishList($task,$alt);
		}
	}

	public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::custom($task, $icon, $iconOver, $alt, $listSelect);
		}
	}

	public static function customX($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::custom($task, $icon, $iconOver, $alt, $listSelect);
		}
	}

	public static function editList($task = 'edit', $alt = 'JTOOLBAR_EDIT')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::editList($task, $alt);
		}
	}

	public static function editListX($task = 'edit', $alt = 'JTOOLBAR_EDIT')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::editList($task, $alt);
		}
	}

	public static function deleteList($msg = '', $task = 'remove', $alt = 'JTOOLBAR_DELETE')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::deleteList($msg, $task, $alt);
		}
	}

	public static function deleteListX($msg = '', $task = 'remove', $alt = 'JTOOLBAR_DELETE')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::deleteList($msg, $task, $alt);
		}
	}
	
	public static function apply($task = 'apply', $alt = 'JTOOLBAR_APPLY')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::apply($task, $alt);
		}
	}
	
	public static function save($task = 'save', $alt = 'JTOOLBAR_SAVE')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::save($task, $alt);
		}
	}
	
	public static function archiveList($task = 'archive', $alt = 'JTOOLBAR_ARCHIVE')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::archiveList($task, $alt);
		}	
	}
	
	public static function cancel($task = 'cancel', $alt = 'JTOOLBAR_CANCEL')
	{
		$allowed = true;
		if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::cancel($task, $alt);
		}
	}
	
}