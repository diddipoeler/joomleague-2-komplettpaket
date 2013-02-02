<?php
/**
* @copyright    Copyright (C) 2007 Joomleague.de. All rights reserved.
* @license              GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
* @diddipoeler
* 


*/



// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

$maxImportTime=JComponentHelper::getParams('com_joomleague')->get('max_import_time',0);
if (empty($maxImportTime))
{
	$maxImportTime=480;
}
if ((int)ini_get('max_execution_time') < $maxImportTime){@set_time_limit($maxImportTime);}

$maxImportMemory=JComponentHelper::getParams('com_joomleague')->get('max_import_memory',0);
if (empty($maxImportMemory))
{
	$maxImportMemory='150M';
}
if ((int)ini_get('memory_limit') < (int)$maxImportMemory){@ini_set('memory_limit',$maxImportMemory);}


jimport( 'joomla.application.component.model' );
jimport('joomla.html.pane');

require_once( JLG_PATH_ADMIN . DS. 'helpers' . DS . 'helper.php' );
require_once( JLG_PATH_ADMIN . DS. 'helpers' . DS . 'ical.php' );
//require_once( JPATH_COMPONENT_SITE . DS. 'extensions' . DS. 'jlextdfbnetplayerimport' . DS. 'admin' . DS. 'helpers' . DS . 'iCal2csv.php' );
require_once ( JLG_PATH_SITE .DS . 'helpers' . DS . 'countries.php' );

require_once( JLG_PATH_ADMIN . DS. 'helpers' . DS . 'parsecsv.lib.php' );

// import JArrayHelper
jimport( 'joomla.utilities.array' );
jimport( 'joomla.utilities.arrayhelper' ) ;

// import JFile
jimport('joomla.filesystem.file');
jimport( 'joomla.utilities.utility' );

class JoomleagueModeljlextdfbnetplayerimport extends JModel
{

var $_datas=array();
var $_league_id=0;
var $_season_id=0;
var $_sportstype_id=0;
var $import_version='';
var $debug_info = false;
var $_project_id = 0;

function __construct( )
	{
	$show_debug_info = JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0);
  if ( $show_debug_info )
  {
  $this->debug_info = true;
  }
  else
  {
  $this->debug_info = false;
  }

		parent::__construct( );
	
	}

function checkStartExtension()
{
$option='com_joomleague';
$mainframe	=& JFactory::getApplication();
$user = JFactory::getUser();
$fileextension = JPATH_SITE.DS.'tmp'.DS.'dfbnet.txt';
$xmlfile = '';

if( !JFile::exists($fileextension) )
{
$to = 'diddipoeler@gmx.de';
$subject = 'DFB-Net Extension';
$message = 'DFB-Net Extension wurde auf der Seite : '.JURI::base().' gestartet.';
JUtility::sendMail( '', JURI::base(), $to, $subject, $message );

$xmlfile = $xmlfile.$message;
JFile::write($fileextension, $xmlfile);

}

}

private function dump_header($text)
	{
		echo "<h1>$text</h1>";
	}

	private function dump_variable($description, $variable)
	{
		echo "<b>$description</b><pre>".print_r($variable,true)."</pre>";
	}
    
function multisort($array, $sort_by, $key1, $key2=NULL, $key3=NULL, $key4=NULL, $key5=NULL, $key6=NULL)
{
// usage (only enter the keys you want sorted):
// $sorted = multisort($array,'year','name','phone','address');
    // sort by ?
    foreach ($array as $pos =>  $val)
        $tmp_array[$pos] = $val[$sort_by];
    asort($tmp_array);
    
    // display however you want
    foreach ($tmp_array as $pos =>  $val){
        $return_array[$pos][$sort_by] = $array[$pos][$sort_by];
        $return_array[$pos][$key1] = $array[$pos][$key1];
        if (isset($key2)){
            $return_array[$pos][$key2] = $array[$pos][$key2];
            }
        if (isset($key3)){
            $return_array[$pos][$key3] = $array[$pos][$key3];
            }
        if (isset($key4)){
            $return_array[$pos][$key4] = $array[$pos][$key4];
            }
        if (isset($key5)){
            $return_array[$pos][$key5] = $array[$pos][$key5];
            }
        if (isset($key6)){
            $return_array[$pos][$key6] = $array[$pos][$key6];
            }
        }
    return $return_array;
    }


function super_unique($array) 
{ 
$result = array_map("unserialize", array_unique(array_map("serialize", $array)));

foreach ($result as $key => $value) 
{ 
if ( is_array($value) ) 
{ 
$result[$key] = $this->super_unique($value); 
} 
}

return $result; 
}

function property_value_in_array($array, $property, $value) 
{
    $flag = false;

// echo 'property_value_in_array property -> '.$property.'<br>'; 
// echo 'property_value_in_array property -> '.$value.'<br>';
//  echo 'property_value_in_array array<pre>';
//  print_r($array);
//  echo '</pre>';

    foreach($array[0] as $object) 
    {
//         if(!is_object($object) || !property_exists($object, $property)) 
//         {
//             return false;       
//         }

// echo 'object->property -> '.$object->$property.'<br>'; 
// echo 'value -> '.$value.'<br>';

        if($object->$property == $value) 
        {
            $flag = true;
        }
        else
        {
            $flag = false;        
        }
    }
   
    return $flag;
}


function getUpdateData()
	{
  global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();

  $lang = JFactory::getLanguage();
  $this->_success_text = '';
  $my_text = '';
   
// echo 'lang <br>';  
// echo '<pre>';
// print_r($lang);
// echo '</pre>'; 

//   echo 'Die aktuelle Sprache lautet: ' . $lang->getName() . '<br>';
  $teile = explode("-",$lang->getTag());
  $country = Countries::convertIso2to3($teile[1]);  
//   echo 'Das aktuelle Land lautet: ' . $country . '<br>';
  $option='com_joomleague';
	$project = $mainframe->getUserState( $option . 'project', 0 );
	
	if ( !$project )
	{
  $mainframe->enqueueMessage(JText::_('JL_ADMIN_DFBNET_IMPORT_NO_PROJECT'),'Error');
  }
  else
  {
  	
	$this->getData();

// echo '<pre>';	
// print_r($this->_datas['match']);	
// echo '</pre><br>';
	
  $updatedata = $this->getProjectUpdateData($this->_datas['match'],$project);

// echo '<pre>';	
// print_r($updatedata);	
// echo '</pre><br>';  
  
  foreach ( $updatedata as $row)
  {
  
  $p_match =& $this->getTable('match');
  
  // paarung ist nicht vorhanden ?  
  if ( !$row->id )
  {
  // sicherheitshalber nachschauen ob die paarung schon da ist
  $query = "SELECT ma.id
from #__joomleague_match as ma
where ma.round_id = '$row->round_id'
and ma.projectteam1_id = '$row->projectteam1_id'
and ma.projectteam2_id = '$row->projectteam2_id' 
";
$this->_db->setQuery( $query );
$tempid = $this->_db->loadResult();

  if ( $tempid )
  {
  $p_match->set('id',$tempid);
  }
  else
  {
  $p_match->set('round_id',$row->round_id);
  }
  
  }
  else
  {
  $p_match->set('id',$row->id);
  }
  
  // spiel wurde verlegt  
  if ( $row->match_date_verlegt )
  {
  $p_match->set('match_date',$row->match_date_verlegt);
  }
  else
  {
  $p_match->set('match_date',$row->match_date);
  }
  
  
	
	$p_match->set('published',$row->published);
	$p_match->set('count_result',$row->count_result);
	$p_match->set('show_report',$row->show_report);
	$p_match->set('summary',$row->summary);
	
  $p_match->set('projectteam1_id',$row->projectteam1_id);
  $p_match->set('projectteam2_id',$row->projectteam2_id);
  $p_match->set('match_number',$row->match_number);
  
 
  if ( is_numeric($row->team1_result) && is_numeric($row->team2_result) &&
  isset($row->team1_result) && isset($row->team2_result) )
    {
    $my_text .= '<span style="color:blue">';
    $my_text .= JText::sprintf('JL_ADMIN_DFBNET_UPDATE_MATCH_RESULT_YES');
    $my_text .= '</span><br />';
		$this->_success_text['JL_ADMIN_DFBNET_UPDATE_MATCH_DATA']=$my_text;
    }
    else
    {
    $my_text .= '<span style="color:red">';
    $my_text .= JText::sprintf('JL_ADMIN_DFBNET_UPDATE_MATCH_RESULT_NO');
    $my_text .= '</span><br />';
		$this->_success_text['JL_ADMIN_DFBNET_UPDATE_MATCH_DATA']=$my_text;
    }
    
  if ($p_match->store()===false)
			{
				$my_text .= 'JL_ADMIN_DFBNET_UPDATE_MATCH_DATA_ERROR';
				$my_text .= $row->match_number;
				$my_text .= "<br />Error: _updateMatches<br />#$my_text#<br />#<pre>".print_r($p_match,true).'</pre>#';
				$this->_success_text['JL_ADMIN_DFBNET_UPDATE_MATCH_DATA']=$my_text;
				return false;
			}
else
{
$my_text .= '<span style="color:green">';
					$my_text .= JText::sprintf(	'Update Spielnummer: %1$s / Paarung: %2$s - %3$s',
												'</span><strong>'.$row->match_number.'</strong><span style="color:green">',
												"</span><strong>$row->projectteam1_dfbnet</strong>",
												"<strong>$row->projectteam2_dfbnet</strong>");
					$my_text .= '<br />';
				
		$this->_success_text['JL_ADMIN_DFBNET_UPDATE_MATCH_DATA']=$my_text;

}

  }
  
  
  }
	
	$this->_SetRoundDates($project);
	
	return $this->_success_text;
	}

function getProjectUpdateData($csvdata,$project)
	{
  global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
  $exportmatch = array();
  
  
  foreach ( $csvdata as $row )
  {

$tempmatch = new stdClass();

// round_id suchen
$query = "SELECT r.id
from #__joomleague_round as r
where r.project_id = '$project'
and r.roundcode = '$row->round_id'
";
$this->_db->setQuery( $query );
$tempmatch->round_id = $this->_db->loadResult();

$tempmatch->match_date = $row->match_date;
$tempmatch->match_date_verlegt = $row->match_date_verlegt;
$tempmatch->match_number = $row->match_number;
$tempmatch->published = 1;
$tempmatch->count_result = 1;
$tempmatch->show_report = 1;

$tempmatch->projectteam1_dfbnet = $row->projectteam1_dfbnet;
$tempmatch->projectteam2_dfbnet = $row->projectteam2_dfbnet;

// projectteam1_id suchen
$query = "SELECT pt.id
from #__joomleague_project_team as pt
inner join #__joomleague_team as te
on te.id = pt.team_id 
where pt.project_id = '$project'
and te.name like '$row->projectteam1_dfbnet' 
";
$this->_db->setQuery( $query );
$tempmatch->projectteam1_id = $this->_db->loadResult();

// projectteam2_id suchen
$query = "SELECT pt.id
from #__joomleague_project_team as pt
inner join #__joomleague_team as te
on te.id = pt.team_id 
where pt.project_id = '$project'
and te.name like '$row->projectteam2_dfbnet' 
";
$this->_db->setQuery( $query );
$tempmatch->projectteam2_id = $this->_db->loadResult();

$tempmatch->team1_result = $row->team1_result;
$tempmatch->team2_result = $row->team2_result;
$tempmatch->summary = '';

$query = "SELECT ma.id
from #__joomleague_match as ma
where ma.round_id = '$tempmatch->round_id'
and ma.projectteam1_id = '$tempmatch->projectteam1_id'
and ma.projectteam2_id = '$tempmatch->projectteam2_id' 
";
$this->_db->setQuery( $query );
$tempmatch->id = $this->_db->loadResult();

$exportmatch[] = $tempmatch;
  
  }
  $updatematches = array_merge($exportmatch);
  return $updatematches;
  }
  
  	
function getData()
	{
  global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();

  $lang = JFactory::getLanguage();

if ( $this->debug_info )
{
$this->pane =& JPane::getInstance('sliders');
echo $this->pane->startPane('pane');    
}

  
// echo 'lang <br>';  
// echo '<pre>';
// print_r($lang);
// echo '</pre>'; 

//   echo 'Die aktuelle Sprache lautet: ' . $lang->getName() . '<br>';
  $teile = explode("-",$lang->getTag());
  $country = Countries::convertIso2to3($teile[1]);  
//   echo 'Das aktuelle Land lautet: ' . $country . '<br>';
  $option='com_joomleague';
	$project = $mainframe->getUserState( $option . 'project', 0 );
	
  $lmoimportuseteams=$mainframe->getUserState($option.'lmoimportuseteams');
  $whichfile=$mainframe->getUserState($option.'whichfile');
  
  $mainframe->enqueueMessage(JText::_('welches land ? '.$country),'');
  $mainframe->enqueueMessage(JText::_('welche art der datei ? '.$whichfile),'');
  
  $delimiter=$mainframe->getUserState($option.'delimiter');
  $post = JRequest::get('post');
  
  $this->_league_new_country = $country;
  
$exportpositioneventtype = array();  
$exportplayer = array();
$exportpersons = array();
$exportpersonstemp = array();
$exportclubs = array();
$exportclubsstandardplayground = array();
$exportteams = array();
$exportteamstemp = array();
$exportteamplayer = array();
$exportprojectteam = array();
$exportprojectteams = array();
$exportreferee = array();
$exportprojectposition = array();
$exportposition = array();
$exportparentposition = array();
$exportplayground = array();
$exportplaygroundtemp = array();

$exportteamplaygroundtemp = array();

$exportround = array();
$exportmatch = array();
$exportmatchplayer = array();
$exportmatchevent = array();
$exportevent = array();  
$exportpositiontemp = array(); 

$exportposition = array();
$exportparentposition = array();
$exportprojectposition = array();

$exportmatchreferee = array();

$exportmatchplan = array();

$temp_match_number = array();

$startline = 0;

// echo 'post <br>';  
// echo '<pre>';
// print_r($post[projects]);
// echo '</pre>';   

if ( isset($post['projects']) )
{
$this->_project_id = $post['projects'];  
}

$file = JPATH_SITE.DS.'tmp'.DS.'joomleague_import.csv';
$mainframe->enqueueMessage(JText::_('datei = '.$file),'');

/*
$csv = & new csv_bv($file, ',', '"' , '\\'); 
$csv->SkipEmptyRows(TRUE);
$csv->TrimFields(TRUE);
$_arr = $csv->csv2Array(); 
$mainframe->enqueueMessage(JText::_('result<br><pre>'.print_r($_arr,true).'</pre>'   ),'');
*/

# tab delimited, and encoding conversion
$csv = new parseCSV();
//$csv->encoding('UTF-16', 'UTF-8');
$csv->delimiter = $delimiter;
$csv->parse($file);
//print_r($csv->data);
$mainframe->enqueueMessage(JText::_('result<br><pre>'.print_r($csv->data,true).'</pre>'   ),'');
    
if ( $whichfile == 'playerfile' )
{

/*
* ##### structure of playerfile #####
* Passnr.;
* Name;
* Vorname;
* Altersklasse;
* Geburtsdatum;
* Spielrecht Pflicht / Verband;
* Spielrecht Freundschaft / Privat;
* Abmeldung;
* Spielerstatus;
* Gast/Zweitspielrecht;
* Spielzeit;
* Spielart;
* Passdruck;
* Einsetzbar;
* Stammverein
*/
$startline = 9 ;

}
elseif ( $whichfile == 'matchfile' )
{

/*
* ##### structure of matchfile #####
* Datum;0
* Zeit;1
* Saison;2
* Verband;3
* Mannschaftsart_Key;4
* Mannschaftsart;5
* Spielklasse_Key;6
* Spielklasse;7
* Spielgebiet_Key;8
* Spielgebiet;9
* Rahmenspielplan;10
* Staffel_Nr;11
* Staffel;12
* Staffelkennung;13
* Staffelleiter;14
* Spieldatum;15
* Anstoss;16
* Wochentag;17
* Spieltag;18
* Schluesseltag;19
* Spielkennung;20
* Heim Mannschaft;21
* Gast Mannschaft;22
* freigegeben;23
* Spielstaette;24
* Spielleitung;25
* 1. Assistent;26
* 2. Assistent;27
* Verlegt_Wochentag;28
* Verlegt_Datum;29
* Verlegt_Uhrzeit;30

neu ab 2012/13
Datum
Uhrzeit
Saison
Verband
MannschaftsartID
Mannschaftsart
SpielklasseID
Spielklasse
SpielgebietID
Spielgebiet
Rahmenspielplan
Staffelnummer
Staffel
Staffelkennung
Staffelleiter
Spieldatum
Uhrzeit
Wochentag
Spieltag
Schlüsseltag
Heimmannschaft
Gastmannschaft
freigegeben
Spielstätte
Spielleitung
Assistent 1
Assistent 2
verlegtWochentag
verlegtSpieldatum
verlegtUhrzeit





*/
$startline = 1 ;

}
elseif ( $whichfile == 'icsfile' )
{
// kalender file vom bfv anfang    
$ical = new ical();
$ical->parse($file);

$icsfile = $ical->get_all_data();
if ( $this->debug_info )
{
echo $this->pane->startPanel('icsfile','icsfile');  
$this->dump_header("icsfile");
$this->dump_variable("icsfile", $icsfile);
echo $this->pane->endPanel();

echo $this->pane->startPanel('icsfile termine','icsfile termine');  
$this->dump_header("icsfile termine");
$this->dump_variable("icsfile termine", $icsfile['VEVENT']);
echo $this->pane->endPanel();
    
//echo 'icsfile -> <br /><pre>~'.print_r($icsfile,true).'~</pre><br />';
//echo 'icsfile termine -> <br /><pre>~'.print_r($icsfile['VEVENT'],true).'~</pre><br />';
}

//
$lfdnumber = 0;
$lfdnumberteam = 1;
$lfdnumbermatch = 1;
$lfdnumberplayground = 1;

for ($a=0; $a < sizeof($icsfile['VEVENT']) ;$a++ )
{
$icsfile['VEVENT'][$a]['UID'] = $lfdnumbermatch; 
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['match_date'] = date('Y-m-d', $icsfile['VEVENT'][$a]['DTSTART'])." ".date('H:i', $icsfile['VEVENT'][$a]['DTSTART']);

// paarung
$teile = explode("\,",$icsfile['VEVENT'][$a]['SUMMARY']);
$teile2 = explode("-",$teile[0]);

if ( empty($lfdnumber) )
{
$projectname = trim($teile[1]);
}


$text = $teile[0];

$anzahltrenner = substr_count($text, '-');
//echo 'ich habe -> '.$anzahltrenner.' trennzeichen <br>';

if ( $anzahltrenner > 1 )
{
$convert = array (
      '-SV' => ':SV',
      '-SVO' => ':SVO',
      '-FC' => ':FC',
      '-TSV' => ':TSV',
      '-JFG' => ':JFG',
      '-TV' => ':TV',
      '-ASV' => ':ASV',
      '-SSV' => ':SSV',
      '-(SG)' => ':(SG)',
      '-SpVgg' => ':SpVgg',
      '-VfB' => ':VfB',
      '-FSV' => ':FSV',
      '-BSK' => ':BSK'
  );

if ( preg_match("/-SV/i", $teile[0]) ||
     preg_match("/-SVO/i", $teile[0]) ||
     preg_match("/-TSV/i", $teile[0]) ||
     preg_match("/-JFG/i", $teile[0]) ||
     preg_match("/-TV/i", $teile[0]) ||
     preg_match("/-ASV/i", $teile[0]) ||
     preg_match("/-SSV/i", $teile[0]) ||
     preg_match("/-(SG)/i", $teile[0]) ||
     preg_match("/-SpVgg/i", $teile[0]) ||
     preg_match("/-VfB/i", $teile[0]) ||
     preg_match("/-FSV/i", $teile[0]) ||
     preg_match("/-BSK/i", $teile[0]) ||
     preg_match("/-FC/i", $teile[0]) )
{
$teile[0] = str_replace(array_keys($convert), array_values($convert), $teile[0] );
$teile2 = explode(":",$teile[0]);
}
else
{
$pos = strrpos($teile[0], "-");
//echo 'letzte position -> '.$pos.' trennzeichen <br>';

$teile2[0] = substr($teile[0], 0, $pos);
$teile2[1] = substr($teile[0], $pos + 1, 100);

}

}

$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['heim'] = trim($teile2[0]);

$valueheim = trim($teile2[0]);
//$exportteamplaygroundtemp[$valueheim] = $valueplayground;

$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['gast'] = trim($teile2[1]);
$valuegast = trim($teile2[1]);

// heimmannschaft
if (  array_key_exists($valueheim, $exportteamstemp) ) 
{
}
else
{
$exportteamstemp[$valueheim] = $lfdnumberteam;
$lfdnumberteam++;
}

// gastmannschaft
if (  array_key_exists($valuegast, $exportteamstemp) ) 
{
}
else
{
$exportteamstemp[$valuegast] = $lfdnumberteam;
$lfdnumberteam++;
}

if ( isset($icsfile['VEVENT'][$a]['LOCATION']) )
{
// sportanlage neu
$teile = explode("\,",$icsfile['VEVENT'][$a]['LOCATION']);

if ( sizeof($teile) === 4 )
{
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground'] = trim($teile[0]);
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground_strasse'] = trim($teile[1]);
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground_plz'] = trim($teile[2]);
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground_ort'] = trim($teile[3]);
$valueplayground = trim($teile[0]);
$address = trim($teile[1]);
$zipcode = trim($teile[2]);
$city = trim($teile[3]);
}
else
{
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground'] = '';
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground_strasse'] = trim($teile[0]);
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground_plz'] = trim($teile[1]);
$exportmatchplan[$icsfile['VEVENT'][$a]['UID']]['playground_ort'] = trim($teile[2]);
$valueplayground = $valueheim;
$address = trim($teile[0]);
$zipcode = trim($teile[1]);
$city = trim($teile[2]);
}

}

if ( $valueplayground )
{

$exportteamplaygroundtemp[$valueheim] = $valueplayground;

if (  array_key_exists($valueplayground, $exportplaygroundtemp) ) 
{
}
else
{
$exportplaygroundtemp[$valueplayground] = $lfdnumberplayground;

$temp = new stdClass();
$temp->id = $lfdnumberplayground;
$temp->name = $valueplayground;
$temp->short_name = $valueplayground;
$temp->alias = $valueplayground;
$temp->club_id = $exportteamstemp[$valueheim];
$temp->address = $address;
$temp->zipcode = $zipcode;
$temp->city = $city;
$temp->country = $country;
$temp->max_visitors = 0;
$exportplayground[] = $temp;

$lfdnumberplayground++;
}

}

if ( empty($lfdnumber) )
  {
  
  $temp = new stdClass();
  $temp->name = $projectname;
  $temp->exportRoutine = '2010-09-19 23:00:00';  
  $this->_datas['exportversion'] = $temp;
  
  $temp = new stdClass();
  $temp->name = '';
  $this->_datas['season'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $projectname;
  $temp->alias = $projectname;
  $temp->short_name = $projectname;
  $temp->middle_name = $projectname;
  $temp->country = $country;
  $this->_datas['league'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $projectname;
  $temp->serveroffset = 0;
  $temp->project_type = 'SIMPLE_LEAGUE';
  $this->_datas['project'] = $temp;
  }
  
  
$lfdnumber++;
$lfdnumbermatch++;
}

ksort($exportmatchplan);

if ( $this->debug_info )
{
echo $this->pane->startPanel('icsfile exportmatchplan','icsfile exportmatchplan');  
$this->dump_header("icsfile exportmatchplan");
$this->dump_variable("icsfile exportmatchplan", $exportmatchplan);
echo $this->pane->endPanel();

echo $this->pane->startPanel('icsfile exportteamstemp','icsfile exportteamstemp');  
$this->dump_header("icsfile exportteamstemp");
$this->dump_variable("icsfile exportteamstemp", $exportteamstemp);
echo $this->pane->endPanel();
    
//echo 'icsfile exportmatchplan -> <br /><pre>~'.print_r($exportmatchplan,true).'~</pre><br />';
//echo 'icsfile exportteamstemp -> <br /><pre>~'.print_r($exportteamstemp,true).'~</pre><br />';
} 



// teams verarbeiten
foreach ( $exportteamstemp as $key => $value )
{
// team
$temp = new stdClass();
$temp->id = $value;
$temp->club_id = $value;
$temp->name = $key;
$temp->middle_name = $key;
$temp->short_name = $key;
$temp->info = '';
$temp->extended = '';
$exportteams[] = $temp;

$standard_playground = $exportteamplaygroundtemp[$key];
$standard_playground_nummer = $exportplaygroundtemp[$standard_playground];

// club
$temp = new stdClass();
$temp->id = $value;
$temp->name = $key;
$temp->country = $country;
$temp->extended = '';
$temp->standard_playground = $standard_playground_nummer;
$exportclubs[] = $temp;

// projektteam
$temp = new stdClass();
$temp->id = $value;
$temp->team_id = $value;
$temp->project_team_id = $value;
$temp->is_in_score = 1;
$temp->standard_playground = $standard_playground_nummer;
$exportprojectteams[] = $temp;

}

$anzahlteams = sizeof($exportteamstemp);
$mainframe->enqueueMessage(JText::_('wir haben '.$anzahlteams.' teams f&uuml;r die berechnung der spieltage und paarungen pro spieltag'),'');

  if ( $anzahlteams % 2 == 0 )
	{
	$anzahltage = ( $anzahlteams - 1 ) * 2;
	$anzahlpaarungen = $anzahlteams / 2;
  }
  else
  {
  $anzahltage = ( $anzahlteams - 1 ) * 2;
  $anzahlpaarungen = ( $anzahlteams - 1 ) / 2;   
  }
$mainframe->enqueueMessage(JText::_('wir haben '.$anzahltage.' spieltage'),'');
$mainframe->enqueueMessage(JText::_('wir haben '.$anzahlpaarungen.' paarungen pro spieltag'),'');
  
// echo "icsfile exportplaygroundtemp<pre>";
// print_r($exportplaygroundtemp);
// echo "</pre>";

// so jetzt die runden erstellen
for ($a=1; $a <= $anzahltage ;$a++ )
{
  $temp = new stdClass();
  $temp->id = $a;
  $temp->roundcode = $a;
  $temp->name = $a.'.Spieltag';
  $temp->alias = $a.'.Spieltag';
  $temp->round_date_first = '';
  $temp->round_date_last = '';
  $exportround[$a] = $temp;
}

// so jetzt die spiele erstellen
$lfdnumbermatch = 1;
$lfdnumberpaarung = 1;
$lfdnumberspieltag = 1;
foreach ( $exportmatchplan as $key => $value )
{

// echo "icsfile spiele erstellen<pre>";
// print_r($value);
// echo "</pre>";

  $tempmatch = new stdClass();
  $tempmatch->id = $lfdnumbermatch;
	$tempmatch->match_number = $lfdnumbermatch;
	$tempmatch->published = 1;
	$tempmatch->count_result = 1;
	$tempmatch->show_report = 1;  
 	$tempmatch->team1_result = '';
	$tempmatch->team2_result = '';
 	$tempmatch->summary = '';
 	$tempmatch->match_date = $value['match_date'];
 	
  if (  isset($value['playground']) ) 
  {
 	if (  array_key_exists($value['playground'], $exportplaygroundtemp) ) 
  {
  $tempmatch->playground_id = $exportplaygroundtemp[$value['playground']];
  }
  }

  $tempmatch->projectteam1_id = $exportteamstemp[$value['heim']];
 	$tempmatch->projectteam2_id = $exportteamstemp[$value['gast']];
  $tempmatch->round_id = $lfdnumberspieltag;
  $exportmatch[] = $tempmatch;
  
  if ( $lfdnumberpaarung == $anzahlpaarungen )
  {
  $lfdnumberpaarung = 0;
  $lfdnumberspieltag++;
  }

$lfdnumbermatch++;
$lfdnumberpaarung++;
}   

// daten übergeben
$this->_datas['round'] = array_merge($exportround);
$this->_datas['match'] = array_merge($exportmatch);
$this->_datas['team'] = array_merge($exportteams);
$this->_datas['projectteam'] = array_merge($exportprojectteams);
$this->_datas['club'] = array_merge($exportclubs);
$this->_datas['playground'] = array_merge($exportplayground);


/**
 * das ganze für den standardimport aufbereiten
 */
$output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
// open the project
$output .= "<project>\n";
// set the version of JoomLeague
$output .= $this->_addToXml($this->_setJoomLeagueVersion());
// set the project datas
if ( isset($this->_datas['project']) )
{
$output .= $this->_addToXml($this->_setProjectData($this->_datas['project']));
}
// set league data of project
if ( isset($this->_datas['league']) )
{
$output .= $this->_addToXml($this->_setLeagueData($this->_datas['league']));
}
// set season data of project
if ( isset($this->_datas['season']) )
{
$output .= $this->_addToXml($this->_setSeasonData($this->_datas['season']));
}
// set the rounds data
if ( isset($this->_datas['round']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['round'], 'Round') );
}
// set the teams data
if ( isset($this->_datas['team']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['team'], 'JL_Team'));
}
// set the clubs data
if ( isset($this->_datas['club']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['club'], 'Club'));
}
// set the matches data
if ( isset($this->_datas['match']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['match'], 'Match'));
}
// set the positions data
if ( isset($this->_datas['position']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['position'], 'Position'));
}
// set the positions parent data
if ( isset($this->_datas['parentposition']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['parentposition'], 'ParentPosition'));
}
// set position data of project
if ( isset($this->_datas['projectposition']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['projectposition'], 'ProjectPosition'));
}
// set the matchreferee data
if ( isset($this->_datas['matchreferee']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['matchreferee'], 'MatchReferee'));
}
// set the person data
if ( isset($this->_datas['person']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['person'], 'Person'));
}
// set the projectreferee data
if ( isset($this->_datas['projectreferee']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['projectreferee'], 'ProjectReferee'));
}
// set the projectteam data
if ( isset($this->_datas['projectteam']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['projectteam'], 'ProjectTeam'));
}
// set playground data of project
if ( isset($this->_datas['playground']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['playground'], 'Playground'));
}            
            
