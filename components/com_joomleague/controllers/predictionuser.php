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

jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
//class JoomleagueControllerPredictionUsers extends JLGController
class JoomleagueControllerPredictionUsers extends JControllerForm
{

	function display()
	{
		$this->showprojectheading();
		$this->showbackbutton();
		$this->showfooter();
	}

	function cancel()
	{
		JFactory::getApplication()->redirect(str_ireplace('&layout=edit','',JFactory::getURI()->toString()));
	}

	function select()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$pID	= JRequest::getVar('prediction_id',	'',		'post',	'int');
		$uID	= JRequest::getVar('uid',			null,	'post',	'int');
		if (empty($uID)){$uID=null;}
		$link = PredictionHelperRoute::getPredictionMemberRoute($pID,$uID);
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

	function savememberdata()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_USERS_INVALID_TOKEN_MEMBER_NOT_SAVED'));
        $option = JRequest::getCmd('option');
        $optiontext = strtoupper(JRequest::getCmd('option').'_');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
        
		$msg	= '';
		$link	= '';

		$post	= JRequest::get('post');
		//echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />';
		$predictionGameID	= JRequest::getVar('prediction_id',	'','post','int');
		$joomlaUserID		= JRequest::getVar('user_id',		'','post','int');

		$model			= $this->getModel('predictionusers');
		$user			=& JFactory::getUser();
		$isMember		= $model->checkPredictionMembership();
		$allowedAdmin	= $model->getAllowed();

		if ( ( ( $user->id != $joomlaUserID ) ) && ( !$allowedAdmin ) )
		{
			$msg .= JText::_('COM_JOOMLEAGUE_JL_PRED_USERS_CONTROLLER_ERROR_1');
			$link = JFactory::getURI()->toString();
		}
		else
		{
			if ((!$isMember) && (!$allowedAdmin))
			{
				$msg .= JText::_('COM_JOOMLEAGUE_JL_PRED_USERS_CONTROLLER_ERROR_2');
				$link = JFactory::getURI()->toString();
			}
			else
			{
				if (!$model->savememberdata())
				{
					$msg .= JText::_('COM_JOOMLEAGUE_JL_PRED_USERS_CONTROLLER_ERROR_3');
					$link = JFactory::getURI()->toString();
				}
				else
				{
					$msg .= JText::_('COM_JOOMLEAGUE_JL_PRED_USERS_CONTROLLER_MSG_1');
					$link = JFactory::getURI()->toString();
				}
			}
		}

		//echo '<br />';
		//echo '' . $link . '<br />';
		//echo '' . $msg . '<br />';
		$this->setRedirect($link,$msg);
	}

	function selectprojectround()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$post	= JRequest::get('post');
		echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />';
		$pID	= JRequest::getVar('prediction_id',	'',	'post',	'int');
		$pjID	= JRequest::getVar('project_id',	'',	'post',	'int');
		//$rID	= JRequest::getVar('round_id',		'',	'post',	'int');
		$uID	= JRequest::getVar('uid',			0,	'post',	'int');
		$set_pj	= JRequest::getVar('set_pj',		'',	'post',	'int');
		//$set_r	= JRequest::getVar('set_r',			'',	'post',	'int');
		//if ($set_r!=$rID){$rID=$set_r;}
		if ($set_pj!=$pjID){$pjID=$set_pj;}
		if (empty($pjID)){$pjID=null;}
		if (empty($uID)){$uID=null;}
		//$link = JoomleagueHelperRoute::getPredictionResultsRoute($pID,$rID,$pjID,'#jl_top');
		$link = PredictionHelperRoute::getPredictionMemberRoute($pID,$uID,null,$pjID);
		echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

}
?>