<?php
/**
 * @copyright  Copyright (C) 2008-2013 Julien Vonthron. All rights reserved.
 * @license    GNU/GPL, see LICENSE.php
 * Xxxx is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.plugin.plugin' );

class plgJoomleagueComments extends JPlugin {

	public function plgJoomleagueComments(&$subject, $params)
	{
		parent::__construct($subject, $params);

		// load language file for frontend
		JPlugin::loadLanguage( 'plg_joomleague_comments', JPATH_ADMINISTRATOR );
	}

	/**
	 * adds comments to match reports
	 * @param object match
	 * @param string title
	 * @return boolean true on success
	 */
	public function onMatchReportComments(&$match, $title, &$html)
	{
		$app = JFactory::getApplication();

		// load plugin params info
		$plugin				= & JPluginHelper::getPlugin('joomleague', 'comments');
		$pluginParams		= new JParameter( $plugin->params );
		$separate_comments 	= $pluginParams->get( 'separate_comments', 0 );

		if ( $separate_comments ) {
			$comments = $app->getCfg('absolute_path') . '/components/com_jcomments/jcomments.php';
			if (file_exists($comments))
			{
				require_once($comments);
				$html = '<div class="jlgcomments">'.JComments::showComments($match->id, 'com_joomleague_matchreport', $title).'</div>';
				return true;
			}
			return false;
		}
	}

	/**
	 * adds comments to match preview
	 * @param object match
	 * @param string title
	 * @return boolean true on success
	 */
	public function onNextMatchComments(&$match, $title, &$html)
	{
		$app = JFactory::getApplication();

		// load plugin params info
		$plugin				= & JPluginHelper::getPlugin('joomleague', 'comments');
		$pluginParams		= new JParameter( $plugin->params );
		$separate_comments 	= $pluginParams->get( 'separate_comments', 0 );

		if ( $separate_comments ) {
			$comments = $app->getCfg('absolute_path') . '/components/com_jcomments/jcomments.php';
			if (file_exists($comments))
			{
				require_once($comments);
				$html = '<div class="jlgcomments">'.JComments::showComments($match->id, 'com_joomleague_nextmatch', $title).'</div>';
				return true;
			}
			return false;
		}
	}

	/**
	 * adds comments to a match (independent if they were made before or after the match)
	 * @param object match
	 * @param string title
	 * @return boolean true on success
	 */
	public function onMatchComments(&$match, $title, &$html)
	{
		$app = JFactory::getApplication();

		// load plugin params info
		$plugin				= & JPluginHelper::getPlugin('joomleague', 'comments');
		$pluginParams		= new JParameter( $plugin->params );
		$separate_comments 	= $pluginParams->get( 'separate_comments', 0 );

		if ( $separate_comments == 0 ) {

			$comments = $app->getCfg('absolute_path') . '/components/com_jcomments/jcomments.php';
			if (file_exists($comments))
			{
				require_once($comments);
				$html = '<div class="jlgcomments">'.JComments::showComments($match->id, 'com_joomleague', $title).'</div>';
				return true;
			}
			return false;
		}
	}

}
?>