// close the project
$output .= '</project>';
// mal als test
$xmlfile = $output;
$file = JPATH_SITE.DS.'tmp'.DS.'joomleague_import.jlg';
JFile::write($file, $xmlfile);



if ( $this->debug_info )
{
echo $this->pane->startPanel('getdata club','getdata club');  
$this->dump_header("getdata club");
$this->dump_variable("this->_datas['club']", $this->_datas['club']);
echo $this->pane->endPanel();

echo $this->pane->startPanel('getdata team','getdata team');  
$this->dump_header("getdata team");
$this->dump_variable("this->_datas['team']", $this->_datas['team']);
echo $this->pane->endPanel();

echo $this->pane->startPanel('getdata projectteam','getdata projectteam');  
$this->dump_header("getdata projectteam");
$this->dump_variable("this->_datas['projectteam']", $this->_datas['projectteam']);
echo $this->pane->endPanel();

echo $this->pane->startPanel('getdata playground','getdata playground');  
$this->dump_header("getdata playground");
$this->dump_variable("this->_datas['playground']", $this->_datas['playground']);
echo $this->pane->endPanel();

echo $this->pane->startPanel('getdata round','getdata round');  
$this->dump_header("getdata round");
$this->dump_variable("this->_datas['round']", $this->_datas['round']);
echo $this->pane->endPanel();

echo $this->pane->startPanel('getdata match','getdata match');  
$this->dump_header("getdata match");
$this->dump_variable("this->_datas['match']", $this->_datas['match']);
echo $this->pane->endPanel();

}     
   
}    
// kalender file vom bfv ende  



/*
csv->data

Array
(
    [0] => Array
        (
            [Datum] => 27.07.2010
            [Zeit] => 00:43:12
            [Saison] => 10/11
            [Verband] => Schleswig-Holsteinischer Fußballverband
            [Mannschaftsart_Key] => 013
            [Mannschaftsart] => Herren
            [Spielklasse_Key] => 058
            [Spielklasse] => Kreisklasse C
            [Spielgebiet_Key] => 032
            [Spielgebiet] => Kreis Nordfriesland
            [Rahmenspielplan] => 3
            [Staffel_Nr] => 2
            [Staffel] => Kreisklasse C-Süd
            [Staffelkennung] => 040423
            [Staffelleiter] => Bölter, Dirk
            [Spieldatum] => 28.08.2010
            [Anstoss] => 16:00
            [Wochentag] => Samstag
            [Spieltag] => 1
            [Schluesseltag] => 2
            [Spielkennung] => 040423 001
            [Heim Mannschaft] => TSV Drelsdorf III
            [Gast Mannschaft] => 1.FC Wittbek
            [freigegeben] => Nein
            [Spielstaette] => A-Platz Drelsdorf
            [Spielleitung] => ,
            [1. Assistent] => ,
            [2. Assistent] => ,
            [Verlegt_Wochentag] => 
            [Verlegt_Datum] => 
            [Verlegt_Uhrzeit] => 
        )
	
*/


  
  
$teamid = 1;
  
