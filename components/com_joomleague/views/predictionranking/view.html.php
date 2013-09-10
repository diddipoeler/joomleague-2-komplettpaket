<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// pagination
require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

jimport('joomla.application.component.view');

/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueViewPredictionRanking extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
    $mainframe = JFactory::getApplication();
		$document	=& JFactory::getDocument();
		$uri = JFactory :: getURI();
//		$js ="registerhome('".JURI::base()."','Prediction Game Extension','".$mainframe->getCfg('sitename')."','0');". "\n";
//    $document->addScriptDeclaration( $js );	
		$model		=& $this->getModel();
    $option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    $this->assignRef( 'optiontext',			$optiontext );

		$this->assignRef('predictionGame',$model->getPredictionGame());

    // Get data from the model
 	$items =& $this->get('Data');	
 	$pagination =& $this->get('Pagination');
 
	// push data into the template
	$this->assignRef('items', $items);	
	$this->assignRef('pagination', $pagination);

/*    
    // limit, limitstart und limitende
    $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
    $limitend = $limitstart + $limit;
    $this->assignRef('limit',$limit);
    $this->assignRef('limitstart',$limitstart);
    $this->assignRef('limitend',$limitend);
*/
    
//     $mdlProject = JModel::getInstance("Project", "JoomleagueModel");
//     $mdlProject->setProjectId($project->id);
//     $map_config		= $mdlProject->getMapConfig();
// 		$this->assignRef( 'mapconfig',		$map_config ); // Loads the project-template -settings for the GoogleMap
		
		if (isset($this->predictionGame))
		{
			$config			= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig	= $model->getPredictionOverallConfig();
      $configavatar			= $model->getPredictionTemplateConfig('predictionusers');
      
      $this->assignRef('debuginfo',	$model->getDebugInfo());
      
			$this->assignRef('model',				$model);
			$this->assignRef('roundID',				$this->model->roundID);
			//$this->assignRef('config',				array_merge($overallConfig,$config));
      $this->assignRef('configavatar',				$configavatar );
      $this->assignRef('config',				array_merge($overallConfig,$config));
      
			$this->assignRef('predictionMember',	$model->getPredictionMember($configavatar));
			$this->assignRef('predictionProjectS',	$model->getPredictionProjectS());
			$this->assignRef('actJoomlaUser',		JFactory::getUser());
			
			//echo '<br /><pre>~' . print_r( $this->config, true ) . '~</pre><br />';

			$type_array = array();
			$type_array[]=JHTML ::_('select.option','0',JText::_('COM_JOOMLEAGUE_PRED_RANK_FULL_RANKING'));
			$type_array[]=JHTML ::_('select.option','1',JText::_('COM_JOOMLEAGUE_PRED_RANK_FIRST_HALF'));
			$type_array[]=JHTML ::_('select.option','2',JText::_('COM_JOOMLEAGUE_PRED_RANK_SECOND_HALF'));
			$lists['type']=$type_array;
			unset($type_array);

			$this->assignRef('lists',$lists);
      $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
			// Set page title
			$pageTitle = JText::_('COM_JOOMLEAGUE_PRED_RANK_TITLE');
			
			$mdlProject = JModel::getInstance("Project", "JoomleagueModel");
			foreach ( $this->predictionProjectS as $project )
			{
      $mdlProject->setProjectId($project->project_id);
      }
      
      $map_config		= $mdlProject->getMapConfig();
		  $this->assignRef( 'mapconfig',		$map_config ); // Loads the project-template -settings for the GoogleMap
			
      $this->assignRef('PredictionMembersList',	$model->getPredictionMembersList($this->config,$this->configavatar) );
      $this->geo = new simpleGMapGeocoder();
	    $this->geo->genkml3prediction($this->predictionGame->id,$this->PredictionMembersList);
	  
			$document->setTitle($pageTitle);

			parent::display($tpl);
		}
		else
		{
			JError::raiseNotice(500,JText::_('COM_JOOMLEAGUE_PRED_PREDICTION_NOT_EXISTING'));
		}
	}

}
?>