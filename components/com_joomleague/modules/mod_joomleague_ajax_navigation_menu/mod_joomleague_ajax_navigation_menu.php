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

JHTML::_('behavior.tooltip');
$ajax= JRequest::getVar('ajaxCalMod',0,'default','POST');
$ajaxmod= JRequest::getVar('ajaxmodid',0,'default','POST');

$document = JFactory::getDocument();
//$document->addScript(JURI::root(true).'/administrator/components/com_joomleague/assets/js/jl2.noconflict.js');

// $jquery_version =  JComponentHelper::getParams('com_joomleague')->get('jqueryversionfrontend',0);
// $jquery_sub_version = JComponentHelper::getParams('com_joomleague')->get('jquerysubversionfrontend',0);
// $jquery_ui_version = JComponentHelper::getParams('com_joomleague')->get('jqueryuiversionfrontend',0);
// $jquery_ui_sub_version = JComponentHelper::getParams('com_joomleague')->get('jqueryuisubversionfrontend',0);

//$document->addScript('https://ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_ui_version.'.'.$jquery_ui_sub_version.'/jquery-ui.min.js');
// $document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/'.$jquery_version.'/jquery.min.js');


$helper = new modJoomleagueAjaxNavigationMenuHelper($params);

$queryvalues = $helper->getQueryValues();

$season_id  = JRequest::getVar('jlamseason',0,'default','POST');

if ( $season_id == 0 )
{

if ( isset($queryvalues['p']) )
{

if ( isset($queryvalues['cid']) )
{
$queryvalues['tid'] = $helper->getTeamId(intval($queryvalues['p']),intval($queryvalues['cid']));
}
$helper->setProject( intval($queryvalues['p']), intval($queryvalues['tid']),intval($queryvalues['division'])  );
// $helper->_project_id = intval($queryvalues['p']);
// $helper->_division_id = intval($queryvalues['division']);
// $helper->_team_id = intval($queryvalues['tid']);
$league_id  = $helper->getLeagueId();
$project_id  = intval($queryvalues['p']);
$division_id  = intval($queryvalues['division']);
$team_id  = intval($queryvalues['tid']);
$helper->_team_id = $team_id;
$season_id  = JRequest::getVar('jlamseason',0,'default','POST');
}

}
else
{
$league_id  = JRequest::getVar('jlamleague',0,'default','POST');
$project_id  = JRequest::getVar('jlamproject',0,'default','POST');
$division_id  = JRequest::getVar('jlamdivisionid',0,'default','POST');
$helper->_division_id = $division_id;
$team_id  = JRequest::getVar('jlamteamid',0,'default','POST');
$helper->_team_id = $team_id;
//$helper->setProject( $project_id, $team_id,$division_id  );
}

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

if ( $params->get('show_favteams_nav_links') )
{
$favteams  = $helper->getFavTeams($project_id);

}



      

      
$seasonselect['seasons']	= $helper->getSeasonSelect();

if ( $season_id )
{
$leagueselect['leagues']	= $helper->getLeagueSelect($season_id);
}

if ( $league_id )
{
$projectselect['projects']	= $helper->getProjectSelect($season_id,$league_id);
}

if ( $project_id )
{
$helper->setProject($project_id,$team_id,$division_id);
$divisionsselect['divisions']	= $helper->getDivisionSelect($project_id);
$projectselect['teams']	= $helper->getTeamSelect($project_id);
}

// $projectselect	= $helper->getProjectSelect();
// $divisionselect = $helper->getDivisionSelect();
// $teamselect		= $helper->getTeamSelect();



//add css file
// $document->addStyleSheet(JURI::base().'modules/mod_joomleague_ajax_navigation_menu/css/mod_joomleague_ajax_navigation_menu.css');
// $document->addScript(JURI::base().'modules/mod_joomleague_ajax_navigation_menu/js/mod_joomleague_ajax_navigation_menu.js');

$inject_container = ($params->get('inject', 0)==1)?$params->get('inject_container', 'joomleague'):'';
$document->addScriptDeclaration(';
    jlcinjectcontainer['.$module->id.'] = \''.$inject_container.'\'; 
    jlcmodal['.$module->id.'] = \''.$lightbox.'\';
      ');
      
if (!defined('JLC_MODULESCRIPTLOADED')) {
	$document->addScript( JURI::base().'modules/mod_joomleague_ajax_navigation_menu/js/mod_joomleague_ajax_navigation_menu.js' );
	$document->addScriptDeclaration(';
    var ajaxmenu_baseurl=\''. JURI::base() . '\';
      ');
	$document->addStyleSheet(JURI::base().'modules/mod_joomleague_ajax_navigation_menu/css/mod_joomleague_ajax_navigation_menu.css');
	define('JLC_MODULESCRIPTLOADED', 1);
}


// $defaultview   = $params->get('project_start');
// $defaultitemid = $params->get('custom_item_id');

require(JModuleHelper::getLayoutPath('mod_joomleague_ajax_navigation_menu'));