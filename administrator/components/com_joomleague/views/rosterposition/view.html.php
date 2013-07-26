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
    $document = JFactory::getDocument();
		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$user =& JFactory::getUser();
		$model =& $this->getModel();
    $edit	= JRequest::getVar('edit',true);
    $addposition	= JRequest::getVar('addposition');
    $this->assignRef('edit',$edit);
		$lists=array();
		//get the project
		$object =& $this->get('data');
		$isNew=($object->id < 1);

$bildpositionenhome = array();
$bildpositionenhome[HOME_POS][0][heim][oben] = 5;
$bildpositionenhome[HOME_POS][0][heim][links] = 233;
$bildpositionenhome[HOME_POS][1][heim][oben] = 113;
$bildpositionenhome[HOME_POS][1][heim][links] = 69;
$bildpositionenhome[HOME_POS][2][heim][oben] = 113;
$bildpositionenhome[HOME_POS][2][heim][links] = 179;
$bildpositionenhome[HOME_POS][3][heim][oben] = 113;
$bildpositionenhome[HOME_POS][3][heim][links] = 288;
$bildpositionenhome[HOME_POS][4][heim][oben] = 113;
$bildpositionenhome[HOME_POS][4][heim][links] = 397;
$bildpositionenhome[HOME_POS][5][heim][oben] = 236;
$bildpositionenhome[HOME_POS][5][heim][links] = 179;
$bildpositionenhome[HOME_POS][6][heim][oben] = 236;
$bildpositionenhome[HOME_POS][6][heim][links] = 288;
$bildpositionenhome[HOME_POS][7][heim][oben] = 318;
$bildpositionenhome[HOME_POS][7][heim][links] = 69;
$bildpositionenhome[HOME_POS][8][heim][oben] = 318;
$bildpositionenhome[HOME_POS][8][heim][links] = 233;
$bildpositionenhome[HOME_POS][9][heim][oben] = 318;
$bildpositionenhome[HOME_POS][9][heim][links] = 397;
$bildpositionenhome[HOME_POS][10][heim][oben] = 400;
$bildpositionenhome[HOME_POS][10][heim][links] = 233;
$bildpositionenaway = array();
$bildpositionenaway[AWAY_POS][0][heim][oben] = 970;
$bildpositionenaway[AWAY_POS][0][heim][links] = 233;
$bildpositionenaway[AWAY_POS][1][heim][oben] = 828;
$bildpositionenaway[AWAY_POS][1][heim][links] = 69;
$bildpositionenaway[AWAY_POS][2][heim][oben] = 828;
$bildpositionenaway[AWAY_POS][2][heim][links] = 179;
$bildpositionenaway[AWAY_POS][3][heim][oben] = 828;
$bildpositionenaway[AWAY_POS][3][heim][links] = 288;
$bildpositionenaway[AWAY_POS][4][heim][oben] = 828;
$bildpositionenaway[AWAY_POS][4][heim][links] = 397;
$bildpositionenaway[AWAY_POS][5][heim][oben] = 746;
$bildpositionenaway[AWAY_POS][5][heim][links] = 179;
$bildpositionenaway[AWAY_POS][6][heim][oben] = 746;
$bildpositionenaway[AWAY_POS][6][heim][links] = 288;
$bildpositionenaway[AWAY_POS][7][heim][oben] = 664;
$bildpositionenaway[AWAY_POS][7][heim][links] = 69;
$bildpositionenaway[AWAY_POS][8][heim][oben] = 664;
$bildpositionenaway[AWAY_POS][8][heim][links] = 397;
$bildpositionenaway[AWAY_POS][9][heim][oben] = 587;
$bildpositionenaway[AWAY_POS][9][heim][links] = 179;
$bildpositionenaway[AWAY_POS][10][heim][oben] = 587;
$bildpositionenaway[AWAY_POS][10][heim][links] = 288;

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

    
		
