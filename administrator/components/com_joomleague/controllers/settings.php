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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Joomleague Component settings Controller
 *
 * @package		Joomleague
 * @since 1.5
 */
class JoomleagueControllerSettings extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	}

	function edit()
	{
		JRequest::setVar( 'hidemainmenu', 0 );
		JRequest::setVar( 'view'  , 'settings');
		parent::display();
	}

	function save()
	{
		$mainframe = JFactory::getApplication();
        // Check for request forgeries
		JRequest::checkToken() or die( 'COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN' );

		// Sanitize
		$task	= JRequest::getVar('task');
        //$mainframe->enqueueMessage(JText::_('settings task -> '.'<pre>'.print_r($task,true).'</pre>' ),'');
        
		$data 	= JRequest::get( 'post' );
		$data['option'] = 'com_joomleague';
		$params = JRequest::getVar('params', array(), 'post', 'array');
		$model=$this->getModel('settings');
		
		$defPh = JoomleagueHelper::getDefaultPlaceholder('player');
		$newPh = $params['ph_player'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_person',
											'picture', 
											$defPh , 
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_team_player',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_team_staff',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_project_referee',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		} 
		$defPh = JoomleagueHelper::getDefaultPlaceholder('clublogobig');
		$newPh = $params['ph_logo_big'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_club',
											'logo_big', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_playground',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		$defPh = JoomleagueHelper::getDefaultPlaceholder('clublogomedium');
		$newPh = $params['ph_logo_medium'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_club',
											'logo_middle', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		$defPh = JoomleagueHelper::getDefaultPlaceholder('clublogosmall');
		$newPh = $params['ph_logo_small'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_club',
											'logo_small', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		$defPh = JoomleagueHelper::getDefaultPlaceholder('icon');
		$newPh = $params['ph_icon'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_statistic',
											'icon', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_sports_type',
											'icon', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_eventtype',
											'icon', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		$defPh = JoomleagueHelper::getDefaultPlaceholder('team');
		$newPh = $params['ph_team'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_team',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}			
			if(!$model->updatePlaceholder(	'#__joomleague_project_team',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		
		$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.$data['option'].DS.'config.xml';
		$form =& JForm::getInstance($data['option'], $xmlfile, array('control'=> 'params'), false, "/config");
		$data['params'] = $model->validate($form, $params);
		// Save the rules.
		if (isset($data['params']['rules'])) {
			$rules	= new JAccessRules($data['params']['rules']);
			$asset	= JTable::getInstance('asset');
		
			if (!$asset->loadByName($data['option'])) {
				$root	= JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = $data['option'];
				$asset->title = $data['option'];
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string) $rules;
			if (!$asset->check() || !$asset->store()) {
				$this->setError($asset->getError());
				return false;
			}
		}
		
		unset($data['params']['rules']);
		
		$table = & JTable::getInstance('extension');
		if (!$table->load(array("element" => "com_joomleague", "type" => "component")))
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		$table->bind($data);
		
		// pre-save checks
		if (!$table->check())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}
		
		// save the changes
		if ($table->store()) {
			$msg	= JText::_( 'COM_JOOMLEAGUE_ADMIN_SETTINGS_CTRL_STAT_SAVED');
		} else {
			$msg	= JText::_( 'COM_JOOMLEAGUE_ADMIN_SETTINGS_CTRL_ERROR_SAVE');
		}

		switch ($task)
		{
			case 'apply':
            	$link = 'index.php?option=com_joomleague&task=settings.edit';
				break;
			case 'save':
				$link='index.php?option=com_joomleague&view=projects&task=project.display';
                //$link = 'index.php?option=com_joomleague&task=settings.edit';
				break;
			default:
				$link = 'index.php?option=com_joomleague&view=settings&task=settings.display';
				break;
		}

		$this->setRedirect( $link, $msg );
	}

	function cancel()
	{
		//$this->setRedirect( 'index.php?option=com_joomleague&view=settings&task=settings.display' );
        $this->setRedirect( 'index.php?option=com_joomleague&view=projects&task=project.display' );
	}
}

?>
