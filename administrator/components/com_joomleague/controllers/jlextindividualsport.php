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
class JoomleagueControllerjlextindividualsport extends JoomleagueController
{
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
// 		$this->registerTask('add','display');
// 		$this->registerTask('edit','display');
// 		$this->registerTask('apply','save');
	}

  /*
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
  */

	function jlextaddsinglematch()
	{
	  $option = JRequest::getCmd('option');
		$document = JFactory::getDocument();
		$mainframe = JFactory::getApplication();
    $cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		
		
		$post=JRequest::get('post');
		$post['project_id']=$mainframe->getUserState($option.'project',0);
		$post['round_id']=$mainframe->getUserState($option.'round_id',0);
		$post['match_id']=$cid;
		$model=$this->getModel('jlextindividualsport');
		if ($model->store($post))
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ADD_MATCH');
		}
		else
		{
			$msg=JText::_('JL_ADMIN_MATCH_CTRL_ERROR_ADD_MATCH').$model->getError();
		}
		$link='index.php?option=com_joomleague&task=jlextindividualsport.jlexteditsinglematches';
		$this->setRedirect($link,$msg);
		
		//JRequest::setVar('view','jlextindividualsport');
		//parent::display();
	}
	
	
  function jlexteditsinglematches()
	{
  $option = JRequest::getCmd('option');
		$document = JFactory::getDocument();
		$mainframe = JFactory::getApplication();
    $model=$this->getModel('jlextindividualsport');
		$viewType=$document->getType();
		$view=$this->getView('jlextindividualsport',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		JRequest::setVar('view','jlextindividualsport');
    JRequest::setVar('hidemainmenu',1);
		parent::display();
	}

	



	
	
	
}
?>