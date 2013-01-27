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
//require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'joomleague.php');

/**
 * Joomleague Prediction Member Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueControllerPredictionMember extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add',		'display');
		$this->registerTask('edit',		'display');
		$this->registerTask('apply',	'save');
		$this->registerTask('reminder',	'sendReminder');
	}

	function display()
	{
		//$mainframe		=& JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

	 	$model=$this->getModel('predictionmembers');
		$viewType=$document->getType();
		$view=$this->getView('predictionmembers',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$prediction_id1	= JRequest::getVar('prediction_id','-1','','int');
		$prediction_id2	= (int)$mainframe->getUserState('com_joomleague' . 'prediction_id');

		if ($prediction_id1 > (-1))
		{
			$mainframe->setUserState('com_joomleague' . 'prediction_id',(int)$prediction_id1);
		}
		else
		{
			$mainframe->setUserState('com_joomleague' . 'prediction_id',(int)$prediction_id2);
		}
		$prediction_id	= (int)$mainframe->getUserState('com_joomleague' . 'prediction_id');

		switch($this->getTask())
		{
			case 'add' :
			{
				JRequest::setVar('hidemainmenu',	1);
				JRequest::setVar('layout',			'form');
				JRequest::setVar('view',			'predictionmember');
				JRequest::setVar('edit',			false);

				// Checkout the project
				$model = $this->getModel('predictionmember');
				//$model->checkout();
			} break;
			case 'edit' :
			{
				JRequest::setVar('hidemainmenu',	1);
				JRequest::setVar('layout',			'form');
				JRequest::setVar('view',			'predictionmember');
				JRequest::setVar('edit',			true);

				// Checkout the project
				$model = $this->getModel('predictionmember');
				//$model->checkout();
			} break;

      case 'editlist' :
			{
			  JRequest::setVar('hidemainmenu',	0);
				JRequest::setVar('layout',			'editlist');
				JRequest::setVar('view',			'predictionmembers');
				JRequest::setVar('edit',			false);
			}
			break;
		}

		parent::display();

	}

	// remove the prediction_member(s) in cid and remove also the tipps associated with the deleted prediction_person(s)
	function remove()
	{
		//$post		= JRequest::get( 'post' );
		//echo '<pre>'; print_r($post); echo '</pre>';

		$d		= ' - ';
		$msg	= '';
		$cid	= JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		$prediction_id	= JRequest::getInt('prediction_id',(-1),'post');
		//echo '<pre>'; print_r($cid); echo '</pre>';

		if (count($cid) < 1)
		{
			JError::raiseError(500,JText::_('JL_ADMIN_PMEMBER_CTRL_DEL_ITEM'));
		}

		$model =& $this->getModel('predictionmember');

		if (!$model->deletePredictionResults($cid,$prediction_id))
		{
			$msg .= $d . JText::_('JL_ADMIN_PMEMBER_CTRL_DEL_MSG') . $model->getError();
		}
		$msg .= $d . JText::_('JL_ADMIN_PMEMBER_CTRL_DEL_PRESULTS');

		if (!$model->deletePredictionMembers($cid))
		{
			$msg .= JText::_('JL_ADMIN_PMEMBER_CTRL_DEL_PMEMBERS_MSG') . $model->getError();
		}

		$msg .= $d . JText::_('JL_ADMIN_PMEMBER_CTRL_DEL_PMEMBERS');

		$link = 'index.php?option=com_joomleague&view=predictionmembers';
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	// send a reminder mail to make a tipp on needed prediction games to selected members
	function sendReminder()
	{
		JToolBarHelper::title( JText::_( 'JL_ADMIN_PMEMBER_CTRL_SEND_REMINDER_MAIL' ), 'generic.png' );
		JToolBarHelper::back( 'JL_ADMIN_PMEMBER_CTRL_BACK', 'index.php?option=com_joomleague&view=predictionmembers' );

		echo 'This will send an email to all members of the prediction game with reminder option enabled. Are you sure?';
		$post		= JRequest::get( 'post' );
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$pgmid		= JRequest::getVar( 'prediction_id', array(0), 'post', 'array' );
		$post['id'] = (int) $cid[0];
		$post['predgameid'] = (int) $pgmid[0];
		echo '<pre>'; print_r($post); echo '</pre>';


		if ( $post['predgameid'] == 0 )
		{
			JError::raiseWarning( 500, JText::_( 'JL_ADMIN_PMEMBER_CTRL_SELECT_ERROR' ) );
		}
		$msg		= '';
		$d			= ' - ';

		$model = $this->getModel( 'predictionmember' );
    $model->sendEmailtoMembers($cid,$pgmid);
    
    

		$link = 'index.php?option=com_joomleague&view=predictionmembers';
		echo $msg;
		//$this->setRedirect( $link, $msg );
	}

	function publish()
	{
		$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cids );
		$predictionGameID	= JRequest::getVar( 'prediction_id', '', 'post', 'int' );

		if ( count( $cids ) < 1 )
		{
			JError::raiseError( 500, JText::_( 'JL_ADMIN_PMEMBER_CTRL_SEL_MEMBER_APPR' ) );
		}

		$model = $this->getModel( 'predictionmember' );
		if( !$model->publish( $cids, 1, $predictionGameID ) )
		{
			echo "<script> alert( '" . $model->getError(true) . "' ); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_joomleague&view=predictionmembers' );
	}

	function unpublish()
	{
		$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cids );
		$predictionGameID	= JRequest::getVar( 'prediction_id', '', 'post', 'int' );

		if ( count( $cids ) < 1 )
		{
			JError::raiseError( 500, JText::_( 'JL_ADMIN_PMEMBER_CTRL_SEL_MEMBER_REJECT' ) );
		}

		$model = $this->getModel( 'predictionmember' );
		if ( !$model->publish( $cids, 0, $predictionGameID ) )
		{
			echo "<script> alert( '" . $model->getError(true)  ."' ); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_joomleague&view=predictionmembers' );
	}

  function save_memberlist()
  {
  $model = $this->getModel('predictionmembers');
  $save_memberlist = $model->save_memberlist();
  
  $msg = JText::_('JL_ADMIN_PMEMBER_LIST_SAVED');
  $link = 'index.php?option=com_joomleague&view=predictionmembers';
	$this->setRedirect($link,$msg);
  
  }
  
  
}
?>