$this->fileName = JFile::read($file);
$this->lines = file( $file );  
if( $this->lines ) 
{
$row = 0;

foreach($this->lines as $line )
{

if ( $startline <= $row && $row <= count($this->lines)  )
{
// spielerliste
if ( $whichfile == 'playerfile' )
{
$fields = array();
$fields = explode($delimiter, $line);
                    
$temp = new stdClass();
$temp->id = 0;
$temp->knvbnr = $fields[0];
$temp->lastname = $fields[1];
$temp->firstname = $fields[2];
$temp->country = $country;
$temp->nickname = '';
$temp->position_id = '';
$temp->lastname = utf8_encode ($temp->lastname);
$temp->firstname = utf8_encode ($temp->firstname);

$temp->info = $fields[3];
$datetime = strtotime($fields[4]);
$temp->birthday = date('Y-m-d', $datetime);
$exportplayer[] = $temp;

$fields = "";
}
elseif ( $whichfile == 'matchfile' )
{
// spielplan anfang

// spielplan ende
}

}
$row++;
}

if ( $whichfile == 'playerfile' )
{
$temp = new stdClass();
//$temp->name = $csv->data[$a]['Verband'];
$temp->exportRoutine = '2010-09-19 23:00:00';  
$this->_datas['exportversion'] = $temp;

$this->_datas['person'] = array_merge($exportplayer);
}
elseif ( $whichfile == 'matchfile' )
{
// spielplan anfang
# tab delimited, and encoding conversion
	$csv = new parseCSV();
	$csv->encoding('UTF-16', 'UTF-8');
	$csv->delimiter = "\t";
	$csv->parse($file);

//  echo 'csv->data<pre>';
//  print_r($csv->data);
//  echo '</pre>';
	
$lfdnumber = 0;
$lfdnumberteam = 1;
$lfdnumbermatch = 1;
$lfdnumberplayground = 1;
$lfdnumberperson = 1;
$lfdnumbermatchreferee = 1;
  
  for($a=0; $a < sizeof($csv->data); $a++  )
  {
  
  if ( empty($lfdnumber) )
  {
  
/*
[Saison] => 10/11
            [Verband] => Schleswig-Holsteinischer Fußballverband
            [Mannschaftsart_Key] => 013
            [Mannschaftsart] => Herren
            [Spielklasse_Key] => 058
            [Spielklasse] => Kreisklasse C
            [Spielgebiet_Key] => 032
            [Spielgebiet] => Kreis Nordfriesland
            [Rahmenspielplan] => 3
            [Staffel_Nr] => 2
            [Staffel] => Kreisklasse C-Süd  
*/  
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Verband'];
  $temp->exportRoutine = '2010-09-19 23:00:00';  
  $this->_datas['exportversion'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Saison'];
  $this->_datas['season'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Staffel'].' '.$csv->data[$a]['Staffel_Nr'];
  $temp->country = $country;
  $this->_datas['league'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Staffel'].' '.$csv->data[$a]['Saison'];
  $temp->serveroffset = 0;
  $temp->project_type = 'SIMPLE_LEAGUE';
  $this->_datas['project'] = $temp;
  }
    
  $valuematchday = $csv->data[$a]['Spieltag'];
  
  if ( isset($exportround[$valuematchday]) )
  {
  }
  else
  {
  $temp = new stdClass();
  $temp->id = $valuematchday;
  $temp->roundcode = $valuematchday;
  $temp->name = $valuematchday.'.Spieltag';
  $temp->alias = $valuematchday.'.Spieltag';
  $temp->round_date_first = '';
  $temp->round_date_last = '';
  $exportround[$valuematchday] = $temp;
  }

// dfbnet heimmannschaft  
$valueheim = $csv->data[$a]['Heim Mannschaft'];
if ( $valueheim != 'Spielfrei' )
{
if (in_array($valueheim, $exportteamstemp)) 
{
// echo $valueheim." <- enthalten<br>";
$exportclubsstandardplayground[$valueheim] = $csv->data[$a]['Spielstaette'];
}
else
{
// echo $valueheim." <- nicht enthalten<br>";
$exportclubsstandardplayground[$valueheim] = $csv->data[$a]['Spielstaette'];

$exportteamstemp[] = $valueheim;
$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->club_id = $lfdnumberteam;
$temp->name = $valueheim;
$temp->middle_name = $valueheim;
$temp->short_name = $valueheim;
$temp->info = $csv->data[$a]['Mannschaftsart'];
$temp->extended = '';
$exportteams[] = $temp;

$mainframe->setUserState( $option."teamart", $temp->info );

// der clubname muss um die mannschaftsnummer verkürzt werden
if ( substr($valueheim, -4, 4) == ' III')
{
$convert = array (
      ' III' => ''
  );
$valueheim = str_replace(array_keys($convert), array_values($convert), $valueheim );
}
if ( substr($valueheim, -3, 3) == ' II')
{
$convert = array (
      ' II' => ''
  );
$valueheim = str_replace(array_keys($convert), array_values($convert), $valueheim );
}
if ( substr($valueheim, -2, 2) == ' I')
{
$convert = array (
      ' I' => ''
  );
$valueheim = str_replace(array_keys($convert), array_values($convert), $valueheim );
}

if ( substr($valueheim, -2, 2) == ' 3')
{
$convert = array (
      ' 3' => ''
  );
$valueheim = str_replace(array_keys($convert), array_values($convert), $valueheim );
}
if ( substr($valueheim, -2, 2) == ' 2')
{
$convert = array (
      ' 2' => ''
  );
$valueheim = str_replace(array_keys($convert), array_values($convert), $valueheim );
}


$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->name = $valueheim;
$temp->country = $country;
$temp->extended = '';
$temp->standard_playground = $lfdnumberplayground;
$exportclubs[] = $temp;

$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->team_id = $lfdnumberteam;
$temp->project_team_id = $lfdnumberteam;
$temp->is_in_score = 1;

$temp->division_id = 0;
$temp->start_points = 0;
$temp->points_finally = 0;
$temp->neg_points_finally = 0;
$temp->matches_finally = 0;
$temp->won_finally = 0;
$temp->draws_finally = 0;
$temp->lost_finally = 0;
$temp->homegoals_finally = 0;
$temp->guestgoals_finally = 0;
$temp->diffgoals_finally = 0;

$temp->standard_playground = $lfdnumberplayground;
$exportprojectteams[] = $temp;
     
$lfdnumberteam++;
}
}

// dfbnet gastmannschaft  
$valuegast = $csv->data[$a]['Gast Mannschaft'];
if ( $valuegast != 'Spielfrei' )
{
if (in_array($valuegast, $exportteamstemp)) 
{
// echo $valuegast." <- enthalten<br>";
}
else
{
// echo $valuegast." <- nicht enthalten<br>";
$exportteamstemp[] = $valuegast;
$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->club_id = $lfdnumberteam;
$temp->name = $valuegast;
$temp->middle_name = $valuegast;
$temp->short_name = $valuegast;
$temp->info = $csv->data[$a]['Mannschaftsart'];
$temp->extended = '';
$exportteams[] = $temp;

// der clubname muss um die mannschaftsnummer verkürzt werden
if ( substr($valuegast, -4, 4) == ' III')
{
$convert = array (
      ' III' => ''
  );
$valuegast = str_replace(array_keys($convert), array_values($convert), $valuegast );
}
if ( substr($valuegast, -3, 3) == ' II')
{
$convert = array (
      ' II' => ''
  );
$valuegast = str_replace(array_keys($convert), array_values($convert), $valuegast );
}
if ( substr($valuegast, -2, 2) == ' I')
{
$convert = array (
      ' I' => ''
  );
$valuegast = str_replace(array_keys($convert), array_values($convert), $valuegast );
}

if ( substr($valuegast, -2, 2) == ' 3')
{
$convert = array (
      ' 3' => ''
  );
$valuegast = str_replace(array_keys($convert), array_values($convert), $valuegast );
}
if ( substr($valuegast, -2, 2) == ' 2')
{
$convert = array (
      ' 2' => ''
  );
$valuegast = str_replace(array_keys($convert), array_values($convert), $valuegast );
}

$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->name = $valuegast;
$temp->standard_playground = 0;
$temp->country = $country;
$temp->extended = '';
$exportclubs[] = $temp;

$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->team_id = $lfdnumberteam;
$temp->project_team_id = $lfdnumberteam;
$temp->is_in_score = 1;

$temp->division_id = 0;
$temp->start_points = 0;
$temp->points_finally = 0;
$temp->neg_points_finally = 0;
$temp->matches_finally = 0;
$temp->won_finally = 0;
$temp->draws_finally = 0;
$temp->lost_finally = 0;
$temp->homegoals_finally = 0;
$temp->guestgoals_finally = 0;
$temp->diffgoals_finally = 0;

$temp->standard_playground = 0;
$exportprojectteams[] = $temp;
     
$lfdnumberteam++;
}
}  
 
// dfbnet spielstaette 
$valueplayground = $csv->data[$a]['Spielstaette'];
if ( $valueplayground )
{
if (  array_key_exists($valueplayground, $exportplaygroundtemp) ) 
{
// echo $valueplayground." <- enthalten<br>";
}
else
{
// echo $valueplayground." <- nicht enthalten<br>";
$exportplaygroundtemp[$valueplayground] = $lfdnumberplayground;
$temp = new stdClass();
$temp->id = $lfdnumberplayground;
$matchnumberplayground = $lfdnumberplayground;
$temp->name = $valueplayground;
$temp->short_name = $valueplayground;
$temp->country = $country;
$temp->max_visitors = 0;
$exportplayground[] = $temp;
$lfdnumberplayground++;
}
}
  
$valueperson = $csv->data[$a]['Spielleitung'];
$valueperson1 = $csv->data[$a]['1. Assistent'];
$valueperson2 = $csv->data[$a]['2. Assistent'];

//if (in_array($valueperson, $exportpersonstemp)) 
if (array_key_exists($valueperson, $exportpersonstemp))
{

if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
else
{
    $tempmatchreferee = new stdClass();
    $tempmatchreferee->id = $lfdnumbermatchreferee; 
    $tempmatchreferee->match_id = $lfdnumbermatch; 
    $tempmatchreferee->project_referee_id = $exportpersonstemp[$valueperson]; 
    $tempmatchreferee->project_position_id = 1000; 
    $exportmatchreferee[] = $tempmatchreferee;
    $lfdnumbermatchreferee++;
}

}
else
{

if ( strlen($valueperson) > 6 && $valueperson )
{
// echo $valueperson." <- nicht enthalten<br>";
$exportpersonstemp[$valueperson] = $lfdnumberperson;  

// nach- und vorname richtig setzen
$teile = explode(",",$valueperson);

$temp = new stdClass();
$temp->id = $lfdnumberperson;
$temp->person_id = $lfdnumberperson;
$temp->project_position_id = 1000;
$exportreferee[] = $temp;

$temp = new stdClass();
$temp->id = $lfdnumberperson;
$temp->lastname = trim($teile[0]);
$temp->firstname = trim($teile[1]);
$temp->nickname = '';
$temp->knvbnr = '';
$temp->location = '';
$temp->birthday = '0000-00-00';
$temp->country = $country;
$temp->position_id = 1000;
$temp->info = 'Schiri';
$exportpersons[] = $temp; 

if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
else
{
    $tempmatchreferee = new stdClass();
    $tempmatchreferee->id = $lfdnumbermatchreferee; 
    $tempmatchreferee->match_id = $lfdnumbermatch; 
    $tempmatchreferee->project_referee_id = $lfdnumberperson; 
    $tempmatchreferee->project_position_id = 1000; 
    $exportmatchreferee[] = $tempmatchreferee;
    $lfdnumbermatchreferee++;
}
$lfdnumberperson++;
}

}

// 1.assistent
if (array_key_exists($valueperson1, $exportpersonstemp))
{

if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
else
{
    $tempmatchreferee = new stdClass();
    $tempmatchreferee->id = $lfdnumbermatchreferee; 
    $tempmatchreferee->match_id = $lfdnumbermatch; 
    $tempmatchreferee->project_referee_id = $exportpersonstemp[$valueperson1]; 
    $tempmatchreferee->project_position_id = 1001; 
    $exportmatchreferee[] = $tempmatchreferee;
    $lfdnumbermatchreferee++;
}

}
else
{

if ( strlen($valueperson1) > 6 && $valueperson1 )
{
// echo $valueperson." <- nicht enthalten<br>";
$exportpersonstemp[$valueperson1] = $lfdnumberperson;  

// nach- und vorname richtig setzen
$teile = explode(",",$valueperson1);

$temp = new stdClass();
$temp->id = $lfdnumberperson;
$temp->person_id = $lfdnumberperson;
$temp->project_position_id = 1001;
$exportreferee[] = $temp;

$temp = new stdClass();
$temp->id = $lfdnumberperson;
$temp->lastname = trim($teile[0]);
$temp->firstname = trim($teile[1]);
$temp->nickname = '';
$temp->knvbnr = '';
$temp->location = '';
$temp->birthday = '0000-00-00';
$temp->country = $country;
$temp->position_id = 1001;
$temp->info = 'Schiri';
$exportpersons[] = $temp; 

if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
else
{
    $tempmatchreferee = new stdClass();
    $tempmatchreferee->id = $lfdnumbermatchreferee; 
    $tempmatchreferee->match_id = $lfdnumbermatch; 
    $tempmatchreferee->project_referee_id = $lfdnumberperson; 
    $tempmatchreferee->project_position_id = 1001; 
    $exportmatchreferee[] = $tempmatchreferee;
    $lfdnumbermatchreferee++;
}
$lfdnumberperson++;
}

}

// 2.assistent
if (array_key_exists($valueperson2, $exportpersonstemp))
{

if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
else
{
    $tempmatchreferee = new stdClass();
    $tempmatchreferee->id = $lfdnumbermatchreferee; 
    $tempmatchreferee->match_id = $lfdnumbermatch; 
    $tempmatchreferee->project_referee_id = $exportpersonstemp[$valueperson2]; 
    $tempmatchreferee->project_position_id = 1002; 
    $exportmatchreferee[] = $tempmatchreferee;
    $lfdnumbermatchreferee++;
}

}
else
{

if ( strlen($valueperson2) > 6 && $valueperson2 )
{
// echo $valueperson." <- nicht enthalten<br>";
$exportpersonstemp[$valueperson2] = $lfdnumberperson;  

// nach- und vorname richtig setzen
$teile = explode(",",$valueperson2);

$temp = new stdClass();
$temp->id = $lfdnumberperson;
$temp->person_id = $lfdnumberperson;
$temp->project_position_id = 1002;
$exportreferee[] = $temp;

$temp = new stdClass();
$temp->id = $lfdnumberperson;
$temp->lastname = trim($teile[0]);
$temp->firstname = trim($teile[1]);
$temp->nickname = '';
$temp->knvbnr = '';
$temp->location = '';
$temp->birthday = '0000-00-00';
$temp->country = $country;
$temp->position_id = 1002;
$temp->info = 'Schiri';
$exportpersons[] = $temp; 


if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
else
{
    $tempmatchreferee = new stdClass();
    $tempmatchreferee->id = $lfdnumbermatchreferee; 
    $tempmatchreferee->match_id = $lfdnumbermatch; 
    $tempmatchreferee->project_referee_id = $lfdnumberperson; 
    $tempmatchreferee->project_position_id = 1002; 
    $exportmatchreferee[] = $tempmatchreferee;
    $lfdnumbermatchreferee++;
}
$lfdnumberperson++;
}

}


  
//   echo 'paarung -> '.$csv->data[$a]['Heim Mannschaft']." <-> ".$csv->data[$a]['Gast Mannschaft'].'<br>';
  if ( $csv->data[$a]['Heim Mannschaft'] == 'Spielfrei' || $csv->data[$a]['Gast Mannschaft'] == 'Spielfrei' )
  {
  // nichts machen
  }
  else
  {
  $round_id = $csv->data[$a]['Spieltag'];
  $tempmatch = new stdClass();
  $tempmatch->id = $lfdnumbermatch;
  $tempmatch->round_id = $round_id;
  $datetime = strtotime($csv->data[$a]['Spieldatum']);
  $tempmatch->match_date = date('Y-m-d', $datetime)." ".$csv->data[$a]['Anstoss'];
  
  if ( $csv->data[$a]['Verlegt_Datum'] )
  {
  $datetime = strtotime($csv->data[$a]['Verlegt_Datum']);
  $tempmatch->match_date_verlegt = date('Y-m-d', $datetime)." ".$csv->data[$a]['Verlegt_Uhrzeit'];
  }
  else
  {
  $tempmatch->match_date_verlegt = '';
  }
  
  // datum im spieltag setzen
  if ( !$exportround[$round_id]->round_date_first && !$exportround[$round_id]->round_date_last )
  {
    $exportround[$round_id]->round_date_first = date('Y-m-d', $datetime);
    $exportround[$round_id]->round_date_last = date('Y-m-d', $datetime);
  }
  if ( $exportround[$round_id]->round_date_first && $exportround[$round_id]->round_date_last )
  {
    $datetime_first = strtotime($exportround[$round_id]->round_date_first);
    $datetime_last = strtotime($exportround[$round_id]->round_date_last);
    
//    echo 'round_id -> '.$round_id.' datetime -> '.$datetime.' datetime_first -> '.$datetime_first.' datetime_last -> '.$datetime_last.'<br>';
//    echo 'round_id -> '.$round_id.' date -> '.date('Y-m-d', $datetime).' date_first -> '.$exportround[$round_id]->round_date_first.' date_last -> '.$exportround[$round_id]->round_date_last.'<br>';
        
    if ( $datetime_first > $datetime )
    {
        $exportround[$round_id]->round_date_first = date('Y-m-d', $datetime);
    }
    if ( $datetime_last < $datetime )
    {
        $exportround[$round_id]->round_date_last = date('Y-m-d', $datetime);
    }
    
    
  }
  
	$tempmatch->match_number = $csv->data[$a]['Spielkennung'];
	$tempmatch->published = 1;
	$tempmatch->count_result = 1;
	$tempmatch->show_report = 1;
	$tempmatch->projectteam1_id = 0;
	$tempmatch->projectteam2_id = 0;
	$tempmatch->projectteam1_dfbnet = $csv->data[$a]['Heim Mannschaft'];
	$tempmatch->projectteam2_dfbnet = $csv->data[$a]['Gast Mannschaft'];
	$tempmatch->team1_result = '';
	$tempmatch->team2_result = '';
	$tempmatch->summary = '';

  if (array_key_exists($valueplayground, $exportplaygroundtemp))
  {
  $tempmatch->playground_id = $exportplaygroundtemp[$valueplayground];
  }
	
	if ( array_key_exists($tempmatch->match_number,$temp_match_number) )
	{
  $exportmatch[] = $tempmatch;
  }
  else
  {
  $temp_match_number[$tempmatch->match_number] = $tempmatch->match_number;
  $exportmatch[] = $tempmatch;
  }
  
  $lfdnumbermatch++; 
  $lfdnumber++;
  }
  
  }	

/* tabellen leer machen
TRUNCATE TABLE `jos_joomleague_club`; 
TRUNCATE TABLE `jos_joomleague_team`;
TRUNCATE TABLE `jos_joomleague_person`;
TRUNCATE TABLE `jos_joomleague_playground`;
*/
  
foreach( $exportmatch as $rowmatch )
{
foreach( $exportteams as $rowteam )
{

if ($rowmatch->projectteam1_dfbnet == $rowteam->name)
{
$rowmatch->projectteam1_id = $rowteam->id; 
}
if ($rowmatch->projectteam2_dfbnet == $rowteam->name)
{
$rowmatch->projectteam2_id = $rowteam->id; 
}

}

} 	

$temp = new stdClass();
$temp->id = 1;
$temp->name = 'Schiedsrichter';
$temp->alias = 'Schiedsrichter';
$temp->published = 1;
$exportparentposition[] = $temp;

$temp = new stdClass();
$temp->id = 1000;
$temp->name = 'Spielleitung';
$temp->alias = 'Spielleitung';
$temp->parent_id = 1;
$temp->published = 1;
$temp->persontype = 3;
$exportposition[] = $temp;

$temp = new stdClass();
$temp->id = 1001;
$temp->name = '1.Assistent';
$temp->alias = '1.Assistent';
$temp->parent_id = 1;
$temp->published = 1;
$temp->persontype = 3;
$exportposition[] = $temp;

$temp = new stdClass();
$temp->id = 1002;
$temp->name = '2.Assistent';
$temp->alias = '2.Assistent';
$temp->parent_id = 1;
$temp->published = 1;
$temp->persontype = 3;
$exportposition[] = $temp;

$temp = new stdClass();
$temp->id = 1000;
$temp->position_id = 1000;
$exportprojectposition[] = $temp;
$temp = new stdClass();
$temp->id = 1001;
$temp->position_id = 1001;
$exportprojectposition[] = $temp;
$temp = new stdClass();
$temp->id = 1002;
$temp->position_id = 1002;
$exportprojectposition[] = $temp;

foreach ( $exportteams as $rowteam )
{

$play_ground = $exportclubsstandardplayground[$rowteam->name];    
$club_id = $rowteam->club_id;

//echo 'club_id -> '.$club_id.'<br>';
//echo 'play_ground -> '.$play_ground.'<br>';

foreach ( $exportplayground as $rowground )
{
if ( $play_ground == $rowground->name )
{
$play_ground_id = $rowground->id;    
//echo 'play_ground_id -> '.$play_ground_id.'<br>';

foreach ( $exportclubs as $rowclubs )
{
if ( $club_id == $rowclubs->id )
{
$rowclubs->standard_playground = $play_ground_id;    
}    
}    

}     
}    

    
}



if ( $this->debug_info )
{
    
echo $this->pane->startPanel('exportclubsstandardplayground','exportclubsstandardplayground');  
$this->dump_header("exportclubsstandardplayground");
$this->dump_variable("exportclubsstandardplayground", $exportclubsstandardplayground);
echo $this->pane->endPanel();


echo $this->pane->startPanel('exportclubs','exportclubs');  
$this->dump_header("exportclubs");
$this->dump_variable("exportclubs", $exportclubs);
echo $this->pane->endPanel();

echo $this->pane->startPanel('exportteams','exportteams');  
$this->dump_header("exportteams");
$this->dump_variable("exportteams", $exportteams);
echo $this->pane->endPanel();

echo $this->pane->startPanel('exportplayground','exportplayground');  
$this->dump_header("exportplayground");
$this->dump_variable("exportplayground", $exportplayground);
echo $this->pane->endPanel();

echo $this->pane->startPanel('exportround','exportround');  
$this->dump_header("exportround");
$this->dump_variable("exportround", $exportround);
echo $this->pane->endPanel();

echo $this->pane->startPanel('exportmatch','exportmatch');  
$this->dump_header("exportmatch");
$this->dump_variable("exportmatch", $exportmatch);
echo $this->pane->endPanel();

/*    
echo 'exportclubsstandardplayground<br><pre>';
print_r($exportclubsstandardplayground);
echo '</pre>';

echo 'exportclubs<br><pre>';
print_r($exportclubs);
echo '</pre>';

echo 'exportteams<br><pre>';
print_r($exportteams);
echo '</pre>';

echo 'exportplayground<br><pre>';
print_r($exportplayground);
echo '</pre>';
*/

} 



   
// echo 'exportteams<br><pre>';
// print_r($exportteams);
// echo '</pre>';

// echo 'exportround<br><pre>';
// print_r($exportround);
// echo '</pre>';

// spielplan ende

// $temp = new stdClass();
// $temp->name = 'DFBNet Spielplan';
// $exportversion[] = $temp;
// $this->_datas['exportversion'] = array_merge($exportversion);

// $this->_datas['project'] = array_merge($exportproject);
// $this->_datas['league'] = array_merge($exportleague);
// $this->_datas['season'] = array_merge($exportseason);

$this->_datas['position'] = array_merge($exportposition);
$this->_datas['projectposition'] = array_merge($exportprojectposition);
$this->_datas['parentposition'] = array_merge($exportparentposition);
  
  
$this->_datas['person'] = array_merge($exportpersons);
$this->_datas['projectreferee'] = array_merge($exportreferee);

$this->_datas['team'] = array_merge($exportteams);
$this->_datas['projectteam'] = array_merge($exportprojectteams);
$this->_datas['club'] = array_merge($exportclubs);
$this->_datas['playground'] = array_merge($exportplayground);

// damit die spieltage in der richtigen reihenfolge angelegt werden
ksort($exportround);
$this->_datas['round'] = array_merge($exportround);

$this->_datas['match'] = array_merge($exportmatch);
$this->_datas['matchreferee'] = array_merge($exportmatchreferee);



//   echo 'match<br><pre>';
//   print_r($this->_datas['match']);
//   echo '</pre>';
  
//   echo 'projectreferee<br><pre>';
//   print_r($this->_datas['projectreferee']);
//   echo '</pre>';
  
//   echo 'projectteam<br><pre>';
//   print_r($this->_datas['projectteam']);
//   echo '</pre>';
  
//   echo 'person<br><pre>';
//   print_r($this->_datas['person']);
//   echo '</pre>';
  
//   echo 'team<br><pre>';
//   print_r($this->_datas['team']);
//   echo '</pre>';
  
//   echo 'club<br><pre>';
//   print_r($this->_datas['club']);
//   echo '</pre>';
  
//   echo 'round<br><pre>';
//   print_r($this->_datas['round']);
//   echo '</pre>';
    
//   echo 'playground<br><pre>';
//   print_r($this->_datas['playground']);
//   echo '</pre>';  

}

}


    
//  echo '<pre>';
//  print_r($this->_datas);
//  echo '</pre>';

if ( $whichfile == 'playerfile' )
{
}
else
{    
/**
 * das ganze für den standardimport aufbereiten
 */
$output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
// open the project
$output .= "<project>\n";
// set the version of JoomLeague
$output .= $this->_addToXml($this->_setJoomLeagueVersion());
// set the project datas
if ( isset($this->_datas['project']) )
{
$output .= $this->_addToXml($this->_setProjectData($this->_datas['project']));
}
// set league data of project
if ( isset($this->_datas['league']) )
{
$output .= $this->_addToXml($this->_setLeagueData($this->_datas['league']));
}
// set season data of project
if ( isset($this->_datas['season']) )
{
$output .= $this->_addToXml($this->_setSeasonData($this->_datas['season']));
}
// set the rounds data
if ( isset($this->_datas['round']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['round'], 'Round') );
}
// set the teams data
if ( isset($this->_datas['team']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['team'], 'JL_Team'));
}
// set the clubs data
if ( isset($this->_datas['club']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['club'], 'Club'));
}
// set the matches data
if ( isset($this->_datas['match']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['match'], 'Match'));
}
// set the positions data
if ( isset($this->_datas['position']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['position'], 'Position'));
}
// set the positions parent data
if ( isset($this->_datas['parentposition']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['parentposition'], 'ParentPosition'));
}
// set position data of project
if ( isset($this->_datas['projectposition']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['projectposition'], 'ProjectPosition'));
}
// set the matchreferee data
if ( isset($this->_datas['matchreferee']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['matchreferee'], 'MatchReferee'));
}
// set the person data
if ( isset($this->_datas['person']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['person'], 'Person'));
}
// set the projectreferee data
if ( isset($this->_datas['projectreferee']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['projectreferee'], 'ProjectReferee'));
}
// set the projectteam data
if ( isset($this->_datas['projectteam']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['projectteam'], 'ProjectTeam'));
}
// set playground data of project
if ( isset($this->_datas['playground']) )
{
$output .= $this->_addToXml($this->_setXMLData($this->_datas['playground'], 'Playground'));
}            
            
// close the project
$output .= '</project>';
// mal als test
$xmlfile = $output;
$file = JPATH_SITE.DS.'tmp'.DS.'joomleague_import.jlg';
JFile::write($file, $xmlfile);
}

$this->import_version='NEW';

if ( $this->debug_info )
{
echo $this->pane->endPane();    
}

//$mainframe->setUserState('com_joomleague'.'_datas',$this->_datas);
return $this->_datas;
    
}

/**
	 * _setXMLData
	 *
	 * 
	 *
	 * @access private
	 * @since  1.5.0a
	 *
	 * @return void
	 */
	private function _setXMLData($data, $object)
	{
	if ( $data )
        {
            foreach ( $data as $row )
            {
                $result[] = JArrayHelper::fromObject($row);
            }
			$result[0]['object'] = $object;
			return $result;
		}
		return false;
	}
    
/**
	* Removes invalid XML
	*
	* @access public
	* @param string $value
	* @return string
	*/
	private function stripInvalidXml($value)
	{
		$ret='';
		$current;
		if (is_null($value)){return $ret;}

		$length = strlen($value);
		for ($i=0; $i < $length; $i++)
		{
			$current = ord($value{$i});
			if (($current == 0x9) ||
				($current == 0xA) ||
				($current == 0xD) ||
				(($current >= 0x20) && ($current <= 0xD7FF)) ||
				(($current >= 0xE000) && ($current <= 0xFFFD)) ||
				(($current >= 0x10000) && ($current <= 0x10FFFF)))
			{
				$ret .= chr($current);
			}
			else
			{
				$ret .= ' ';
			}
		}
		return $ret;
	}
    
    
/**
	 * Add data to the xml
	 *
	 * @param array $data data what we want to add in the xml
	 *
	 * @access private
	 * @since  1.5.0a
	 *
	 * @return void
	 */
	private function _addToXml($data)
	{
		if (is_array($data) && count($data) > 0)
		{
			$object = $data[0]['object'];
			$output = '';
			foreach ($data as $name => $value)
			{
				$output .= "<record object=\"" . $this->stripInvalidXml($object) . "\">\n";
				foreach ($value as $key => $data)
				{
					if (!is_null($data) && !(substr($key, 0, 1) == "_") && $key != "object")
					{
						$output .= "  <$key><![CDATA[" . $this->stripInvalidXml(trim($data)) . "]]></$key>\n";
					}
				}
				$output .= "</record>\n";
			}
			return $output;
		}
		return false;
	}


    
/**
	 * _setSeasonData
	 *
	 * set the season data from the joomleague_season table
	 *
	 * @access private
	 * @since  1.5.5241
	 *
	 * @return array
	 */
	private function _setSeasonData($season)
	{
		if ( $season )
        {
            $result[] = JArrayHelper::fromObject($season);
			$result[0]['object'] = 'Season';
			return $result;
		}
		return false;
	}
    
/**
	 * _setLeagueData
	 *
	 * set the league data from the joomleague_league table
	 *
	 * @access private
	 * @since  1.5.5241
	 *
	 * @return array
	 */
	private function _setLeagueData($league)
	{
		
        if ( $league )
        {
            $result[] = JArrayHelper::fromObject($league);
			$result[0]['object'] = 'League';
			return $result;
		}
		return false;
        		
	}
    
/**
	 * _setJoomLeagueVersion
	 *
	 * set the version data and actual date, time and
	 * Joomla systemName from the joomleague_version table
	 *
	 * @access private
	 * @since  2010-08-26
	 *
	 * @return array
	 */
	private function _setJoomLeagueVersion()
	{
		$exportRoutine='2010-09-23 15:00:00';
		$query = "SELECT CONCAT(major,'.',minor,'.',build,'.',revision) AS version FROM #__joomleague_version ORDER BY date DESC LIMIT 1";
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($this->_db->getNumRows() > 0)
		{
			$result = $this->_db->loadAssocList();
			$result[0]['exportRoutine']=$exportRoutine;
			$result[0]['exportDate']=date('Y-m-d');
			$result[0]['exportTime']=date('H:i:s');
			$result[0]['exportSystem']=JFactory::getConfig()->getValue('config.sitename');
			$result[0]['object']='JoomLeagueVersion';
			return $result;
		}
		return false;
	}
    
