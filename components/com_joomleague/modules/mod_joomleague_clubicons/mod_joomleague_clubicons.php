<?php
/**
* Module mod_jl_clubicons For Joomla 1.5 and joomleague 1.5b.2
* Version: 1.5b.2
* Created by: johncage
* Created on: 21 June 2011
* 
* URL: www.yourlife.de
* License http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$data = new modJLClubiconsHelper ($params);

$cnt = count($data->teams);
$cnt = ($cnt < $params->get('iconsperrow', 20)) ? $cnt : $params->get('iconsperrow', 20);

JHTML::_('behavior.mootools');
$doc =& JFactory::getDocument();
$doc->addStyleSheet(JURI::base() . 'modules/mod_joomleague_clubicons/css/style.css');
$css = 'img.smstarticon { width:25px;}';
if ($params->get('max_width', 800) > 0 AND $cnt <= 20) $css .= 'table.modjlclubicons { max-width: '.$params->get('max_width', 800).'px;}
div.modjlclubicons { max-width: '.$params->get('max_width', 800).'px;}';
$doc->addStyleDeclaration($css);

$tdw = intval(100/$cnt);
$doc->addStyleDeclaration('td.modjlclubicons { width:'.$tdw.'%;}
span.modjlclubicons { width:'.$tdw.'%;}
span.modjlclubicons > img{ width:70%;}');
$tpl = $params->get( 'template','default' );
$rowsep = 'tr';
$initjs = "
    var jcclubiconsglobalmaxwidth = ".$params->get('jcclubiconsglobalmaxwidth', 0).";
    window.addEvent('domready', function(){
      $('clubicons".$module->id."').setStyle('opacity', 0);
      new Asset.images($('clubicons".$module->id."').getElements('img.smstarticon'), {
      onComplete: function(){
          observeClubIcons(".$module->id.", 'img.smstarticon', ".$cnt.", ".$params->get( 'logo_widthdif',12 ).", '".$rowsep."');
          $('clubicons".$module->id."').fade('in');
      }
    });
});
window.addEvent('resize', function(){
    observeClubIcons(".$module->id.", 'img.smstarticon', ".$cnt.", ".$params->get( 'logo_widthdif',12 ).", '".$rowsep."');

});

";
$mv = JFactory::getApplication()->get('MooToolsVersion');
$script =  'script';
$doc->addScript( JURI::base() . 'modules/mod_joomleague_clubicons/js/'.$script.'.js');
$doc->addScriptDeclaration($initjs);
require(JModuleHelper::getLayoutPath('mod_joomleague_clubicons', $tpl));

/* NOTE: this is not implemented yet:
if ($tpl == 'tableless') {
  ;
  $initjs = "
    window.addEvent('domready', function(){
      var modjlicons".$module->id." = new observeClubIcons($('clubicons".$module->id."'), {'imgclass': 'img.smstarticon', 'itemcnt':".$cnt.", 'wdiff':".$params->get( 'logo_widthdif',12 ).", 'position':'".$params->get( 'iconpos','middle' )."'});
    });
";
  $script .= '.class';
  if ($my->id == 62) $script.='.uncompressed';
}
*/
//echo '<strong>'.$mv.'</strong><br />';
?>