//     $document->addScript( JURI::base(true).'/components/com_joomleague/assets/js/dragpull.js');
    
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
    $this->assign('jquery_version', JComponentHelper::getParams('com_joomleague')->get('jqueryversionfrontend',0) );
    $this->assign('jquery_sub_version', JComponentHelper::getParams('com_joomleague')->get('jquerysubversionfrontend',0) );
    $this->assign('jquery_ui_version', JComponentHelper::getParams('com_joomleague')->get('jqueryuiversionfrontend',0) );
    $this->assign('jquery_ui_sub_version', JComponentHelper::getParams('com_joomleague')->get('jqueryuisubversionfrontend',0) );
		
    if (!$this->edit)
		{
    // neu
    $position = 1;
    $object->name = $addposition;
    $object->short_name = $addposition;
    $xmlfile=JPATH_COMPONENT_ADMINISTRATOR.DS.'assets'.DS.'extended'.DS.'rosterposition.xml';
    $extended = JForm::getInstance('extended', $xmlfile,array('control'=> 'extended'),
				false, '/config');
    $jRegistry = new JRegistry;
$jRegistry->loadString('' , 'ini');
$extended->bind($jRegistry);


    
    
    
    switch ($addposition)
    {
    case 'HOME_POS':
    for($a=0; $a < 11; $a++)
    {
    $extended->setValue('COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$position.'_TOP', null,$bildpositionenhome[$object->name][$a]['heim']['oben']);
    $extended->setValue('COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$position.'_LEFT', null,$bildpositionenhome[$object->name][$a]['heim']['links']);
    $position++;
    }
    $this->assignRef('bildpositionen',$bildpositionenhome);
    break;
    case 'AWAY_POS':
    for($a=0; $a < 11; $a++)
    {
    $extended->setValue('COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$position.'_TOP', null,$bildpositionenaway[$object->name][$a]['heim']['oben']);
    $extended->setValue('COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$position.'_LEFT', null,$bildpositionenaway[$object->name][$a]['heim']['links']);
    $position++;
    }
    $this->assignRef('bildpositionen',$bildpositionenaway);
    break;
    }
    $object->extended = $extended;
    }
    else
    {
    // bearbeiten positionen übergeben
    $position = 1;
    //$xmlfile=JPATH_COMPONENT_ADMINISTRATOR.DS.'assets'.DS.'extended'.DS.'rosterposition.xml';
		$jRegistry = new JRegistry;
		$jRegistry->loadString($object->extended, 'ini');
    
    for($a=0; $a < 11; $a++)
    {
    $bildpositionen[$object->name][$a]['heim']['oben'] = $jRegistry->get('COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$position.'_TOP');
    $bildpositionen[$object->name][$a]['heim']['links'] = $jRegistry->get('COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$position.'_LEFT');
    $position++;
    }
    $this->assignRef('bildpositionen',$bildpositionen);
    }
    
    $project_type=array (	JHTMLSelect::option('HOME_POS',JText::_('HOME_POS'),'id','name'),
								JHTMLSelect::option('AWAY_POS',JText::_('AWAY_POS'),'id','name')
							);
		$lists['project_type']=JHTMLSelect::genericlist($project_type,'short_name','class="inputbox" size="1"','id','name',$object->short_name);
		unset($project_type);
    
    // Add Script
//$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/'.$this->jquery_version.'/jquery.min.js');
//$document->addScript('https://ajax.googleapis.com/ajax/libs/jqueryui/'.$this->jquery_ui_version.'.'.$this->jquery_ui_sub_version.'/jquery-ui.min.js');



//$javascript .= "\n".'var $JoLe2 = jQuery.noConflict();' . "\n";
$javascript .= "\n";
$javascript .= 'jQuery(document).ready(function() {' . "\n";

$start = 1;
$ende = 11;
for ($a = $start; $a <= $ende; $a++ )
{
$javascript .= '    jQuery("#draggable_'.$a.'").draggable({stop: function(event, ui) {
    	// Show dropped position.
    	var Stoppos = jQuery(this).position();
    	jQuery("div#stop").text("STOP: \nLeft: "+ Stoppos.left + "\nTop: " + Stoppos.top);
    	jQuery("#extended_COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$a.'_TOP").val(Stoppos.top);
      jQuery("#extended_COM_JOOMLEAGUE_EXT_ROSTERPOSITIONS_'.$a.'_LEFT").val(Stoppos.left);
    }});' . "\n";    
}


    
$javascript .= '  });' . "\n";

$javascript .= "\n";


    
    $document->addScriptDeclaration( $javascript );
    $this->assignRef('form'      	, $this->get('form'));
    $this->assignRef('lists',$lists);
		$this->assignRef('object',$object);

		parent::display($tpl);
	}

}
?>