<?php
/**
 * @copyright	Copyright (C) 2006-2011 JoomLeague.net. All rights reserved.
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
class JoomleagueViewrosterposition extends JLGView
{
	function display($tpl=null)
	{
		if ($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}

		//get the object
		$object =& $this->get('data');

		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$user =& JFactory::getUser();
		$model =& $this->getModel();
		$lists=array();
		//get the project
		$object =& $this->get('data');
		$isNew=($object->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('COM_JOOMLEAGUE_ADMIN_ROSTERPOSITION'),$object->name);
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
			$object->order=0;
		}

		//build the html select list for countries
		$countries[]=JHTML::_('select.option','',JText::_('COM_JOOMLEAGUE_ADMIN_ROSTERPOSITIONS_SELECT_COUNTRY'));
		if ($res =& Countries::getCountryOptions()){$countries=array_merge($countries,$res);}
		$lists['countries']=JHTML::_('select.genericlist',$countries,'country','class="inputbox" size="1"','value','text',$object->country);
		unset($countries);

		// build the html select list for ordering
		$query='SELECT ordering AS value,name AS text FROM	#__joomleague_rosterposition ORDER BY ordering ';
		$lists['ordering']=JHTML::_('list.specificordering',$object,$object->id,$query,1);

    $project_type=array (	JHTMLSelect::option('HOME_POS',JText::_('HOME_POS'),'id','name'),
								JHTMLSelect::option('AWAY_POS',JText::_('AWAY_POS'),'id','name')
							);
		$lists['project_type']=JHTMLSelect::genericlist($project_type,'short_name','class="inputbox" size="1"','id','name',$object->short_name);
		unset($project_type);
		
    /*
    * extended data
    */
//     echo JPATH_COMPONENT.'<br>';
//     echo JPATH_COMPONENT_SITE.'<br>';
    //$paramsdata=$object->extended;
    
//    $paramsdefs=JPATH_COMPONENT.DS.'assets'.DS.'extended'.DS.'rosterposition.xml';
//     echo $paramsdefs.'<br>';
//    $extended=new JLGExtraParams($paramsdata,$paramsdefs);
    $this->assignRef('form'      	, $this->get('form'));
    $extended = $this->getExtended($object->extended, 'rosterposition');
    $this->assignRef('extended',$extended);

    $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
		$this->assignRef('lists',$lists);
		$this->assignRef('object',$object);

		parent::display($tpl);
	}

}
?>