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

jimport('joomla.application.component.view');

/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100628
 */
class JoomleagueViewPredictionRules extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document	=& JFactory::getDocument();
		$model		=& $this->getModel();

		$this->assignRef('predictionGame',$model->getPredictionGame());

		if (isset($this->predictionGame))
		{
			$config			= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig	= $model->getPredictionOverallConfig();

			$this->assignRef('model',				$model);
			$this->assignRef('config',				array_merge($overallConfig,$config));

			$this->assignRef('predictionMember',	$model->getPredictionMember());
			$this->assignRef('predictionProjectS',	$model->getPredictionProjectS());
			$this->assignRef('actJoomlaUser',		JFactory::getUser());
			//echo '<br /><pre>~'.print_r($this,true).'~</pre><br />';
      $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
			// Set page title
			$pageTitle = JText::_('JL_PRED_USERS_TITLE'); // 'Tippspiel Regeln'

			$document->setTitle($pageTitle);

			parent::display($tpl);
		}
		else
		{
			JError::raiseNotice(500,JText::_('JL_PRED_PREDICTION_NOT_EXISTING'));
		}
	}

}
?>