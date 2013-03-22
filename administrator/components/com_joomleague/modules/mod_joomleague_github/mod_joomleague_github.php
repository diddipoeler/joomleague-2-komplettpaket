<?php
/**
 * GitHub Module for Joomla!
 *
 * @package    GitHubModule
 *
 * @copyright  Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

// Check if cURL is loaded; if not, proceed no further
if (!extension_loaded('curl'))
{
	echo JText::_('MOD_GITHUB_ERROR_NOCURL');
	return;
}

// Include the helper
require_once dirname(__FILE__).'/helper.php';

// Check if caching is enabled
if ($params->get('cache') == 1)
{
	// Set the cache parameters
	$options = array(
		'defaultgroup' => 'mod_joomleague_github');
	$cache		= JCache::getInstance('callback', $options);
	$cacheTime	= round($cacheTime / 60);
	$cache->setLifeTime($cacheTime);
	$cache->setCaching(true);

	// Call the cache; if expired, pull new data
	$github = $cache->call(array('ModGithubHelper', 'compileData'), $params);
}
else
{
	// Pull new data
	$github = modGithubHelper::compileData($params);
}

if ((!$github) || (isset($github->error)))
{
	echo JText::_('MOD_GITHUB_ERROR_UNABLETOLOAD');
	return;
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_joomleague_github', $params->get('templateLayout', 'default'));

if ($params->get('relativeTime', 1) == 1)
{
	$script='<script type="text/javascript">'
			.'window.addEvent("domready", function() {'
			.'var options = {'
			.'now      : "'.JText::_('MOD_GITHUB_CREATE_LESSTHANAMINUTE').'",'
			.'minute   : "'.JText::_('MOD_GITHUB_CREATE_MINUTE').'",'
			.'minutes  : "'.JText::_('MOD_GITHUB_CREATE_MINUTES').'",'
			.'hour     : "'.JText::_('MOD_GITHUB_CREATE_HOUR').'",'
			.'hours    : "'.JText::_('MOD_GITHUB_CREATE_HOURS').'",'
			.'yesterday: "'.JText::_('MOD_GITHUB_CREATE_DAY').'",'
			.'days     : "'.JText::_('MOD_GITHUB_CREATE_DAYS').'",'
			.'weeks    : "'.JText::_('MOD_GITHUB_CREATE_WEEKS').'"'
			.'};'
			.'$$("span.commit-time").prettyDate(options);'
			.'});'
			. '</script>';
	JFactory::getDocument()->addCustomTag($script);
}
