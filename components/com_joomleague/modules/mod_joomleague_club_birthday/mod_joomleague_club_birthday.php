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

// Prevent that result is null when either $players or $crew is null by casting each to an array.
//$persons = array_merge((array)$players, (array)$crew);
if(count($clubs)>1)   $clubs = jl_birthday_sort($clubs, array("n+days_to_birthday", "n".$params->get('sort_order')."age"), false);

if ( $show_debug_info )
{
echo 'this->mod_joomleague_club_birthday persons<br /><pre>~' . print_r($clubs,true) . '~</pre><br />';
echo 'this->mod_joomleague_club_birthday params<br /><pre>~' . print_r($params,true) . '~</pre><br />';
}

$k=0;
$counter=0;
?>
<table class="birthday">
<?php
if(count($clubs) > 0) {
	foreach ($clubs AS $club) 
    {
        $club['default_picture'] = JoomleagueHelper::getDefaultPlaceholder('clublogobig');
        
		if (($params->get('limit')> 0) && ($counter == intval($params->get('limit')))) break;
		$class = ($k == 0)? $params->get('sectiontableentry1') : $params->get('sectiontableentry2');

		$thispic = "";
		$flag = $params->get('show_club_flag')? Countries::getCountryFlag($club['country']) . "&nbsp;" : "";
		
        $text = $club['name'];
        $usedname = $flag.$text;
		
		$club_link = "";
        $club_link = JoomleagueHelperRoute::getClubInfoRoute($club['project_id'],
																$club['id']);
		
		$showname = JHTML::link( $club_link, $usedname );
		?>
	<tr class="<?php echo $params->get('heading_style');?>">
		<td class="birthday"><?php echo $showname;?></td>
	</tr>
	<tr class="<?php echo $class;?>">
		<td class="birthday">
		<?php
		if ($params->get('show_picture')==1) 
        {
			if (file_exists(JPATH_BASE.'/'.$club['picture'])&&$club['picture']!='') 
            {
				$thispic = $club['picture'];
			}
			elseif (file_exists(JPATH_BASE.'/'.$club['default_picture'])&&$club['default_picture']!='') 
            {
				$thispic = $club['default_picture'];
			}
			echo '<img src="'.JURI::base().'/'.$thispic.'" alt="'.$text.'" title="'.$text.'"';
			if ($params->get('picture_width') != '') echo ' width="'.$params->get('picture_width').'"';
			echo ' /><br />';

		}
		switch ($club['days_to_birthday']) {
			case 0: $whenmessage = $params->get('todaymessage');break;
			case 1: $whenmessage = $params->get('tomorrowmessage');break;
			default: $whenmessage = str_replace('%DAYS_TO%', $club['days_to_birthday'], trim($params->get('futuremessage')));break;
		}
        
        /*
		$birthdaytext = htmlentities(trim(JText::_($params->get('birthdaytext'))), ENT_COMPAT , 'UTF-8');
		$dayformat = htmlentities(trim($params->get('dayformat')));
		$birthdayformat = htmlentities(trim($params->get('birthdayformat')));
		$birthdaytext = str_replace('%WHEN%', $whenmessage, $birthdaytext);
        */
        
        if ( $club['founded'] != '0000-00-00' )
        {
            $birthdaytext = htmlentities(trim(JText::_($params->get('birthdaytext'))), ENT_COMPAT , 'UTF-8');
            $dayformat = htmlentities(trim($params->get('dayformat')));
		    $birthdayformat = htmlentities(trim($params->get('birthdayformat')));
		    $birthdaytext = str_replace('%WHEN%', $whenmessage, $birthdaytext);
            $birthdaytext = str_replace('%AGE%', $club['age'], $birthdaytext);
            $birthdaytext = str_replace('%DATE%', strftime($dayformat, strtotime($club['year'].'-'.$club['daymonth'])), $birthdaytext);
    		$birthdaytext = str_replace('%DATE_OF_BIRTH%', strftime($birthdayformat, strtotime($club['date_of_birth'])), $birthdaytext);
        }
        else
        {
            $birthdaytext = htmlentities(trim(JText::_($params->get('birthdaytextyear'))), ENT_COMPAT , 'UTF-8');
            $birthdaytext = str_replace('%AGE%', $club['age_year'], $birthdaytext);
        }
		
		
		$birthdaytext = str_replace('%BR%', '<br />', $birthdaytext);
		$birthdaytext = str_replace('%BOLD%', '<b>', $birthdaytext);
		$birthdaytext = str_replace('%BOLDEND%', '</b>', $birthdaytext);
			
		echo $birthdaytext;
		?></td>
	</tr>
	<?php
	$k = 1 - $k;
	$counter++;
	}
}
else {
?>
<tr>
	<td class="birthday"><?php echo''.str_replace('%DAYS%', $params->get('maxdays'), htmlentities(trim($params->get('not_found_text')))).''; ?></td>
</tr>
<?php } ?>
</table>
