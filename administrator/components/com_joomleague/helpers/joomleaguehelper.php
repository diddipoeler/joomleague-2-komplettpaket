<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class joomleaguemenueHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_JOOMLEAGUE_MENU'), 'index.php?option=com_joomleague', $submenu == 'menu');
        JSubMenuHelper::addEntry(JText::_('COM_JOOMLEAGUE_SUBMENU_PROJECTS'), 'index.php?option=com_joomleague&view=projects', $submenu == 'projects');
		JSubMenuHelper::addEntry(JText::_('COM_JOOMLEAGUE_SUBMENU_EXTENSIONS'), 'index.php?option=com_joomleague&view=cpanelextensions', $submenu == 'cpanelextensions');
        JSubMenuHelper::addEntry(JText::_('COM_JOOMLEAGUE_SUBMENU_PREDICTIONS'), 'index.php?option=com_joomleague&view=cpanelpredictions', $submenu == 'cpanelpredictions');
        JSubMenuHelper::addEntry(JText::_('COM_JOOMLEAGUE_SUBMENU_CURRENT_SEASONS'), 'index.php?option=com_joomleague&view=currentseasons', $submenu == 'currentseasons');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_joomleague/images/tux-48x48.png);}');
		if ($submenu == 'extensions') 
		{
			$document->setTitle(JText::_('COM_JOOMLEAGUE_ADMINISTRATION_EXTENSIONS'));
		}
	}
    
}

?>    