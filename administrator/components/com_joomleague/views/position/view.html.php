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
class JoomleagueViewPosition extends JLGView
{
	function display($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$model = $this->getModel();
		$lists=array();
		//get the position
		$position =& $this->get('data');
		$isNew= ($position->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_THEPOSITION'),JText::_($position->name));
			$mainframe->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout($user->get('id'));
		}
		else
		{
			// initialise new record
			$position->order=0;
		}

		// build the html select list for ordering
		$query=$model->getOrderingAndPositionsQuery();
		$lists['ordering']=JHTML::_('list.specificordering',$position,$position->id,$query,1);

		//build the html select list for events
		$res=array();
		$res1=array();
		$notusedevents=array();
		if ($res =& $model->getEventsPosition())
		{
			$lists['position_events']=JHTML::_(	'select.genericlist',$res,'position_eventslist[]',
								' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.max(10,count($res)).'"',
								'value','text');
		}
		else
		{
			$lists['position_events']='<select name="position_eventslist[]" id="position_eventslist" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}
		$res1 =& $model->getEvents();
		if ($res =& $model->getEventsPosition())
		{
			if($res1!="")
			foreach ($res1 as $miores1)
			{
				$used=0;
				foreach ($res as $miores)
				{
					if ($miores1->text == $miores->text){$used=1;}
				}
				if ($used == 0){$notusedevents[]=$miores1;}
			}
		}
		else
		{
			$notusedevents=$res1;
		}

		//build the html select list for events
		if (($notusedevents) && (count($notusedevents) > 0))
		{
			$lists['events']=JHTML::_(	'select.genericlist',$notusedevents,'eventslist[]',
							' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.max(10,count($notusedevents)).'"',
							'value','text');
		}
		else
		{
			$lists['events']='<select name="eventslist[]" id="eventslist" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}
		unset($res);
		unset($res1);
		unset($notusedevents);

		// position statistics
		$position_stats=$model->getPositionStatsOptions();
		$lists['position_statistic']=JHTML::_(	'select.genericlist',$position_stats,'position_statistic[]',
							' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.max(10,count($position_stats)).'"',
							'value','text');
		$available_stats=$model->getAvailablePositionStatsOptions();
		$lists['statistic']=JHTML::_(	'select.genericlist',$available_stats,'statistic[]',
						' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.max(10,count($available_stats)).'"',
						'value','text');

		//build the html select list for parent positions
		$parents[]=JHTML::_('select.option','',JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_IS_P_POSITION'));
		if ($res =& $model->getParentsPositions())
		{
			$parents=array_merge($parents,$res);
		}
		$lists['parents']=JHTML::_('select.genericlist',$parents,'parent_id','class="inputbox" size="1"','value','text',$position->parent_id);
		unset($parents);
		/*
		//build the html select list for sports_type
		$sports_type[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_SELECT_SPORTSTYPE'),'id','name');
		if ($res =& JoomleagueHelper::getSportsTypes()){$sports_type=array_merge($sports_type,$res);}
		$lists['sports_type']=JHTML::_(	'select.genericlist',$sports_type,'sports_type_id',
						'class="inputbox validate-select-required" size="1"',
						'id','name',$position->sports_type_id);
		unset($sports_type);

		$person_type=array (	JHTML::_('select.option','1',JText::_('COM_JOOMLEAGUE_F_PLAYERS'),'id','name'),
					JHTML::_('select.option','2',JText::_('COM_JOOMLEAGUE_F_TEAM_STAFF'),'id','name'),
					JHTML::_('select.option','3',JText::_('COM_JOOMLEAGUE_F_REFEREES'),'id','name'),
					JHTML::_('select.option','4',JText::_('COM_JOOMLEAGUE_F_CLUB_STAFF'),'id','name')
							);
		$lists['person_type']=JHTML::_('select.genericlist',$person_type,'persontype','class="inputbox" size="1"','id','name',$position->persontype);
		unset($project_type);
		*/
		$this->assignRef('lists',$lists);
		$this->assignRef('position',$position);
		$this->assignRef('form'      	, $this->get('form'));	
		//$extended = $this->getExtended($position->extended, 'position');
		//$this->assignRef( 'extended', $extended );
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

		JLToolBarHelper::save('position.save');

		if (!$edit)
		{
			JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_ADD_NEW'));
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('position.cancel');
		}
		else
		{
			// for existing items the button is renamed `close` and the apply button is showed
			JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_EDIT'),'Positions');
			JLToolBarHelper::apply('position.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('position.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.joomleague',true);	
	}
}
?>
