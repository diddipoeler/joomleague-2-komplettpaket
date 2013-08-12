<?php
/**
 * @version $Id: mod_jl_pl_birthday.php 4902 2010-01-30 07:40:04Z and_one $
 * @package Joomleague
 * @subpackage player_birthday
 * @copyright Copyright (C) 2009  JoomLeague
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
 */

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');
require_once(dirname(__FILE__).DS.'helper.php');

$document = & JFactory::getDocument();
$config = array();

//add css file
$document->addStyleSheet(JURI::base().'modules/mod_joomleague_top_tipper/css/mod_joomleague_top_tipper.css');

$pg_id = $params->get('pg');

$config['limit'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_LIMIT'); 
$config['show_project_name'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_PROJECT_NAME');
$config['show_project_name_selector'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_PROJECT_NAME_SELECTOR');

$config['show_rankingnav'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_RANKING_NAV');
$config['show_all_user'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_ALL_USER');
$config['show_user_icon'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_USER_ICON');

$config['show_user_link'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_USER_LINK');

$config['show_tip_details'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_TIP_DETAILS');
$config['show_tip_ranking'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_TIP_RANKING');

$config['show_tip_ranking_round'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_TIP_RANKING_ROUNDID');
$config['show_tip_link_ranking_round'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_LINK_RANKING_ROUNDID');

$config['show_average_points'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_AVERAGE_POINTS');
$config['show_count_tips'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_COUNT_TIPS');
$config['show_count_joker'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_COUNT_JOKER');
$config['show_count_topptips'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_COUNT_TOPP_TIPS');
$config['show_count_difftips'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_COUNT_DIFF_TIPS');
$config['show_count_tendtipps'] = $params->get('JL_MOD_TT_XML_PREDICTION_GAME_SHOW_COUNT_TEND_TIPS');

$config['show_debug_modus'] = $params->get('show_debug_modus');

//echo 'prediction game id -> '.$pg_id.'<br>';

/*
// sprachedatei der extension nachladen, damit wir nicht noch mal alles eintragen müssen
$lang =& JFactory::getLanguage();
$extension = 'com_joomleague_predictiongame';
$base_dir = JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'extensions'.DS.'predictiongame';
// $language_tag = 'en-GB';
$language_tag = '';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
// sprachdatei der komponente nachladen
$extension = 'com_joomleague';
$base_dir = JPATH_SITE;
$lang->load($extension, $base_dir, $language_tag, $reload);
*/

JRequest::setVar('prediction_id', $pg_id);

// das model laden
$modelpg = &JLGModel::getInstance('PredictionRanking', 'JoomleagueModel');

// jetzt nach das overall template nachladen
// dadurch erhalten wir die sortierung aus dem backend
$overallConfig	= $modelpg->getPredictionOverallConfig();
$config = array_merge($overallConfig,$config);
$configavatar = $modelpg->getPredictionTemplateConfig('predictionusers');
$predictionGame[] = $modelpg->getPredictionGame();
$predictionMember[] = $modelpg->getPredictionMember($configavatar);
$predictionProjectS[] = $modelpg->getPredictionProjectS();
$actJoomlaUser[] = JFactory::getUser();
$roundID = $modelpg->roundID;

$type_array = array();
$type_array[]=JHTML ::_('select.option','0',JText::_('JL_PRED_RANK_FULL_RANKING'));
$type_array[]=JHTML ::_('select.option','1',JText::_('JL_PRED_RANK_FIRST_HALF'));
$type_array[]=JHTML ::_('select.option','2',JText::_('JL_PRED_RANK_SECOND_HALF'));
$lists['type']=$type_array;
unset($type_array);
			
//echo 'predictionGame -> <pre>'.print_r($predictionGame,true).'</pre><br>';
//echo 'predictionMember -> <pre>'.print_r($predictionMember,true).'</pre><br>';
//echo 'predictionProjectS -> <pre>'.print_r($predictionProjectS,true).'</pre><br>';
//echo 'actJoomlaUser -> <pre>'.print_r($actJoomlaUser,true).'</pre><br>';

require(JModuleHelper::getLayoutPath('mod_joomleague_top_tipper'));
?>