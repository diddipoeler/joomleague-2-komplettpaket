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

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	Joomleague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionTemplate extends JLGView
{
	function display( $tpl = null )
	{
		$mainframe	=& JFactory::getApplication();

		if ( $this->getLayout() == 'form' )
		{
			$this->_displayForm( $tpl );
			return;
		}

		//get the prediction template
		//$predictionTemplate =& $this->get( 'data' );

		parent::display( $tpl );
	}

	function _displayForm( $tpl )
	{
		//$mainframe			=& JFactory::getApplication();
		//$option				= 'com_joomleague';
        $option = JRequest::getCmd('option');
		$app = JFactory::getApplication();

		$prediction_id		= (int) $mainframe->getUserState( $option . 'prediction_id' );
		$lists				= array();
		$db					=& JFactory::getDBO();
		$uri				=& JFactory::getURI();
		$user 				=& JFactory::getUser();
        $model = $this->getModel();
        
		$predictionTemplate	=& $this->get( 'Data' );
		$predictionGame		=& $this->getModel()->getPredictionGame( $prediction_id );
		//$defaultpath		= JPATH_COMPONENT_SITE . DS . 'extensions'.DS.'predictiongame'.DS.'settings';
		$defaultpath		= JPATH_COMPONENT_SITE . DS . 'settings';
		$extensiontpath		= JPATH_COMPONENT_SITE . DS . 'extensions' . DS;
		$isNew				= ( $predictionTemplate->id < 1 );

		// fail if checked out not by 'me'
		if ( $this->getModel()->isCheckedOut( $user->get( 'id' ) ) )
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'JL_ADMIN_PTMPL_THE_PTMPL' ), $predictionTemplate->name );
			$app->redirect( 'index.php?option=' . $option, $msg );
		}

		// Edit or Create?
		if ( !$isNew ) { $this->getModel()->checkout( $user->get( 'id' ) ); }

		// first check custom template folder if template is not default
		if ( $predictionGame->extension != '' )
		{
			if ( is_dir( $extensiontpath . $predictionGame->extension . DS . 'settings' ) )
			{
				$xmlfile = $extensiontpath . $predictionGame->extension . DS . 'settings' . DS . $predictionTemplate->template . ".xml";
			}

			if ( !isset( $xmlfile ) || !file_exists( $xmlfile ) )
			{
				$xmlfile = $defaultpath . DS . 'default' . DS . $predictionTemplate->template . ".xml";
			}
		}
		else
		{
		 // xml file for module
			$xmlfile = $defaultpath . DS . 'default' . DS . $predictionTemplate->template . ".xml";
		}

		$params = new JLParameter( $predictionTemplate->params, $xmlfile );
    $this->assignRef('form'      	, $this->get('form'));
		$this->assignRef( 'predictionTemplate',	$predictionTemplate );
		$this->assignRef( 'predictionGame',		$predictionGame );
		$this->assignRef( 'pred_id',			$prediction_id );
		$this->assignRef( 'params',				$params );
		$this->assignRef( 'lists',				$lists );
		$this->assignRef( 'user',				$user );

		parent::display( $tpl );
	}

}
?>