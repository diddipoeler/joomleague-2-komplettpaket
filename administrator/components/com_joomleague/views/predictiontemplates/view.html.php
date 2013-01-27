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
 * @package	JoomLeague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionTemplates extends JLGView
{
	function display( $tpl = null )
	{
		$mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';

		$prediction_id		= (int) $mainframe->getUserState( $option . 'prediction_id' );
		$lists				= array();
		$db					=& JFactory::getDBO();
		$uri				=& JFactory::getURI();
		$items				=& $this->get( 'Data' );
		$total				=& $this->get( 'Total' );
		$pagination			=& $this->get( 'Pagination' );
		$predictiongame		=& $this->getModel()->getPredictionGame( $prediction_id );
//echo '<pre>' . print_r( $predictiongame, true ) . '</pre>';
		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'tmpl_filter_order',		'filter_order',		'tmpl.title',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		//build the html select list for prediction games
		$predictions[] = JHTML::_( 'select.option', '0', '- ' . JText::_( 'JL_GLOBAL_SELECT_PRED_GAME' ) . ' -', 'value', 'text' );
		if ( $res =& $this->getModel()->getPredictionGames() ) { $predictions = array_merge( $predictions, $res ); }
		$lists['predictions'] = JHTML::_(	'select.genericlist',
											$predictions,
											'prediction_id',
											'class="inputbox" onChange="this.form.submit();" ',
											'value',
											'text',
											$prediction_id
										);
		unset( $res );

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'JL_ADMIN_PTMPLS_TITLE' ), 'generic.png' );
		if ( $prediction_id > 0 )
		{
			JToolBarHelper::editListX('predictiontemplate.edit');
			//JToolBarHelper::save();  // TO BE FIXED: Marked out. Better an import Button should be added here if it is not master-template
			JToolBarHelper::divider();
			if ( ( $prediction_id > 0 ) && ( $predictiongame->master_template ) )
			{
				JToolBarHelper::deleteList();
				//JToolBarHelper::deleteList( JText::_( 'Warning: all prediction-user-data and tipps of selected member will COMPLETELY be deleted!!! This is NOT reversible!!!' ) );
			}
			else
			{
				JToolBarHelper::custom( 'reset', 'restore', 'restore', JText::_( 'JL_GLOBAL_RESET' ), true );
			}
			JToolBarHelper::divider();
		}
		JToolBarHelper::help( 'screen.joomleague', true );

		$this->assignRef( 'user',			JFactory::getUser() );
		$this->assignRef( 'pred_id',		$prediction_id );
		$this->assignRef( 'lists',			$lists );
		$this->assignRef( 'items',			$items );
		$this->assignRef( 'pagination',		$pagination );
		$this->assignRef( 'predictiongame',	$predictiongame );
		
		parent::display( $tpl );
	}

}
?>