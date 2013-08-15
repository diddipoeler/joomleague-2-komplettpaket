<?php
/**
 * @version	 $Id: mod_joomleague_ticker.php 4905 2012-02-02 22:51:33Z and_one $
 * @package	 Joomla
 * @subpackage  Joomleague ticker module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
defined('_JEXEC') or die('Restricted access');

//get helper
require_once(dirname(__FILE__).DS.'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');

$document = JFactory::getDocument();

//add css file
$document->addStyleSheet(JURI::base().'modules/mod_joomleague_ticker/css/mod_joomleague_ticker.css');

$mode = $params->def("mode");
$resultsmatch = $params->get('results');
$round = $params->get('round');
$ordering = $params->get('ordering');
$matchstatus=$params->get('matchstatus');
$selectiondate = modJoomleagueTickerHelper::getSelectionDate($params->get('daysback'), $params->get('timezone', 'Europe/Amsterdam'));
$bUseFav = $params->get('usefavteams');

$matches = modJoomleagueTickerHelper::getMatches($resultsmatch, $params->get('p'), $params->get('teamid'), $selectiondate, $ordering, $round, $matchstatus,$bUseFav);
if(empty($matches) || count($matches) == 0)
{
	echo JText::_("No matches");
	return;
} else {
	$timezone = new DateTimeZone($params->get('timezone', 'Europe/Amsterdam'));
	$utc = new DateTime();
	$offset = $timezone->getOffset($utc);
	$date = modJoomleagueTickerHelper::getCorrectDateFormat($params->get('dateformat'), $matches, $offset);
	if (count($matches)<$resultsmatch)
	{
		$resultsmatch=count($matches);
	}

	$tickerpause = $params->def("tickerpause");
	$scrollspeed = $params->def("scrollspeed");
	$scrollpause = $params->def("scrollpause");

	switch ($mode)
	{
		case 'T':
			include(dirname(__FILE__).DS.'js'.DS.'ticker.js');
			break;
		case 'V':
			include(dirname(__FILE__).DS.'js'.DS.'qscrollerv.js');
			$document->addScript(JURI::base().'modules/mod_joomleague_ticker/js/qscroller.js');
			break;
		case 'H':
			include(dirname(__FILE__).DS.'js'.DS.'qscrollerh.js');
			$document->addScript(JURI::base().'modules/mod_joomleague_ticker/js/qscroller.js');
			break;
	}
}
require(JModuleHelper::getLayoutPath('mod_joomleague_ticker'));
?>