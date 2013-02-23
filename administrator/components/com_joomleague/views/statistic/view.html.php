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
jimport( 'joomla.filesystem.file' );

require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'statistics' . DS . 'base.php' );

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package		Joomleague
 * @since 1.5
 */
class JoomleagueViewStatistic extends JLGView
{
	function display($tpl = null)
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();

		$document	= JFactory::getDocument();
		$uri 	= JFactory::getURI();
		$user 	= JFactory::getUser();
		$model	= $this->getModel();

		$item 	=& $this->get('item');
		$form 	=& $this->get('form');
		$isNew	= ( $item->id < 1 );

		$edit = JRequest::getVar('edit',true);

		// fail if checked out not by 'me'
		if ( $model->isCheckedOut( $user->get('id') ) )
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The item' ), $item->name );
			$mainframe->redirect( 'index.php?option=' . $option .'&view=statistics', $msg, 'error' );
		}

		// Edit or Create?
		if ( !$isNew )
		{
			$model->checkout( $user->get('id') );
		}

		// icon
		//if there is no icon selected, use default icon
		$default = JoomleagueHelper::getDefaultPlaceholder("icon");		
		if (empty($item->icon)){$item->icon=$default;}
		
		if (!empty($item->class))
		{
			/*
			 * statistic class parameters
			 */
			$class = &JLGStatistic::getInstance($item->class);
			$this->assign( 'calculated',   $class->getCalculated());
		}

		$this->assignRef( 'item', $item );
		$this->assignRef( 'edit', $edit );
		$this->assignRef('form',  $form);
		$this->assignRef('fieldsets', $form->getFieldsets());
        $this->assign('cfg_which_media_tool', JComponentHelper::getParams('com_joomleague')->get('cfg_which_media_tool',0) );	

		$this->addToolbar();
		
		JHTML::_('behavior.tooltip');
		
		parent::display($tpl);
	}
	
	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{		
		// Set toolbar items for the page
		$text = !$this->edit ? JText::_( 'COM_JOOMLEAGUE_GLOBAL_NEW' ) : JText::_( 'COM_JOOMLEAGUE_GLOBAL_EDIT' );
		JToolBarHelper::title(   JText::_( 'COM_JOOMLEAGUE_ADMIN_STAT_TITLE' ).': <small><small>[ ' . $text.' ]</small></small>', 'statistics.png' );
		if ( !$this->edit )
		{
			JLToolBarHelper::apply('statistic.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('statistic.cancel');
		}
		else
		{
			// for existing items the button is renamed `close` and the apply button is showed
			JLToolBarHelper::save('statistic.save');
			JLToolBarHelper::apply('statistic.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('statistic.cancel', 'COM_JOOMLEAGUE_GLOBAL_CLOSE' );
		}
		JLToolBarHelper::onlinehelp();	
	}
}