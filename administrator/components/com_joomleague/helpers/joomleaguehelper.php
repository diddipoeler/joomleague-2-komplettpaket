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
		JSubMenuHelper::addEntry(JText::_('COM_SPORTSMANAGEMENT_MENU'), 'index.php?option=com_joomleague', $submenu == 'menu');
		JSubMenuHelper::addEntry(JText::_('COM_SPORTSMANAGEMENT_SUBMENU_EXTENSIONS'), 'index.php?option=com_joomleague&view=extensions', $submenu == 'extensions');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_joomleague/images/tux-48x48.png);}');
		if ($submenu == 'extensions') 
		{
			$document->setTitle(JText::_('COM_SPORTSMANAGEMENT_ADMINISTRATION_EXTENSIONS'));
		}
	}
    
}

?>    