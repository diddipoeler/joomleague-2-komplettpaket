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

//require_once( JLG_PATH_ADMIN . DS. 'helpers' . DS . 'helper.php' );
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

/*
# tab delimited, and encoding conversion
$csv = new JLparseCSV();
//$csv->encoding('UTF-16', 'UTF-8');
$csv->delimiter = $delimiter;
$csv->parse($file);
//print_r($csv->data);
*/

//$csv->delimiter = $delimiter;
//$csvdata = parseCSV::parse($file);

/*
$csvdata = new parseCSV();
$csvdata->delimiter = $delimiter;
$csvdata->parse($file);
*/

//$mainframe->enqueueMessage(JText::_('csv result<br><pre>'.print_r($csvdata->data,true).'</pre>'   ),'');
    
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


/*
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
*/
   
}    
// kalender file vom bfv ende  






  
  
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
// spielerdatei    
$temp = new stdClass();
$temp->name = 'playerfile';
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

if ( sizeof($csv->data) == 0 )
{
$mainframe->enqueueMessage(JText::_('falsches Dateiformat'),'Error');
$importcsv = false;    
}
else
{
//$mainframe->enqueueMessage(JText::_('result<br><pre>'.print_r($csv->data[0],true).'</pre>'   ),'');    
//$mainframe->enqueueMessage(JText::_('result<br><pre>'.print_r($csv->data,true).'</pre>'   ),'');
$importcsv = true;
}

	
$lfdnumber = 0;
$lfdnumberteam = 1;
$lfdnumbermatch = 1;
$lfdnumberplayground = 1;
$lfdnumberperson = 1;
$lfdnumbermatchreferee = 1;

// anfang schleife csv file  
  for($a=0; $a < sizeof($csv->data); $a++  )
  {
  
  if ( empty($lfdnumber) )
  {
  
/*
[0] => Array
        (
1            [Datum] => 20.02.2013
2            [Uhrzeit] => 11:00
3            [Saison] => 12/13
4            [Verband] => Schleswig-Holsteinischer Fußballverband
5            [MannschaftsartID] => 016
6            [Mannschaftsart] => C-Junioren
7            [SpielklasseID] => 054
8            [Spielklasse] => Kreisklasse A
9            [SpielgebietID] => 032
10            [Spielgebiet] => Kreis Nordfriesland
11            [Rahmenspielplan] => 6
12            [Staffelnummer] => 001
13            [Staffel] => C - Junioren KKA 11er II
14            [Staffelkennung] => 040718
15            [Staffelleiter] => Stöhrmann, Wolfgang
16            [Spieldatum] => 10.03.2013
17            [Wochentag] => Sonntag
18            [Spieltag] => 1
19            [Schlüsseltag] => 1
20            [Heimmannschaft] => FSV Wyk-Föhr II
21            [Gastmannschaft] => TSV Amrum
22            [freigegeben] => Nein
23            [Spielstätte] => verlegt auf So, 24.03.2013
24            [Spielleitung] => 
25            [Assistent 1] => 
26            [Assistent 2] => 
27            [verlegtWochentag] => Sonntag
28            [verlegtSpieldatum] => 24.03.2013
29            [verlegtUhrzeit] => 14:00
        )  
*/  
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Spalte4'];
  $temp->exportRoutine = '2010-09-19 23:00:00';  
  $this->_datas['exportversion'] = $temp;
  

  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Spalte3'];
  $this->_datas['season'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Spalte13'].' '.$csv->data[$a]['Spalte12'];
  $temp->country = $country;
  $this->_datas['league'] = $temp;
  
  $temp = new stdClass();
  $temp->id = 1;
  $temp->name = 'COM_JOOMLEAGUE_ST_SOCCER';
  $this->_datas['sportstype'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $csv->data[$a]['Spalte13'].' '.$csv->data[$a]['Spalte3'];
  $temp->serveroffset = 0;
  $temp->start_time = '15:30';
  $temp->start_date = '0000-00-00';
  $temp->sports_type_id = 1;
  $temp->project_type = 'SIMPLE_LEAGUE';
  $temp->staffel_id = $csv->data[$a]['Spalte14'];
  $this->_datas['project'] = $temp;
  }
  
  if ( empty($csv->data[$a]['Spalte28']) )
  { 
    
  $valuematchday = $csv->data[$a]['Spalte19'];
  
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
$valueheim = $csv->data[$a]['Spalte21'];


if ( $valueheim != 'Spielfrei' )
{
if (in_array($valueheim, $exportteamstemp)) 
{
// echo $valueheim." <- enthalten<br>";
$exportclubsstandardplayground[$valueheim] = $csv->data[$a]['Spalte24'];
}
else
{
// echo $valueheim." <- nicht enthalten<br>";
$exportclubsstandardplayground[$valueheim] = $csv->data[$a]['Spalte24'];

$exportteamstemp[] = $valueheim;
$temp = new stdClass();
$temp->id = $lfdnumberteam;
$temp->club_id = $lfdnumberteam;
$temp->name = $valueheim;
$temp->middle_name = $valueheim;
$temp->short_name = $valueheim;
$temp->info = $csv->data[$a]['Spalte6'];
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
$valuegast = $csv->data[$a]['Spalte22'];


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
$temp->info = $csv->data[$a]['Spalte6'];
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
$valueplayground = $csv->data[$a]['Spalte24'];
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
//if ( empty($csv->data[$a]['Spalte28']) )
//{	
$exportplayground[] = $temp;
//}
$lfdnumberplayground++;
}
}
  
$valueperson = $csv->data[$a]['Spalte25'];
$valueperson1 = $csv->data[$a]['Spalte26'];
$valueperson2 = $csv->data[$a]['Spalte27'];

//if (in_array($valueperson, $exportpersonstemp)) 
if (array_key_exists($valueperson, $exportpersonstemp))
{

if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
|| $csv->data[$a]['Spalte22'] == 'Spielfrei' )
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

if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
|| $csv->data[$a]['Spalte22'] == 'Spielfrei')
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

if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
|| $csv->data[$a]['Spalte22'] == 'Spielfrei')
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

if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
|| $csv->data[$a]['Spalte22'] == 'Spielfrei')
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

if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
|| $csv->data[$a]['Spalte22'] == 'Spielfrei')
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


