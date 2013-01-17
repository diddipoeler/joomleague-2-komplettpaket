<?php

/**
 * @author And_One <andone@mfga.at>
 * @version	 $Id$
 * @package	 Joomla
 * @subpackage  Joomleague sports type statistics module
 * @copyright   Copyright (C) 2012 JoomLeague.net. All rights reserved.
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
$sportstypes = $params->get('sportstypes');
$data = modJLGSportsHelper::getData($params);

$document = JFactory::getDocument();
//add css file
$document->addStyleSheet(JURI::base().'modules/mod_joomleague_sports_type_statistics/css/mod_joomleague_sports_type_statistics.css');

// language file
//$lang = &JFactory::getLanguage();
//$lang->load('com_joomleague');

require(JModuleHelper::getLayoutPath('mod_joomleague_sports_type_statistics'));