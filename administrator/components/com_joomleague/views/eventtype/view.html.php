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
class JoomleagueViewEventtype extends JLGView
{
	function display($tpl=null)
	{
		if($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}

		//get the object
		$event =& $this->get('data');

		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$model = $this->getModel();

		$lists=array();
		//get the project
		$event =& $this->get('data');
		$isNew = ($event->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('COM_JOOMLEAGUE_EVENTTYPE_THE_EVENT'),JText::_($event->name));
			$mainframe->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout($user->get('id'));
		}
/*
		// build the html select list for ordering
		$query=$model->getOrderingAndEventtypeQuery();
		$lists['ordering']=JHTML::_('list.specificordering',$event,$event->id,$query,1);

		// icon
		//if there is no icon selected,use default icon
		$default = JoomleagueHelper::getDefaultPlaceholder("icon");
		if (empty($event->icon)){$event->icon=$default;}

		// image selector
		$imageselect=ImageSelect::getSelector('icon','picture_preview','events',$event->icon,$default,'icon','icon');

		//build the html select list for event
		$myoptions=array();
		$myoptions[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[]=JHTML::_('select.option','1',JText::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['splitt']=JHTML::_('select.radiolist',$myoptions,'splitt','class="inputbox" size="1"','value','text',$event->splitt);
		$lists['double']=JHTML::_('select.radiolist',$myoptions,'double','class="inputbox" size="1"','value','text',$event->double);
		unset($myoptions);

		//build the html select list for direction
		$directions[]=JHTML::_('select.option','DESC',JText::_('COM_JOOMLEAGUE_GLOBAL_DESCENDING'));
		$directions[]=JHTML::_('select.option','ASC',JText::_('COM_JOOMLEAGUE_GLOBAL_ASCENDING'));
   		$lists['directions']= JHTML::_(	'select.genericlist',$directions,'direction','class="inputbox" size="1"','value',
   						'text',$event->direction);
		unset($directions);

		//build the html select list for sports_type
		$sports_type[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SPORTSTYPE'),'id','name');
		if ($res =& JoomleagueHelper::getSportsTypes()){$sports_type=array_merge($sports_type,$res);}
		$lists['sports_type']=JHTML::_(	'select.genericlist',$sports_type,'sports_type_id',
						'class="inputbox validate-select-required" size="1"',
						'id','name',$event->sports_type_id);
		unset($sports_type);

		$this->assignRef('imageselect',$imageselect);
		$this->assignRef('lists',$lists);
*/
		$this->assignRef('event',$event);
    $this->assign('cfg_which_media_tool', JComponentHelper::getParams('com_joomleague')->get('cfg_which_media_tool',0) );
		
    //$extended = $this->getExtended($projectreferee->extended, 'eventtype');
		//$this->assignRef( 'extended', $extended );
		
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
		JToolBarHelper::title((JText::_('COM_JOOMLEAGUE_ADMIN_EVENTTYPE_EVENT').': <small><small>[ '.$text.' ]</small></small>'),'events');
		JLToolBarHelper::save('eventtype.save');
		if (!$edit)
		{
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('eventtype.cancel');
		}
		else
		{		
			// for existing items the button is renamed `close` and the apply button is showed
			JLToolBarHelper::apply('eventtype.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('eventtype.cancel',JText::_('COM_JOOMLEAGUE_GLOBAL_CLOSE'));
		}
		JToolBarHelper::help('screen.joomleague',true);
	}
}
?>
