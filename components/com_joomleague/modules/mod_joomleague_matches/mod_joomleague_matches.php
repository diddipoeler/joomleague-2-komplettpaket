<?php
/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
require_once(JPATH_ROOT . DS .'components'. DS.'com_joomleague' . DS . 'joomleague.core.php' );
if (!defined('_JLMATCHLISTMODPATH')) { define('_JLMATCHLISTMODPATH', dirname( __FILE__ ));}
if (!defined('_JLMATCHLISTMODURL')) { define('_JLMATCHLISTMODURL', JURI::base().'modules/mod_joomleague_matches/');}
require_once (_JLMATCHLISTMODPATH.DS.'helper.php');
require_once (_JLMATCHLISTMODPATH.DS.'connectors'.DS.'joomleague.php');

$ajax= JRequest::getVar('ajaxMListMod',0,'default','POST');
$match_id = JRequest::getVar('match_id',0,'default','POST');
$nr = JRequest::getVar('nr',-1,'default','POST');
$ajaxmod= JRequest::getVar('ajaxmodid',0,'default','POST');
$template = $params->get('template','default');
JHTML::_('behavior.mootools');
$doc = JFactory::getDocument();
$doc->addScript( _JLMATCHLISTMODURL.'assets/js/mod_joomleague_matches.js' );
$doc->addStyleSheet(_JLMATCHLISTMODURL.'tmpl/'.$template.'/mod_joomleague_matches.css');
$cssimgurl = ($params->get('use_icons') != '-1') ? _JLMATCHLISTMODURL.'assets/images/'.$params->get('use_icons').'/'
: _JLMATCHLISTMODURL.'assets/images/';
$doc->addStyleDeclaration('
div.tool-tip div.tool-title a.sticky_close{
	display:block;
	position:absolute;
	background:url('.$cssimgurl.'cancel.png) !important;
	width:16px;
	height:16px;
}
');
JHTML::_('behavior.tooltip');
$doc->addScriptDeclaration('
  window.addEvent(\'domready\', function() {
    if ($$(\'#modJLML'.$module->id.'holder .jlmlTeamname\')) addJLMLtips(\'#modJLML'.$module->id.'holder .jlmlTeamname\', \'over\');
  }
  );
  ');
$mod = new MatchesJoomleagueConnector($params, $module->id, $match_id);
$lastheading = '';
$oldprojectid = 0;
$oldround_id  = 0;
if($ajax == 0) { echo '<div id="modJLML'.$module->id.'holder" class="modJLMLholder">';}
$matches = $mod->getMatches();

$cnt=($nr >= 0) ? $nr : 0;
if (count($matches) > 0){
	//$user = JFactory::getUser();
	foreach ($matches AS $key => $match) {
		if(!isset($match['project_id'])) continue; 
		$styleclass=($cnt%2 == 1) ? $params->get('sectiontableentry1') : $params->get('sectiontableentry2');
		$show_pheading = false;
		$pheading = '';
		if (isset($match['type'])) {
			$heading = $params->get($match['type'].'_notice');
		}
		else { $heading = ''; }
		if ($match['project_id'] != $oldprojectid OR $match['round_id'] != $oldround_id) {
			if (!empty($match['heading'])) $show_pheading = true;
			$pheading .= $match['heading'];
		}
		include(JModuleHelper::getLayoutPath('mod_joomleague_matches', $template.DS.'match'));
		$lastheading = $heading;
		$oldprojectid = $match['project_id'];
		$oldround_id = $match['round_id'];
		$cnt++;
	}
}
elseif ($params->get('show_no_matches_notice') == 1) {
	echo '<br />'.$params->get('no_matches_notice').'<br />';
}
if($ajax == 0) { echo '</div>';}
?>
