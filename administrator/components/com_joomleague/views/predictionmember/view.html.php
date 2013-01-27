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

class JoomleagueViewPredictionMember extends JLGView
{
	function display( $tpl = null )
	{
		$mainframe	=& JFactory::getApplication();

		if ( $this->getLayout() == 'form' )
		{
			$this->_displayForm( $tpl );
			return;
		}

		//get the predictionuser
		$predictionuser =& $this->get( 'data' );

		parent::display( $tpl );
	}

	function _displayForm( $tpl )
	{
		$mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';
		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$lists = array();
		//get the member data
		$predictionuser	=& $this->get( 'data' );
		$isNew			= ( $predictionuser->id < 1 );

		// fail if checked out not by 'me'
		/*
		if ( $model->isCheckedOut( $user->get( 'id' ) ) )
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The prediction user' ), $predictionuser->name );
			$mainframe->redirect( 'index.php?option=' . $option, $msg );
		}
		*/

		// Edit or Create?
		if ( !$isNew )
		{
			//$model->checkout( $user->get( 'id' ) );
		}
		else
		{
			// initialise new record
			$predictionuser->order = 0;
		}

		// build the html select list for ordering
		/*
		$query = "	SELECT  ordering AS value,
							name AS text
					FROM #__joomleague_prediction_member
					ORDER BY ordering
					";

		$lists['ordering'] 	= JHTML::_( 'list.specificordering',  $predictionuser, $predictionuser->id, $query, 1 );
*/
		#$myoptions			= array();
		#$myoptions[]		= JHTML::_( 'select.option', '0', JText::_( 'Position for Player' ) );
		#$myoptions[]		= JHTML::_( 'select.option', '1', JText::_( 'Function for Staff' ) );
		#$lists['isStaff']	= JHTML::_(	'select.radiolist', $myoptions, 'isStaff', 'class="inputbox" size="1"', 'value', 'text',
		#								$position->isStaff );
		#unset( $myoptions );

		/*
		// build the html radio for isPlayer, isStaff, isReferee and isClubStaff
		$lists['isPlayer']		= JHTML::_( 'select.booleanlist', 'isPlayer',		'class="inputbox"', $position->isPlayer );
		$lists['isStaff']		= JHTML::_( 'select.booleanlist', 'isStaff',		'class="inputbox"', $position->isStaff );
		$lists['isReferee']		= JHTML::_( 'select.booleanlist', 'isReferee',		'class="inputbox"', $position->isReferee );
		$lists['isClubStaff']	= JHTML::_( 'select.booleanlist', 'isClubStaff',	'class="inputbox"', $position->isClubStaff );

		//build the html select list for events

		$res	= array();
		$res1	= array();
		$notusedevents = array();

		if ( $res =& $model->getEventsPosition() )
		{
			$lists['position_events'] = JHTML::_(	'select.genericlist', $res, 'position_eventslist[]',
													' style="width:150px" class="inputbox" multiple="true" size="' . max(10, count($res) ) . '"',
													'value', 'text' );
		}
		else
		{
			$lists['position_events'] = '<select name="position_eventslist[]" id="position_eventslist" style="width:150px" class="inputbox" multiple="true" size="10"></select>';
		}

 		$res1 =& $model->getEvents();

		if ( $res =& $model->getEventsPosition() )
		{
			foreach ( $res1 as $miores1 )
			{
				$used = 0;
				foreach ( $res as $miores )
				{
					if ( $miores1->text == $miores->text )
					{
						$used = 1;
					}
				}
				if ( $used == 0 )
				{
					$notusedevents[] = $miores1;
				}
			}
		}
		else
		{
			$notusedevents = $res1;
		}

		//build the html select list for events

		if ( ( $notusedevents ) && ( count( $notusedevents ) > 0 ) )
		{
			$lists['events'] = JHTML::_('select.genericlist', $notusedevents, 'eventslist[]',
										' style="width:150px" class="inputbox" multiple="true" size="' . max( 10, count( $notusedevents ) ) . '"',
										'value', 'text' );
		}
		else
		{
			$lists['events'] = '<select name="eventslist[]" id="eventslist" style="width:150px" class="inputbox" multiple="true" size="10"></select>';
		}
		unset( $res );
		unset( $res1 );
		unset( $notusedevents );

		//build the html select list for parent positions
		$parents[] = JHTML::_( 'select.option', '0', '- ' . JText::_( 'Parent Position' ) . ' -' );
		if ( $res =& $model->getParentsPositions() )
		{
			$parents = array_merge( $parents, $res );
		}
		$lists['parents'] = JHTML::_(	'select.genericlist', $parents, 'parent_id', 'class="inputbox" size="1"', 'value', 'text',
										$position->parent_id );
		unset( $parents );
		*/

		$this->assignRef( 'lists',			$lists );
		$this->assignRef( 'predictionuser',	$predictionuser );

		parent::display( $tpl );
	}

}
?>