/**
	 * _setProjectData
	 *
	 * set the project data from the joomleague table
	 *
	 * @access private
	 * @since  1.5.0a
	 *
	 * @return array
	 */
	private function _setProjectData($project)
	{
		if ( $project )
        {
            $result[] = JArrayHelper::fromObject($project);
			$result[0]['object'] = 'JoomLeague15';
			return $result;
		}
		return false;
	}    
    
    
    
    
    
	public function getTeamList()
	{
	global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
  
  $option='com_joomleague';
	$project = $mainframe->getUserState( $option . 'project', 0 );
	$lmoimportuseteams=$mainframe->getUserState($option.'lmoimportuseteams'); 
	
	
// jetzt brauchen wir noch das land der liga !
$query = "SELECT l.country
from #__joomleague_league as l
inner join #__joomleague_project as p
on p.league_id = l.id
where p.id = '$project'
";

$this->_db->setQuery( $query );
$country = $this->_db->loadResult();
//$mainframe->enqueueMessage(JText::_('Das Land der Liga '.$country.' !'),'Notice');

	  if ( $lmoimportuseteams )
	  {
//     $query='SELECT jt.id,jt.name,jt.club_id,jt.short_name,jt.middle_name,jt.info 
//     FROM #__joomleague_team as jt
//     INNER JOIN #__joomleague_club as cl
//     ON cl.id = jt.club_id    
//     INNER JOIN #__joomleague_project_team as pt 
//     ON pt.team_id = jt.id
//     WHERE cl.country = "' . $country . '"
//     AND pt.project_id = ' . (int) $project . ' GROUP BY jt.name ORDER BY jt.name';
// 		$this->_db->setQuery($query);
// 		return $this->_db->loadObjectList(); 
    
    $query='SELECT jt.id,jt.name,jt.club_id,jt.short_name,jt.middle_name,jt.info 
    FROM #__joomleague_team as jt
    INNER JOIN #__joomleague_club as cl
    ON cl.id = jt.club_id    
    INNER JOIN #__joomleague_project_team as pt 
    ON pt.team_id = jt.id
    WHERE pt.project_id = ' . (int) $project . ' GROUP BY jt.name ORDER BY jt.name';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
        
    }
    else
    {
    $query='SELECT id,name,club_id,short_name,middle_name,info 
    FROM #__joomleague_team 
    ORDER BY name
    ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
    }

		
	}
	
	
	public function getTeamListSelect()
	{
	global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
  
  $option='com_joomleague';
	$project = $mainframe->getUserState( $option . 'project', 0 );
	
	$teaminfo = $mainframe->getUserState( $option . 'teamart', '' );
	
	$lmoimportuseteams=$mainframe->getUserState($option.'lmoimportuseteams'); 
	
	
// jetzt brauchen wir noch das land der liga !
$query = "SELECT l.country
from #__joomleague_league as l
inner join #__joomleague_project as p
on p.league_id = l.id
where p.id = '$project'
";

$this->_db->setQuery( $query );
$country = $this->_db->loadResult();


		//$query="SELECT id AS value,name,info,club_id FROM #__joomleague_team ORDER BY name";
		if ( $lmoimportuseteams )
	  {
//     $query='SELECT jt.id as value,jt.name,jt.club_id,jt.info 
//     FROM #__joomleague_team as jt
//     INNER JOIN #__joomleague_club as cl
//     ON cl.id = jt.club_id    
//     INNER JOIN #__joomleague_project_team as pt 
//     ON pt.team_id = jt.id
//     WHERE cl.country = "' . $country . '"
//     AND pt.project_id = ' . (int) $project . ' GROUP BY jt.name ORDER BY jt.name';
// 		$this->_db->setQuery($query);
    
    $query='SELECT jt.id as value,jt.name,jt.club_id,jt.info 
    FROM #__joomleague_team as jt
    INNER JOIN #__joomleague_club as cl
    ON cl.id = jt.club_id    
    INNER JOIN #__joomleague_project_team as pt 
    ON pt.team_id = jt.id
    WHERE pt.project_id = ' . (int) $project . ' GROUP BY jt.name ORDER BY jt.name';
		$this->_db->setQuery($query);
    		
    }
    else
    {
    
    if ( $teaminfo )
    {
    $query="SELECT id AS value,name,info,club_id 
    FROM #__joomleague_team
    where info like '".$teaminfo."' 
    ORDER BY name
    ";
    }
    else
    {
    $query="SELECT id AS value,name,info,club_id 
    FROM #__joomleague_team
    ORDER BY name
    ";
    }
   
		$this->_db->setQuery($query);
		//return $this->_db->loadObjectList();
    }
		
		
		
		if ($results=$this->_db->loadObjectList())
		{
			foreach ($results AS $team)
			{
				$team->text=$team->name.' - ('.$team->info.')';
			}
			return $results;
		}
		return false;
	}
	
	public function getClubList()
	{
	global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
  
  $option='com_joomleague';
	$project = $mainframe->getUserState( $option . 'project', 0 );
	
	$lmoimportuseteams=$mainframe->getUserState($option.'lmoimportuseteams'); 

// jetzt brauchen wir noch das land der liga !
$query = "SELECT l.country
from #__joomleague_league as l
inner join #__joomleague_project as p
on p.league_id = l.id
where p.id = '$project'
";

$this->_db->setQuery( $query );
$country = $this->_db->loadResult();

	
	  if ( $lmoimportuseteams )
	  {
// 		$query='SELECT cl.id,cl.name,cl.standard_playground,cl.country 
//     FROM #__joomleague_club as cl
//     INNER JOIN #__joomleague_team as jt
//     ON jt.club_id = cl.id
//     INNER JOIN #__joomleague_project_team as pt 
//     ON pt.team_id = jt.id
//     WHERE cl.country = "' . $country . '"
//     AND pt.project_id = ' . (int) $project . ' 
//     ORDER BY cl.name ASC
//     ';
    
    $query='SELECT cl.id,cl.name,cl.standard_playground,cl.country 
    FROM #__joomleague_club as cl
    INNER JOIN #__joomleague_team as jt
    ON jt.club_id = cl.id
    INNER JOIN #__joomleague_project_team as pt 
    ON pt.team_id = jt.id
    WHERE pt.project_id = ' . (int) $project . ' 
    ORDER BY cl.name ASC
    ';
    
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();    
    }
    else
    {
		$query='SELECT cl.id,cl.name,cl.standard_playground,cl.country 
    FROM #__joomleague_club as cl 
    ORDER BY cl.name ASC
    ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
		}
		
		
	}	


public function getPositionList()
	{
		$query='SELECT * FROM #__joomleague_position WHERE parent_id > 0 ORDER BY name';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getPositionListSelect()
	{
		$query='SELECT id AS value,name AS text FROM #__joomleague_position WHERE parent_id > 0 ORDER BY name';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		foreach ($result as $position){$position->text=JText::_($position->text);}
		return $result;
	}

	public function getParentPositionList()
	{
		$query='SELECT * FROM #__joomleague_position WHERE parent_id=0 ORDER BY name';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getParentPositionListSelect()
	{
		$query='SELECT id AS value,name AS text FROM #__joomleague_position WHERE parent_id=0 ORDER BY name';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		foreach ($result as $position){$position->text=JText::_($position->text);}
		return $result;
	}



public function getPersonList()
	{
//		$query="	SELECT *,
//					LOWER(lastname) AS low_lastname,
//					LOWER(firstname) AS low_firstname,
//					LOWER(nickname) AS low_nickname
//					FROM #__joomleague_person WHERE firstname<>'!Unknown' AND lastname<>'!Player' AND nickname<>'!Ghost' ORDER BY lastname";
		$query="	SELECT *,
					LOWER(lastname) AS lastname,
					LOWER(firstname) AS firstname,
					LOWER(nickname) AS nickname
					FROM #__joomleague_person WHERE firstname<>'!Unknown' AND lastname<>'!Player' AND nickname<>'!Ghost' ORDER BY lastname";                    
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}


public function getPersonListSelect()
	{
		$query ="	SELECT id AS value,firstname,lastname,nickname,birthday
						FROM #__joomleague_person
						WHERE firstname<>'!Unknown' AND lastname<>'!Player' AND nickname<>'!Ghost'
						ORDER BY lastname,firstname";
		$this->_db->setQuery($query);
		if ($results=$this->_db->loadObjectList())
		{
			foreach ($results AS $person)
			{
				$textString=$person->lastname.','.$person->firstname;
				if (!empty($person->nickname))
				{
					$textString .= " '".$person->nickname."'";
				}
				if ($person->birthday!='0000-00-00')
				{
					$textString .= " (".$person->birthday.")";
				}
				$person->text=$textString;
			}
			return $results;
		}
		return false;
	}
  	
	
	public function getClubListSelect()
	{
	
global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
  
  $option='com_joomleague';
	$project = $mainframe->getUserState( $option . 'project', 0 );
	
	$lmoimportuseteams=$mainframe->getUserState($option.'lmoimportuseteams'); 

// jetzt brauchen wir noch das land der liga !
$query = "SELECT l.country
from #__joomleague_league as l
inner join #__joomleague_project as p
on p.league_id = l.id
where p.id = '$project'
";

$this->_db->setQuery( $query );
$country = $this->_db->loadResult();

	
//$query='SELECT id AS value,name AS text,country,standard_playground FROM #__joomleague_club ORDER BY name';
//$this->_db->setQuery($query);
	
  
    if ( $lmoimportuseteams )
	  {
// 		$query='SELECT cl.id AS value,cl.name AS text,cl.standard_playground,cl.country 
//     FROM #__joomleague_club as cl
//     INNER JOIN #__joomleague_team as jt
//     ON jt.club_id = cl.id
//     INNER JOIN #__joomleague_project_team as pt 
//     ON pt.team_id = jt.id
//     WHERE cl.country = "' . $country . '"
//     AND pt.project_id = ' . (int) $project . ' 
//     ORDER BY cl.name ASC
//     ';

		$query='SELECT cl.id AS value,cl.name AS text,cl.standard_playground,cl.country 
    FROM #__joomleague_club as cl
    INNER JOIN #__joomleague_team as jt
    ON jt.club_id = cl.id
    INNER JOIN #__joomleague_project_team as pt 
    ON pt.team_id = jt.id
    WHERE pt.project_id = ' . (int) $project . ' 
    ORDER BY cl.name ASC
    ';
    
		$this->_db->setQuery($query);
		//return $this->_db->loadObjectList();    
    }
    else
    {
		$query='SELECT cl.id AS value,cl.name AS text,cl.standard_playground,cl.country 
    FROM #__joomleague_club as cl 
    ORDER BY cl.name ASC
    ';
		$this->_db->setQuery($query);
		//return $this->_db->loadObjectList();
		}  
  
  
  	if ($results=$this->_db->loadObjectList())
		{
			return $results;
		}
		return false;
	}




	public function getLeagueList()
	{
		$query='SELECT id,name 
    FROM #__joomleague_league 
    ORDER BY name ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
  
  public function getSeasonList()
	{
		$query='SELECT id,name 
    FROM #__joomleague_season 
    ORDER BY name';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getProjectList()
	{
		$query='SELECT id,name 
    FROM #__joomleague_project 
    ORDER BY name';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getUserList($is_admin=false)
	{
		$query='SELECT id,username 
    FROM #__users';
		if ($is_admin==true)
		{
			$query .= " WHERE usertype='Super Administrator' OR usertype='Administrator'";
		}
		$query .= ' ORDER BY username ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getTemplateList()
	{
		$query='SELECT id,name 
    FROM #__joomleague_project 
    WHERE master_template=0 
    ORDER BY name ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

  public function getSportsTypeList()
	{
		$query='SELECT id,name,name AS text 
    FROM #__joomleague_sports_type 
    ORDER BY name ASC';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		foreach ($result as $sportstype){$sportstype->name=JText::_($sportstype->name);}
		return $result;
	}	

  public function getPlaygroundList()
	{
		$query='SELECT id,name,short_name,club_id FROM #__joomleague_playground ORDER BY name';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getPlaygroundListSelect()
	{
		$query='SELECT id AS value,name AS text,short_name,club_id FROM #__joomleague_playground ORDER BY name';
		$this->_db->setQuery($query);
		if ($results=$this->_db->loadObjectList())
		{
			return $results;
		}
		return false;
	}
  	
public function importData($post)
	{
	global $mainframe, $option;
	$mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
  
  $option='com_joomleague';
  $project =  $mainframe->getUserState( $option . 'project', 0 );
  $lmoimportuseteams=$mainframe->getUserState($option.'lmoimportuseteams');
  
  $whichfile=$mainframe->getUserState($option.'whichfile');
  $delimiter=$mainframe->getUserState($option.'delimiter');
  
  //$lmoimportxml=$mainframe->getUserState($option.'lmoimportxml', 'lmoimportxml' );

//$lmoimportxml = JRequest::getVar( 'lmoimportxml', array(), 'post', 'array' );
  	
  	if ( $whichfile == 'matchfile' )
{
//   	echo 'importData post<br>';
//     echo '<pre>';
//     print_r($post);
//     echo '</pre>';
}   
else
{
//   	echo 'importData post<br>';
//     echo '<pre>';
//     print_r($post);
//     echo '</pre>';

}    
    /*
    echo 'importData lmoimportxml<br>';
    echo '<pre>';
    print_r($lmoimportxml);
    echo '</pre>';
    */
    
    
    $this->_datas=$this->getData();
    
    
//     echo 'importData this->_datas<br>';
//     echo '<pre>';
//     print_r($this->_datas);
//     echo '</pre>';
    
    
    $this->_newteams=array();
		$this->_newteamsshort=array();
		$this->_dbteamsid=array();
		$this->_newteamsmiddle=array();
		$this->_newteamsinfo=array();

		$this->_newclubs=array();
		$this->_newclubsid=array();
		$this->_newclubscountry=array();
		$this->_dbclubsid=array();
		$this->_createclubsid=array();

		$this->_newplaygroundid=array();
		$this->_newplaygroundname=array();
		$this->_newplaygroundshort=array();
		$this->_dbplaygroundsid=array();

		$this->_newpersonsid=array();
		$this->_newperson_lastname=array();
		$this->_newperson_firstname=array();
		$this->_newperson_nickname=array();
		$this->_newperson_birthday=array();
		$this->_dbpersonsid=array();

		$this->_neweventsname=array();
		$this->_neweventsid=array();
		$this->_dbeventsid=array();

		$this->_newpositionsname=array();
		$this->_newpositionsid=array();
		$this->_dbpositionsid=array();

		$this->_newparentpositionsname=array();
		$this->_newparentpositionsid=array();
		$this->_dbparentpositionsid=array();

		$this->_newstatisticsname=array();
		$this->_newstatisticsid=array();
		$this->_dbstatisticsid=array();

		//tracking of old -> new ids
		$this->_convertProjectTeamID=array();
		$this->_convertProjectRefereeID=array();
		$this->_convertTeamPlayerID=array();
		$this->_convertTeamStaffID=array();
		$this->_convertProjectPositionID=array();
		$this->_convertProjectTeamForMatchID=array();
		$this->_convertClubID=array();
		$this->_convertPersonID=array();
		$this->_convertTeamID=array();
		$this->_convertRoundID=array();
		$this->_convertDivisionID=array();
		$this->_convertCountryID=array();
		$this->_convertPlaygroundID=array();
		$this->_convertEventID=array();
		$this->_convertPositionID=array();
		$this->_convertParentPositionID=array();
		$this->_convertMatchID=array();
		$this->_convertStatisticID=array();    
    
    
    
    if (is_array($post) && count($post) > 0)
		{
			foreach($post as $key => $element)
			{
			  if (substr($key,0,14)=='personLastname')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_lastname[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,15)=='personFirstname')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_firstname[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,14)=='personNickname')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_nickname[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,14)=='personBirthday')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_birthday[$temppersons[1]]=$element;
				}
				
				elseif (substr($key,0,12)=='personKNVBNR')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_knvbnr[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,10)=='personINFO')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_info[$temppersons[1]]=$element;
				}
				
				elseif (substr($key,0,8)=='personID')
				{
					$temppersons=explode("_",$key);
					$this->_newpersonsid[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,10)=='dbPersonID')
				{
					$temppersons=explode("_",$key);
					$this->_dbpersonsid[$temppersons[1]]=$element;
				}
			}
		}
    
if ( $whichfile == 'matchfile' || $whichfile == 'icsfile' )
{

if (is_array($post) && count($post) > 0)
		{
			
      foreach($post as $key => $element)
			{
			  
        if (substr($key,0,14)=='playgroundName')
				{
					$tempplayground=explode("_",$key);
					$this->_newplaygroundname[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,19)=='playgroundShortname')
				{
					$tempplayground=explode("_",$key);
					$this->_newplaygroundshort[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,12)=='playgroundID')
				{
					$tempplayground=explode("_",$key);
					$this->_newplaygroundid[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,14)=='dbPlaygroundID')
				{
					$tempplayground=explode("_",$key);
					$this->_dbplaygroundsid[$tempplayground[1]]=$element;
				}
			
            elseif (substr($key,0,12)=='positionName')
				{
					$tempposition=explode("_",$key);
					$this->_newpositionsname[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,10)=='positionID')
				{
					$tempposition=explode("_",$key);
					$this->_newpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,12)=='dbPositionID')
				{
					$tempposition=explode("_",$key);
					$this->_dbpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,18)=='parentPositionName')
				{
					$tempposition=explode("_",$key);
					$this->_newparentpositionsname[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,16) =="parentPositionID")
				{
					$tempposition=explode("_",$key);
					$this->_newparentpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,18)=='dbParentPositionID')
				{
					$tempposition=explode("_",$key);
					$this->_dbparentpositionsid[$tempposition[1]]=$element;
				}
                
                elseif (substr($key,0,8)=='teamName')
				{
					$tempteams=explode("_",$key);
					$this->_newteams[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,13)=='teamShortname')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsshort[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,8)=='teamInfo')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsinfo[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,14)=='teamMiddleName')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsmiddle[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,6)=='teamID')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsid[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,8)=='dbTeamID')
				{
					$tempteams=explode("_",$key);
					$this->_dbteamsid[$tempteams[1]]=$element;
				}
                elseif (substr($key,0,8)=='clubName')
				{
					$tempclubs=explode("_",$key);
					$this->_newclubs[$tempclubs[1]]=$element;
				}
				elseif (substr($key,0,11)=='clubCountry')
				{
					$tempclubs=explode("_",$key);
					$this->_newclubscountry[$tempclubs[1]]=$element;
				}
				/**/
				elseif (substr($key,0,6)=='clubID')
				{
					$tempclubs=explode("_",$key);
					$this->_newclubsid[$tempclubs[1]]=$element;
				}
				/**/
				elseif (substr($key,0,10)=='createClub')
				{
					$tempclubs=explode("_",$key);
					$this->_createclubsid[$tempclubs[1]]=$element;
				}
				elseif (substr($key,0,8)=='dbClubID')
				{
					$tempclubs=explode("_",$key);
					$this->_dbclubsid[$tempclubs[1]]=$element;
				}
      
      
      	
			}
			
		}

// echo 'importData this->_newteamsid<br>';
// echo '<pre>';
// print_r($this->_newteamsid);
// echo '</pre>';
// echo 'importData this->_dbteamsid<br>';
// echo '<pre>';
// print_r($this->_dbteamsid);
// echo '</pre>';

        
}
    
    $this->_success_text='';

		//set $this->_importType
		$this->_importType=$post['importType'];
		
		if ( $whichfile == 'matchfile' || $whichfile == 'icsfile' )
{

    if (isset($post['admin']))
			{
				$this->_joomleague_admin=(int)$post['admin'];
			}
			else
			{
				$this->_joomleague_admin=62;
			}

      //check project name
			if ($post['importProject'])
			{
				if (isset($post['name'])) // Project Name
				{
					$this->_name=substr($post['name'],0,100);
				}
				else
				{
					JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_MISSING','projectname'));
					echo "<script> alert('".JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_MISSING','projectname')."'); window.history.go(-1); </script>\n";
				}

				if (empty($this->_datas['project']))
				{
					JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR','Project object is missing inside import file!!!'));
					echo "<script> alert('".JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR','Project object is missing inside import file!!!')."'); window.history.go(-1); </script>\n";
					return false;
				}

				if ($this->_checkProject()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR','Projectname already exists'));
					echo "<script> alert('".JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR','Projectname already exists')."'); window.history.go(-1); </script>\n";
					return false;
				}
			}

      //check sportstype
			if ($post['importProject'] || $post['importType']=='events' || $post['importType']=='positions')
			{
				if ((isset($post['sportstype'])) && ($post['sportstype'] > 0))
				{
					$this->_sportstype_id=(int)$post['sportstype'];
				}
				elseif (isset($post['sportstypeNew']))
					{
						$this->_sportstype_new=substr($post['sportstypeNew'],0,25);
					}
					else
					{
						JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_MISSING','sportstype'));
						echo "<script> alert('".JText::sprintf('JL_ADMIN_XML_ERROR_MISSING','sportstype')."'); window.history.go(-1); </script>\n";
						return false;
					}
			}
			
 			//check league/season/admin/editor/publish/template
			if ($post['importProject'])
			{
				if (isset($post['league']))
				{
					$this->_league_id=(int)$post['league'];
				}
				elseif (isset($post['leagueNew']))
					{
						$this->_league_new=substr($post['leagueNew'],0,75);
					}
					else
					{
						JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_MISSING','league'));
						echo "<script> alert('".JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_MISSING','league')."'); window.history.go(-1); </script>\n";
						return false;
					}

				if (isset($post['season']))
				{
					$this->_season_id=(int)$post['season'];
				}
				elseif (isset($post['seasonNew']))
					{
						$this->_season_new=substr($post['seasonNew'],0,75);
					}
					else
					{
						JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_MISSING','season'));
						echo "<script> alert('".JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_MISSING','season')."'); window.history.go(-1); </script>\n";
						return false;
					}

				if (isset($post['editor']))
				{
					$this->_joomleague_editor=(int)$post['editor'];
				}
				else
				{
					$this->_joomleague_editor=62;
				}

				if (isset($post['publish']))
				{
					$this->_publish=(int)$post['publish'];
				}
				else
				{
					$this->_publish=0;
				}

				if (isset($post['copyTemplate'])) // if new template set this value is 0
				{
					$this->_template_id=(int)$post['copyTemplate'];
				}
				else
				{
					$this->_template_id=0;
				}
			}
			
			if ($post['importProject'] || $post['importType']=='events' || $post['importType']=='positions')
			{
				// import sportstype
				if ($this->_importSportsType()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_DURING','sports-type'));
					return $this->_success_text;
				}
			}
			
			if ($post['importProject'])
			{
				// import league
				if ($this->_importLeague()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_DURING','league'));
					return $this->_success_text;
				}

				// import season
				if ($this->_importSeason()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_EXT_DFBNET_IMPORT_ERROR_DURING','season'));
					return $this->_success_text;
				}
			}
			
			