if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
|| $csv->data[$a]['Spalte22'] == 'Spielfrei')
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
  if ( $csv->data[$a]['Spalte21'] == 'Spielfrei' 
  || $csv->data[$a]['Spalte22'] == 'Spielfrei')
  {
  // nichts machen
  }
  else
  {
  $round_id = $csv->data[$a]['Spalte19'];
  $tempmatch = new stdClass();
  $tempmatch->id = $lfdnumbermatch;
  $tempmatch->round_id = $round_id;
  $datetime = strtotime($csv->data[$a]['Spalte16']);
  $tempmatch->match_date = date('Y-m-d', $datetime)." ".$csv->data[$a]['Spalte17'];
  
  if ( $csv->data[$a]['Spalte29'] )
  {
  $datetime = strtotime($csv->data[$a]['Spalte29']);
  $tempmatch->match_date_verlegt = date('Y-m-d', $datetime)." ".$csv->data[$a]['Spalte30'];
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
     
    if ( $datetime_first > $datetime )
    {
        $exportround[$round_id]->round_date_first = date('Y-m-d', $datetime);
    }
    if ( $datetime_last < $datetime )
    {
        $exportround[$round_id]->round_date_last = date('Y-m-d', $datetime);
    }
    
    
  }
  
	//$tempmatch->match_number = $csv->data[$a]['Spielkennung'];
    $tempmatch->match_number = $lfdnumbermatch;
	$tempmatch->published = 1;
	$tempmatch->count_result = 1;
	$tempmatch->show_report = 1;
	$tempmatch->projectteam1_id = 0;
	$tempmatch->projectteam2_id = 0;
    
    $tempmatch->projectteam1_dfbnet = $csv->data[$a]['Spalte21'];    
    
    
	
    $tempmatch->projectteam2_dfbnet = $csv->data[$a]['Spalte22'];    
    
	
	$tempmatch->team1_result = '';
	$tempmatch->team2_result = '';
	$tempmatch->summary = '';

  if (array_key_exists($valueplayground, $exportplaygroundtemp))
  {
  $tempmatch->playground_id = $exportplaygroundtemp[$valueplayground];
  }

//if ( empty($csv->data[$a]['Spalte28']) )
//  {	
if ( array_key_exists($tempmatch->match_number,$temp_match_number) )
{
  $exportmatch[] = $tempmatch;
  }
  else
  {
  $temp_match_number[$tempmatch->match_number] = $tempmatch->match_number;
  $exportmatch[] = $tempmatch;
  }
//}
  
  $lfdnumbermatch++; 
  $lfdnumber++;
  }
  
  }
  
  }	
