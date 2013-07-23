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
 * Joomleague Component Template Controller
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerTemplate extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('save','save');
		$this->registerTask('apply','apply');
		$this->registerTask('reset','remove');
	}

	function display()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$model=$this->getModel('templates');
		
		$viewType=$document->getType();
		$view=$this->getView('templates',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$projectws=$this->getModel('project');
		$projectws->set('name','projectws');
		$projectws->setId($mainframe->getUserState($option.'project',0));
		$view->setModel($projectws, false);

		switch ($this->getTask())
		{
			case 'add'	 :
				{
				} break;

			case 'edit'	:
				{
					$model=$this->getModel('template');
					$viewType=$document->getType();
					$view=$this->getView('template',$viewType);
					$view->setModel($model,true);	// true is for the default model;

					$projectws=$this->getModel('project');
					$projectws->set('name', 'projectws');
					$projectws->setId($mainframe->getUserState($option.'project',0));
					$view->setModel($projectws, false);
					$view->setLayout('form');
					
					JRequest::setVar('layout', 'form');
					JRequest::setVar('view','template');
					JRequest::setVar('edit',true);
					
					// Checkout the project
					$model->checkout();
				} break;

		}
		parent::display();
	}

	function apply()
	{
	   $mainframe =& JFactory::getApplication();
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];

        if (isset($post['params']['person_events']))
		{
			if (count($post['params']['person_events']) > 0)
			{
				$temp=implode(",",$post['params']['person_events']);
			}
			else
			{
				$temp='';
			}
			$post['params']['person_events']=$temp;
		}
        
        //$mainframe->enqueueMessage(JText::_('template<br><pre>'.print_r($post,true).'</pre>'   ),'');
        
		$model=$this->getModel('template');
		if ($model->store($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_SAVED_TEMPLATE');
		}
		else
		{
			$msg=JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_SAVE_TEMPLATE',$index).' '.$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask() == 'save')
		{
			$link='index.php?option=com_joomleague&view=templates&task=template.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=template.edit&cid[]='.$post['select_id'];
		}
		$this->setRedirect($link,$msg);
	}

	function save()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		//$master_id=JRequest::getVar('master_id',0,'post','int');
		if(count($cid) == 1)
		{
			$post['id']=(int) $cid[0];
			$model=$this->getModel('template');
			if ($model->store($post))
			{
				$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_SAVED_TEMPLATE');
			}
			else
			{
				$msg=JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_SAVE_TEMPLATE',$index).' '.$model->getError();
				break;
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();
		}
		else
		{
			for ($index=0; $index < count($cid); $index++)
			{
				$post['id']=(int) $cid[$index];
				$model=$this->getModel('template');
				$model->setId($post['id']);
				$template 		=& $model->getData();
				$templatepath	= JPATH_COMPONENT_SITE.DS.'settings';
				$xmlfile 		= $templatepath.DS.'default'.DS.$template->template;
				$jlParams 		= new JLParameter($template->params,$xmlfile);
				$results		= array();
				$params 		= null;
				$name			= "params";
				foreach ($jlParams->getGroups() as $group => $groups)
				{
					foreach ($jlParams->_xml[$group]->children() as $param)
					{
						if(!in_array($param->attributes('name'),$template->params))
						{
							$post['params'][$param->attributes('name')]=$param->attributes('default');
						}
					}
				}
				if ($model->store($post))
				{
					$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_REBUILD_TEMPLATES');
				}
				else
				{
					$msg=JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_REBUILD_TEMPLATE',$index).' '.$model->getError();
					break;
				}
				// Check the table in so it can be edited.... we are done with it anyway
				$model->checkin();
			}
		}
		if ($this->getTask() == 'save')
		{
			$link='index.php?option=com_joomleague&view=templates&task=template.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=template.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		$cid=JRequest::getVar('cid',array(0),'post','array');
		JArrayHelper::toInteger($cid);
		$isMaster=JRequest::getVar('isMaster',array(),'post','array');
		JArrayHelper::toInteger($isMaster);
		if (count($cid) < 1){
			JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));
		}
		foreach ($cid AS $id)
		{
			if ($isMaster[$id])
			{
				echo "<script> alert('" . JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_DELETE_WARNING') . "'); window.history.go(-1); </script>\n";
				return;
			}
		}
		$model=$this->getModel('template');
		if (!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$msg = JText::_("COM_JOOMLEAGUE_ADMIN_TEMPLATES_RESET_SUCCESS");
		$this->setRedirect('index.php?option=com_joomleague&view=templates&task=template.display', $msg);
	}

	function cancel()
	{
		// Checkin the template
		$model=$this->getModel('template');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=templates&task=template.display');
	}

	function masterimport()
	{
		$templateid=JRequest::getVar('templateid',0,'post','int');
		$projectid=JRequest::getVar('project_id',0,'post','int');
		$model=$this->getModel('template');
		if ($model->import($templateid,$projectid))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_IMPORTED_TEMPLATE');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_IMPORT_TEMPLATE').$model->getError();
		}
		$this->setRedirect('index.php?option=com_joomleague&view=templates&task=template.display',$msg);
	}

}
?>