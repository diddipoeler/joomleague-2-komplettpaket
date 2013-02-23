<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.form.form');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package	JoomLeague
 * @since	1.5
 */
class JoomleagueViewSettings extends JLGView
{
	function display($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$params = JComponentHelper::getParams($option);
		$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.xml';
		
		$jRegistry = new JRegistry;
		$jRegistry->loadString($params->toString('ini'), 'ini');
		$form =& JForm::getInstance($option, $xmlfile, array('control'=> 'params'), false, "/config");
		$form->bind($jRegistry);
		$this->assignRef('form', $form);

		$this->addToolbar();		
		parent::display($tpl);
	}

	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{
		//create the toolbar
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_SETTINGS_TITLE'),'config');
		JLToolBarHelper::apply('settings.apply');
		JLToolBarHelper::save('settings.save');
		JLToolBarHelper::cancel('settings.cancel');
		JToolBarHelper::spacer();
		JLToolBarHelper::onlinehelp();		
	}
}
?>