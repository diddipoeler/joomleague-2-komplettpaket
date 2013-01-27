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
//require_once( JPATH_COMPONENT . DS . 'controllers' . DS . 'joomleague.php' );

/**
 * Joomleague Prediction Template Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueControllerPredictionTemplate extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add',		'display' );
		$this->registerTask( 'edit',	'display' );
		$this->registerTask( 'apply',	'save' );
		$this->registerTask( 'reset',	'remove' );
	}

	function display()
	{
		//$mainframe		=& JFactory::getApplication();
		//$option			= 'com_joomleague';

    $option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

	 	$model=$this->getModel('predictiontemplates');
		$viewType=$document->getType();
		$view=$this->getView('predictiontemplates',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$prediction_id1	= JRequest::getVar( 'prediction_id', '-1', '', 'int' ); //echo 'CPT-A' . $prediction_id1 . 'CPT-A<br />';
		$prediction_id2	= (int) $mainframe->getUserState( 'com_joomleague' . 'prediction_id' ); //echo 'CPT-B' . $prediction_id2 . 'CPT-B<br />';

		if ( $prediction_id1 > (-1) )
		{
			$mainframe->setUserState( 'com_joomleague' . 'prediction_id', (int) $prediction_id1 );
		}
		else
		{
			$mainframe->setUserState( 'com_joomleague' . 'prediction_id', (int) $prediction_id2 );
		}
		$prediction_id	= (int) $mainframe->getUserState( 'com_joomleague' . 'prediction_id' ); //echo 'CPT-C' . $prediction_id . 'CPT-C<br />';

		switch( $this->getTask() )
		{
			case 'add'	 :
			{
				JRequest::setVar( 'hidemainmenu',	0 );
				JRequest::setVar( 'layout',			'form' );
				JRequest::setVar( 'view',			'predictiontemplate' );
				JRequest::setVar( 'edit',			false );

				// Checkout the project
				$model = $this->getModel( 'predictiontemplate' );
				//$model->checkout();
			} break;
			case 'edit'	:
			{
				JRequest::setVar( 'hidemainmenu',	0 );
				JRequest::setVar( 'layout',			'form' );
				JRequest::setVar( 'view',			'predictiontemplate');
				JRequest::setVar( 'edit',			true );

				// Checkout the project
				$model = $this->getModel( 'predictiontemplate' );
				//$model->checkout();
			} break;

		}

		parent::display();

	}

	function save()
	{
		JRequest::checkToken() or die( '' );

		$msg	= '';
		$post	= JRequest::get( 'post' );
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
//echo 'CPT-C~' . count( $cid ) . '~CPT-C<br />';
//echo 'CPT-A<pre>' . print_r( $post, true ) . '</pre>CPT-A<br />';

		if ( count( $cid ) == 1 ) // We were in the edit mode of only one template
		{
			$post['id'] = (int) $cid[0];
			$model = $this->getModel( 'predictiontemplate' );
			if ( $model->store( $post ) )
			{
				$msg .= JText::_( 'JL_ADMIN_PTMPL_CTRL_SAVED' );
			}
			else
			{
				$msg .= JText::_( 'JL_ADMIN_PTMPL_CTRL_SAVED_ERROR' ) . $index . ": " . $model->getError();
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();
		}
/*
		else
		{
			for ($index = 0; $index < count($cid); $index++)
			{
				$model			= $this->getModel( 'predictiontemplate' );
				$post['id']		= (int) $cid[$index];
				$model->setId($post['id']);
				$template 		=& $model->getData();
				$templatepath	= JPATH_COMPONENT_SITE . DS . 'settings';
				$xmlfile 		= $templatepath . DS . 'default' . DS . $template->template;
				$jlParams 		= new JLParameter( $template->params, $xmlfile );
				$results		= array();
				$params 		= null;
				$name			= "params";
				foreach ($jlParams->getGroups() as $group => $groups)
				{
					foreach ($jlParams->_xml[$group]->children() as $param)
					{
						if(!in_array($param->attributes('name'), $template->params))
						{
							$post['params'][$param->attributes('name')] = $param->attributes('default');
						}
					}
				}
				if ( $model->store( $post ) )
				{
					$msg = JText::_( 'templates rebuild' );
				}
				else
				{
					$msg = JText::_( 'Error rebuild template ' ) . $index . ": " . $model->getError();
					break;
				}

				// Check the table in so it can be edited.... we are done with it anyway
				$model->checkin();
			}
		}
*/

		if ( $this->getTask() == 'save' )
		{
			$link = 'index.php?option=com_joomleague&view=predictiontemplates';
			$link = 'index.php?option=com_joomleague&view=predictiontemplates';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&controller=predictiontemplate&task=edit&cid[]=' . $post['id'];
		}

		//echo $link . '<br />';
		//echo $msg . '<br />';
		$this->setRedirect( $link, $msg );
	}

	function remove()
	{
		JRequest::checkToken() or die( 'JL_GLOBAL_INVALID_TOKEN' );

		$msg = '';
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cid );

		if ( count( $cid ) < 1 )
		{
			JError::raiseError(500, JText::_( 'JL_ADMIN_PTMPL_CTRL_DEL_ITEM' ) );
		}

		$model = $this->getModel( 'predictiontemplate' );

		if ( $model->delete( $cid ) )
		{
			$msg .= JText::_( 'JL_ADMIN_PTMPL_CTRL_DEL_ITEM_MSG' );
		}
		else
		{
			$msg .= JText::_( 'JL_ADMIN_PTMPL_CTRL_DEL_ITEM_ERROR' ) . $model->getError();
		}

		$link = 'index.php?option=com_joomleague&view=predictiontemplates';
		//echo $msg;
		$this->setRedirect( $link, $msg );
	}

	function cancel()
	{
		JRequest::checkToken() or die( 'JL_GLOBAL_INVALID_TOKEN' );

		$msg = '';
		// Checkin the template
		//$model = $this->getModel( 'predcitiontemplates' );
		$model = $this->getModel( 'predictiontemplate' );
		$model->checkin();

		$link = 'index.php?option=com_joomleague&view=predictiontemplates';
		$this->setRedirect( $link, $msg );
	}

	function masterimport()
	{
		JRequest::checkToken() or die( 'JL_GLOBAL_INVALID_TOKEN' );

		$msg			= '';
		$templateid		= JRequest::getVar( 'templateid', 0, 'post', 'int' );
		//$projectid	= JRequest::getVar( 'project_id', 0, 'post', 'int' );
		$prediction_id	= JRequest::getVar( 'prediction_id', 0, 'post', 'int' );

		$model = $this->getModel( 'predictiontemplate' );

		if ( $model->import( $templateid, $prediction_id ) )
		{
			$msg = JText::_( 'JL_ADMIN_PTMPL_CTRL_TMPL_IMPORTED' );
		}
		else
		{
			$msg = JText::_( 'Error importing prediction template' ) . $model->getError();
		}
		//$this->setRedirect( 'index.php?option=com_joomleague&view=predictiontemplates', $msg );

		$link = 'index.php?option=com_joomleague&view=predictiontemplates';
		//echo $link . '<br />';
		//echo $msg . '<br />';
		$this->setRedirect( $link, $msg );
	}

}
?>