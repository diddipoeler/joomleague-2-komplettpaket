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
		$option = JRequest::getCmd('option');
		$db			= JFactory::getDBO();
		$uri 		= JFactory::getURI();
		$user 		= JFactory::getUser();
		$model		= $this->getModel();

		$lists = array();
		//get the club
		$team	=& $this->get('data');
		$isNew	= ($team->id < 1);
        
        //$mainframe->enqueueMessage(JText::_('view -> '.'<pre>'.print_r(JRequest::getVar('view'),true).'</pre>' ),'');

    $team->merge_clubs = explode(",", $team->merge_clubs);
    
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
        
        $this->assignRef( 'checkextrafields', $model->checkUserExtraFields() );
        if ( $this->checkextrafields )
        {
            $lists['ext_fields'] = $model->getUserExtraFields($team->id);
            //$mainframe->enqueueMessage(JText::_('view -> '.'<pre>'.print_r($lists['ext_fields'],true).'</pre>' ),'');
        }

		$this->assignRef('team',	$team);
        $this->assignRef('lists',	$lists);

		$extended = $this->getExtended($team->extended, 'team');
		$this->assignRef( 'extended', $extended );
		$this->assignRef('form'      	, $this->get('form'));
        $this->assign('cfg_which_media_tool', JComponentHelper::getParams($option)->get('cfg_which_media_tool',0) );			
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
	
		//JToolBarHelper::help( 'screen.joomleague.edit' );
		JLToolBarHelper::onlinehelp();
	}	
	
}
?>