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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100628
 */
class JoomleagueControllerPredictionEntry extends JoomleagueController
{
	
	function __construct()
	{
		//$post	= JRequest::get('post');
		// Register Extra tasks
		//$this->registerTask( 'add',			'display' );
		//$this->registerTask( 'edit',		'display' );
		//$this->registerTask( 'apply',		'save' );
		//$this->registerTask( 'copy',		'copysave' );
		//$this->registerTask( 'apply',		'savepredictiongame' );
		parent::__construct();
	}
	

	function display( )
	{
		// Get the view name from the query string
		//$viewName = JRequest::getVar( 'view', 'editmatch' );
		//$viewName = JRequest::getVar( 'view' );
//echo '<br /><pre>~' . print_r( $viewname, true ) . '~</pre><br />';

		// Get the view
		//$view =& $this->getView( $viewName );

		$this->showprojectheading();
		$this->showbackbutton();
		$this->showfooter();
	}

	function register()
	{
		$option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
    
    $mainframe->enqueueMessage(JText::_('PredictionEntry Task -> '.$this->getTask()),'');
    
    JRequest::checkToken() or jexit(JText::_($optiontext.'JL_PRED_INVALID_TOKEN_REFUSED'));
		
    $msg	= '';
		$link	= '';
		$post	= JRequest::get('post');

		$predictionGameID	= JRequest::getVar('prediction_id',	'',	'post',	'int');
		$joomlaUserID		= JRequest::getVar('user_id',		'',	'post',	'int');
		$approved			= JRequest::getVar('approved',		0,	'',		'int');
		
		
		$model		= $this->getModel('predictionentry');
		$user		=& JFactory::getUser();
		$isMember	= $model->checkPredictionMembership();

		if ( ( $user->id != $joomlaUserID )  )
		{
			$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_1');
			$link = JFactory::getURI()->toString();
		}
		else
		{
			if ($isMember)
			{
				$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_4');
				$link = JFactory::getURI()->toString();
			}
			else
			{
				$post['registerDate'] = JHTML::date(time(),'%Y-%m-%d %H:%M:%S');
				//if (!$model->store($post,'PredictionEntry'))
				if (!$model->store($post))
				{
					$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_5');
					$link = JFactory::getURI()->toString();
				}
				else
				{
					$cids = array();
					$cids[] = $model->getDbo()->insertid();
					JArrayHelper::toInteger($cids);

					$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_MSG_2');
					if ($model->sendMembershipConfirmation($cids))
					{
						$msg .= ' - ';
						$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_MSG_3');
					}
					else
					{
						$msg .= ' - ';
						$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_6');
					}
					$params = array(	'option' => 'com_joomleague',
										'view' => 'predictionentry',
										'prediction_id' => $predictionGameID,
										's' => '1' );

					$query = JoomleagueHelperRoute::buildQuery($params);
					$link = JRoute::_('index.php?' . $query, false);
				}
			}
		}

		echo '<br /><br />';
		echo '#' . $msg . '#<br />'; 
		$this->setRedirect($link,$msg);
	}

	function select()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$pID	= JRequest::getVar('prediction_id',	'',		'post',	'int');
		$uID	= JRequest::getVar('uid',			null,	'post',	'int');
		if (empty($uID)){$uID=null;}
		$link = PredictionHelperRoute::getPredictionTippEntryRoute($pID,$uID);
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

	function selectprojectround()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$post	= JRequest::get('post');
		$pID	= JRequest::getVar('prediction_id',	null,	'post',	'int');
        
        // diddipoeler
		//$pjID	= JRequest::getVar('project_id',	null,	'post',	'int');
        $pjID	= JRequest::getVar('p',	null,	'post',	'int');
        
		$rID	= JRequest::getVar('r',				null,	'post',	'int');
		$uID	= JRequest::getVar('uid',			null,	'post',	'int');
		$link = PredictionHelperRoute::getPredictionTippEntryRoute($pID,$uID,$rID,$pjID);
		$this->setRedirect($link);
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
	function getModel($name = 'predictionentry', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function addtipp()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_ENTRY_INVALID_TOKEN_PREDICTIONS_NOT_SAVED'));
    $option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$msg	= '';
		$link	= '';

		$predictionGameID	= JRequest::getVar('prediction_id',	'','post','int');
		$joomlaUserID		= JRequest::getVar('user_id',		'','post','int');
        $memberID		= JRequest::getVar('memberID',		'','post','int');
		$round_id			= JRequest::getVar('round_id',		'','post','int');
		$pjID				= JRequest::getVar('pjID',			'','post','int');
		$set_r				= JRequest::getVar('set_r',			'','post','int');
		$set_pj				= JRequest::getVar('set_pj',		'','post','int');

		$model		= $this->getModel('predictionentry');
		$user		=& JFactory::getUser();
		$isMember	= $model->checkPredictionMembership();
		$allowedAdmin = $model->getAllowed();

		if ( ( ( $user->id != $joomlaUserID ) ) && ( !$allowedAdmin ) )
		{
			$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_1');
			$link = JFactory::getURI()->toString();
		}
		else
		{
			if ( ( !$isMember ) && ( !$allowedAdmin ) )
			{
				$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_2');
				$link = JFactory::getURI()->toString();
			}
			else
			{
				if ($pjID!=$set_pj)
				{
					$params = array	(	'option' => 'com_joomleague',
										'view' => 'predictionentry',
										'prediction_id' => $predictionGameID,
										'pj' => $set_pj
									);

					$query = JoomleagueHelperRoute::buildQuery($params);
					$link = JRoute::_('index.php?' . $query,false);
					$this->setRedirect($link);
				}

				if ( $round_id != $set_r )
				{
					$params = array	(	'option' => 'com_joomleague',
										'view' => 'predictionentry',
										'prediction_id' => $predictionGameID,
										'r' => $set_r,
										'pj' => $pjID
									);

					$query = JoomleagueHelperRoute::buildQuery($params);
					$link = JRoute::_('index.php?' . $query,false);
					$this->setRedirect($link);
				}

				if ( !$model->savePredictions($allowedAdmin) )
				{
					$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_ERROR_3');
					$link = JFactory::getURI()->toString();
				}
				else
				{
					$msg .= JText::_($optiontext.'JL_PRED_ENTRY_CONTROLLER_MSG_1');
					$link = JFactory::getURI()->toString();
				}
			}
		}
		
    //echo '<br />' . $link . '<br />';
		//echo '<br />' . $msg . '<br />';
		
		$this->setRedirect($link,$msg);
	}

}
?>