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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueViewPlayground extends JLGView
{
	function display($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$model = $this->getModel();
		$lists=array();
		//get the venue
		$venue =& $this->get('data');
		$isNew=($venue->id < 1);
		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_THEPLAYGROUND'),$venue->name);
			$mainframe->redirect('index.php?option='. $option,$msg);
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout($user->get('id'));
		}
		else
		{
			// initialise new record
			$venue->order=0;
		}
/*
		// build the html select list for ordering
		$query = $model->getOrderingAndPlaygroundQuery();
		$lists['ordering'] 	= JHTML::_('list.specificordering',$venue,$venue->id,$query,1);

		//build the html select list for countries
		$countries[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_COUNTRY'));
		if ($res =& Countries::getCountryOptions()){$countries=array_merge($countries,$res);}
		$lists['countries']=JHTML::_('select.genericlist',$countries,'country','class="inputbox" size="1"','value','text',$venue->country);
    	unset($countries);

		//build the html select list for clubs
		$clubs[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_CLUB'),'id','name');
		if ($res=& $this->get('Clubs')){$clubs=array_merge($clubs,$res);}
		$lists['clubs']=JHTML::_('select.genericlist',$clubs,'club_id','class="inputbox" size="1"','id','name',$venue->club_id);
    	unset($clubs);
*/
		$extended = $this->getExtended($venue->extended, 'playground');
		$this->assignRef( 'extended', $extended );
		//$this->assignRef('lists',$lists);
		$this->assignRef('venue',$venue);
		$this->assignRef('form'      	, $this->get('form'));	
		$this->addToolbar();		
		parent::display($tpl);
	}
	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{		
		// Set toolbar items for the page
		$edit=JRequest::getVar('edit',true);
		$text=!$edit ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_GLOBAL_EDIT');
		JLToolBarHelper::save('playground.save');
		if (!$edit)
		{
			JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_ADD_NEW'),'playground');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('playground.cancel');
		}
		else
		{
			// for existing items the button is renamed `close` and the apply button is showed
			JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_EDIT'),'playground');
			JLToolBarHelper::apply('playground.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('playground.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.joomleague',true);	
	}
}
?>