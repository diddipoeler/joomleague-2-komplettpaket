<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
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
 * @static
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueViewTeam extends JLGView
{
	function display($tpl = null)
	{
		$mainframe	= JFactory::getApplication();

		if($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}

		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		$mainframe	= JFactory::getApplication();
		$db			= JFactory::getDBO();
		$uri 		= JFactory::getURI();
		$user 		= JFactory::getUser();
		$model		= $this->getModel();

		$lists = array();
		//get the club
		$team	=& $this->get('data');
		$isNew	= ($team->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') ))
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'COM_JOOMLEAGUE_ADMIN_TEAM_THETEAM' ), $team->name );
			$mainframe->redirect( 'index.php?option=com_joomleague', $msg );
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout( $user->get('id') );
		}
		else
		{
			// initialise new record
			//$season->published = 1;
			$club->order 	= 0;
		}
/*
		//build the html select list for admin
		//	$lists['admin'] = JHTML::_('list.users',  'admin', $club->admin);

		// build the html select list for ordering
		$query = $model->getOrderingAndTeamQuery();
		$lists['ordering']	= JHTML::_( 'list.specificordering', $team, $team->id, $query, 1 );

		//build the html select list for clubs
		$clubs[] = JHTML::_( 'select.option', '0', JText::_( 'COM_JOOMLEAGUE_GLOBAL_SELECT_CLUB' ), 'id', 'name' );
		if ( $res = & $this->get('Clubs') )
		{
			$clubs = array_merge( $clubs, $res );
		}

		if($team->club_id){
			$selected_club = $team->club_id;
		} else {
			$selected_club = JRequest::getInt('cid',0);
		}

		$lists['clubs'] = JHTML::_( 'select.genericlist', $clubs, 'club_id', 'class="inputbox" size="1"', 'id', 'name', $selected_club );
		unset($clubs);

	$this->assignRef('lists',	$lists);
*/				
		$this->assignRef('team',	$team);

		$extended = $this->getExtended($team->extended, 'team');
		$this->assignRef( 'extended', $extended );
		$this->assignRef('form'      	, $this->get('form'));			
		$this->addToolbar();		
		parent::display( $tpl );
	}

	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{
		// Set toolbar items for the page
		$edit		= JRequest::getVar('edit',true);
		$text = !$edit ? JText::_( 'COM_JOOMLEAGUE_GLOBAL_NEW' ) : JText::_( 'COM_JOOMLEAGUE_GLOBAL_EDIT' );
		JToolBarHelper::title((   JText::_( 'COM_JOOMLEAGUE_ADMIN_TEAM' ).': <small><small>[ ' . $text.' ]</small></small>' ),'Teams');
		JLToolBarHelper::save('team.save');

		if (!$edit)  {
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('team.cancel');
		} else {
			// for existing items the button is renamed `close` and the apply button is showed
			JLToolBarHelper::apply('team.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel( 'team.cancel', 'COM_JOOMLEAGUE_GLOBAL_CLOSE' );
		}
	
		JToolBarHelper::help( 'screen.joomleague.edit' );
	}	
	
}
?>