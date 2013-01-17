<?php

/**
 * @version	 $Id: mod_joomleague_results.php 4905 2010-01-30 08:51:33Z and_one $
 * @package	 Joomla
 * @subpackage  Joomleague results module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

// get helper
require_once (dirname(__FILE__).DS.'helper.php');

require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');

JHTML::_('behavior.mootools');
$document = JFactory::getDocument();
//add css file
$document->addStyleSheet(JURI::base().'modules/mod_joomleague_navigation_menu/css/mod_joomleague_navigation_menu.css');
$document->addScript(JURI::base().'modules/mod_joomleague_navigation_menu/js/mod_joomleague_navigation_menu.js');

$helper = new modJoomleagueNavigationMenuHelper($params);

$seasonselect	= $helper->getSeasonSelect();
$leagueselect	= $helper->getLeagueSelect();
$projectselect	= $helper->getProjectSelect();
$divisionselect = $helper->getDivisionSelect();
$teamselect		= $helper->getTeamSelect();

$defaultview   = $params->get('project_start');
$defaultitemid = $params->get('custom_item_id');

require(JModuleHelper::getLayoutPath('mod_joomleague_navigation_menu'));