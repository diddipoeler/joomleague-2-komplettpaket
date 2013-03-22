<?php
/**
 * GitHub Module for Joomla!
 *
 * @package    GitHubModule
 *
 * @copyright  Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     GitHubModule
 * @since       1.0
 */
class Mod_GitHubInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string  $type    The action being performed
	 * @param   string  $parent  The function calling this method
	 *
	 * @return  mixed  Boolean false on failure, void otherwise
	 *
	 * @since   1.0
	 */
	function preflight($type, $parent)
	{
		// Requires Joomla! 1.7
		$jversion = new JVersion;
		if (version_compare($jversion->getShortVersion(), '1.7', 'lt'))
		{
			JError::raiseNotice(null, JText::_('MOD_GITHUB_ERROR_INSTALL_J17'));
			return false;
		}
	}
}