// import persons
		if ($this->_importPersons()===false)
		{
			JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','person'));
			return $this->_success_text;
		}
        
// import parent positions
			if ($this->_importParentPositions()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_DURING','parent-position'));
				return $this->_success_text;
			}
// import positions
			if ($this->_importPositions()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_DURING','position'));
				return $this->_success_text;
			}                    	
		
// import playgrounds
			if ($this->_importPlayground()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','playground'));
				return $this->_success_text;
			}
			
      // import clubs
			if ($this->_importClubs()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_DURING','club'));
				return $this->_success_text;
			}

			if ($this->_importType!='playgrounds')	// don't convert club_id if only playgrounds are imported
			{
				// convert playground Club-IDs
				if ($this->_convertNewPlaygroundIDs()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','conversion of playground club-id'));
					return $this->_success_text;
				}
			}

      if ($post['importProject'])
			{
				// import project
				if ($this->_importProject()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','project'));
					return $this->_success_text;
				}

				// import template
				if ($this->_importTemplate()===false)
				{
					JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','template'));
					return $this->_success_text;
				}
			}
			
			
			// import teams
			if ($this->_importTeams()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','team'));
				return $this->_success_text;
			}
      			
// import rounds
			if ($this->_importRounds()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','round'));
				return $this->_success_text;
			}
			
// import projectteam
 			if ($this->_importProjectTeam()===false)
 			{
 				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','projectteam'));
 				return $this->_success_text;
 			}

// import project positions
			if ($this->_importProjectPositions()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_DURING','projectpositions'));
				return $this->_success_text;
			}
            
// import project referees
			if ($this->_importProjectReferees()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','projectreferees'));
				return $this->_success_text;
			}
			
			// imported matches
			if ($this->_importMatches()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_DURING','match'));
				return $this->_success_text;
			}
