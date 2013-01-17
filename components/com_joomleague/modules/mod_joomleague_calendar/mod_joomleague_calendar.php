<?php
/**
 * @version    1.2.2.1
 * @package    Blog Calendar
 * @author   Justo Gonzalez de Rivera
 * @license    GNU/GPL
 * modified by johncage for use with joomleague
 * @version    1.5.0.1
 */


// no direct access

// Include the syndicate functions only once

require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');

JHTML::_('behavior.tooltip');
$ajax= JRequest::getVar('ajaxCalMod',0,'default','POST');
$ajaxmod= JRequest::getVar('ajaxmodid',0,'default','POST');
if(!$params->get('cal_start_date')){
	$year = JRequest::getVar('year',date('Y'));    /*if there is no date requested, use the current month*/
	$month  = JRequest::getVar('month',date('m'));
	$day  = JRequest::getVar('day',0);
}
else{
	$startDate= new JDate($params->get('cal_start_date'));
	$year = JRequest::getVar('year', $startDate->toFormat('%Y'));
	$month  = JRequest::getVar('month', $startDate->toFormat('%m'));
	$day  = $ajax? '' : JRequest::getVar('day', $startDate->toFormat('%d'));
}
$helper = new modJLCalendarHelper;
$doc = JFactory::getDocument();
$lightbox    = $params->get('lightbox', 1);

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');
if ($lightbox ==1 && (!isset($_GET['format']) OR ($_GET['format'] != 'pdf'))) {
	$doc->addScriptDeclaration(";
      window.addEvent('domready', function() {
          $$('a.jlcmodal".$module->id."').each(function(el) {
            el.addEvent('click', function(e) {
              new Event(e).stop();
              SqueezeBox.fromElement(el);
            });
          });
      });
      ");
}
$inject_container = ($params->get('inject', 0)==1)?$params->get('inject_container', 'joomleague'):'';
$doc->addScriptDeclaration(';
    jlcinjectcontainer['.$module->id.'] = \''.$inject_container.'\'; 
    jlcmodal['.$module->id.'] = \''.$lightbox.'\';
      ');

if (!defined('JLC_MODULESCRIPTLOADED')) {
	$doc->addScript( JURI::base().'modules/mod_joomleague_calendar/js/mod_joomleague_calendar.js' );
	$doc->addScriptDeclaration(';
    var calendar_baseurl=\''. JURI::base() . '\';
      ');
	$doc->addStyleSheet(JURI::base().'modules/mod_joomleague_calendar/css/mod_joomleague_calender.css');
	define('JLC_MODULESCRIPTLOADED', 1);
}
$calendar = $helper->showCal($params,$year,$month,$ajax,$module->id);

require(JModuleHelper::getLayoutPath('mod_joomleague_calendar'));

?>