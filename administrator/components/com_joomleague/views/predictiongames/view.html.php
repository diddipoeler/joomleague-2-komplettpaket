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

class JoomleagueViewPredictionGames extends JLGView
{
	function display( $tpl = null )
	{
// 		$mainframe			=& JFactory::getApplication();
// 		$option				= 'com_joomleague';
//     $option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
    
    // Get a refrence of the page instance in joomla
		$document	=& JFactory::getDocument();
    $option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    $this->assignRef( 'optiontext',			$optiontext );
    
		$prediction_id		= (int) $mainframe->getUserState( $option . 'prediction_id' );
		//echo '#' . $prediction_id . '#<br />';
    $model = $this->getModel();
    
		$lists				= array();
		$db					=& JFactory::getDBO();
		$uri				=& JFactory::getURI();
		$items				=& $this->get( 'Data' );
		$total				=& $this->get( 'Total' );
		$pagination			=& $this->get( 'Pagination' );
		$filter_state		= $mainframe->getUserStateFromRequest( $option . 'pre_filter_state',		'filter_state',		'',				'word' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'pre_filter_order',		'filter_order',		'pre.name',		'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'pre_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option . 'pre_search',				'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// state filter
		$lists['state']		= JHTML::_( 'grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;

		//build the html select list for prediction games
		$predictions[] = JHTML::_( 'select.option', '0', '- ' . JText::_( 'Select Prediction Game' ) . ' -', 'value', 'text' );
		if ( $res =& $this->getModel()->getPredictionGames() ) { $predictions = array_merge( $predictions, $res ); }
		$lists['predictions'] = JHTML::_(	'select.genericlist',
											$predictions,
											'prediction_id',
											//'class="inputbox validate-select-required" ',
											'class="inputbox" onChange="this.form.submit();" ',
											//'class="inputbox" onChange="this.form.submit();" style="width:200px"',
											'value',
											'text',
											$prediction_id
										);
		unset( $res );

		// Set toolbar items for the page
		if ($prediction_id==0)
		{
			JToolBarHelper::title(JText::_('JL_ADMIN_PGAMES_TITLE'),'generic.png');

			JToolBarHelper::publishList('predictiongame.publish');
			JToolBarHelper::unpublishList('predictiongame.unpublish');
			JToolBarHelper::divider();

			JToolBarHelper::addNew('predictiongame.add');
			JToolBarHelper::editList('predictiongame.edit');
			//JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', JText::_( 'Copy'), true );
			JToolBarHelper::divider();
			JToolBarHelper::deleteList( JText::_('JL_ADMIN_PGAMES_DELETE'));
			JToolBarHelper::divider();
			JToolBarHelper::customX('rebuild','restore.png','restore_f2.png',JText::_('JL_ADMIN_PGAMES_REBUILDS'),true);
		}
		else
		{
			JToolBarHelper::title( JText::_( 'JL_ADMIN_PGAMES_PROJLIST_TITLE' ), 'generic.png' );

			//JToolBarHelper::publishList();
			//JToolBarHelper::unpublishList();
			//JToolBarHelper::divider();
			//JToolBarHelper::editListX();
			//JToolBarHelper::deleteList( JText::_( 'Warning: all prediction-game-data and assigned projects, tipps and members of selected prediction game will COMPLETELY be deleted!!! This is NOT reversible!!!' ) );
		}

		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.joomleague', true );

		$this->assignRef( 'user',			JFactory::getUser() );
		$this->assignRef( 'lists',			$lists );
		$this->assignRef( 'items',			$items );
		$this->assignRef( 'dPredictionID',	$prediction_id );
		$this->assignRef( 'pagination',		$pagination );
		
		if ( $prediction_id > 0 )
		{
			$this->assignRef( 'predictionProjects',	$this->getModel()->getChilds( $prediction_id ) );
			//$this->assignRef( 'predictionAdmins',	$this->getModel()->getAdmins( $prediction_id ) );
		}

    $url=$uri->toString();
		$this->assignRef('request_url',$url);
    
		parent::display( $tpl );
	}

}
?>