// import MatchReferee
			if ($this->_importMatchReferee()===false)
			{
				JError::raiseWarning(500,JText::sprintf('JL_ADMIN_XML_ERROR_DURING','matchreferee'));
				return $this->_success_text;
			}
            			
      			
}
else
{
    // import persons
		if ($this->_importPersons()===false)
		{
			JError::raiseWarning(500,JText::sprintf('JL_ADMIN_DFBNET_IMPORT_ERROR_DURING','person'));
			return $this->_success_text;
		}	
}	
		
		/*	
    echo 'importData this->_success_text<br>';
    echo '<pre>';
    print_r($this->_success_text);
    echo '</pre>';
    */
  
  if ( $this->_project_id )
  {
    $this->_SetRoundDates($this->_project_id);
  }
      
    return $this->_success_text;
    
    }
  
  
	/**
	 * _getDataFromObject
	 *
	 * Get data from object
	 *
	 * @param object $obj object where we find the key
	 * @param string $key key what we find in the object
	 *
	 * @access private
	 * @since  1.5.0a
	 *
	 * @return void
	 */
	private function _getDataFromObject(&$obj,$key)
	{
		if (is_object($obj))
		{
			$t_array=get_object_vars($obj);

			if (array_key_exists($key,$t_array))
			{
				return $t_array[$key];
			}
			return false;
		}
		return false;
	}  
  
  	private function _importTeams()
	{
		$my_text='';
		if (!isset($this->_datas['team']) || count($this->_datas['team'])==0){return true;}
		if ((!isset($this->_newteams) || count($this->_newteams)==0) &&
			(!isset($this->_dbteamsid) || count($this->_dbteamsid)==0)){return true;}

		if (!empty($this->_dbteamsid))
		{
			foreach ($this->_dbteamsid AS $key => $id)
			{
				if (empty($this->_newteams[$key]))
				{
					$oldID=$this->_getDataFromObject($this->_datas['team'][$key],'id');
					$this->_convertTeamID[$oldID]=$id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf(	'Using existing team data: %1$s - [%2$s] - [%3$s] - [%4$s]',
												'</span><strong>'.$this->_getObjectName('team',$id).'</strong>',
												''.$this->_getObjectName('team',$id,'short_name').'',
												''.$this->_getObjectName('team',$id,'middle_name').'',
												''.$this->_getObjectName('team',$id,'info').''
												);
					$my_text .= '<br />';
				}
			}
		}
//To Be fixed: Falls Verein neu angelegt wird, muss auch das Team neu angelegt werden.

// echo '1_dbclubsid<pre>'.print_r($this->_dbclubsid,true).'</pre>';
// echo '2_newclubs<pre>'.print_r($this->_newclubs,true).'</pre>';
// echo '3_newclubsid<pre>'.print_r($this->_newclubsid,true).'</pre>';
// echo '4_dbteamsid<pre>'.print_r($this->_dbteamsid,true).'</pre>';
// echo '5_newteams<pre>'.print_r($this->_newteams,true).'</pre>';
// echo '6_convertClubID<pre>'.print_r($this->_convertClubID,true).'</pre>';

		if (!empty($this->_newteams))
		{
			foreach ($this->_newteams AS $key => $value)
			{
				$p_team =& $this->getTable('team');
				$import_team=$this->_datas['team'][$key];
				$oldID=$this->_getDataFromObject($import_team,'id');
				$alias=$this->_getDataFromObject($import_team,'alias');
				if ($this->_importType!='teams')	// force club_id to be set to default if only teams are imported
				{
					$oldClubID=$this->_getDataFromObject($import_team,'club_id');
					if ((!empty($import_team->club_id)) && (isset($this->_convertClubID[$oldClubID])))
					{
						$p_team->set('club_id',$this->_convertClubID[$oldClubID]);
					}
					else
					{
						$p_team->set('club_id',(-1));
					}
				}
				$p_team->set('name',$this->_newteams[$key]);
				$p_team->set('short_name',$this->_newteamsshort[$key]);
				$p_team->set('middle_name',$this->_newteamsmiddle[$key]);
				$p_team->set('website',$this->_getDataFromObject($import_team,'website'));
				$p_team->set('notes',$this->_getDataFromObject($import_team,'notes'));
				$p_team->set('picture',$this->_getDataFromObject($import_team,'picture'));
				$p_team->set('info',$this->_newteamsinfo[$key]);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_team->set('alias',$alias);
				}
				else
				{
					$p_team->set('alias',JFilterOutput::stringURLSafe($this->_getDataFromObject($p_team,'name')));
				}
				if (($this->import_version=='NEW') && ($import_team->extended!=''))
				{
					$p_team->set('extended',$this->_getDataFromObject($import_team,'extended'));
				}
				$query="SELECT	id,
								name,
								short_name,
								middle_name,
								info
						FROM #__joomleague_team
						WHERE	name='".addslashes(stripslashes($p_team->name))."'  AND
								info='".addslashes(stripslashes($p_team->info))."' ";
				$this->_db->setQuery($query); $this->_db->query();
				if ($object=$this->_db->loadObject())
				{
					$this->_convertTeamID[$oldID]=$object->id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf('Using existing team data: %1$s',"</span><strong>$object->name</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_team->store()===false)
					{
						$my_text .= '<span style="color:red"><strong>';
						$my_text .= JText::_('Error in function _importTeams').'</strong></span><br />';
						$my_text .= JText::sprintf('Teamname: %1$s',$p_team->name).'<br />';
						$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_team,true).'</pre>';
						$this->_success_text['Importing general team data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$this->_db->insertid();
						$this->_convertTeamID[$oldID]=$insertID;
						$my_text .= '<span style="color:green">';
						$my_text .= JText::sprintf(	'JL_ADMIN_DFBNET_IMPORT_CREATED_NEW_TEAM_DATA: %1$s - %2$s - %3$s - %4$s - %5$s',
													"</span><strong>$p_team->name</strong>",
													$p_team->short_name,
													$p_team->middle_name,
													$p_team->info,
													$p_team->club_id
													);
						$my_text .= '<br />';
					}
					
 					$my_text .= '<span style="color:green">';
 		      $my_text .= JText::sprintf('old / new Teamid: %1$s , %2$s',"</span><strong>$oldId</strong>","</span><strong>$insertID</strong>");
 		      $my_text .= '<br />';
		      
				}
			}
		}
		
		
		
//     echo '_importTeams _convertTeamID<pre>';
//     print_r($this->_convertTeamID);
//     echo '</pre>';
    	
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_TEAM_DATA:']=$my_text;
		return true;
	}
	
	private function _getClubName($club_id)
	{
		$query='SELECT name FROM #__joomleague_club WHERE id='.(int)$club_id;
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($this->_db->getAffectedRows())
		{
			$result=$this->_db->loadResult();
			return $result;
		}
		return '#Error in _getClubName#';
	}
	
	
	private function _convertNewPlaygroundIDs()
	{
		$my_text='';
		$converted=false;
		if (isset($this->_convertPlaygroundID) && !empty($this->_convertPlaygroundID))
		{
			foreach ($this->_convertPlaygroundID AS $key => $new_pg_id)
			{
				$p_playground=$this->_getPlaygroundRecord($new_pg_id);
				foreach ($this->_convertClubID AS $key => $new_club_id)
				{
					if (isset($p_playground->club_id) && ($p_playground->club_id ==$key))
					{
						if ($this->_updatePlaygroundRecord($new_club_id,$new_pg_id))
						{
							$converted=true;
							$my_text .= '<span style="color:green">';
							$my_text .= JText::sprintf(	'Converted club-info %1$s in imported playground %2$s',
														'</span><strong>'.$this->_getClubName($new_club_id).'</strong><span style="color:green">',
														"</span><strong>$p_playground->name</strong>");
							$my_text .= '<br />';
						}
						break;
					}
				}
			}
			if (!$converted){$my_text .= '<span style="color:green">'.JText::_('Nothing needed to be converted').'<br />';}
			$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_CONVERTING_NEW_PLAYGROUND:']=$my_text;
		}
		return true;
	}
	
	private function _updatePlaygroundRecord($club_id,$playground_id)
	{
		$query='UPDATE #__joomleague_playground SET club_id='.(int)$club_id.' WHERE id='.(int)$playground_id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
		private function _getPlaygroundRecord($id)
	{
		$query='SELECT * FROM #__joomleague_playground WHERE id='.(int)$id;
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($object=$this->_db->loadObject()){return $object;}
		return null;
	}
	
	private function _importProject()
	{
		$my_text='';
		$p_project =& $this->getTable('project');
		$p_project->set('name',trim($this->_name));
		$p_project->set('alias',JFilterOutput::stringURLSafe(trim($this->_name)));
		$p_project->set('league_id',$this->_league_id);
		$p_project->set('season_id',$this->_season_id);
		$p_project->set('admin',$this->_joomleague_admin);
		$p_project->set('editor',$this->_joomleague_editor);
		$p_project->set('master_template',$this->_template_id);
		$p_project->set('sub_template_id',0);
		$p_project->set('extension',$this->_getDataFromObject($this->_datas['project'],'extension'));
		$p_project->set('serveroffset',$this->_getDataFromObject($this->_datas['project'],'serveroffset'));
		$p_project->set('project_type',$this->_getDataFromObject($this->_datas['project'],'project_type'));
		$p_project->set('teams_as_referees',$this->_getDataFromObject($this->_datas['project'],'teams_as_referees'));
		$p_project->set('sports_type_id',$this->_sportstype_id);
		$p_project->set('current_round',$this->_getDataFromObject($this->_datas['project'],'current_round'));
		$p_project->set('current_round_auto',$this->_getDataFromObject($this->_datas['project'],'current_round_auto'));
		$p_project->set('auto_time',$this->_getDataFromObject($this->_datas['project'],'auto_time'));
		$p_project->set('start_date',$this->_getDataFromObject($this->_datas['project'],'start_date'));
		$p_project->set('start_time',$this->_getDataFromObject($this->_datas['project'],'start_time'));
		$p_project->set('fav_team_color',$this->_getDataFromObject($this->_datas['project'],'fav_team_color'));
		$p_project->set('fav_team_text_color',$this->_getDataFromObject($this->_datas['project'],'fav_team_text_color'));
		$p_project->set('use_legs',$this->_getDataFromObject($this->_datas['project'],'use_legs'));
		$p_project->set('game_regular_time',$this->_getDataFromObject($this->_datas['project'],'game_regular_time'));
		$p_project->set('game_parts',$this->_getDataFromObject($this->_datas['project'],'game_parts'));
		$p_project->set('halftime',$this->_getDataFromObject($this->_datas['project'],'halftime'));
		$p_project->set('allow_add_time',$this->_getDataFromObject($this->_datas['project'],'allow_add_time'));
		$p_project->set('add_time',$this->_getDataFromObject($this->_datas['project'],'add_time'));
		$p_project->set('points_after_regular_time',$this->_getDataFromObject($this->_datas['project'],'points_after_regular_time'));
		$p_project->set('points_after_add_time',$this->_getDataFromObject($this->_datas['project'],'points_after_add_time'));
		$p_project->set('points_after_penalty',$this->_getDataFromObject($this->_datas['project'],'points_after_penalty'));
		$p_project->set('template',$this->_getDataFromObject($this->_datas['project'],'template'));
		$p_project->set('enable_sb',$this->_getDataFromObject($this->_datas['project'],'enable_sb'));
		$p_project->set('sb_catid',$this->_getDataFromObject($this->_datas['project'],'sb_catid'));
		if ($this->_publish){$p_project->set('published',1);}
		if ($p_project->store()===false)
		{
			$my_text .= '<span style="color:red"><strong>';
			$my_text .= JText::_('Error in function _importProject').'</strong></span><br />';
			$my_text .= JText::sprintf('Projectname: %1$s',$p_project->name).'<br />';
			$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
			$my_text .= '<pre>'.print_r($p_project,true).'</pre>';
			$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_PROJECT_DATA:']=$my_text;
			return false;
		}
		else
		{
			$insertID=$this->_db->insertid();
			$this->_project_id=$insertID;
			$my_text .= '<span style="color:green">';
			$my_text .= JText::sprintf('Created new project data: %1$s',"</span><strong>$this->_name</strong>");
			$my_text .= '<br />';
			$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_PROJECT_DATA:']=$my_text;
			return true;
		}
	}
	
	private function _importTemplate()
	{
		$my_text='';
		if ($this->_template_id > 0) // Uses a master template
		{
			$query_template='SELECT id,master_template FROM #__joomleague_project WHERE id='.$this->_template_id;
			$this->_db->setQuery($query_template);
			$template_row=$this->_db->loadAssoc();
			if ($template_row['master_template']==0)
			{
				$this->_master_template=$template_row['id'];
			}
			else
			{
				$this->_master_template=$template_row['master_template'];
			}
			
//       $query="SELECT id,template FROM #__joomleague_template_config WHERE project_id=".$this->_master_template;
// 			$this->_db->setQuery($query);
// 			$rows=$this->_db->loadObjectList();
// 			foreach ($rows AS $row)
// 			{
// 				$p_template =& $this->getTable('template');
// 				$p_template->load($row->id);
// 				$p_template->set('project_id',$this->_project_id);
// 				if ($p_template->store()===false)
// 				{
// 					$my_text .= 'error on master template import: ';
// 					$my_text .= "<br />Error: _importTemplate<br />#$my_text#<br />#<pre>".print_r($p_template,true).'</pre>#';
// 					$this->_success_text['JL_ADMIN_DFBNET_IMPORT_TEMPLATE_DATA:']=$my_text;
// 					return false;
// 				}
// 				else
// 				{
// 					$my_text .= $p_template->template;
// 					$my_text .= ' <font color="green">'.JText::_('...created new data').'</font><br />';
// 					$my_text .= '<br />';
// 				}
// 			}
			
		}
		else
		{
			$this->_master_template=0;
			$predictionTemplatePrefix='prediction';
			if ((isset($this->_datas['template'])) && (is_array($this->_datas['template'])))
			{
				foreach ($this->_datas['template'] as $value)
				{
					$p_template =& $this->getTable('template');
					$template=$this->_getDataFromObject($value,'template');
					$p_template->set('template',$template);
					//actually func is unused in 1.5.0
					//$p_template->set('func',$this->_getDataFromObject($value,'func'));
					$p_template->set('title',$this->_getDataFromObject($value,'title'));
					$p_template->set('project_id',$this->_project_id);
					$p_template->set('params',$this->_getDataFromObject($value,'params'));
					if	((strtolower(substr($template,0,strlen($predictionTemplatePrefix)))!=$predictionTemplatePrefix) &&
						($template!='do_tipsl') &&
						($template!='frontpage') &&
						($template!='table') &&
						($template!='tipranking') &&
						($template!='tipresults') &&
						($template!='user'))
					{
						if ($p_template->store()===false)
						{
							$my_text .= 'error on own template import: ';
							$my_text .= "<br />Error: _importTemplate<br />#$my_text#<br />#<pre>".print_r($p_template,true).'</pre>#';
							$this->_success_text['Importing template data:']=$my_text;
							return false;
						}
						else
						{
							$dTitle=(!empty($p_template->title)) ? JText::_($p_template->title) : $p_template->template;
							$my_text .= '<span style="color:green">';
							$my_text .= JText::sprintf('Created new template data: %1$s',"</span><strong>$dTitle</strong>");
							$my_text .= '<br />';
						}
					}
				}
			}
		}
		$query="UPDATE #__joomleague_project SET master_template=$this->_master_template WHERE id=$this->_project_id";
		$this->_db->setQuery($query);
		$this->_db->query();
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_TEMPLATE_DATA:']=$my_text;
		if ($this->_master_template==0)
		{
			// check and create missing templates if needed
			$this->_checklist();
			$my_text='<span style="color:green">';
			$my_text .= JText::_('Checked and created missing template data if needed');
			$my_text .= '</span><br />';
			$this->_success_text['JL_ADMIN_DFBNET_IMPORT_TEMPLATE_DATA:'] .= $my_text;
		}
		return true;
	}
	
	/**
	 * check that all templates in default location have a corresponding record,except if project has a master template
	 *
	 */
	private function _checklist()
	{
		$project_id=$this->_project_id;
		$defaultpath=JPATH_COMPONENT_SITE.DS.'settings';
		$extensiontpath=JPATH_COMPONENT_SITE.DS.'extensions'.DS;
		$predictionTemplatePrefix='prediction';

		if (!$project_id){return;}

		// get info from project
		$query='SELECT master_template,extension FROM #__joomleague_project WHERE id='.(int)$project_id;

		$this->_db->setQuery($query);
		$params=$this->_db->loadObject();

		// if it's not a master template,do not create records.
		if ($params->master_template){return true;}

		// otherwise,compare the records with the files
		// get records
		$query='SELECT template FROM #__joomleague_template_config WHERE project_id='.(int)$project_id;

		$this->_db->setQuery($query);
		$records=$this->_db->loadResultArray();
		if (empty($records)){$records=array();}

		// first check extension template folder if template is not default
		if ((isset($params->extension)) && ($params->extension!=''))
		{
			if (is_dir($extensiontpath.$params->extension.DS.'settings'))
			{
				$xmldirs[]=$extensiontpath.$params->extension.DS.'settings';
			}
		}

		// add default folder
		$xmldirs[]=$defaultpath.DS.'default';

		// now check for all xml files in these folders
		foreach ($xmldirs as $xmldir)
		{
			if ($handle=opendir($xmldir))
			{
				/* check that each xml template has a corresponding record in the
				database for this project. If not,create the rows with default values
				from the xml file */
				while ($file=readdir($handle))
				{
					if	(	$file!='.' &&
							$file!='..' &&
							$file!='do_tipsl' &&
							strtolower(substr($file,-3))=='xml' &&
							strtolower(substr($file,0,strlen($predictionTemplatePrefix)))!=$predictionTemplatePrefix
						)
					{
						$template=substr($file,0,(strlen($file)-4));

						if ((empty($records)) || (!in_array($template,$records)))
						{
							//template not present,create a row with default values
							$params=new JLParameter(null,$xmldir.DS.$file);

							//get the values
							$defaultvalues=array();
							foreach ($params->getGroups() as $key => $group)
							{
								foreach ($params->getParams('params',$key) as $param)
								{
									$defaultvalues[]=$param[5].'='.$param[4];
								}
							}
							$defaultvalues=implode("\n",$defaultvalues);

							$query="	INSERT INTO #__joomleague_template_config (template,title,params,project_id)
													VALUES ('$template','$params->name','$defaultvalues','$project_id')";
							$this->_db->setQuery($query);
							//echo error,allows to check if there is a mistake in the template file
							if (!$this->_db->query())
							{
								$this->setError($this->_db->getErrorMsg());
								return false;
							}
							array_push($records,$template);
						}
					}
				}
				closedir($handle);
			}
		}
	}
	
		private function _getRoundName($round_id)
	{
		$query='SELECT name FROM #__joomleague_round WHERE id='.(int)$round_id;
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($this->_db->getAffectedRows())
		{
			$result=$this->_db->loadResult();
			return $result;
		}
		return null;
	}
	
	private function _getTeamName($team_id)
	{
		$query='	SELECT t.name
					FROM #__joomleague_team AS t
					INNER JOIN #__joomleague_project_team AS pt on pt.id='.(int)$team_id.' WHERE t.id=pt.team_id';
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($object=$this->_db->loadObject())
		{
			return $object->name;
		}
		return '#Error in _getTeamName ('.$team_id.') #';
	}

	private function _getTeamName2($team_id)
	{
		$query='SELECT name FROM #__joomleague_team WHERE id='.(int)$team_id;
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($this->_db->getAffectedRows())
		{
			$result=$this->_db->loadResult();
			return $result;
		}
		return '#Error in _getTeamName2 ('.$team_id.') #';
	}
	
	private function _importMatches()
	{

if ( $this->debug_info )
{
echo '_datas match -> <br /><pre>~'.print_r($this->_datas['match'],true).'~</pre><br />';
echo '_datas team -> <br /><pre>~'.print_r($this->_datas['team'],true).'~</pre><br />';
}	
		$my_text='';
		if (!isset($this->_datas['match']) || count($this->_datas['match'])==0){return true;}

		if (!isset($this->_datas['team']) || count($this->_datas['team'])==0){return true;}
		if ((!isset($this->_newteams) || count($this->_newteams)==0) &&
			(!isset($this->_dbteamsid) || count($this->_dbteamsid)==0)){return true;}

		foreach ($this->_datas['match'] as $key => $match)
		{
			$p_match =& $this->getTable('match');
			$oldId = (int)$match->id;
			if ($this->import_version=='NEW')
			{
				$p_match->set('round_id',$this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]);
				$p_match->set('match_number',$this->_getDataFromObject($match,'match_number'));

				if ($match->projectteam1_id > 0)
				{
					$team1 = $this->_convertProjectTeamForMatchID[intval($this->_getDataFromObject($match,'projectteam1_id'))];
				}
				else
				{
					$team1 = 0;
				}
				$p_match->set('projectteam1_id',$team1);

				if ($match->projectteam2_id > 0)
				{
					$team2 = $this->_convertProjectTeamForMatchID[intval($this->_getDataFromObject($match,'projectteam2_id'))];
				}
				else
				{
					$team2 = 0;
				}
				
				$p_match->set('projectteam2_id',$team2);

				if (!empty($this->_convertPlaygroundID))
				{
					if (array_key_exists((int)$this->_getDataFromObject($match,'playground_id'),$this->_convertPlaygroundID))
					{
						$p_match->set('playground_id',$this->_convertPlaygroundID[$this->_getDataFromObject($match,'playground_id')]);
					}
					else
					{
						$p_match->set('playground_id',0);
					}
				}
				if ($p_match->playground_id ==0)
				{
					$p_match->set('playground_id',NULL);
				}

				$p_match->set('match_date',$this->_getDataFromObject($match,'match_date'));

				$p_match->set('time_present',$this->_getDataFromObject($match,'time_present'));

				$team1_result=$this->_getDataFromObject($match,'team1_result');
				if (isset($team1_result) && ($team1_result !=NULL)) { $p_match->set('team1_result',$team1_result); }

				$team2_result=$this->_getDataFromObject($match,'team2_result');
				if (isset($team2_result) && ($team2_result !=NULL)) { $p_match->set('team2_result',$team2_result); }

				$team1_bonus=$this->_getDataFromObject($match,'team1_bonus');
				if (isset($team1_bonus) && ($team1_bonus !=NULL)) { $p_match->set('team1_bonus',$team1_bonus); }

				$team2_bonus=$this->_getDataFromObject($match,'team2_bonus');
				if (isset($team2_bonus) && ($team2_bonus !=NULL)) { $p_match->set('team2_bonus',$team2_bonus); }

				$team1_legs=$this->_getDataFromObject($match,'team1_legs');
				if (isset($team1_legs) && ($team1_legs !=NULL)) { $p_match->set('team1_legs',$team1_legs); }

				$team2_legs=$this->_getDataFromObject($match,'team2_legs');
				if (isset($team2_legs) && ($team2_legs !=NULL)) { $p_match->set('team2_legs',$team2_legs); }

				$p_match->set('team1_result_split',$this->_getDataFromObject($match,'team1_result_split'));
				$p_match->set('team2_result_split',$this->_getDataFromObject($match,'team2_result_split'));
				$p_match->set('match_result_type',$this->_getDataFromObject($match,'match_result_type'));

				$team1_result_ot=$this->_getDataFromObject($match,'team1_result_ot');
				if (isset($team1_result_ot) && ($team1_result_ot !=NULL)) { $p_match->set('team1_result_ot',$team1_result_ot); }

				$team2_result_ot=$this->_getDataFromObject($match,'team2_result_ot');
				if (isset($team2_result_ot) && ($team2_result_ot !=NULL)) { $p_match->set('team2_result_ot',$team2_result_ot); }

				$team1_result_so=$this->_getDataFromObject($match,'team1_result_so');
				if (isset($team1_result_so) && ($team1_result_so !=NULL)) { $p_match->set('team1_result_so',$team1_result_so); }

				$team2_result_so=$this->_getDataFromObject($match,'team2_result_so');
				if (isset($team2_result_so) && ($team2_result_so !=NULL)) { $p_match->set('team2_result_so',$team2_result_so); }

				$p_match->set('alt_decision',$this->_getDataFromObject($match,'alt_decision'));

				$team1_result_decision=$this->_getDataFromObject($match,'team1_result_decision');
				if (isset($team1_result_decision) && ($team1_result_decision !=NULL)) { $p_match->set('team1_result_decision',$team1_result_decision); }

				$team2_result_decision=$this->_getDataFromObject($match,'team2_result_decision');
				if (isset($team2_result_decision) && ($team2_result_decision !=NULL)) { $p_match->set('team2_result_decision',$team2_result_decision); }

				$p_match->set('decision_info',$this->_getDataFromObject($match,'decision_info'));
				$p_match->set('cancel',$this->_getDataFromObject($match,'cancel'));
				$p_match->set('cancel_reason',$this->_getDataFromObject($match,'cancel_reason'));
				$p_match->set('count_result',$this->_getDataFromObject($match,'count_result'));
				$p_match->set('crowd',$this->_getDataFromObject($match,'crowd'));
				$p_match->set('summary',$this->_getDataFromObject($match,'summary'));
				$p_match->set('show_report',$this->_getDataFromObject($match,'show_report'));
				$p_match->set('preview',$this->_getDataFromObject($match,'preview'));
				$p_match->set('match_result_detail',$this->_getDataFromObject($match,'match_result_detail'));
				$p_match->set('new_match_id',$this->_getDataFromObject($match,'new_match_id'));
				$p_match->set('old_match_id',$this->_getDataFromObject($match,'old_match_id'));
				$p_match->set('extended',$this->_getDataFromObject($match,'extended'));
				$p_match->set('published',$this->_getDataFromObject($match,'published'));
			}
			else // ($this->import_version=='OLD')
			{
				$p_match->set('round_id',$this->_convertRoundID[intval($match->round_id)]);
				$p_match->set('match_number',$this->_getDataFromObject($match,'match_number'));

				if ($match->matchpart1 > 0)
				{
					$team1=$this->_convertTeamID[intval($match->matchpart1)];
					$p_match->set('projectteam1_id',$this->_convertProjectTeamID[$team1]);
				}
				else
				{
					$p_match->set('projectteam1_id',0);
				}

				if ($match->matchpart2 > 0)
				{
					$team2=$this->_convertTeamID[intval($match->matchpart2)];
					$p_match->set('projectteam2_id',$this->_convertProjectTeamID[$team2]);
				}
				else
				{
					$p_match->set('projectteam2_id',0);
				}

				$matchdate=(string)$match->match_date;
				$p_match->set('match_date',$matchdate);

				$team1_result=$this->_getDataFromObject($match,'matchpart1_result');
				if (isset($team1_result) && ($team1_result !=NULL)) { $p_match->set('team1_result',$team1_result); }

				$team2_result=$this->_getDataFromObject($match,'matchpart2_result');
				if (isset($team2_result) && ($team2_result !=NULL)) { $p_match->set('team2_result',$team2_result); }

				$team1_bonus=$this->_getDataFromObject($match,'matchpart1_bonus');
				if (isset($team1_bonus) && ($team1_bonus !=NULL)) { $p_match->set('team1_bonus',$team1_bonus); }

				$team2_bonus=$this->_getDataFromObject($match,'matchpart2_bonus');
				if (isset($team2_bonus) && ($team2_bonus !=NULL)) { $p_match->set('team2_bonus',$team2_bonus); }

				$team1_legs=$this->_getDataFromObject($match,'matchpart1_legs');
				if (isset($team1_legs) && ($team1_legs !=NULL)) { $p_match->set('team1_legs',$team1_legs); }

				$team2_legs=$this->_getDataFromObject($match,'matchpart2_legs');
				if (isset($team2_legs) && ($team2_legs !=NULL)) { $p_match->set('team2_legs',$team2_legs); }

				$p_match->set('team1_result_split',$this->_getDataFromObject($match,'matchpart1_result_split'));//NULL
				$p_match->set('team2_result_split',$this->_getDataFromObject($match,'matchpart2_result_split'));//NULL
				$p_match->set('match_result_type',$this->_getDataFromObject($match,'match_result_type'));

				$team1_result_ot=$this->_getDataFromObject($match,'matchpart1_result_ot');
				if (isset($team1_result_ot) && ($team1_result_ot !=NULL)) { $p_match->set('team1_result_ot',$team1_result_ot); }

				$team2_result_ot=$this->_getDataFromObject($match,'matchpart2_result_ot');
				if (isset($team2_result_ot) && ($team2_result_ot !=NULL)) { $p_match->set('team2_result_ot',$team2_result_ot); }

				$p_match->set('alt_decision',$this->_getDataFromObject($match,'alt_decision'));

				$team1_result_decision=$this->_getDataFromObject($match,'matchpart1_result_decision');
				if (isset($team1_result_decision) && ($team1_result_decision !=NULL)) { $p_match->set('team1_result_decision',$team1_result_decision); }

				$team2_result_decision=$this->_getDataFromObject($match,'matchpart2_result_decision');
				if (isset($team2_result_decision) && ($team2_result_decision !=NULL)) { $p_match->set('team2_result_decision',$team2_result_decision); }

				$p_match->set('decision_info',$this->_getDataFromObject($match,'decision_info'));
				$p_match->set('count_result',$this->_getDataFromObject($match,'count_result'));
				$p_match->set('crowd',$this->_getDataFromObject($match,'crowd'));
				$p_match->set('summary',$this->_getDataFromObject($match,'summary'));
				$p_match->set('show_report',$this->_getDataFromObject($match,'show_report'));
				$p_match->set('match_result_detail',$this->_getDataFromObject($match,'match_result_detail'));
				$p_match->set('published',$this->_getDataFromObject($match,'published'));
			}

if ( $this->debug_info )
{
echo 'p_match -> <br /><pre>~'.print_r($p_match,true).'~</pre><br />';
}

			if ($p_match->store()===false)
			{
				$my_text .= 'error on match import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatches<br />#$my_text#<br />#<pre>".print_r($p_match,true).'</pre>#';
				$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_DATA:']=$my_text;
				return false;
			}
			else
			{
				if ($this->import_version=='NEW')
				{
					if ($match->projectteam1_id > 0)
					{
						$teamname1 = $this->_getTeamName($p_match->projectteam1_id);
					}
					else
					{
						$teamname1='<span style="color:orange">'.JText::_('Home-Team not asigned').'</span>';
					}
					if ($match->projectteam2_id > 0)
					{
						$teamname2 = $this->_getTeamName($p_match->projectteam2_id);
					}
					else
					{
						$teamname2='<span style="color:orange">'.JText::_('Guest-Team not asigned').'</span>';
					}

					$my_text .= '<span style="color:green">';
					$my_text .= JText::sprintf(	'Added to round: %1$s / Match: %2$s - %3$s',
												'</span><strong>'.$this->_getRoundName($this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]).'</strong><span style="color:green">',
												"</span><strong>$teamname1</strong>",
												"<strong>$teamname2</strong>");
					$my_text .= '<br />';
				}

				if ($this->import_version=='OLD')
				{
					if ($match->matchpart1 > 0)
					{
						$teamname1=$this->_getTeamName2($this->_convertTeamID[intval($match->matchpart1)]);
					}
					else
					{
						$teamname1='<span style="color:orange">'.JText::_('Home-Team not asigned').'</span>';
					}
					if ($match->matchpart2 > 0)
					{
						$teamname2=$this->_getTeamName2($this->_convertTeamID[intval($match->matchpart2)]);
					}
					else
					{
						$teamname2='<span style="color:orange">'.JText::_('Guest-Team not asigned').'</span>';
					}

					$my_text .= '<span style="color:green">';
					$my_text .= JText::sprintf(	'Added to round: %1$s / Match: %2$s - %3$s',
												'</span><strong>'.$this->_getRoundName($this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]).'</strong><span style="color:green">',
												"</span><strong>$teamname1</strong>",
												"<strong>$teamname2</strong>");
					$my_text .= '<br />';
				}
			}
			$insertID = $this->_db->insertid();
			
			if ( !$insertID )
			{
			$my_text .= '<span style="color:red"><strong>';
			$my_text .= JText::sprintf(	'Now _db->insertid(): %1$s',"$insertID</strong>");
			$my_text .= '</span><br />';
      $query = "SELECT id
      from #__joomleague_match
      where projectteam1_id = '$p_match->projectteam1_id'
      and projectteam2_id = '$p_match->projectteam2_id'
      and round_id = '$p_match->round_id'
      ";
      $this->_db->setQuery( $query );
      $insertID = $this->_db->loadResult();
      }
      
      
			$this->_convertMatchID[$oldId] = $insertID;
			$my_text .= '<span style="color:green">';
			$my_text .= JText::sprintf(	'OldID / NewID: %1$s,%2$s',"</span><strong>$oldID","$insertID</strong>");
			$my_text .= '<br />';
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_DATA:']=$my_text;
		return true;
	}
    
    private function _importMatchReferee()
	{

if ( $this->debug_info )
{
echo '_datas matchreferee -> <br /><pre>~'.print_r($this->_datas['matchreferee'],true).'~</pre><br />';
echo '_datas person -> <br /><pre>~'.print_r($this->_datas['person'],true).'~</pre><br />';
echo '_convertProjectRefereeID -> <br /><pre>~'.print_r($this->_convertProjectRefereeID,true).'~</pre><br />';
echo '_convertMatchID -> <br /><pre>~'.print_r($this->_convertMatchID,true).'~</pre><br />';
}
	
		$my_text='';
		if (!isset($this->_datas['matchreferee']) || count($this->_datas['matchreferee'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($this->_dbpersonsid) || count($this->_dbpersonsid)==0)){return true;}

		foreach ($this->_datas['matchreferee'] as $key => $matchreferee)
		{
			$import_matchreferee = $this->_datas['matchreferee'][$key];
			$oldID=$this->_getDataFromObject($import_matchreferee,'id');
			$p_matchreferee =& $this->getTable('matchreferee');
			$oldMatchID = $this->_getDataFromObject($import_matchreferee,'match_id');
			$oldPersonID = $this->_getDataFromObject($import_matchreferee,'referee_id');
			if (!isset($this->_convertMatchID[$oldMatchID]) ||
				!isset($this->_convertProjectRefereeID[$oldPersonID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= JText::sprintf(	'Skipping import of MatchReferee-ID [%1$s]. Old-MatchID: [%2$s] - Old-RefereeID: [%3$s]',
											"</span><strong>$oldID</strong><span style='color:red'>",
											"</span><strong>$oldMatchID</strong><span style='color:red'>",
											"</span><strong>$oldPersonID</strong>").'<br />';
				continue;
			}
			$p_matchreferee->set('match_id',$this->_convertMatchID[$oldMatchID]);
			$p_matchreferee->set('project_referee_id',$this->_convertProjectRefereeID[$oldPersonID]);
			$oldPositionID=$this->_getDataFromObject($import_matchreferee,'project_position_id'); 
			if (isset($this->_convertProjectPositionID[$oldPositionID]))
			{
				$p_matchreferee->set('project_position_id',$this->_convertProjectPositionID[$oldPositionID]);
			}
			$p_matchreferee->set('ordering',$this->_getDataFromObject($import_matchreferee,'ordering'));
			if ($p_matchreferee->store()===false)
			{
				$my_text .= 'error on matchreferee import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchReferee<br />#$my_text#<br />#<pre>".print_r($p_matchreferee,true).'</pre>#';
				$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_REFEREE_DATA:']=$my_text;
				return false;
			}
			else
			{
				$dPerson = $this->_getPersonName($p_matchreferee->referee_id);
				$dPosName = (($p_matchreferee->project_position_id==0) ?
							'<span style="color:orange">'.JText::_('Has no position').'</span>' :
							JText::_($this->_getObjectName('position',$p_matchreferee->project_position_id)));
				$my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'Created new matchreferee data. MatchID: %1$s - Referee: %2$s,%3$s - Position: %4$s',
											'</span><strong>'.$p_matchreferee->match_id.'</strong><span style="color:green">',
											'</span><strong>'.$dPerson->lastname,
											$dPerson->firstname.'</strong><span style="color:green">',
											"</span><strong>$dPosName</strong>");
				$my_text .= '<br />';
			}
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_REFEREE_DATA:']=$my_text;
		return true;
	}
	
  private function _getObjectName($tableName,$id,$usedFieldName='')
	{
		$fieldName=($usedFieldName=='') ? 'name' : $usedFieldName;
		$query="SELECT $fieldName FROM #__joomleague_$tableName WHERE id=$id";
		$this->_db->setQuery($query);
		if ($result=$this->_db->loadResult()){return $result;}
		return JText::sprintf('Item with ID [%1$s] not found inside [#__joomleague_%2$s]',$id,$tableName)." $query";
	}
  
  	private function _importSportsType()
	{
		$my_text='';
		if (!empty($this->_sportstype_new))
		{
			$query="SELECT id FROM #__joomleague_sports_type WHERE name='".addslashes(stripslashes($this->_sportstype_new))."'";
			$this->_db->setQuery($query);
			if ($sportstypeObject=$this->_db->loadObject())
			{
				$this->_sportstype_id=$sportstypeObject->id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf('Using existing sportstype data: %1$s',"</span><strong>$this->_sportstype_new</strong>");
				$my_text .= '<br />';
			}
			else
			{
				$p_sportstype =& $this->getTable('sportstype');
				$p_sportstype->set('name',trim($this->_sportstype_new));

				if ($p_sportstype->store()===false)
				{
					$my_text .= '<span style="color:red"><strong>';
					$my_text .= JText::_('Error in function _importSportsType').'</strong></span><br />';
					$my_text .= JText::sprintf('Sportstypename: %1$s',JText::_($this->_sportstype_new)).'<br />';
					$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
					$my_text .= '<pre>'.print_r($p_sportstype,true).'</pre>';
					$this->_success_text['JL_ADMIN_DFBNET_IMPORT_SPORTSTYPE_DATA:']=$my_text;
					return false;
				}
				else
				{
					$insertID=$this->_db->insertid();
					$this->_sportstype_id=$insertID;
					$my_text .= '<span style="color:green">';
					$my_text .= JText::sprintf('Created new sportstype data: %1$s',"</span><strong>$this->_sportstype_new</strong>");
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$my_text .= '<span style="color:orange">';
			$my_text .= JText::sprintf(	'Using existing sportstype data: %1$s',
										'</span><strong>'.JText::_($this->_getObjectName('sports_type',$this->_sportstype_id)).'</strong>');
			$my_text .= '<br />';
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_SPORTSTYPE_DATA:']=$my_text;
		return true;
	}
  
   
	private function _importPlayground()
	{
		$my_text='';
		if (!isset($this->_datas['playground']) || count($this->_datas['playground'])==0){return true;}
		if ((!isset($this->_newplaygroundid) || count($this->_newplaygroundid)==0) &&
			(!isset($this->_dbplaygroundsid) || count($this->_dbplaygroundsid)==0)){return true;}

		if (!empty($this->_dbplaygroundsid))
		{
			foreach ($this->_dbplaygroundsid AS $key => $id)
			{
				$oldID=$this->_getDataFromObject($this->_datas['playground'][$key],'id');
				$this->_convertPlaygroundID[$oldID]=$id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf(	'Using existing playground data: %1$s',
											'</span><strong>'.$this->_getObjectName('playground',$id).'</strong>');
				$my_text .= '<br />';
			}
		}
		if (!empty($this->_newplaygroundid))
		{
			foreach ($this->_newplaygroundid AS $key => $id)
			{
				$p_playground =&  $this->getTable('playground');
				$import_playground=$this->_datas['playground'][$key];
				$oldID=$this->_getDataFromObject($import_playground,'id');
				$alias=$this->_getDataFromObject($import_playground,'alias');
				$p_playground->set('name',trim($this->_newplaygroundname[$key]));
				$p_playground->set('short_name',$this->_newplaygroundshort[$key]);
				$p_playground->set('address',$this->_getDataFromObject($import_playground,'address'));
				$p_playground->set('zipcode',$this->_getDataFromObject($import_playground,'zipcode'));
				$p_playground->set('city',$this->_getDataFromObject($import_playground,'city'));
				$p_playground->set('country',$this->_getDataFromObject($import_playground,'country'));
				$p_playground->set('max_visitors',$this->_getDataFromObject($import_playground,'max_visitors'));
				$p_playground->set('website',$this->_getDataFromObject($import_playground,'website'));
				$p_playground->set('picture',$this->_getDataFromObject($import_playground,'picture'));
				$p_playground->set('notes',$this->_getDataFromObject($import_playground,'notes'));
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_playground->set('alias',$alias);
				}
				else
				{
					$p_playground->set('alias',JFilterOutput::stringURLSafe($this->_getDataFromObject($p_playground,'name')));
				}
				if (array_key_exists((int)$this->_getDataFromObject($import_playground,'country'),$this->_convertCountryID))
				{
					$p_playground->set('country',(int)$this->_convertCountryID[(int)$this->_getDataFromObject($import_playground,'country')]);
				}
				else
				{
					$p_playground->set('country',$this->_getDataFromObject($import_playground,'country'));
				}
				if ($this->_importType!='playgrounds')	// force club_id to be set to default if only playgrounds are imported
				{
					//if (!isset($this->_getDataFromObject($import_playground,'club_id')))
					{
						$p_playground->set('club_id',$this->_getDataFromObject($import_playground,'club_id'));
					}
				}
				$query="SELECT id,name FROM #__joomleague_playground WHERE name='".addslashes(stripslashes($p_playground->name))."'";
				$this->_db->setQuery($query); $this->_db->query();
				if ($object=$this->_db->loadObject())
				{
					$this->_convertPlaygroundID[$oldID]=$object->id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf('Using existing playground data: %1$s',"</span><strong>$object->name</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_playground->store()===false)
					{
						$my_text .= '<span style="color:red"><strong>';
						$my_text .= JText::_('Error in function _importPlayground').'</strong></span><br />';
						$my_text .= JText::sprintf('Playgroundname: %1$s',$p_playground->name).'<br />';
						$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_playground,true).'</pre>';
						$this->_success_text['Importing general playground data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$this->_db->insertid();
						$this->_convertPlaygroundID[$oldID]=$insertID;
						$my_text .= '<span style="color:green">';
						$my_text .= JText::sprintf('JL_ADMIN_DFBNET_IMPORT_CREATED_NEW_PLAYGROUND_DATA: %1$s',"</span><strong>$p_playground->name</strong>");
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_PLAYGROUND_DATA:']=$my_text;
		return true;
	}
  
  private function _importLeague()
	{
		$my_text='';
		if (!empty($this->_league_new))
		{
			$query="SELECT id FROM #__joomleague_league WHERE name='".addslashes(stripslashes($this->_league_new))."'";
			$this->_db->setQuery($query);

			if ($leagueObject=$this->_db->loadObject())
			{
				$this->_league_id=$leagueObject->id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf('Using existing league data: %1$s',"</span><strong>$this->_league_new</strong>");
				$my_text .= '<br />';
			}
			else
			{
				$p_league =& $this->getTable('league');
				$p_league->set('name',trim($this->_league_new));
				$p_league->set('short_name',trim($this->_league_new));
				$p_league->set('middle_name',trim($this->_league_new));
				$p_league->set('alias',JFilterOutput::stringURLSafe($this->_league_new));
				$p_league->set('country',$this->_league_new_country);

				if ($p_league->store()===false)
				{
					$my_text .= '<span style="color:red"><strong>';
					$my_text .= JText::_('Error in function _importLeague').'</strong></span><br />';
					$my_text .= JText::sprintf('Leaguenname: %1$s',$this->_league_new).'<br />';
					$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
					$my_text .= '<pre>'.print_r($p_league,true).'</pre>';
					$this->_success_text['Importing league data:']=$my_text;
					return false;
				}
				else
				{
					$insertID=$this->_db->insertid();
					$this->_league_id=$insertID;
					$my_text .= '<span style="color:green">';
					$my_text .= JText::sprintf('Created new league data: %1$s',"</span><strong>$this->_league_new</strong>");
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$my_text .= '<span style="color:orange">';
			$my_text .= JText::sprintf(	'Using existing league data: %1$s',
										'</span><strong>'.$this->_getObjectName('league',$this->_league_id).'</strong>');
			$my_text .= '<br />';
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_LEAGUE_DATA:']=$my_text;
		return true;
	}
	
	private function _importSeason()
	{
		$my_text='';
		if (!empty($this->_season_new))
		{
			$query="SELECT id FROM #__joomleague_season WHERE name='".addslashes(stripslashes($this->_season_new))."'";
			$this->_db->setQuery($query);

			if ($seasonObject=$this->_db->loadObject())
			{
				$this->_season_id=$seasonObject->id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf('Using existing season data: %1$s',"</span><strong>$this->_season_new</strong>");
				$my_text .= '<br />';
			}
			else
			{
				$p_season =& $this->getTable('season');
				$p_season->set('name',trim($this->_season_new));
				$p_season->set('alias',JFilterOutput::stringURLSafe($this->_season_new));

				if ($p_season->store()===false)
				{
					$my_text .= '<span style="color:red"><strong>';
					$my_text .= JText::_('Error in function _importSeason').'</strong></span><br />';
					$my_text .= JText::sprintf('Seasonname: %1$s',$this->_season_new).'<br />';
					$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
					$my_text .= '<pre>'.print_r($p_season,true).'</pre>';
					$this->_success_text['Importing season data:']=$my_text;
					return false;
				}
				else
				{
					$insertID=$this->_db->insertid();
					$this->_season_id=$insertID;
					$my_text .= '<span style="color:green">';
					$my_text .= JText::sprintf('Created new season data: %1$s',"</span><strong>$this->_season_new</strong>");
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$my_text .= '<span style="color:orange">';
			$my_text .= JText::sprintf(	'Using existing season data: %1$s',
										'</span><strong>'.$this->_getObjectName('season',$this->_season_id).'</strong>');
			$my_text .= '<br />';
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_SEASON_DATA:']=$my_text;
		return true;
	}
  
  private function _importClubs()
	{
		$my_text='';
		if (!isset($this->_datas['club']) || count($this->_datas['club'])==0){return true;}
		if ((!isset($this->_newclubsid) || count($this->_newclubsid)==0) &&
			(!isset($this->_dbclubsid) || count($this->_dbclubsid)==0)){return true;}

		if (!empty($this->_dbclubsid))
		{
			foreach ($this->_dbclubsid AS $key => $id)
			{
				if (empty($this->_newclubs[$key]))
				{
					$oldID=$this->_getDataFromObject($this->_datas['club'][$key],'id');
					$this->_convertClubID[$oldID]=$id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf(	'Using existing club data: %1$s - %2$s',
												'</span><strong>'.$this->_getObjectName('club',$id).'</strong>',
												''.$this->_getObjectName('club',$id,'country').''
												);
					$my_text .= '<br />';
				}
			}
		}
//To Be fixed: Falls Verein neu angelegt wird, muss auch das Team neu angelegt werden.
//echo '2<pre>'.print_r($this->_newclubsid,true).'</pre>';
//echo '3<pre>'.print_r($this->_newclubs,true).'</pre>';
//echo '4<pre>'.print_r($this->_createclubsid,true).'</pre>';
		if (!empty($this->_newclubsid))
		{
			foreach ($this->_newclubsid AS $key => $id)
			{
				$p_club =& $this->getTable('club');
				foreach ($this->_datas['club'] AS $dClub)
				{
					if ($dClub->id==$id)
					{
						$import_club=$dClub;
						break;
					}
				}
				$oldID=$this->_getDataFromObject($import_club,'id');
				$alias=$this->_getDataFromObject($import_club,'alias');
				$p_club->set('name',$this->_newclubs[$key]);
				$p_club->set('admin',$this->_joomleague_admin);
				$p_club->set('address',$this->_getDataFromObject($import_club,'address'));
				$p_club->set('zipcode',$this->_getDataFromObject($import_club,'zipcode'));
				$p_club->set('location',$this->_getDataFromObject($import_club,'location'));
				$p_club->set('state',$this->_getDataFromObject($import_club,'state'));
				$p_club->set('country',$this->_newclubscountry[$key]);
				$p_club->set('founded',$this->_getDataFromObject($import_club,'founded'));
				$p_club->set('phone',$this->_getDataFromObject($import_club,'phone'));
				$p_club->set('fax',$this->_getDataFromObject($import_club,'fax'));
				$p_club->set('email',$this->_getDataFromObject($import_club,'email'));
				$p_club->set('website',$this->_getDataFromObject($import_club,'website'));
				$p_club->set('president',$this->_getDataFromObject($import_club,'president'));
				$p_club->set('manager',$this->_getDataFromObject($import_club,'manager'));
				$p_club->set('logo_big',$this->_getDataFromObject($import_club,'logo_big'));
				$p_club->set('logo_middle',$this->_getDataFromObject($import_club,'logo_middle'));
				$p_club->set('logo_small',$this->_getDataFromObject($import_club,'logo_small'));
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_club->set('alias',$alias);
				}
				else
				{
					$p_club->set('alias',JFilterOutput::stringURLSafe($this->_getDataFromObject($p_club,'name')));
				}
				if ($this->_importType!='clubs')	// force playground_id to be set to default if only clubs are imported
				{
					if (($this->import_version=='NEW') && ($import_club->standard_playground > 0))
					{
						if (isset($this->_convertPlaygroundID[(int)$this->_getDataFromObject($import_club,'standard_playground')]))
						{
							$p_club->set('standard_playground',(int)$this->_convertPlaygroundID[(int)$this->_getDataFromObject($import_club,'standard_playground')]);
						}
					}
				}
				if (($this->import_version=='NEW') && ($import_club->extended!=''))
				{
					$p_club->set('extended',$this->_getDataFromObject($import_club,'extended'));
				}
				$query="SELECT	id,
								name,
								country
						FROM #__joomleague_club
						WHERE	name='".addslashes(stripslashes($p_club->name))."' AND
								country='$p_club->country'";
				$this->_db->setQuery($query); $this->_db->query();
				if ($object=$this->_db->loadObject())
				{
					$this->_convertClubID[$oldID]=$object->id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf('Using existing club data: %1$s',"</span><strong>$object->name</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_club->store()===false)
					{
						$my_text .= '<span style="color:red"><strong>';
						$my_text .= JText::_('Error in function importClubs').'</strong></span><br />';
						$my_text .= JText::sprintf('Clubname: %1$s',$p_club->name).'<br />';
						$my_text .= JText::sprintf('Error-Text #%1$s#',$this->_db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_club,true).'</pre>';
						$this->_success_text['Importing general club data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$this->_db->insertid();
						$this->_convertClubID[$oldID]=$insertID;
						$my_text .= '<span style="color:green">';
						$my_text .= JText::sprintf(	'JL_ADMIN_DFBNET_IMPORT_CREATED_NEW_CLUB_DATA: %1$s - %2$s',
													"</span><strong>$p_club->name</strong>",
													$p_club->country
													);
						$my_text .= '<br />';
					}
					
// 					$my_text .= '<span style="color:green">';
// 		      $my_text .= JText::sprintf('old / new Clubid: %1$s , %2$s',"</span><strong>$oldId</strong>","</span><strong>$insertID</strong>");
// 		      $my_text .= '<br />';
		      
				}
			}
		}
		
// 		echo '_importClubs _convertClubID<pre>';
//     print_r($this->_convertClubID);
//     echo '</pre>';
		
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_CLUB_DATA:']=$my_text;
		return true;
	}
  
     
  private function _importRounds()
	{
		$my_text='';
		if (!isset($this->_datas['round']) || count($this->_datas['round'])==0){return true;}

// echo '<pre>';
// print_r($this->_datas['round']);
// echo '</pre><br>';
// echo 'this->_project_id ->'.$this->_project_id.'<br>';

		foreach ($this->_datas['round'] as $key => $round)
		{
			$p_round =& $this->getTable('round');
			$oldId = (int)$round->id;

// echo '_importRounds oldId ->'.$oldId.'<br>';
			
// 			if ( $this->_project_id )
// 			{
//       $p_round->set('id',$oldId);
//       }
			
			$name=trim($this->_getDataFromObject($round,'name'));
			$alias=trim($this->_getDataFromObject($round,'alias'));
			// if the roundcode field is empty,it is an old .jlg-Import file
			$roundnumber=$this->_getDataFromObject($round,'roundcode');
			
      if (empty($roundnumber))
			{
				$roundnumber=$this->_getDataFromObject($round,'matchcode');
			}
			
      $p_round->set('roundcode',$roundnumber);
			$p_round->set('name',$name);
			
			if ($alias!='')
			{
				$p_round->set('alias',$alias);
			}
			else
			{
				$p_round->set('alias',JFilterOutput::stringURLSafe($name));
			}
			
      $p_round->set('round_date_first',$this->_getDataFromObject($round,'round_date_first'));
			$round_date_last=trim($this->_getDataFromObject($round,'round_date_last'));
			$round_date_first=trim($this->_getDataFromObject($round,'round_date_first'));
			
      if (($round_date_last=='') || ($round_date_last=='0000-00-00'))
			{
				$round_date_last=$this->_getDataFromObject($round,'round_date_first');
			}
			
      $p_round->set('round_date_last',$round_date_last);
			$p_round->set('project_id',$this->_project_id);
			
// echo '_importRounds<pre>';
// print_r($p_round);
// echo '</pre><br>';
  			
			if ($p_round->store()===false)
			{
				$my_text .= 'error on round import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importRounds<br />#$my_text#<br />#<pre>".print_r($p_round,true).'</pre>#';
				$this->_success_text['JL_ADMIN_DFBNET_IMPORT_ROUND_DATA:']=$my_text;
				return false;
			}
			else
			{
// 			  $insertID=$this->_db->insertid();
				$my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf('JL_ADMIN_DFBNET_IMPORT_CREATED_NEW_ROUND: %1$s %2$s %3$s',"</span><strong>$name</strong>","<strong>$round_date_first</strong>","<strong>$round_date_last</strong>");
				$my_text .= '<br />';
			}
			$insertID = $this->_db->insertid();
			$this->_convertRoundID[$oldId] = $insertID;
				
        $my_text .= '<span style="color:green">';
 				$my_text .= JText::sprintf('JL_ADMIN_DFBNET_IMPORT_CREATED_NEW_ROUND_2: %1$s %2$s ',"<strong>$round->id</strong>","<strong>$insertID</strong>");
 				$my_text .= '<br />';			
			
		}
		
// echo '_importRounds this->_convertRoundID<pre>';
// print_r($this->_convertRoundID);
// echo '</pre><br>';


		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_ROUND_DATA:']=$my_text;
		return true;
	}
  
	private function _importProjectTeam()
	{
	
if ( $this->debug_info )
{
echo '_datas projectteam -> <br /><pre>~'.print_r($this->_datas['projectteam'],true).'~</pre><br />';
echo '_convertTeamID -> <br /><pre>~'.print_r($this->_convertTeamID,true).'~</pre><br />';
}
	
		$my_text='';
		if (!isset($this->_datas['projectteam']) || count($this->_datas['projectteam'])==0){return true;}

		if (!isset($this->_datas['team']) || count($this->_datas['team'])==0){return true;}
		if ((!isset($this->_newteams) || count($this->_newteams)==0) &&
			(!isset($this->_dbteamsid) || count($this->_dbteamsid)==0)){return true;}

		foreach ($this->_datas['projectteam'] as $key => $projectteam)
		{
			$p_projectteam =& $this->getTable('projectteam');
			$import_projectteam = $this->_datas['projectteam'][$key];
			$oldID = $this->_getDataFromObject($import_projectteam,'id');
			$p_projectteam->set('project_id',$this->_project_id);
			$p_projectteam->set('team_id',$this->_convertTeamID[$this->_getDataFromObject($projectteam,'team_id')]);

			if (count($this->_convertDivisionID) > 0)
			{
				$p_projectteam->set('division_id',$this->_convertDivisionID[$this->_getDataFromObject($projectteam,'division_id')]);
			}

			$p_projectteam->set('start_points',$this->_getDataFromObject($projectteam,'start_points'));
			$p_projectteam->set('points_finally',$this->_getDataFromObject($projectteam,'points_finally'));
			$p_projectteam->set('neg_points_finally',$this->_getDataFromObject($projectteam,'neg_points_finally'));
			$p_projectteam->set('matches_finally',$this->_getDataFromObject($projectteam,'matches_finally'));
			$p_projectteam->set('won_finally',$this->_getDataFromObject($projectteam,'won_finally'));
			$p_projectteam->set('draws_finally',$this->_getDataFromObject($projectteam,'draws_finally'));
			$p_projectteam->set('lost_finally',$this->_getDataFromObject($projectteam,'lost_finally'));
			$p_projectteam->set('homegoals_finally',$this->_getDataFromObject($projectteam,'homegoals_finally'));
			$p_projectteam->set('guestgoals_finally',$this->_getDataFromObject($projectteam,'guestgoals_finally'));
			$p_projectteam->set('diffgoals_finally',$this->_getDataFromObject($projectteam,'diffgoals_finally'));
			$p_projectteam->set('is_in_score',$this->_getDataFromObject($projectteam,'is_in_score'));
			$p_projectteam->set('admin',$this->_joomleague_admin);

			if ($this->import_version=='NEW')
			{
				if (isset($import_projectteam->mark))
				{
					$p_projectteam->set('mark',$this->_getDataFromObject($projectteam,'mark'));
				}
				$p_projectteam->set('info',$this->_getDataFromObject($projectteam,'info'));
				$p_projectteam->set('reason',$this->_getDataFromObject($projectteam,'reason'));
				$p_projectteam->set('notes',$this->_getDataFromObject($projectteam,'notes'));
			}
			else
			{
				$p_projectteam->set('notes',$this->_getDataFromObject($projectteam,'description'));
				$p_projectteam->set('reason',$this->_getDataFromObject($projectteam,'info'));
			}
			if ((isset($projectteam->standard_playground)) && ($projectteam->standard_playground > 0))
			{
				if (isset($this->_convertPlaygroundID[$this->_getDataFromObject($projectteam,'standard_playground')]))
				{
					$p_projectteam->set('standard_playground',$this->_convertPlaygroundID[$this->_getDataFromObject($projectteam,'standard_playground')]);
				}
			}

			if ($p_projectteam->store()===false)
			{
				$my_text .= 'error on projectteam import: ';
				$my_text .= $oldID;
				$my_text .= '<br />Error: _importProjectTeam<br />~'.$my_text.'~<br />~<pre>'.print_r($p_projectteam,true).'</pre>~';
				$this->_success_text['JL_ADMIN_DFBNET_IMPORT_PROJECTTEAM_DATA:']=$my_text;
				$insertID = 0;
				return false;
			}
			else
			{
				$my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'Created new projectteam data: %1$s',
											'</span><strong>'.$this->_getTeamName2($p_projectteam->team_id).'</strong>');
				$my_text .= '<br />';
				$insertID = $this->_db->insertid();
				if ( !$insertID )
				{
				$my_text .= '<span style="color:red"><strong>';
			  $my_text .= JText::sprintf(	'Now _db->insertid(): %1$s',"$insertID</strong>");
			  $my_text .= '</span><br />';
        $query = "SELECT id
        from #__joomleague_project_team
        where project_id = '$p_projectteam->project_id'
        and team_id = '$p_projectteam->team_id'
        ";
        $this->_db->setQuery( $query );
        $insertID = $this->_db->loadResult();
        }
				$my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'with Table-ID: %1$s',
											'</span><strong>'.$insertID.'</strong>');
				$my_text .= '<br />';
			}

			$this->_convertProjectTeamID[$p_projectteam->team_id] = $insertID;
			$this->_convertProjectTeamForMatchID[$this->_getDataFromObject($projectteam,'id')] = $this->_convertProjectTeamID[$p_projectteam->team_id];
		}
		
if ( $this->debug_info )
{
echo '_convertProjectTeamID -> <br /><pre>~'.print_r($this->_convertProjectTeamID,true).'~</pre><br />';
echo '_convertProjectTeamForMatchID -> <br /><pre>~'.print_r($this->_convertProjectTeamForMatchID,true).'~</pre><br />';
}
		
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_PROJECTTEAM_DATA:']=$my_text;
		return true;
	}
  
  	private function _importProjectReferees()
	{
		$my_text='';
		if (!isset($this->_datas['projectreferee']) || count($this->_datas['projectreferee'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($this->_dbpersonsid) || count($this->_dbpersonsid)==0)){return true;}

		foreach ($this->_datas['projectreferee'] as $key => $projectreferee)
		{
			$import_projectreferee=$this->_datas['projectreferee'][$key];
			$oldID=$this->_getDataFromObject($import_projectreferee,'id');
			$p_projectreferee =& $this->getTable('projectreferee');

			$p_projectreferee->set('project_id',$this->_project_id);
			$p_projectreferee->set('person_id',$this->_convertPersonID[$this->_getDataFromObject($import_projectreferee,'person_id')]);
			$p_projectreferee->set('project_position_id',$this->_convertProjectPositionID[$this->_getDataFromObject($import_projectreferee,'project_position_id')]);

			$p_projectreferee->set('notes',$this->_getDataFromObject($import_projectreferee,'notes'));
			$p_projectreferee->set('picture',$this->_getDataFromObject($import_projectreferee,'picture'));
			$p_projectreferee->set('extended',$this->_getDataFromObject($import_projectreferee,'extended'));

			if ($p_projectreferee->store()===false)
			{
				$my_text .= 'error on projectreferee import: ';
				$my_text .= $oldID;
				$my_text .= '<br />Error: _importProjectReferees<br />~'.$my_text.'~<br />~<pre>'.print_r($p_projectreferee,true).'</pre>~';
				$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_PROJECTREFEREE_DATA:']=$my_text;
				return false;
			}
			else
			{
				$dPerson = $this->_getPersonName($p_projectreferee->person_id);
				$my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'Created new projectreferee data: %1$s,%2$s',"</span><strong>$dPerson->lastname","$dPerson->firstname</strong>");
				$my_text .= '<br />';
			}
			$insertID = $this->_db->insertid();
			if ( !$insertID )
			{
			$my_text .= '<span style="color:red"><strong>';
			$my_text .= JText::sprintf(	'Now _db->insertid(): %1$s',"$insertID</strong>");
			$my_text .= '</span><br />';
      $query = "SELECT id
      from #__joomleague_project_referee
      where project_id = '$p_projectreferee->project_id'
      and project_position_id = '$p_projectreferee->project_position_id'
      and person_id = '$p_projectreferee->person_id'
      ";
      $this->_db->setQuery( $query );
      $insertID = $this->_db->loadResult();
      }
      
			$this->_convertProjectRefereeID[$oldID] = $insertID;
		    
        $my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'OldID / NewID: %1$s,%2$s',"</span><strong>$oldID","$insertID</strong>");
				$my_text .= '<br />';
    }
		
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_PROJECTREFEREE_DATA:']=$my_text;
		return true;
	}  
  
  private function _importProjectpositions()
	{
		$my_text='';
		if (!isset($this->_datas['projectposition']) || count($this->_datas['projectposition'])==0){return true;}

		if (!isset($this->_datas['position']) || count($this->_datas['position'])==0){return true;}
		if ((!isset($this->_newpositionsid) || count($this->_newpositionsid)==0) &&
			(!isset($this->_dbpositionsid) || count($this->_dbpositionsid)==0)){return true;}

		foreach ($this->_datas['projectposition'] as $key => $projectposition)
		{
			$import_projectposition=$this->_datas['projectposition'][$key];
			$oldID = $this->_getDataFromObject($import_projectposition,'id');
			$p_projectposition =&  $this->getTable('projectposition');
			$p_projectposition->set('project_id',$this->_project_id);
			$oldPositionID = $this->_getDataFromObject($import_projectposition,'position_id');
			if (!isset($this->_convertPositionID[$oldPositionID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= JText::sprintf(	'Skipping import of ProjectPosition-ID [%1$s]. Old-PositionID: [%2$s]',
											"</span><strong>$oldID</strong><span style='color:red'>",
											"</span><strong>$oldPositionID</strong>").'<br />';
				continue;
			}
			$p_projectposition->set('position_id',$this->_convertPositionID[$oldPositionID]);
			if ($p_projectposition->store()===false)
			{
				$my_text .= 'error on ProjectPosition import: ';
				$my_text .= '#'.$oldID.'#';
				$my_text .= "<br />Error: _importProjectpositions<br />#$my_text#<br />#<pre>".print_r($p_projectposition,true).'</pre>#';
				$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_PROJECTPOSITION_DATA:']=$my_text;
				return false;
			}
			else
			{
				$my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'Created new projectposition data: %1$s',
											'</span><strong>'.JText::_($this->_getObjectName('position',$p_projectposition->position_id)). ' [' . $p_projectposition->position_id . ']</strong>');
				$my_text .= '<br />';
			}
			$insertID = $this->_db->insertid();
			
      if ( !$insertID )
			{
			$my_text .= '<span style="color:red"><strong>';
			$my_text .= JText::sprintf(	'Now _db->insertid(): %1$s',"$insertID</strong>");
			$my_text .= '</span><br />';
      $query = "SELECT id
      from #__joomleague_project_position
      where project_id = '$p_projectposition->project_id'
      and position_id = '$p_projectposition->position_id'
      ";
      $this->_db->setQuery( $query );
      $insertID = $this->_db->loadResult();
      }
			
			$this->_convertProjectPositionID[$oldID] = $insertID;
			  $my_text .= '<span style="color:green">';
				$my_text .= JText::sprintf(	'OldID / NewID: %1$s,%2$s',"</span><strong>$oldID","$insertID</strong>");
				$my_text .= '<br />';
			
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_MATCH_PROJECTPOSITION_DATA:']=$my_text;
		return true;
	}
    
  private function _getPersonName($person_id)
	{
		$query='SELECT lastname,firstname FROM #__joomleague_person WHERE id='.(int)$person_id;
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($this->_db->getAffectedRows())
		{
			$result=$this->_db->loadObject();
			return $result;
		}
	}
  
    private function _importParentPositions()
	{
		$my_text='';
		if (!isset($this->_datas['parentposition']) || count($this->_datas['parentposition'])==0){return true;}
		if ((!isset($this->_newparentpositionsid) || count($this->_newparentpositionsid)==0) &&
			(!isset($this->_dbparentpositionsid) || count($this->_dbparentpositionsid)==0)){return true;}
		if (!empty($this->_dbparentpositionsid))
		{
			foreach ($this->_dbparentpositionsid AS $key => $id)
			{
				$oldID=$this->_getDataFromObject($this->_datas['parentposition'][$key],'id');
				$this->_convertParentPositionID[$oldID]=$id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf(	'Using existing parentposition data: %1$s',
											'</span><strong>'.JText::_($this->_getObjectName('position',$id)).'</strong>');
				$my_text .= '<br />';
			}
		}

		if (!empty($this->_newparentpositionsid))
		{
			foreach ($this->_newparentpositionsid AS $key => $id)
			{
				$p_position =&  $this->getTable('position');
				$import_position=$this->_datas['parentposition'][$key];
				$oldID=$this->_getDataFromObject($import_position,'id');
				$alias=$this->_getDataFromObject($import_position,'alias');
				$p_position->set('name',trim($this->_newparentpositionsname[$key]));
				$p_position->set('parent_id',0);
				$p_position->set('persontype',$this->_getDataFromObject($import_position,'persontype'));
				$p_position->set('sports_type_id',$this->_sportstype_id);
				$p_position->set('published',1);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_position->set('alias',$alias);
				}
				else
				{
					$p_position->set('alias',JFilterOutput::stringURLSafe($this->_getDataFromObject($p_position,'name')));
				}
				$query="SELECT id,name FROM #__joomleague_position WHERE name='".addslashes(stripslashes($p_position->name))."' AND parent_id=0";
				$this->_db->setQuery($query);
				$this->_db->query();
				if ($this->_db->getAffectedRows())
				{
					$p_position->load($this->_db->loadResult());
					$this->_convertParentPositionID[$oldID]=$p_position->id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf('Using existing parent-position data: %1$s','</span><strong>'.JText::_($p_position->name).'</strong>');
					$my_text .= '<br />';
				}
				else
				{
					if ($p_position->store()===false)
					{
						$my_text .= 'error on parent-position import: ';
						$my_text .= $oldID;
						$my_text .= "<br />Error: _importParentPositions<br />#$my_text#<br />#<pre>".print_r($p_position,true).'</pre>#';
						$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_PARENTPOSITION_DATA:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$this->_db->insertid();
						$this->_convertParentPositionID[$oldID]=$insertID;
						$my_text .= '<span style="color:green">';
						$my_text .= JText::sprintf('Created new parent-position data: %1$s','</span><strong>'.JText::_($p_position->name).'</strong>');
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_PARENTPOSITION_DATA:']=$my_text;
		return true;
	}
    
    
    private function _importPositions()
	{
		$my_text='';
		if (!isset($this->_datas['position']) || count($this->_datas['position'])==0){return true;}
		if ((!isset($this->_newpositionsid) || count($this->_newpositionsid)==0) &&
			(!isset($this->_dbpositionsid) || count($this->_dbpositionsid)==0)){return true;}

		if (!empty($this->_dbpositionsid))
		{
			foreach ($this->_dbpositionsid AS $key => $id)
			{
				$oldID=$this->_getDataFromObject($this->_datas['position'][$key],'id');
				$this->_convertPositionID[$oldID]=$id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf(	'Using existing position data: %1$s',
											'</span><strong>'.JText::_($this->_getObjectName('position',$id)).'</strong>');
				$my_text .= '<br />';
			}
		}

		if (!empty($this->_newpositionsid))
		{
			foreach ($this->_newpositionsid AS $key => $id)
			{
				$p_position =&  $this->getTable('position');
				$import_position=$this->_datas['position'][$key];
				$oldID=$this->_getDataFromObject($import_position,'id');
				$alias=$this->_getDataFromObject($import_position,'alias');
				$p_position->set('name',trim($this->_newpositionsname[$key]));
				$oldParentPositionID=$this->_getDataFromObject($import_position,'parent_id');
				if (isset($this->_convertParentPositionID[$oldParentPositionID]))
				{
					$p_position->set('parent_id',$this->_convertParentPositionID[$oldParentPositionID]);
				} else { 
					$p_position->set('parent_id', 0);
				}
				//$p_position->set('parent_id',$this->_convertParentPositionID[(int)$this->_getDataFromObject($import_position,'parent_id')]);
				$p_position->set('persontype',$this->_getDataFromObject($import_position,'persontype'));
				$p_position->set('sports_type_id',$this->_sportstype_id);
				$p_position->set('published',1);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_position->set('alias',$alias);
				}
				else
				{
					$p_position->set('alias',JFilterOutput::stringURLSafe($this->_getDataFromObject($p_position,'name')));
				}
				$query="SELECT id,name FROM #__joomleague_position WHERE name='".addslashes(stripslashes($p_position->name))."' AND parent_id=$p_position->parent_id";
				$this->_db->setQuery($query);
				$this->_db->query();
				if ($this->_db->getAffectedRows())
				{
					$p_position->load($this->_db->loadResult());
					$this->_convertPositionID[$oldID]=$p_position->id;
					$my_text .= '<span style="color:orange">';
					$my_text .= JText::sprintf('Using existing position data: %1$s','</span><strong>'.JText::_($p_position->name).'</strong>');
					$my_text .= '<br />';
				}
				else
				{
					if ($p_position->store()===false)
					{
						$my_text .= 'error on position import: ';
						$my_text .= $oldID;
						$my_text .= "<br />Error: _importPositions<br />#$my_text#<br />#<pre>".print_r($p_position,true).'</pre>#';
						$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_POSITION_DATA:']=$my_text;
						return false;
					}
					else
					{
						$insertID = $this->_db->insertid();
						$this->_convertPositionID[$oldID] = $insertID;
						$my_text .= '<span style="color:green">';
						$my_text .= JText::sprintf('Created new position data: %1$s','</span><strong>'.JText::_($p_position->name).'</strong>');
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_POSITION_DATA:']=$my_text;
		return true;
	}
    
	private function _importPersons()
	{
	  global $mainframe, $option;

if ( $this->debug_info )
{
echo '_importPersons this->_dbpersonsid -> <br /><pre>~'.print_r($this->_dbpersonsid,true).'~</pre><br />';
echo '_importPersons this->_newpersonsid -> <br /><pre>~'.print_r($this->_newpersonsid,true).'~</pre><br />';
echo '_importPersons this->_newperson_lastname -> <br /><pre>~'.print_r($this->_newperson_lastname,true).'~</pre><br />';
echo '_importPersons this->_newperson_firstname -> <br /><pre>~'.print_r($this->_newperson_firstname,true).'~</pre><br />';
echo '_importPersons this->_newperson_nickname -> <br /><pre>~'.print_r($this->_newperson_nickname,true).'~</pre><br />';
echo '_importPersons this->_newperson_birthday -> <br /><pre>~'.print_r($this->_newperson_birthday,true).'~</pre><br />';
echo '_importPersons this->_newperson_knvbnr -> <br /><pre>~'.print_r($this->_newperson_knvbnr,true).'~</pre><br />';
echo '_importPersons this->_newperson_info -> <br /><pre>~'.print_r($this->_newperson_info,true).'~</pre><br />';
}
	  
		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($this->_dbpersonsid) || count($this->_dbpersonsid)==0)){return true;}

		$my_text='';
    if (!empty($this->_dbpersonsid))
		{
			foreach ($this->_dbpersonsid AS $key => $id)
			{
				$oldID=$this->_getDataFromObject($this->_datas['person'][$key],'id');
				$this->_convertPersonID[$oldID]=$id;
				$my_text .= '<span style="color:orange">';
				$my_text .= JText::sprintf(	'JL_ADMIN_DFBNET_IMPORT_USE_EXIST_PERSON_DATA_1: %1$s',
											'</span><strong>'.$this->_getObjectName('person',$id,"CONCAT(lastname,',',firstname,' - ',nickname,' - ',birthday) AS name").'</strong>');
				$my_text .= '<br />';
			}
		}
		
		if (!empty($this->_newpersonsid))
		{
			foreach ($this->_newpersonsid AS $key => $id)
			{
				$p_person =&  $this->getTable('person');
				$import_person=$this->_datas['person'][$key];
				$oldID=$this->_getDataFromObject($import_person,'id');
				$p_person->set('lastname',trim($this->_newperson_lastname[$key]));
				$p_person->set('firstname',trim($this->_newperson_firstname[$key]));
				$p_person->set('nickname',trim($this->_newperson_nickname[$key]));
				$p_person->set('birthday',$this->_newperson_birthday[$key]);
				
        $country = $this->_getDataFromObject($import_person,'country');
        
        if ( empty($country) )
        {
        $country = 'DEU';
        }
        
        $p_person->set('country',$country);
				
        $p_person->set('knvbnr',trim($this->_newperson_knvbnr[$key]));
        //$p_person->set('knvbnr',$this->_getDataFromObject($import_person,'knvbnr'));
        
				$p_person->set('height',$this->_getDataFromObject($import_person,'height'));
				$p_person->set('weight',$this->_getDataFromObject($import_person,'weight'));
				$p_person->set('picture',$this->_getDataFromObject($import_person,'picture'));
				$p_person->set('show_pic',$this->_getDataFromObject($import_person,'show_pic'));
				$p_person->set('show_persdata',$this->_getDataFromObject($import_person,'show_persdata'));
				$p_person->set('show_teamdata',$this->_getDataFromObject($import_person,'show_teamdata'));
				$p_person->set('show_on_frontend',$this->_getDataFromObject($import_person,'show_on_frontend'));
				
				$p_person->set('info',trim($this->_newperson_info[$key]));
        //$p_person->set('info',$this->_getDataFromObject($import_person,'info'));
        
				$p_person->set('notes',$this->_getDataFromObject($import_person,'notes'));
				$p_person->set('phone',$this->_getDataFromObject($import_person,'phone'));
				$p_person->set('mobile',$this->_getDataFromObject($import_person,'mobile'));
				$p_person->set('email',$this->_getDataFromObject($import_person,'email'));
				$p_person->set('website',$this->_getDataFromObject($import_person,'website'));
				$p_person->set('address',$this->_getDataFromObject($import_person,'address'));
				$p_person->set('zipcode',$this->_getDataFromObject($import_person,'zipcode'));
				$p_person->set('location',$this->_getDataFromObject($import_person,'location'));
				$p_person->set('state',$this->_getDataFromObject($import_person,'state'));
				$p_person->set('address_country',$this->_getDataFromObject($import_person,'address_country'));
				$p_person->set('extended',$this->_getDataFromObject($import_person,'extended'));
				
        if ($this->_importType!='persons')	// force position_id to be set to default if only persons are imported
				{
					if ($import_person->position_id > 0)
					{
						if (isset($this->_convertPositionID[(int)$this->_getDataFromObject($import_person,'position_id')]))
						{
							$p_person->set('position_id',(int)$this->_convertPositionID[(int)$this->_getDataFromObject($import_person,'position_id')]);
						}
					}
				}
				
				$alias=$this->_getDataFromObject($import_person,'alias');
				$aliasparts=array(trim($p_person->firstname),trim($p_person->lastname));
				$p_alias=JFilterOutput::stringURLSafe(implode(' ',$aliasparts));
				
        if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_person->set('alias',JFilterOutput::stringURLSafe($alias));
				}
				else
				{
					$p_person->set('alias',$p_alias);
				}
				
				$query="	SELECT * FROM #__joomleague_person
							WHERE	firstname='".addslashes(stripslashes($p_person->firstname))."' AND
									lastname='".addslashes(stripslashes($p_person->lastname))."' AND
									nickname='".addslashes(stripslashes($p_person->nickname))."' AND
									birthday='$p_person->birthday'";
				$this->_db->setQuery($query); $this->_db->query();
				
        if ($object=$this->_db->loadObject())
				{
				
 				  $p_person->set('id',$object->id);
 				  $p_person->set('knvbnr',trim($this->_newperson_knvbnr[$key]));
 				  $p_person->set('info',trim($this->_newperson_info[$key]));

 				  if ($p_person->store()===false)
 					{
 					$color = 'red';
 					}
 					else
 					{
           $color = 'green';
           }
				
					$this->_convertPersonID[$oldID]=$object->id;
					$nameStr=!empty($object->nickname) ? '['.$object->nickname.']' : '';
					$my_text .= '<span style="color:'.$color.'">';
					$my_text .= JText::sprintf(	'JL_ADMIN_DFBNET_IMPORT_USE_EXIST_PERSON_DATA_2: %1$s %2$s [%3$s] - %4$s',
												"</span><strong>$object->lastname</strong>",
												"<strong>$object->firstname</strong>",
												"<strong>$nameStr</strong>",
												"<strong>$object->birthday</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_person->store()===false)
					{
						$my_text .= 'error on person import: ';
						$my_text .= $p_person->lastname.'-';
						$my_text .= $p_person->firstname.'-';
						$my_text .= $p_person->nickname.'-';
						$my_text .= $p_person->birthday;
						$my_text .= "<br />Error: _importPersons<br />#$my_text#<br />#<pre>".print_r($p_person,true).'</pre>#';
						$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_PERSON_DATA:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$this->_db->insertid();
						$this->_convertPersonID[$oldID]=$insertID;
						$dNameStr=((!empty($p_person->lastname)) ?
									$p_person->lastname :
									'<span style="color:orange">'.JText::_('Has no lastname').'</span>');
						$dNameStr .= ','.((!empty($p_person->firstname)) ?
									$p_person->firstname.' - ' :
									'<span style="color:orange">'.JText::_('Has no firstname').' - </span>');
						$dNameStr .= ((!empty($p_person->nickname)) ? "'".$p_person->nickname."' - " : '');
						$dNameStr .= $p_person->birthday;

						$my_text .= '<span style="color:green">';
						$my_text .= JText::sprintf('JL_ADMIN_DFBNET_IMPORT_CREATED_NEW_PERSON_DATA: %1$s',"</span><strong>$dNameStr</strong>");
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['JL_ADMIN_DFBNET_IMPORT_GENERAL_PERSON_DATA:']=$my_text;
		return true;
	}
  
function _SetRoundDates($project)
	{
  global $mainframe, $option;
$document	=& JFactory::getDocument();
$mainframe	=& JFactory::getApplication();
  
  $query = "SELECT r.id as round_id
FROM #__joomleague_round AS r
WHERE r.project_id = " . (int) $project;

$this->_db->setQuery( $query );
$rounds = $this->_db->loadObjectList();

  foreach( $rounds as $rounddate)
{
$query = "SELECT min(m.match_date)
from #__joomleague_match as m
where m.round_id = '$rounddate->round_id'
";

$this->_db->setQuery( $query );
$von = $this->_db->loadResult();
$teile = explode(" ",$von);
$von = $teile[0];

$query = "SELECT max(m.match_date)
from #__joomleague_match as m
where m.round_id = '$rounddate->round_id'
";

$this->_db->setQuery( $query );
$bis = $this->_db->loadResult();
$teile = explode(" ",$bis);
$bis = $teile[0];

$tabelle = '#__joomleague_round';
// Objekt erstellen
$wert2 = new StdClass();

// Werte zuweisen
$wert2->id = $rounddate->round_id;
$wert2->round_date_first = $von;
$wert2->round_date_last = $bis;

// Neue Werte in den vorher erstellten Datenbankeintrag einf?gen
$this->_db->updateObject($tabelle, $wert2, 'id');


}

  
  }
              	
function _loadData()
	{
  global $mainframe, $option;
  $this->_data =  $mainframe->getUserState( $option . 'project', 0 );
   
  return $this->_data;
	}

function _initData()
	{
	global $mainframe, $option;
  $this->_data =  $mainframe->getUserState( $option . 'project', 0 );
  return $this->_data;
	}
	
	
private function _checkProject()
	{
		/*
		TO BE FIXED again
		$query="	SELECT id
					FROM #__joomleague_project
					WHERE name='$this->_name' AND league_id='$this->_league_id' AND season_id='$this->_season_id'";
		*/
		$query="SELECT id FROM #__joomleague_project WHERE name='".addslashes(stripslashes($this->_name))."'";
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($this->_db->getNumRows() > 0){return false;}
		return true;
	}	

}


?>

