<?php
/**
 * @version $Id: 
 * @package Joomleague
 * @subpackage club_birthday
 * @copyright Copyright (C) 2013 fussballineuropa
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
 */

defined('_JEXEC') or die('Restricted access');
require_once(dirname(__FILE__).DS.'helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');
$document = JFactory::getDocument();
$show_debug_info = JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) ;

//add css file
$document->addStyleSheet(JURI::base().'modules/mod_joomleague_club_birthday/css/mod_joomleague_club_birthday.css');

$mode = $params->def("mode");
$results = $params->get('limit');
$limit = $params->get('limit');
$refresh = $params->def("refresh");
$minute = $params->def("minute");
$height = $params->def("height");
$width  = $params->def("width");
// Prevent that result is null when either $players or $crew is null by casting each to an array.
//$persons = array_merge((array)$players, (array)$crew);
$clubs = modJoomleagueClubBirthdayHelper::getClubs($limit);
if(count($clubs)>1)   $clubs = modJoomleagueClubBirthdayHelper::jl_birthday_sort($clubs,$params->def("sort_order"));

if ( $show_debug_info )
{
echo 'this->mod_joomleague_club_birthday clubs<br /><pre>~' . print_r($clubs,true) . '~</pre><br />';
echo 'this->mod_joomleague_club_birthday params<br /><pre>~' . print_r($params,true) . '~</pre><br />';
}

$k=0;
$counter=0;

//echo 'mode -> '.$mode.'<br>';
//echo 'refresh -> '.$refresh.'<br>';
//echo 'minute -> '.$minute.'<br>';


if(count($clubs) > 0) {

if (count($clubs)<$results)
	{
		$results=count($clubs);
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
			$document->addScript(JURI::base().'modules/mod_joomleague_club_birthday/js/qscroller.js');
			break;
		case 'H':
			include(dirname(__FILE__).DS.'js'.DS.'qscrollerh.js');
			$document->addScript(JURI::base().'modules/mod_joomleague_club_birthday/js/qscroller.js');
			break;
	}
    
}




require(JModuleHelper::getLayoutPath('mod_joomleague_club_birthday'));
?>
