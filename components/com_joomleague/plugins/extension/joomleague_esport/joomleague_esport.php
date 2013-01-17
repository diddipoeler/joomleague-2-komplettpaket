<?php
/**
 * @author		And_One <andone@mfga.at>
 * @package		JoomLeague
 * @subpackage	plg_extension_joomleague_esport
 * @copyright	Copyright (c)2010-2013 JoomLeague Developers
 * @license		GNU General Public License version 2, or later
 * @since		2.5
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * JoomLeague esport extension plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	Extension.JoomLeague_Clan
 * @since		2.5
 */
class plgExtensionJoomLeague_Clan extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	

}

class xx extends JLGView {
	function display($tpl=null)
	{
		echo "plugin loaded"; die();
	}
}
