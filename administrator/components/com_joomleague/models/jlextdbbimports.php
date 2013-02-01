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

/* tabellen leer machen
TRUNCATE TABLE `jos_joomleague_club`; 
TRUNCATE TABLE `jos_joomleague_team`;
TRUNCATE TABLE `jos_joomleague_person`;
TRUNCATE TABLE `jos_joomleague_playground`;
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

//require_once( JPATH_COMPONENT_SITE . DS. 'extensions' . DS. 'jlextdfbnetplayerimport' . DS. 'admin' . DS. 'helpers' . DS . 'helper.php' );
//require_once( JPATH_COMPONENT_SITE . DS. 'extensions' . DS. 'jlextdfbnetplayerimport' . DS. 'admin' . DS. 'helpers' . DS . 'ical.php' );
//require_once( JPATH_COMPONENT_SITE . DS. 'extensions' . DS. 'jlextdfbnetplayerimport' . DS. 'admin' . DS. 'helpers' . DS . 'iCal2csv.php' );
require_once ( JPATH_COMPONENT_SITE .DS . 'helpers' . DS . 'countries.php' );

// import JArrayHelper
jimport( 'joomla.utilities.array' );
jimport( 'joomla.utilities.arrayhelper' ) ;

// import JFile
jimport('joomla.filesystem.file');
jimport( 'joomla.utilities.utility' );

class JoomleagueModeljlextdbbimports extends JModel
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
  
private function dump_header($text)
	{
		echo "<h1>$text</h1>";
	}

	private function dump_variable($description, $variable)
	{
		echo "<b>$description</b><pre>".print_r($variable,true)."</pre>";
	}
  
  
function getData()
	{
  global $mainframe, $option;
  $mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();

  $lang = JFactory::getLanguage();
  $teile = explode("-",$lang->getTag());
  $country = Countries::convertIso2to3($teile[1]);  
  
  $post = JRequest::get('post');

$convert = array (
      '&nbsp;' => ''
  );
  
  
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

/*
echo 'post <br>';  
echo '<pre>';
print_r($post);
echo '</pre>';   
*/

$derlink = $post['dbblink'];
$teamart = $post['teamart'];

$chx = curl_init();
curl_setopt ($chx, CURLOPT_URL, $derlink );
curl_setopt ($chx, CURLOPT_TIMEOUT, 200);
curl_setopt ($chx, CURLOPT_RETURNTRANSFER,1);
$buffer = parse_url($derlink);
$resulttab = curl_exec ($chx);
curl_close ($chx);


// alle spiele der seite holen
$pattern1 = '!<table(.*?)class="sportView">(.*?)</table>!isU';
$pattern2 = '!<tr>(.*?)</tr>!isS';
$pattern3 = '!<td(.*?)>(.*?)</td>!isS';
$pattern4 = '!<td(.*?)class="sportViewTitle">(.*?)</td>!isS';

// RegEx mit preg_match_all() auswerten
preg_match_all($pattern1, $resulttab, $spielplan, PREG_PATTERN_ORDER);

$this->dump_variable("getData spielplan", $spielplan);




preg_match_all($pattern4, $spielplan[1][0], $liganame, PREG_PATTERN_ORDER);

$this->dump_variable("getData liganame", $liganame);




$projectname = utf8_encode (trim(strip_tags($liganame[2][0])));


$this->dump_variable("getData projectname", $projectname);

$spielplan[2][0] = str_replace(array_keys($convert), array_values($convert), $spielplan[2][0] );
preg_match_all($pattern2, $spielplan[2][0], $matches, PREG_PATTERN_ORDER);

$this->dump_variable("getData matches", $matches);



$lfdnumber = 0;
$lfdnumbermatch = 0;
$lfdnumberteam = 1;
$lfdnumberplayground = 1;
$start = 6;
$spielplan = false;
$team1_result_split = '';
$team2_result_split = '';

if (preg_match("/SpielplanViewPublic/i", $derlink)) 
{
$start = 7;
$spielplan = true;
}

$this->dump_variable("getData start ab position", $start);