// ende schleife csv file

  
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

if ( $importcsv )
{
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
}

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





}

}


    
//  echo '<pre>';
//  print_r($this->_datas);
//  echo '</pre>';

if ( $whichfile == 'playerfile' )
{
/**
 * das ganze für den standardimport aufbereiten
 */
$output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
// open the project
$output .= "<project>\n";
// set the version of JoomLeague
$output .= $this->_addToXml($this->_setJoomLeagueVersion());
// set the person data
if ( isset($this->_datas['person']) )
{
$mainframe->enqueueMessage(JText::_('personen daten '.'generiert'),'');  
$output .= $this->_addToXml($this->_setXMLData($this->_datas['person'], 'Person'));
}    
// close the project
$output .= '</project>';
// mal als test
$xmlfile = $output;
$file = JPATH_SITE.DS.'tmp'.DS.'joomleague_import.jlg';
JFile::write($file, $xmlfile);    
$this->import_version='NEW';
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
$mainframe->enqueueMessage(JText::_('project daten '.'generiert'),'');    
$output .= $this->_addToXml($this->_setProjectData($this->_datas['project']));
}

// set the rounds sportstype
if ( isset($this->_datas['sportstype']) )
{
$mainframe->enqueueMessage(JText::_('sportstype daten '.'generiert'),'');      
$output .= $this->_addToXml($this->_setSportsType($this->_datas['sportstype']));    
}

// set league data of project
if ( isset($this->_datas['league']) )
{
$mainframe->enqueueMessage(JText::_('league daten '.'generiert'),'');    
$output .= $this->_addToXml($this->_setLeagueData($this->_datas['league']));
}
// set season data of project
if ( isset($this->_datas['season']) )
{
$mainframe->enqueueMessage(JText::_('season daten '.'generiert'),'');    
$output .= $this->_addToXml($this->_setSeasonData($this->_datas['season']));
}
// set the rounds data
if ( isset($this->_datas['round']) )
{
$mainframe->enqueueMessage(JText::_('round daten '.'generiert'),'');    
$output .= $this->_addToXml($this->_setXMLData($this->_datas['round'], 'Round') );
}
// set the teams data
if ( isset($this->_datas['team']) )
{
    $mainframe->enqueueMessage(JText::_('team daten '.'generiert'),''); 
$output .= $this->_addToXml($this->_setXMLData($this->_datas['team'], 'JL_Team'));
}
// set the clubs data
if ( isset($this->_datas['club']) )
{
    $mainframe->enqueueMessage(JText::_('club daten '.'generiert'),'');  
$output .= $this->_addToXml($this->_setXMLData($this->_datas['club'], 'Club'));
}
// set the matches data
if ( isset($this->_datas['match']) )
{
    $mainframe->enqueueMessage(JText::_('match daten '.'generiert'),'');  
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
	 * _setSportsType
	 *
	 * set the SportsType
	 *
	 * @access private
	 * @since  1.5.5241
	 *
	 * @return array
	 */
	private function _setSportsType($sportstype)
	{
		
        if ( $sportstype )
        {
            $result[] = JArrayHelper::fromObject($sportstype);
			$result[0]['object'] = 'SportsType';
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
	
	
    
    
	
  private function _getObjectName($tableName,$id,$usedFieldName='')
	{
		$fieldName=($usedFieldName=='') ? 'name' : $usedFieldName;
		$query="SELECT $fieldName FROM #__joomleague_$tableName WHERE id=$id";
		$this->_db->setQuery($query);
		if ($result=$this->_db->loadResult()){return $result;}
		return JText::sprintf('Item with ID [%1$s] not found inside [#__joomleague_%2$s]',$id,$tableName)." $query";
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

