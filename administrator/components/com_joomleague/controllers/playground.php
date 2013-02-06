<?php
/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
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
 * Joomleague Component Playground Controller
 *
 * @package	JoomLeague
 * @since	0.1
*/
class JoomleagueControllerPlayground extends JoomleagueController
{

	protected $view_list = 'playgrounds';
	
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

		switch($this->getTask())
		{
			case 'add'     :
				{
					JRequest::setVar('hidemainmenu',0);
					JRequest::setVar('layout','form');
					JRequest::setVar('view','playground');
					JRequest::setVar('edit',false);

					// Checkout the project
					$model=$this->getModel('playground');
					$model->checkout();
				} break;
			case 'edit'    :
				{
					JRequest::setVar('hidemainmenu',0);
					JRequest::setVar('layout','form');
					JRequest::setVar('view','playground');
					JRequest::setVar('edit',true);

					// Checkout the project
					$model=$this->getModel('playground');
					$model->checkout();
				} break;
		}
		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		// decription must be fetched without striping away html code
		$post['notes']=JRequest:: getVar('notes','none','post','STRING',JREQUEST_ALLOWHTML);
		$model=$this->getModel('playground');
		
		if (!empty($post['address']))
		{
			$address_parts[] = $post['address'];
		}
		if (!empty($post['state']))
		{
			$address_parts[] = $post['state'];
		}
		if (!empty($post['location']))
		{
			if (!empty($post['zipcode']))
			{
				$address_parts[] = $post['zipcode']. ' ' .$post['location'];
			}
			else
			{
				$address_parts[] = $post['location'];
			}
		}
		if (!empty($post['country']))
		{
			$address_parts[] = Countries::getShortCountryName($post['country']);
		}
		$address = implode(', ', $address_parts);
		$coords = $model->resolveLocation($address);
		
		//$mainframe->enqueueMessage(JText::_('coords -> '.'<pre>'.print_r($coords,true).'</pre>' ),'');
		
		foreach( $coords as $key => $value )
		{
    $post['extended'][$key] = $value;
    }
		
		$post['latitude'] = $coords['latitude'];
		$post['longitude'] = $coords['longitude'];
		
		if ($model->store($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_VENUE_CTRL_SAVED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_VENUE_CTRL_ERROR_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=playgrounds';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=playground.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){
			JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));
		}
		$model=$this->getModel('playground');
		if(!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			return;
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_VENUE_CTRL_DELETED');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=playgrounds&task=playground.display', $msg);
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('playground');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=playgrounds&task=playground.display');
	}

	function saveorder()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$cid=JRequest::getVar('cid',array(),'post','array');
		$order=JRequest::getVar('order',array(),'post','array');
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		$model=$this->getModel('playground');
		$model->saveorder($cid,$order);
		$msg=JText::_('COM_JOOMLEAGUE_GLOBAL_NEW_ORDERING_SAVED');
		$this->setRedirect('index.php?option=com_joomleague&view=playgrounds&task=playground.display',$msg);
	}

	function import()
	{
		JRequest::setVar('view','import');
		JRequest::setVar('table','playground');
		parent::display();
	}

	function export()
	{
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){
			JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'));
		}
		$model = $this->getModel("playground");
		$model->export($cid, "playground", "Playground");
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
	function getModel($name = 'Playground', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
?>