for($a=$start; $a < sizeof($matches[1]); $a++ )
{

if ( empty($lfdnumber) )
  {
  
  $temp = new stdClass();
  $temp->name = $projectname;
  $temp->exportRoutine = '2010-09-19 23:00:00';  
  $this->_datas['exportversion'] = $temp;
  
  $temp = new stdClass();
  $temp->name = preg_replace('/\D/', '', $projectname);
  $this->_datas['season'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $projectname;
  $temp->alias = $projectname;
  $temp->short_name = $projectname;
  $temp->middle_name = $projectname;
  $temp->country = $country;
  $this->_datas['league'] = $temp;
  
  $temp = new stdClass();
  $temp->id = 1;
  $temp->name = 'Basketball';
  $this->_datas['sportstype'] = $temp;
  
  $temp = new stdClass();
  $temp->name = $projectname;
  $temp->sports_type_id = 1;
  $temp->points_after_regular_time = '2,1,0';
  $temp->game_parts = 4;
  $temp->serveroffset = 0;
  $temp->project_type = 'SIMPLE_LEAGUE';
  $temp->extended = 'JL_EXT_DBB_XML_LINK='.$derlink;
  $this->_datas['project'] = $temp;
  }
  
preg_match_all($pattern3, $matches[1][$a], $paarung, PREG_PATTERN_ORDER);

$this->dump_variable("getData paarung", $paarung);
$this->dump_variable("getData spieltag", trim(strip_tags($paarung[2][0])));
$this->dump_variable("getData nummer", trim(strip_tags($paarung[2][1])));
$this->dump_variable("getData datum", trim(strip_tags($paarung[2][2])));
$this->dump_variable("getData heim", trim(strip_tags($paarung[2][3])));
$this->dump_variable("getData gast", trim(strip_tags($paarung[2][4])));
$this->dump_variable("getData ergebnis", trim(strip_tags($paarung[2][5])));

$valuematchday = trim(strip_tags($paarung[2][0]));

if ( is_numeric($valuematchday) )
{

if ( $spielplan)
{
}
else
{
if ( isset($paarung[2][6]) )
{
$teile = explode(":",trim(strip_tags($paarung[2][6]))); 
$team1_result_split[] =  trim($teile[0]);
$team2_result_split[] =  trim($teile[1]);   
}
if ( isset($paarung[2][7]) )
{
$teile = explode(":",trim(strip_tags($paarung[2][7]))); 
$team1_result_split[] =  trim($teile[0]);
$team2_result_split[] =  trim($teile[1]);   
}
if ( isset($paarung[2][8]) )
{
$teile = explode(":",trim(strip_tags($paarung[2][8]))); 
$team1_result_split[] =  trim($teile[0]);
$team2_result_split[] =  trim($teile[1]);   
}
}

$tempdatum = trim(strip_tags($paarung[2][2]));
$teile = explode(" ",$tempdatum );
$matchdate = strtotime($teile[0]);
$matchtime = $teile[1];

$valueheim = utf8_encode (trim(strip_tags($paarung[2][3])));
$valuegast = utf8_encode (trim(strip_tags($paarung[2][4])));
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

  $valuematchday = trim(strip_tags($paarung[2][0]));
  if ( empty($valuematchday) )
  {
    $valuematchday = 1;
  }
  
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


$tempmatch = new stdClass();
$tempmatch->id = $lfdnumbermatch;
$tempmatch->match_number = trim(strip_tags($paarung[2][1]));
$tempmatch->published = 1;
$tempmatch->count_result = 1;
$tempmatch->show_report = 1;  
$tempmatch->team1_result = trim($teile[0]);
$tempmatch->team2_result = trim($teile[1]);

if ( $spielplan)
{
$teile[0] = '';
$teile[1] = '';
//$valueplayground = utf8_encode (trim(strip_tags($paarung[2][5])));
$valueplayground = trim(strip_tags($paarung[2][5]));

if (  array_key_exists($valueplayground, $exportplaygroundtemp) ) 
{
$tempmatch->playground_id = $exportplaygroundtemp[$valueplayground];
}
else
{
$exportplaygroundtemp[$valueplayground] = $lfdnumberplayground;
$tempmatch->playground_id = $lfdnumberplayground;
$lfdnumberplayground++;
}

}
else
{
$teile = explode(":",trim(strip_tags($paarung[2][5])));
$tempmatch->playground_id = 0;
}




if ( is_array($team1_result_split) )
{
$tempmatch->team1_result_split = implode(";",$team1_result_split);    
}
if ( is_array($team2_result_split) )
{
$tempmatch->team2_result_split = implode(";",$team2_result_split);    
}
$tempmatch->summary = '';
$tempmatch->match_date = date('Y-m-d', $matchdate)." ".$matchtime.':00';
$tempmatch->projectteam1_id = $exportteamstemp[$valueheim];
$tempmatch->projectteam2_id = $exportteamstemp[$valuegast];
$tempmatch->round_id = $valuematchday;
$exportmatch[] = $tempmatch;

unset($team1_result_split);
unset($team2_result_split);

}

$lfdnumbermatch++;
$lfdnumber++;
}

// beenden
//exit;

// playgrounds verarbeiten
foreach ( $exportplaygroundtemp as $key => $value )
{
// playground
$temp = new stdClass();
$temp->id = $value;
$temp->name = $key;
$temp->middle_name = $key;
$temp->short_name = $key;
$exportplayground[] = $temp;

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
$temp->info = $teamart;
$temp->extended = '';
$exportteams[] = $temp;

$standard_playground = '';
$standard_playground_nummer = 0;

// club
$temp = new stdClass();
$temp->id = $value;
$temp->name = preg_replace('/\d/', '', $key);;
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

// daten übergeben
// damit die spieltage in der richtigen reihenfolge angelegt werden
ksort($exportround);
$this->_datas['round'] = array_merge($exportround);
$this->_datas['match'] = array_merge($exportmatch);
$this->_datas['team'] = array_merge($exportteams);
$this->_datas['projectteam'] = array_merge($exportprojectteams);
$this->_datas['club'] = array_merge($exportclubs);
$this->_datas['playground'] = array_merge($exportplayground);

/*
echo 'getData _datas->match<br>';
echo '<pre>';
print_r($this->_datas['match']);
echo '</pre><br>';
*/
  
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


// set sportstype data of project
if ( isset($this->_datas['sportstype']) )
{
$output .= $this->_addToXml($this->_setSportsType($this->_datas['sportstype']));
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
	 * _setLeagueData
	 *
	 * set the league data from the joomleague_league table
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
      
}

?>  