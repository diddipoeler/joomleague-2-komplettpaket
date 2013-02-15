<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueViewjlextindividualsportringen extends JLGView
{
	function display($tpl=null)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
        $model =& $this->getModel('jlextindividualsportringen');

		if ($this->getLayout()=='form')
		{
			$this->_displayForm($tpl);
			return;
		}
        
        if ($this->getLayout()=='default_edit')
    	{
			$this->_displayDefaultEdit($tpl);
			return;
		}

		if ($this->getLayout()=='info')
		{
			$this->_displayInfo($tpl);
			return;
		}

		if ($this->getLayout()=='selectpage')
		{
			$this->_displaySelectpage($tpl);
			return;
		}

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JL_ADMIN_EXTENSION_XML_IMPORT_TITLE_1_3'),'generic.png');
		JToolBarHelper::help('screen.joomleague',true);

		$uri =& JFactory::getURI();
		$config =& JComponentHelper::getParams('com_media');
		$post=JRequest::get('post');
		$files=JRequest::get('files');

        $revisionDate='2012-02-12 - 12:00';
    	$this->assignRef('revisionDate',$revisionDate);
		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('config',$config);
        
        $this->assignRef('tmpfiles',$model->getTmpFiles());

$javascript = 'onchange=document.adminForm.submit()';
$images = array(  JHTML::_('select.option',  '', '- '. JText::_( 'vorhandene Dateien' ).' -' ) );
foreach ( $this->tmpfiles as $key => $value ) 
{
$images[] = JHTML::_('select.option',  $value ,  $value );
}
$images = JHTML::_('select.genericlist',  $images, 'serverfile', 'class="inputbox" size="10" '. $javascript, 'value', 'text', '' );
        $this->assignRef('tmpfiles',$images );
        
		parent::display($tpl);
	}

public function dump_variable($name, $value)
{
	echo "<h2>$name</h2>";
	echo "<pre>".print_r($value, true)."</pre>";
}

	private function _displayForm($tpl)
	{
		$mtime=microtime();
		$mtime=explode(" ",$mtime);
		$mtime=$mtime[1] + $mtime[0];
		$starttime=$mtime;
		$option			= 'com_joomleague';
		$mainframe		=& JFactory::getApplication();
		$document		=& JFactory::getDocument();
		//$document->addScript('/administrator/components/com_joomleague/assets/js/JL_import.js');
		$db				=& JFactory::getDBO();
		$uri			=& JFactory::getURI();
		$model			=& $this->getModel('jlextindividualsportringen');
		$data			= $model->getData();
		$uploadArray	= $mainframe->getUserState($option.'uploadArray',array());
		$value  		= isset($data['project']->serveroffset) ? $data['project']->serveroffset: null;
		if(!strlen($value)) {
			$conf =& JFactory::getConfig();
			$value = $conf->getValue('config.offset');
		}
		// LOCALE SETTINGS
		$timezones = array (
		JHTML::_('select.option', -12, JText::_('(UTC -12:00) International Date Line West')),
		JHTML::_('select.option', -11, JText::_('(UTC -11:00) Midway Island, Samoa')),
		JHTML::_('select.option', -10, JText::_('(UTC -10:00) Hawaii')),
		JHTML::_('select.option', -9.5, JText::_('(UTC -09:30) Taiohae, Marquesas Islands')),
		JHTML::_('select.option', -9, JText::_('(UTC -09:00) Alaska')),
		JHTML::_('select.option', -8, JText::_('(UTC -08:00) Pacific Time (US &amp; Canada)')),
		JHTML::_('select.option', -7, JText::_('(UTC -07:00) Mountain Time (US &amp; Canada)')),
		JHTML::_('select.option', -6, JText::_('(UTC -06:00) Central Time (US &amp; Canada), Mexico City')),
		JHTML::_('select.option', -5, JText::_('(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima')),
		JHTML::_('select.option', -4, JText::_('(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz')),
		JHTML::_('select.option', -4.5, JText::_('(UTC -04:30) Venezuela')),
		JHTML::_('select.option', -3.5, JText::_('(UTC -03:30) St. John\'s, Newfoundland, Labrador')),
		JHTML::_('select.option', -3, JText::_('(UTC -03:00) Brazil, Buenos Aires, Georgetown')),
		JHTML::_('select.option', -2, JText::_('(UTC -02:00) Mid-Atlantic')),
		JHTML::_('select.option', -1, JText::_('(UTC -01:00) Azores, Cape Verde Islands')),
		JHTML::_('select.option', 0, JText::_('(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca')),
		JHTML::_('select.option', 1, JText::_('(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris')),
		JHTML::_('select.option', 2, JText::_('(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa')),
		JHTML::_('select.option', 3, JText::_('(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg')),
		JHTML::_('select.option', 3.5, JText::_('(UTC +03:30) Tehran')),
		JHTML::_('select.option', 4, JText::_('(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi')),
		JHTML::_('select.option', 4.5, JText::_('(UTC +04:30) Kabul')),
		JHTML::_('select.option', 5, JText::_('(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent')),
		JHTML::_('select.option', 5.5, JText::_('(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo')),
		JHTML::_('select.option', 5.75, JText::_('(UTC +05:45) Kathmandu')),
		JHTML::_('select.option', 6, JText::_('(UTC +06:00) Almaty, Dhaka')),
		JHTML::_('select.option', 6.5, JText::_('(UTC +06:30) Yagoon')),
		JHTML::_('select.option', 7, JText::_('(UTC +07:00) Bangkok, Hanoi, Jakarta')),
		JHTML::_('select.option', 8, JText::_('(UTC +08:00) Beijing, Perth, Singapore, Hong Kong')),
		JHTML::_('select.option', 8.75, JText::_('(UTC +08:00) Ulaanbaatar, Western Australia')),
		JHTML::_('select.option', 9, JText::_('(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk')),
		JHTML::_('select.option', 9.5, JText::_('(UTC +09:30) Adelaide, Darwin, Yakutsk')),
		JHTML::_('select.option', 10, JText::_('(UTC +10:00) Eastern Australia, Guam, Vladivostok')),
		JHTML::_('select.option', 10.5, JText::_('(UTC +10:30) Lord Howe Island (Australia)')),
		JHTML::_('select.option', 11, JText::_('(UTC +11:00) Magadan, Solomon Islands, New Caledonia')),
		JHTML::_('select.option', 11.5, JText::_('(UTC +11:30) Norfolk Island')),
		JHTML::_('select.option', 12, JText::_('(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka')),
		JHTML::_('select.option', 12.75, JText::_('(UTC +12:45) Chatham Island')),
		JHTML::_('select.option', 13, JText::_('(UTC +13:00) Tonga')),
		JHTML::_('select.option', 14, JText::_('(UTC +14:00) Kiribati')),);

		$lists['serveroffset']= JHTML::_('select.genericlist', $timezones, 'serverOffset', ' class="inputbox"', 'value', 'text', $value);
		
		$countries=new Countries();
		$this->assignRef('uploadArray',$uploadArray);
		$this->assignRef('starttime',$starttime);
		$this->assignRef('countries',$countries->getCountries());
		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('xml', $data);
		$this->assignRef('leagues',$model->getLeagueList());
		$this->assignRef('seasons',$model->getSeasonList());
		$this->assignRef('sportstypes',$model->getSportsTypeList());
		$this->assignRef('admins',$model->getUserList(true));
		$this->assignRef('editors',$model->getUserList(false));
		$this->assignRef('templates',$model->getTemplateList());
		$this->assignRef('teams',$model->getTeamList());
		$this->assignRef('clubs',$model->getClubList());
		$this->assignRef('events',$model->getEventList());
		$this->assignRef('positions',$model->getPositionList());
		$this->assignRef('parentpositions',$model->getParentPositionList());
		$this->assignRef('playgrounds',$model->getPlaygroundList());
		$this->assignRef('persons',$model->getPersonList());
		$this->assignRef('statistics',$model->getStatisticList());
		$this->assignRef('OldCountries',$model->getCountryByOldid());
		$this->assignRef('import_version',$model->import_version);
		$this->assignRef('lists',$lists);
		
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JL_ADMIN_EXTENSION_XML_IMPORT_TITLE_2_3'),'generic.png');
		//                       task    image  mouseover_img           alt_text_for_image              check_that_standard_list_item_is_checked
		JToolBarHelper::custom('insert','upload','upload',Jtext::_('JL_ADMIN_XML_IMPORT_START_BUTTON'), false); // --> bij clicken op import wordt de insert view geactiveerd
		JToolBarHelper::back();
		JToolBarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}

    private function _displayDefaultEdit($tpl)
    {
		$mtime=microtime();
		$mtime=explode(" ",$mtime);
		$mtime=$mtime[1] + $mtime[0];
		$starttime=$mtime;
		$option			= 'com_joomleague';
		$mainframe		=& JFactory::getApplication();
		$document		=& JFactory::getDocument();
		//$document->addScript('/administrator/components/com_joomleague/assets/js/JL_import.js');
		$db				=& JFactory::getDBO();
		$uri			=& JFactory::getURI();
		$model			=& $this->getModel('jlextindividualsportringen');
		$data			= $model->getData();
		$uploadArray	= $mainframe->getUserState($option.'uploadArray',array());
        $serverfile    = $mainframe->getUserState($option.'serverfile');
		$value  		= isset($data['project']->serveroffset) ? $data['project']->serveroffset: null;
		if(!strlen($value)) {
			$conf =& JFactory::getConfig();
			$value = $conf->getValue('config.offset');
		}
		// LOCALE SETTINGS
		$timezones = array (
		JHTML::_('select.option', -12, JText::_('(UTC -12:00) International Date Line West')),
		JHTML::_('select.option', -11, JText::_('(UTC -11:00) Midway Island, Samoa')),
		JHTML::_('select.option', -10, JText::_('(UTC -10:00) Hawaii')),
		JHTML::_('select.option', -9.5, JText::_('(UTC -09:30) Taiohae, Marquesas Islands')),
		JHTML::_('select.option', -9, JText::_('(UTC -09:00) Alaska')),
		JHTML::_('select.option', -8, JText::_('(UTC -08:00) Pacific Time (US &amp; Canada)')),
		JHTML::_('select.option', -7, JText::_('(UTC -07:00) Mountain Time (US &amp; Canada)')),
		JHTML::_('select.option', -6, JText::_('(UTC -06:00) Central Time (US &amp; Canada), Mexico City')),
		JHTML::_('select.option', -5, JText::_('(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima')),
		JHTML::_('select.option', -4, JText::_('(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz')),
		JHTML::_('select.option', -4.5, JText::_('(UTC -04:30) Venezuela')),
		JHTML::_('select.option', -3.5, JText::_('(UTC -03:30) St. John\'s, Newfoundland, Labrador')),
		JHTML::_('select.option', -3, JText::_('(UTC -03:00) Brazil, Buenos Aires, Georgetown')),
		JHTML::_('select.option', -2, JText::_('(UTC -02:00) Mid-Atlantic')),
		JHTML::_('select.option', -1, JText::_('(UTC -01:00) Azores, Cape Verde Islands')),
		JHTML::_('select.option', 0, JText::_('(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca')),
		JHTML::_('select.option', 1, JText::_('(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris')),
		JHTML::_('select.option', 2, JText::_('(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa')),
		JHTML::_('select.option', 3, JText::_('(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg')),
		JHTML::_('select.option', 3.5, JText::_('(UTC +03:30) Tehran')),
		JHTML::_('select.option', 4, JText::_('(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi')),
		JHTML::_('select.option', 4.5, JText::_('(UTC +04:30) Kabul')),
		JHTML::_('select.option', 5, JText::_('(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent')),
		JHTML::_('select.option', 5.5, JText::_('(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo')),
		JHTML::_('select.option', 5.75, JText::_('(UTC +05:45) Kathmandu')),
		JHTML::_('select.option', 6, JText::_('(UTC +06:00) Almaty, Dhaka')),
		JHTML::_('select.option', 6.5, JText::_('(UTC +06:30) Yagoon')),
		JHTML::_('select.option', 7, JText::_('(UTC +07:00) Bangkok, Hanoi, Jakarta')),
		JHTML::_('select.option', 8, JText::_('(UTC +08:00) Beijing, Perth, Singapore, Hong Kong')),
		JHTML::_('select.option', 8.75, JText::_('(UTC +08:00) Ulaanbaatar, Western Australia')),
		JHTML::_('select.option', 9, JText::_('(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk')),
		JHTML::_('select.option', 9.5, JText::_('(UTC +09:30) Adelaide, Darwin, Yakutsk')),
		JHTML::_('select.option', 10, JText::_('(UTC +10:00) Eastern Australia, Guam, Vladivostok')),
		JHTML::_('select.option', 10.5, JText::_('(UTC +10:30) Lord Howe Island (Australia)')),
		JHTML::_('select.option', 11, JText::_('(UTC +11:00) Magadan, Solomon Islands, New Caledonia')),
		JHTML::_('select.option', 11.5, JText::_('(UTC +11:30) Norfolk Island')),
		JHTML::_('select.option', 12, JText::_('(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka')),
		JHTML::_('select.option', 12.75, JText::_('(UTC +12:45) Chatham Island')),
		JHTML::_('select.option', 13, JText::_('(UTC +13:00) Tonga')),
		JHTML::_('select.option', 14, JText::_('(UTC +14:00) Kiribati')),);

		$lists['serveroffset']= JHTML::_('select.genericlist', $timezones, 'serverOffset', ' class="inputbox"', 'value', 'text', $value);
		
		$countries=new Countries();
        $revisionDate='2012-02-12 - 12:00';
        $this->assignRef('revisionDate',$revisionDate);
		$this->assignRef('uploadArray',$uploadArray);
        $this->assignRef('serverfile',$serverfile);
		$this->assignRef('starttime',$starttime);
		$this->assignRef('countries',$countries->getCountries());
		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('xml', $data);
		$this->assignRef('leagues',$model->getLeagueList());
		$this->assignRef('seasons',$model->getSeasonList());
		$this->assignRef('sportstypes',$model->getSportsTypeList());
		$this->assignRef('admins',$model->getUserList(true));
		$this->assignRef('editors',$model->getUserList(false));
		$this->assignRef('templates',$model->getTemplateList());
		$this->assignRef('teams',$model->getTeamList());
		$this->assignRef('clubs',$model->getClubList());
		$this->assignRef('events',$model->getEventList());
		$this->assignRef('positions',$model->getPositionList());
		$this->assignRef('parentpositions',$model->getParentPositionList());
		$this->assignRef('playgrounds',$model->getPlaygroundList());
		$this->assignRef('persons',$model->getPersonList());
		$this->assignRef('statistics',$model->getStatisticList());
		$this->assignRef('OldCountries',$model->getCountryByOldid());
		$this->assignRef('import_version',$model->import_version);
		$this->assignRef('lists',$lists);
		
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JL_ADMIN_EXTENSION_XML_IMPORT_TITLE_2_3'),'generic.png');
		//                       task    image  mouseover_img           alt_text_for_image              check_that_standard_list_item_is_checked
		JToolBarHelper::custom('insert','upload','upload',Jtext::_('JL_ADMIN_XML_IMPORT_START_BUTTON'), false); // --> bij clicken op import wordt de insert view geactiveerd
		JToolBarHelper::back();
		JToolBarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}
	private function _displayInfo($tpl)
	{
		$mtime=microtime();
		$mtime=explode(" ",$mtime);
		$mtime=$mtime[1] + $mtime[0];
		$starttime=$mtime;
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$post=JRequest::get('post');
		$model =& $this->getModel('jlextindividualsportringen');

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JL_ADMIN_XML_IMPORT_TITLE_3_3'),'generic.png');
		//JToolBarHelper::back();
		JToolBarHelper::help('screen.joomleague',true);

		$this->assignRef('starttime',$starttime);
		$this->assignRef('importData',$model->importData($post));
		$this->assignRef('postData',$post);

		parent::display($tpl);
	}

	private function _displaySelectpage($tpl)
	{
		$option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$model =& $this->getModel('jlextindividualsportringen');
		$lists=array();

		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('selectType',$mainframe->getUserState($option.'selectType'));
		$this->assignRef('recordID',$mainframe->getUserState($option.'recordID'));

		switch ($this->selectType)
		{
			case '10':   { // Select new Club
						$this->assignRef('clubs',$model->getNewClubListSelect());
						$clublist=array();
						$clublist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_CLUB'));
						$clublist=array_merge($clublist,$this->clubs);
						$lists['clubs']=JHTML::_(	'select.genericlist',$clublist,'clubID','class="inputbox select-club" onchange="javascript:insertNewClub(\''.$this->recordID.'\')" ','value','text', 0);
						unset($clubteamlist);
						}
						break;
			case '9':   { // Select Club & Team
						$this->assignRef('clubsteams',$model->getClubAndTeamListSelect());
						$clubteamlist=array();
						$clubteamlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_CLUB_AND_TEAM'));
						$clubteamlist=array_merge($clubteamlist,$this->clubsteams);
						$lists['clubsteams']=JHTML::_(	'select.genericlist',$clubteamlist,'teamID','class="inputbox select-team" onchange="javascript:insertClubAndTeam(\''.$this->recordID.'\')" ','value','text', 0);
						unset($clubteamlist);
						}
						break;
			case '8':	{ // Select Statistics
						$this->assignRef('statistics',$model->getStatisticsListSelect());
						$statisticlist=array();
						$statisticlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_STATISTIC'));
						$statisticlist=array_merge($statisticlist,$this->statistics);
						$lists['statistics']=JHTML::_('select.genericlist',$statisticlist,'statisticID','class="inputbox select-statistic" onchange="javascript:insertStatistic(\''.$this->recordID.'\')" ');
						unset($statisticlist);
						}
						break;

			case '7':	{ // Select ParentPosition
						$this->assignRef('parentpositions',$model->getParentPositionListSelect());
						$parentpositionlist=array();
						$parentpositionlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_PARENT_POSITION'));
						$parentpositionlist=array_merge($parentpositionlist,$this->parentpositions);
						$lists['parentpositions']=JHTML::_('select.genericlist',$parentpositionlist,'parentPositionID','class="inputbox select-parentposition" onchange="javascript:insertParentPosition(\''.$this->recordID.'\')" ');
						unset($parentpositionlist);
						}
						break;

			case '6':	{ // Select Position
						$this->assignRef('positions',$model->getPositionListSelect());
						$positionlist=array();
						$positionlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_POSITION'));
						$positionlist=array_merge($positionlist,$this->positions);
						$lists['positions']=JHTML::_('select.genericlist',$positionlist,'positionID','class="inputbox select-position" onchange="javascript:insertPosition(\''.$this->recordID.'\')" ');
						unset($positionlist);
						}
						break;

			case '5':	{ // Select Event
						$this->assignRef('events',$model->getEventListSelect());
						$eventlist=array();
						$eventlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_EVENT'));
						$eventlist=array_merge($eventlist,$this->events);
						$lists['events']=JHTML::_('select.genericlist',$eventlist,'eventID','class="inputbox select-event" onchange="javascript:insertEvent(\''.$this->recordID.'\')" ');
						unset($eventlist);
						}
						break;

			case '4':	{ // Select Playground
						$this->assignRef('playgrounds',$model->getPlaygroundListSelect());
						$playgroundlist=array();
						$playgroundlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_PLAYGROUND'));
						$playgroundlist=array_merge($playgroundlist,$this->playgrounds);
						$lists['playgrounds']=JHTML::_('select.genericlist',$playgroundlist,'playgroundID','class="inputbox select-playground" onchange="javascript:insertPlayground(\''.$this->recordID.'\')" ');
						unset($playgroundlist);
						}
						break;

			case '3':	{ // Select Person
						$this->assignRef('persons',$model->getPersonListSelect());
						$personlist=array();
						$personlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_PERSON'));
						$personlist=array_merge($personlist,$this->persons);
						$lists['persons']=JHTML::_('select.genericlist',$personlist,'personID','class="inputbox select-person" onchange="javascript:insertPerson(\''.$this->recordID.'\')" ');
						unset($personlist);
						}
						break;

			case '2':	{ // Select Club
						$this->assignRef('clubs',$model->getClubListSelect());
						$clublist=array();
						$clublist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_CLUB'));
						$clublist=array_merge($clublist,$this->clubs);
						$lists['clubs']=JHTML::_('select.genericlist',$clublist,'clubID','class="inputbox select-club" onchange="javascript:insertClub(\''.$this->recordID.'\')" ');
						unset($clublist);
						}
						break;

			case '1':
			default:	{ // Select Team
						$this->assignRef('teams',$model->getTeamListSelect());
						$this->assignRef('clubs',$model->getClubListSelect());
						$teamlist=array();
						$teamlist[]=JHTML::_('select.option',0,JText::_('JL_ADMIN_XML_IMPORT_SELECT_TEAM'));
						$teamlist=array_merge($teamlist,$this->teams);
						$lists['teams']=JHTML::_('select.genericlist',$teamlist,'teamID','class="inputbox select-team" onchange="javascript:insertTeam(\''.$this->recordID.'\')" ','value','text',0);
						unset($teamlist);
						}
						break;
		}

		$this->assignRef('lists',$lists);
		// Set page title
		$pageTitle=JText::_('JL_ADMIN_XML_IMPORT_ASSIGN_TITLE');
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}

}
?>