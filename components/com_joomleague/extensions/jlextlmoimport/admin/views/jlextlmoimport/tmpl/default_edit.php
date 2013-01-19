<?php defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
//$document =& JFactory::getDocument();
//$document->addScript( JURI::base() . 'components/com_joomleague/assets/js/JL_import.js' );

// Set toolbar items for the page
//JToolBarHelper::title( JText::_( 'JL_ADMIN_LMO_IMPORT_TITLE_2'  , 'extension.png') );

$url = 'components/com_joomleague/extensions/jlextlmoimport/admin/assets/images/h1.png';
$alt = 'Lmo Logo';
// $attribs['width'] = '170px';
// $attribs['height'] = '26px';
$attribs['align'] = 'left';
$logo = JHtml::_('image', $url, $alt, $attribs);

if (isset($this->xml) && is_array($this->xml))
{
	{
		//echo 'this<pre>'.print_r($this,true).'</pre>';
		if (array_key_exists('exportversion',$this->xml))
		{
			$exportversion =& $this->xml['exportversion'];
		
// 		echo 'exportversion <br>';
//     echo '<pre>';
//     print_r($exportversion);
//     echo '</pre>';	
			
		}
		if (array_key_exists('project',$this->xml))
		{
			$proj =& $this->xml['project'];
		
//     echo 'proj <br>';	
// 		echo '<pre>';
//     print_r($proj);
//     echo '</pre>';
//     echo 'proj-name '.$proj->name.' <br>';
    
			
		}
		if (array_key_exists('team',$this->xml))
		{
			$teams =& $this->xml['team'];
	  
//     echo 'teams <br>';
// 		echo '<pre>';
//     print_r($teams);
//     echo '</pre>';
    			
		}
		if (array_key_exists('club',$this->xml))
		{
			$clubs =& $this->xml['club'];
		
//     echo 'clubs <br>';	
// 		echo '<pre>';
//     print_r($clubs);
//     echo '</pre>';
    			
		}
		if (array_key_exists('playground',$this->xml))
		{
			$playgrounds =& $this->xml['playground'];
		}
		if (array_key_exists('league',$this->xml))
		{
			$league =& $this->xml['league'];
		}
		if (array_key_exists('season',$this->xml))
		{
			$season =& $this->xml['season'];
		}
		if (array_key_exists('sportstype',$this->xml))
		{
			$sportstype =& $this->xml['sportstype'];
		}
		if (array_key_exists('person',$this->xml))
		{
			$persons =& $this->xml['person'];
		}
		if (array_key_exists('event',$this->xml))
		{
			$events =& $this->xml['event'];
		}
		if (array_key_exists('position',$this->xml))
		{
			$positions =& $this->xml['position'];
			
//     echo 'position <br>';	
//  		echo '<pre>';
//     print_r($positions);
//     echo '</pre>';

		}
		
    if (array_key_exists('parentposition',$this->xml))
		{
			$parentpositions =& $this->xml['parentposition'];
		}
		
    if (array_key_exists('statistic',$this->xml))
		{
			$statistics =& $this->xml['statistic'];
		}
		
	}

	$xmlProjectImport=true;
	$xmlImportType='';
	
	if (!isset($proj))
	{

//		$xmlProjectImport=false;

		if (isset($clubs))
		{
			$xmlImportType='clubs';	// There shouldn't be any problems with import of clubs-xml-export files
			$xmlImportTitle='JL_ADMIN_LMO_IMPORT_TITLE_CLUBS';
			$teamsClubs=$clubs;
		}
		elseif (isset($events)) // There shouldn't be any problems with import of events-xml-export files
		{
			$xmlImportType='events';
			$xmlImportTitle='Standart XML-Import of JoomLeague Events';
		}
		elseif (isset($positions))	// There shouldn't be any problems with import of positions-xml-export files
		{							// maybe the positions export routine should also export position_eventtype and events
			$xmlImportType='positions';
			$xmlImportTitle='Standart XML-Import of JoomLeague Positions';
		}
		elseif (isset($parentpositions))	// There shouldn't be any problems with import of positions-xml-export files
		{									// maybe the positions export routine should also export position_eventtype and events
			$xmlImportType='positions';
			$xmlImportTitle='Standart XML-Import of JoomLeague Positions';
		}
		elseif (isset($persons))	// There shouldn't be any problems with import of persons-xml-export files
		{
			$xmlImportType='persons';
			$xmlImportTitle='Standart XML-Import of JoomLeague Persons';
		}
		elseif (isset($playgrounds))	// There shouldn't be any problems with import of statistics-xml-export files
		{
			$xmlImportType='playgrounds';
			$xmlImportTitle='Standart XML-Import of JoomLeague Playgrounds';
		}
		elseif (isset($statistics)) // There shouldn't be any problems with import of statistics-xml-export files
		{							// maybe the statistic export routine should also export position_statistic and positions
			$xmlImportType='statistics';
			$xmlImportTitle='Standart XML-Import of JoomLeague Statistics';
		}
		elseif (isset($teams))	// There shouldn't be any problems with import of teams-xml-export files
		{
			$xmlImportType='teams';
			$xmlImportTitle='JL_ADMIN_LMO_IMPORT_TITLE_TEAMS';
			$teamsClubs=$teams;
		}
		JError::raiseNotice(500,JText::_($xmlImportTitle));
	}
	else
	{
		$teamsClubs=$teams;
		//$xmlImportTitle='JL_ADMIN_LMO_IMPORT_TITLE_VERSION'.$exportversion->name;
		//JError::raiseNotice(500,JText::_($xmlImportTitle));
		//JError::raiseNotice(500,JText::_('JL_ADMIN_LMO_IMPORT_TITLE_VERSION'.$exportversion->name));
		JError::raiseNotice(500,JText::sprintf('JL_ADMIN_LMO_IMPORT_TITLE_VERSION',$exportversion->name));
	}
	
	
	if (!empty($teamsClubs)){$teamsClubsCount=count($teamsClubs);}
	
	
?>
<script language="javascript" type="text/javascript"><!--

	function showActOffset()
	{
		var form=document.adminForm;
		var a=new Date();
		var b=parseInt(a.getHours());
		var dummy=parseInt(<?php echo JHTML::date(time(),'%H'); ?>)-b;
		var c=parseInt(a.getMinutes());
		var timeOffsetValue=parseInt(form.timeoffset.value);
		var result=b+timeOffsetValue+dummy;
		if (result < 0){result=24+result;}
		if (result > 23){result=result-24;}
		if (result < 10){result='0'+result;}
		if (c < 10){c='0'+c;}
		form.acttime.value=result+':'+c;
	}

	function trim(stringToTrim)
	{
		return stringToTrim.replace(/^\s+|\s+$/g,"");
	}

	function checkAllCustom(n,fldName)
	{
		if (!fldName) { fldName='tc'; }

		var f=document.adminForm;
		var c=f.toggleTeamsClubs.checked;
		var n2=0;

		for (i=0; i < n; i++)
		{
			tc=eval('f.' + fldName + '' + i);
			if (tc)
			{
				tc.checked=c;
				testTeamsClubsData(tc,tc.value);
				n2++;
			}
		}
	}

	function testTeamsClubsData(box,id)
	{
		if (box.checked)
		{
			<?php
			if (!empty($teams))
			{
				?>
				eval("document.adminForm.chooseTeam_"+id+".disabled=true");
				eval("document.adminForm.chooseTeam_"+id+".checked=false");
				eval("document.adminForm.selectTeam_"+id+".disabled=true");
				eval("document.adminForm.selectTeam_"+id+".checked=false");
				eval("document.adminForm.createTeam_"+id+".disabled=false");
				eval("document.adminForm.createTeam_"+id+".checked=true");
				eval("document.adminForm.teamName_"+id+".disabled=false");
				eval("document.adminForm.teamShortname_"+id+".disabled=false");
				eval("document.adminForm.teamInfo_"+id+".disabled=false");
				eval("document.adminForm.teamMiddleName_"+id+".disabled=false");
				<?php
			}
			?>
			<?php
			if (!empty($clubs))
			{
				?>
				eval("document.adminForm.chooseClub_"+id+".disabled=true");
				eval("document.adminForm.chooseClub_"+id+".checked=false");
				eval("document.adminForm.selectClub_"+id+".disabled=true");
				eval("document.adminForm.selectClub_"+id+".checked=false");
				eval("document.adminForm.createClub_"+id+".disabled=false");
				eval("document.adminForm.createClub_"+id+".checked=true");
				eval("document.adminForm.clubName_"+id+".disabled=false");
				eval("document.adminForm.clubCountry_"+id+".disabled=false");
				eval("document.adminForm.clubID_"+id+".disabled=false");
				<?php
			}
			?>
		}
		else
		{
			<?php
			if (!empty($teams))
			{
				?>
				if (eval("document.adminForm.selectTeam_"+id+".value!=''")){
					eval("document.adminForm.chooseTeam_"+id+".disabled=false");
				}
				eval("document.adminForm.selectTeam_"+id+".disabled=false");
				eval("document.adminForm.selectTeam_"+id+".checked=false");
				eval("document.adminForm.createTeam_"+id+".checked=false");
				eval("document.adminForm.teamName_"+id+".disabled=true");
				eval("document.adminForm.teamShortname_"+id+".disabled=true");
				eval("document.adminForm.teamInfo_"+id+".disabled=true");
				eval("document.adminForm.teamMiddleName_"+id+".disabled=true");
				<?php
			}
			?>
			<?php
			if (!empty($clubs))
			{
				?>
				if (eval("document.adminForm.selectClub_"+id+".value!=''")){
					eval("document.adminForm.chooseClub_"+id+".disabled=false");
				}
				eval("document.adminForm.selectClub_"+id+".disabled=false");
				eval("document.adminForm.selectClub_"+id+".checked=false");
				eval("document.adminForm.createClub_"+id+".checked=false");
				eval("document.adminForm.clubName_"+id+".disabled=true");
				eval("document.adminForm.clubCountry_"+id+".disabled=true");
				eval("document.adminForm.clubID_"+id+".disabled=true");
				<?php
			}
			?>
		}
	}

	function checkAllPlaygrounds(n,fldName)
	{
		if (!fldName) { fldName='pl'; }

		var f=document.adminForm;
		var c=f.togglePlaygrounds.checked;
		var n5=0;

		for (i=0; i < n; i++)
		{
			pl=eval('f.' + fldName + '' + i);
			if (pl)
			{
				pl.checked=c;
				testPlaygroundData(pl,pl.value);
				n5++;
			}
		}
	}

	function testPlaygroundData(box,id)
	{
		if (box.checked)
		{
			eval("document.adminForm.choosePlayground_"+id+".disabled=true");
			eval("document.adminForm.choosePlayground_"+id+".checked=false");
			eval("document.adminForm.selectPlayground_"+id+".disabled=true");
			eval("document.adminForm.selectPlayground_"+id+".checked=false");
			eval("document.adminForm.createPlayground_"+id+".disabled=false");
			eval("document.adminForm.createPlayground_"+id+".checked=true");
			eval("document.adminForm.playgroundName_"+id+".disabled=false");
			eval("document.adminForm.playgroundShortname_"+id+".disabled=false");
			eval("document.adminForm.playgroundID_"+id+".disabled=false");
		}
		else
		{
			if (eval("document.adminForm.selectPlayground_"+id+".value!=''")){
				eval("document.adminForm.choosePlayground_"+id+".disabled=false");
			}
			eval("document.adminForm.selectPlayground_"+id+".disabled=false");
			eval("document.adminForm.selectPlayground_"+id+".checked=false");
			eval("document.adminForm.createPlayground_"+id+".checked=false");
			eval("document.adminForm.playgroundName_"+id+".disabled=true");
			eval("document.adminForm.playgroundShortname_"+id+".disabled=true");
			eval("document.adminForm.playgroundID_"+id+".disabled=true");
		}
	}

	function checkAllEvents(n,fldName)
	{
		if (!fldName) { fldName='ev'; }

		var f=document.adminForm;
		var c=f.toggleEvents.checked;
		var n5=0;

		for (i=0; i < n; i++)
		{
			ev=eval('f.' + fldName + '' + i);
			if (ev)
			{
				ev.checked=c;
				testEventsData(ev,ev.value);
				n5++;
			}
		}
	}

	function testEventsData(box,id)
	{
		if (box.checked)
		{
			eval("document.adminForm.chooseEvent_"+id+".disabled=true");
			eval("document.adminForm.chooseEvent_"+id+".checked=false");
			eval("document.adminForm.selectEvent_"+id+".disabled=true");
			eval("document.adminForm.selectEvent_"+id+".checked=false");
			eval("document.adminForm.createEvent_"+id+".disabled=false");
			eval("document.adminForm.createEvent_"+id+".checked=true");
			eval("document.adminForm.eventName_"+id+".disabled=false");
			eval("document.adminForm.eventID_"+id+".disabled=false");
		}
		else
		{
			if (eval("document.adminForm.selectEvent_"+id+".value!=''")){
				eval("document.adminForm.chooseEvent_"+id+".disabled=false");
			}
			eval("document.adminForm.selectEvent_"+id+".disabled=false");
			eval("document.adminForm.selectEvent_"+id+".checked=false");
			eval("document.adminForm.createEvent_"+id+".checked=false");
			eval("document.adminForm.eventName_"+id+".disabled=true");
			eval("document.adminForm.eventID_"+id+".disabled=true");
		}
	}

	function checkAllParentPositions(n,fldName)
	{
		if (!fldName) { fldName='pp'; }

		var f=document.adminForm;
		var c=f.toggleParentPositions.checked;
		var n5=0;

		for (i=0; i < n; i++)
		{
			pp=eval('f.' + fldName + '' + i);
			if (pp) {
				pp.checked=c;
				testParentPositionsData(pp,pp.value);
				n5++;
			}
		}
	}

	function testParentPositionsData(box,id)
	{
		if (box.checked)
		{
			eval("document.adminForm.chooseParentPosition_"+id+".disabled=true");
			eval("document.adminForm.chooseParentPosition_"+id+".checked=false");
			eval("document.adminForm.selectParentPosition_"+id+".disabled=true");
			eval("document.adminForm.selectParentPosition_"+id+".checked=false");
			eval("document.adminForm.createParentPosition_"+id+".disabled=false");
			eval("document.adminForm.createParentPosition_"+id+".checked=true");
			eval("document.adminForm.parentPositionName_"+id+".disabled=false");
			eval("document.adminForm.parentPositionID_"+id+".disabled=false");
		}
		else
		{
			if (eval("document.adminForm.selectParentPosition_"+id+".value!=''")){
				eval("document.adminForm.chooseParentPosition_"+id+".disabled=false");
			}
			eval("document.adminForm.selectParentPosition_"+id+".disabled=false");
			eval("document.adminForm.selectParentPosition_"+id+".checked=false");
			eval("document.adminForm.createParentPosition_"+id+".checked=false");
			eval("document.adminForm.parentPositionName_"+id+".disabled=true");
			eval("document.adminForm.parentPositionID_"+id+".disabled=true");
		}
	}

	function checkAllPositions(n,fldName)
	{
		if (!fldName) { fldName='po'; }

		var f=document.adminForm;
		var c=f.togglePositions.checked;
		var n5=0;

		for (i=0; i < n; i++)
		{
			po=eval('f.' + fldName + '' + i);
			if (po)
			{
				po.checked=c;
				testPositionsData(po,po.value);
				n5++;
			}
		}
	}

	function testPositionsData(box,id)
	{
		if (box.checked)
		{
			eval("document.adminForm.choosePosition_"+id+".disabled=true");
			eval("document.adminForm.choosePosition_"+id+".checked=false");
			eval("document.adminForm.selectPosition_"+id+".disabled=true");
			eval("document.adminForm.selectPosition_"+id+".checked=false");
			eval("document.adminForm.createPosition_"+id+".disabled=false");
			eval("document.adminForm.createPosition_"+id+".checked=true");
			eval("document.adminForm.positionName_"+id+".disabled=false");
			eval("document.adminForm.positionID_"+id+".disabled=false");
		}
		else
		{
			if (eval("document.adminForm.selectPosition_"+id+".value!=''")){
				eval("document.adminForm.choosePosition_"+id+".disabled=false");
			}
			eval("document.adminForm.selectPosition_"+id+".disabled=false");
			eval("document.adminForm.selectPosition_"+id+".checked=false");
			eval("document.adminForm.createPosition_"+id+".checked=false");
			eval("document.adminForm.positionName_"+id+".disabled=true");
			eval("document.adminForm.positionID_"+id+".disabled=true");
		}
	}

	function checkAllStatistics(n,fldName)
	{
		if (!fldName) { fldName='st'; }

		var f=document.adminForm;
		var c=f.toggleStatistics.checked;
		var n5=0;

		for (i=0; i < n; i++)
		{
			st=eval('f.' + fldName + '' + i);
			if (st)
			{
				st.checked=c;
				testStatisticsData(st,st.value);
				n5++;
			}
		}
	}

	function testStatisticsData(box,id)
	{
		if (box.checked)
		{
			eval("document.adminForm.chooseStatistic_"+id+".disabled=true");
			eval("document.adminForm.chooseStatistic_"+id+".checked=false");
			eval("document.adminForm.selectStatistic_"+id+".disabled=true");
			eval("document.adminForm.selectStatistic_"+id+".checked=false");
			eval("document.adminForm.createStatistic_"+id+".disabled=false");
			eval("document.adminForm.createStatistic_"+id+".checked=true");
			eval("document.adminForm.statisticName_"+id+".disabled=false");
			eval("document.adminForm.statisticID_"+id+".disabled=false");
		}
		else
		{
			if (eval("document.adminForm.selectStatistic_"+id+".value!=''")){
				eval("document.adminForm.chooseStatistic_"+id+".disabled=false");
			}
			eval("document.adminForm.selectStatistic_"+id+".disabled=false");
			eval("document.adminForm.selectStatistic_"+id+".checked=false");
			eval("document.adminForm.createStatistic_"+id+".checked=false");
			eval("document.adminForm.statisticName_"+id+".disabled=true");
			eval("document.adminForm.statisticID_"+id+".disabled=true");
		}
	}

	function checkAllPersons(n,fldName)
	{
		if (!fldName) { fldName='pe'; }

		var f=document.adminForm;
		var c=f.togglePersons.checked;
		var n5=0;

		for (i=0; i < n; i++)
		{
			pe=eval('f.' + fldName + '' + i);
			if (pe)
			{
				pe.checked=c;
				testPersonsData(pe,pe.value);
				n5++;
			}
		}
	}

	function testPersonsData(box,id)
	{
		if (box.checked)
		{
			eval("document.adminForm.choosePerson_"+id+".disabled=true");
			eval("document.adminForm.choosePerson_"+id+".checked=false");
			eval("document.adminForm.selectPerson_"+id+".disabled=true");
			eval("document.adminForm.selectPerson_"+id+".checked=false");
			eval("document.adminForm.createPerson_"+id+".disabled=false");
			eval("document.adminForm.createPerson_"+id+".checked=true");

			eval("document.adminForm.personLastname_"+id+".disabled=false");
			eval("document.adminForm.personFirstname_"+id+".disabled=false");
			eval("document.adminForm.personNickname_"+id+".disabled=false");
			eval("document.adminForm.personBirthday_"+id+".disabled=false");
			eval("document.adminForm.personID_"+id+".disabled=false");
		}
		else
		{
			if (eval("document.adminForm.selectPerson_"+id+".value!=''")){
				eval("document.adminForm.choosePerson_"+id+".disabled=false");
			}
			eval("document.adminForm.selectPerson_"+id+".disabled=false");
			eval("document.adminForm.selectPerson_"+id+".checked=false");
			eval("document.adminForm.createPerson_"+id+".checked=false");
			eval("document.adminForm.personLastname_"+id+".disabled=true");
			eval("document.adminForm.personFirstname_"+id+".disabled=true");
			eval("document.adminForm.personNickname_"+id+".disabled=true");
			eval("document.adminForm.personBirthday_"+id+".disabled=true");
			eval("document.adminForm.personID_"+id+".disabled=true");
		}
	}

	function chkFormular()
	{
		return true;
		var message='';

		<?php
		if (($xmlProjectImport) || ($xmlImportType=='events') || ($xmlImportType=='positions'))
		{
			?>
			if (((document.adminForm.sportstype.selectedIndex=='0') && (document.adminForm.sportstypeNew.disabled) &&
				(!document.adminForm.newSportsTypeCheck.checked)) ||
				((document.adminForm.sportstypeNew.disabled==false) && (trim(document.adminForm.sportstypeNew.value)=='')))
			{
				message+="<?php echo JText::_('Sports type is missing!'); ?>\n";
			}
			<?php
			if ($xmlProjectImport)
			{
				?>
				if (trim(document.adminForm.name.value)=='')
				{
					message+="<?php echo JText::_('Please select name of this project!'); ?>\n";
				}
				if (((document.adminForm.league.selectedIndex=='0') && (document.adminForm.leagueNew.disabled)) ||
					((document.adminForm.leagueNew.disabled==false) && (trim(document.adminForm.leagueNew.value)=='')))
				{
					message+="<?php echo JText::_('League is missing!'); ?>\n";
				}
				if (((document.adminForm.season.selectedIndex=='0') && (document.adminForm.seasonNew.disabled)) ||
					((document.adminForm.seasonNew.disabled==false) && (trim(document.adminForm.seasonNew.value)=='')))
				{
					message+="<?php echo JText::_('Season is missing!'); ?>\n";
				}
				<?php
			}
		}
		?>
		<?php
		if (isset($teams) && count($teams) > 0)
		{
			for ($counter=0; $counter < count($teams); $counter++)
			{
				?>
				if (((document.adminForm.chooseTeam_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createTeam_<?php echo $counter; ?>.checked==false)) ||
					((trim(document.adminForm.teamName_<?php echo $counter; ?>.value)=='') ||
					(trim(document.adminForm.teamShortname_<?php echo $counter; ?>.value)=='') ||
					(trim(document.adminForm.teamMiddleName_<?php echo $counter; ?>.value)=='')))
				{
					message+='<?php echo JText::sprintf('No data selected for team [%1$s]',addslashes($teams[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if (isset($clubs) && count($clubs) > 0)
		{
			$maxCounter=(!empty($teams)) ? count($teams) : count($teamsClubs);
			//$maxCounter=((isset($clubs) && count($clubs) > 0)) ? count($clubs) : count($teams);
			for ($counter=0; $counter < $maxCounter; $counter++)
			{
				?>
				if (((document.adminForm.chooseClub_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createClub_<?php echo $counter; ?>.checked==false)) ||
					((trim(document.adminForm.clubName_<?php echo $counter; ?>.value)=='') ||
					(trim(document.adminForm.clubCountry_<?php echo $counter; ?>.value)=='')))
				{
					message+='<?php echo JText::sprintf('No data selected for club [%1$s]',addslashes($clubs[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if ((isset($playgrounds)) && (count($playgrounds) > 0))
		{
			for ($counter=0; $counter < count($playgrounds); $counter++)
			{
				?>
				if (((document.adminForm.choosePlayground_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createPlayground_<?php echo $counter; ?>.checked==false)) ||
					((trim(document.adminForm.playgroundName_<?php echo $counter; ?>.value)=='') ||
					(trim(document.adminForm.playgroundShortname_<?php echo $counter; ?>.value)=='')))
				{
					message+='<?php echo JText::sprintf('No data selected for playground [%1$s]',addslashes($playgrounds[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if ((isset($events)) && (count($events) > 0))
		{
			for ($counter=0; $counter < count($events); $counter++)
			{
				?>
				if (((document.adminForm.chooseEvent_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createEvent_<?php echo $counter; ?>.checked==false)) ||
					(trim(document.adminForm.eventName_<?php echo $counter; ?>.value)==''))
				{
					message+='<?php echo JText::sprintf('No data selected for event [%1$s]',addslashes($events[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if ((isset($parentpositions)) && (count($parentpositions) > 0))
		{
			for ($counter=0; $counter < count($parentpositions); $counter++)
			{
				?>
				if (((document.adminForm.chooseParentPosition_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createParentPosition_<?php echo $counter; ?>.checked==false)) ||
					(trim(document.adminForm.parentPositionName_<?php echo $counter; ?>.value)==''))
				{
					message+='<?php echo JText::sprintf('No data selected for parentposition [%1$s]',addslashes($parentpositions[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if ((isset($positions)) && (count($positions) > 0))
		{
			for ($counter=0; $counter < count($positions); $counter++)
			{
				?>
				if (((document.adminForm.choosePosition_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createPosition_<?php echo $counter; ?>.checked==false)) ||
					(trim(document.adminForm.positionName_<?php echo $counter; ?>.value)==''))
				{
					message+='<?php echo JText::sprintf('No data selected for position [%1$s]',addslashes($positions[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if ((isset($statistics)) && (count($statistics) > 0))
		{
			for ($counter=0; $counter < count($statistics); $counter++)
			{
				?>
				if (((document.adminForm.chooseStatistic_<?php echo $counter; ?>.checked==false) &&
					(document.adminForm.createStatistic_<?php echo $counter; ?>.checked==false)) ||
					(trim(document.adminForm.statisticName_<?php echo $counter; ?>.value)==''))
				{
					message+='<?php echo JText::sprintf('No data selected for statistic [%1$s]',addslashes($statistics[$counter]->name)); ?>\n';
				}
				<?php
			}
		}
		?>
		<?php
		if ((isset($persons)) && (count($persons) > 0))
		{
			for ($counter=0; $counter < count($persons); $counter++)
			{
				?>
				if(document.adminForm.choosePerson_<?php echo $counter; ?>.checked==false) {
					if ((document.adminForm.createPerson_<?php echo $counter; ?>.checked==false)||
						((trim(document.adminForm.personLastname_<?php echo $counter; ?>.value)=='') ||
						(trim(document.adminForm.personFirstname_<?php echo $counter; ?>.value)=='')))
					{
						message+='<?php echo JText::sprintf('No data selected for person [%1$s,%2$s]',addslashes($persons[$counter]->lastname),addslashes($persons[$counter]->firstname)); ?>\n';
					}
				}
				<?php
			}
		}
		?>
		if (message=='')
		{
			return true;
		}
		else
		{
		  alert("<?php echo JText::_('JL_ADMIN_LMO_IMPORT_ERROR'); ?>\n\n"+message);
		  return false;
		}
	}

	function openSelectWindow(recordid,key,selector,box,datatype)
	{
		if (datatype==1){ // Team-Selector
		eval("document.adminForm.chooseTeam_"+key+".checked=false");
		eval("document.adminForm.selectTeam_"+key+".checked=false");
		eval("document.adminForm.createTeam_"+key+".checked=false");
		eval("document.adminForm.teamName_"+key+".disabled=true");
		eval("document.adminForm.teamShortname_"+key+".disabled=true");
		eval("document.adminForm.teamInfo_"+key+".disabled=true");
		eval("document.adminForm.teamMiddleName_"+key+".disabled=true");
		}
		else if (datatype==2){ // Club-Selector
		eval("document.adminForm.chooseClub_"+key+".checked=false");
		eval("document.adminForm.selectClub_"+key+".checked=false");
		eval("document.adminForm.createClub_"+key+".checked=false");
		eval("document.adminForm.clubName_"+key+".disabled=true");
		eval("document.adminForm.clubCountry_"+key+".disabled=true");
		}
		else if (datatype==3){ // Person-Selector
		eval("document.adminForm.choosePerson_"+key+".checked=false");
		eval("document.adminForm.selectPerson_"+key+".checked=false");
		eval("document.adminForm.createPerson_"+key+".checked=false");
		eval("document.adminForm.personLastname_"+key+".disabled=true");
		eval("document.adminForm.personFirstname_"+key+".disabled=true");
		eval("document.adminForm.personNickname_"+key+".disabled=true");
		eval("document.adminForm.personBirthday_"+key+".disabled=true");
		}
		else if (datatype==4){ // Playground-Selector
		eval("document.adminForm.choosePlayground_"+key+".checked=false");
		eval("document.adminForm.selectPlayground_"+key+".checked=false");
		eval("document.adminForm.createPlayground_"+key+".checked=false");
		eval("document.adminForm.playgroundName_"+key+".disabled=true");
		eval("document.adminForm.playgroundShortname_"+key+".disabled=true");
		}
		else if (datatype==5){ // Event-Selector
		eval("document.adminForm.chooseEvent_"+key+".checked=false");
		eval("document.adminForm.selectEvent_"+key+".checked=false");
		eval("document.adminForm.createEvent_"+key+".checked=false");
		eval("document.adminForm.eventName_"+key+".disabled=true");
		}
		else if (datatype==6){ // Position-Selector
		eval("document.adminForm.choosePosition_"+key+".checked=false");
		eval("document.adminForm.selectPosition_"+key+".checked=false");
		eval("document.adminForm.createPosition_"+key+".checked=false");
		eval("document.adminForm.positionName_"+key+".disabled=true");
		}
		else if (datatype==7){ // ParentPosition-Selector
		eval("document.adminForm.chooseParentPosition_"+key+".checked=false");
		eval("document.adminForm.selectParentPosition_"+key+".checked=false");
		eval("document.adminForm.createParentPosition_"+key+".checked=false");
		eval("document.adminForm.parentPositionName_"+key+".disabled=true");
		}
		else if (datatype==8){ // Statistic-Selector
		eval("document.adminForm.chooseStatistic_"+key+".checked=false");
		eval("document.adminForm.selectStatistic_"+key+".checked=false");
		eval("document.adminForm.createStatistic_"+key+".checked=false");
		eval("document.adminForm.statisticName_"+key+".disabled=true");
		}
		//alert(datatype + "-" + recordid + "-" + key + "-" + selector + "-" + box);
		query='index.php?option=com_joomleague&tmpl=component&view=jlextlmoimport&controller=jlextlmoimport'
				+ '&task=select'
				+ '&type=' + datatype
				+ '&id=' + key;
		//alert(query);
		selectWindow=window.open(query,'teamSelectWindow','width=600,height=100,scrollbars=yes,resizable=yes');
		selectWindow.focus();

		return false;
	}

//--></script>
	<div id='editcell'>
		<a name='page_top'></a>
		<table class='adminlist'>
			<thead>
      <tr>
      <th><?php echo JHtml::_('image', $url, $alt, $attribs);; ?>
      <?php echo JText::_('JL_ADMIN_LMO_IMPORT_TABLE_TITLE_2'); ?>
      </th>
      </tr>
      </thead>
			<tbody>
				<tr>
					<td style='text-align:center; '>
						<p style='text-align:center;'><b style='color:green; '><?php echo JText::sprintf('JL_ADMIN_LMO_IMPORT_UPLOAD_SUCCESS','<i>'.$this->uploadArray['name'].'</i>'); ?></b></p>
						
            <?php
						
//						if ($this->import_version!='OLD')
//						{
//							if (isset($exportversion->exportRoutine) &&
//								strtotime($exportversion->exportRoutine) >= strtotime('2010-09-19 23:00:00'))
//							{
								?>
								<p>
                <?php
//									echo JText::sprintf('This file was created using JoomLeague-Export-Routine dated: %1$s',$exportversion->exportRoutine).'<br />';
//									echo JText::sprintf('Date and time of this file is: %1$s - %2$s',$exportversion->exportDate,$exportversion->exportTime).'<br />';
//									echo JText::sprintf('The name of the Joomla-System where this file was created is: %1$s',$exportversion->exportSystem).'<br />';
									?>
                  </p>
                  <?php
//							}
//							else
//							{
								?>
								<p>
                <?php
//									echo JText::_('This file was created by an older revision of JoomLeague 1.5.0a!').'<br />';
//									echo JText::_('As we can not guarantee a correct processing the import routine will STOP here!!!');
									?>
                  </p>
                  </td>
                  </tr>
                  </tbody>
                  </table>
                  </div>
                  <?php
//									return;
//							}
//						}
						?>
						<table class='adminlist'>
            		<tr>
                <td>
						<p><?php echo JText::sprintf('JL_ADMIN_LMO_IMPORT_HINT2',$this->revisionDate); ?></p> </td></tr>
						<tr><td><p><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_CLUBS_HINT'); ?></p>
					</td>
				</tr>
			
		</table>
		<form name='adminForm' action='<?php echo $this->request_url; ?>' method='post' onsubmit='return chkFormular();' >
			<input type='hidden' name='importProject' value="<?php echo $xmlProjectImport; ?>" />
			<input type='hidden' name='importType' value="<?php echo $xmlImportType; ?>" />
			<input type='hidden' name='sent' value="2" id='sent' />
			<input type='hidden' name='controller' value="jlextlmoimport" />
			<input type='hidden' name='task' value="insert" />
			<?php echo JHTML::_('form.token')."\n"; ?>
			<?php
/**/
			if (($xmlProjectImport) || ($xmlImportType=='events') || ($xmlImportType=='positions'))
			{
				?>
				<fieldset>
					<legend><strong><?php echo JText::_('JL_ADMIN_LMO_IMPORT_GENERAL_DATA_LEGEND'); ?></strong></legend>
					<table class='adminlist'>
						<?php
						if (($xmlImportType!='events') && ($xmlImportType!='positions'))
						{
							?>
							<tr>
								<td style='background-color:#EEEEEE'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_PROJECT_NAME'); ?></td>
								<td style='background-color:#EEEEEE'>
									<input type='text' name='name' id='name' size='110' maxlength='100' value="<?php echo stripslashes(htmlspecialchars($proj->name)); ?>" />
								</td>
							</tr>
							
							<tr>
								<td style='background-color:#EEEEEE'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_PROJECT_NAME_VORSCHLAG'); ?></td>
								<td style='background-color:#EEEEEE'>
									<input type='text' name='namevorschlag' id='namevorschlag' size='110' maxlength='100' value="<?php echo stripslashes(htmlspecialchars($proj->namevorschlag)); ?>" />
								</td>
							</tr>
							
							<?php
						}
						?>
						<tr>
							<td style='background-color:#DDDDDD'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_SPORTSTYPE'); ?></td>
							<td style='background-color:#DDDDDD'>
								<?php
								if (isset($sportstype->name))
								{
									$dSportsTypeName=$sportstype->name;
								}
								else
								{
									$dSportsTypeName=$proj->name;
								}
								if (count($this->sportstypes) > 0)
								{
									?>
									<select name='sportstype' id='sportstype'>
										<option selected value="0"><?php echo JText::_('JL_ADMIN_LMO_IMPORT_SPORTSTYPE_SELECT'); ?></option>
										<?php
										foreach ($this->sportstypes AS $row)
										{
											echo '<option ';
											if (($row->name==$dSportsTypeName) ||
												($row->name==JText::_($dSportsTypeName)) ||
												(count($this->sportstypes)==1))
											{
												echo "selected='selected' ";
											}
											echo "value='$row->id'>";
											echo JText::_($row->name);
											echo '</option>';
										}
										?>
									</select>
									<br />
									<input	type='checkbox' name='newSportsTypeCheck' value="1"
											onclick="
											if (this.checked) {
												document.adminForm.sportstype.disabled=true;
												document.adminForm.sportstypeNew.disabled=false;
												document.adminForm.sportstypeNew.value=document.adminForm.sportstypeNew.value;
											} else {
												document.adminForm.sportstype.disabled=false;
												document.adminForm.sportstypeNew.disabled=true;
											}" /><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_NEW'); ?>
									<input type='text' name='sportstypeNew' size='30' maxlength='25' id='sportstypeNew' value="<?php echo stripslashes(htmlspecialchars(JText::_($dSportsTypeName))); ?>" disabled='disabled' />
								<?php
								}
								else
								{
									?>
									<input type="hidden" name="newSportsTypeCheck" value="1" />
									<?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_NEW'); ?>
									<input type='text' name='sportstypeNew' size='30' maxlength='25' id='sportstypeNew' value="<?php echo stripslashes(htmlspecialchars(JText::_($dSportsTypeName))); ?>" />
									<?php
								}
								?>
							</td>
						</tr>
						<?php
						if (($xmlImportType!='events') && ($xmlImportType!='positions'))
						{
							?>
							<tr>
								<td style='background-color:#EEEEEE'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_LEAGUE'); ?></td>
								<td style='background-color:#EEEEEE'>
									<?php
									if (isset($league->name))
									{
										$dLeagueName=$league->name;
										$leagueCountry=$league->country;
									}
									else
									{
										$dLeagueName=$proj->name;
										$leagueCountry='';
									}
									$dCountry=$leagueCountry;
									if (preg_match('=^[0-9]+$=',$dCountry))
									{
										$dCountry=$this->OldCountries[(int)$dCountry];
									}
									?>
									<select name='league' id='league'>
										<option selected value="0"><?php echo JText::_('JL_ADMIN_LMO_IMPORT_LEAGUE_SELECT'); ?></option>
										<?php
										if (count($this->leagues) > 0)
										{
											foreach ($this->leagues AS $row)
											{
												echo '<option ';
												//if ($row->name==$dLeagueName){echo "selected='selected' ";}
												if (($row->name==$dLeagueName)||(count($this->leagues)==1)){echo "selected='selected' ";}
												echo "value='$row->id'>";
												echo $row->name;
												echo '</option>';
											}
										}
										?>
									</select>
									<br />
									<?php
									if (count($this->leagues) < 1)
									{
										?>
										<input	checked='checked' type='checkbox' name='newLeagueCheck' value="1"
												onclick='this.checked=true;' /><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_NEW'); ?>
										<input	type='text' name='leagueNew' size='90' maxlength='75' id='leagueNew' value="<?php echo stripslashes(htmlspecialchars($dLeagueName)); ?>" disabled='disabled' />
										<script type="text/javascript">
											document.adminForm.newLeagueCheck.value=1;
											document.adminForm.league.disabled=true;
											document.adminForm.leagueNew.disabled=false;
											document.adminForm.leagueNew.value=document.adminForm.leagueNew.value;
										</script>
										<?php
									}
									else
									{
										?>
										<input	type='checkbox' name='newLeagueCheck' value="1"
												onclick="
												if (this.checked) {
													document.adminForm.league.disabled=true;
													document.adminForm.leagueNew.disabled=false;
													document.adminForm.leagueNew.value=document.adminForm.leagueNew.value;
												} else {
													document.adminForm.league.disabled=false;
													document.adminForm.leagueNew.disabled=true;
												}" /><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_NEW'); ?>
										<input type='text' name='leagueNew' size='90' maxlength='75' id='leagueNew' value="<?php echo stripslashes(htmlspecialchars($dLeagueName)); ?>" disabled='disabled' />
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td style='background-color:#DDDDDD'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_SEASON'); ?></td>
								<td style='background-color:#DDDDDD'>
									<?php
									if (isset($season->name))
									{
										$dSeasonName=$season->name;
									}
									else
									{
										$dSeasonName=$proj->name;
									}
									?>
									<select name='season' id='season'>
										<option selected value="0"><?php echo JText::_('JL_ADMIN_LMO_IMPORT_SEASON_SELECT'); ?></option>
										<?php
										if (count($this->seasons) > 0)
										{
											foreach ($this->seasons AS $row)
											{
												echo '<option ';
												//if ($row->name==$dSeasonName){echo "selected='selected' ";}
												if (($row->name==$dSeasonName)||(count($this->seasons)==1)){echo "selected='selected' ";}
												echo "value='$row->id'>";
												echo $row->name;
												echo '</option>';
											}
										}
										?>
									</select>
									<br />
									<?php
									if (count($this->leagues) < 1)
									{
										?>
										<input	checked='checked' type='checkbox' name='newSeasonCheck' value="1"
												onclick='this.checked=true;' /><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_NEW'); ?>
										<input	type='text' name='seasonNew' size='90' maxlength='75' id='seasonNew' value="<?php echo stripslashes(htmlspecialchars($dSeasonName)); ?>" disabled='disabled' />
										<script type="text/javascript">
											document.adminForm.newSeasonCheck.value=1;
											document.adminForm.season.disabled=true;
											document.adminForm.seasonNew.disabled=false;
											document.adminForm.seasonNew.value=document.adminForm.seasonNew.value;
										</script>
										<?php
									}
									else
									{
										?>
										<input	type='checkbox' name='newSeasonCheck' value="1"
												onclick="
												if (this.checked) {
													document.adminForm.season.disabled=true;
													document.adminForm.seasonNew.disabled=false;
													document.adminForm.seasonNew.value=document.adminForm.seasonNew.value;
												} else {
													document.adminForm.season.disabled=false;
													document.adminForm.seasonNew.disabled=true;
												}" /><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_NEW'); ?>
										<input type='text' name='seasonNew' size='90' maxlength='75' id='seasonNew' value="<?php echo stripslashes(htmlspecialchars($dSeasonName)); ?>" disabled='disabled' />
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td style='background-color:#EEEEEE'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_ADMIN'); ?></td>
								<td style='background-color:#EEEEEE'>
									<select name='admin' id='admin'>
										<?php
										foreach ($this->admins AS $row)
										{
											echo '<option ';
												if ($row->id==62){echo "selected='selected' ";}
												echo "value='$row->id'>";
												echo $row->username;
											echo '</option>';
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td style='background-color:#DDDDDD'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_EDITOR'); ?></td>
								<td style='background-color:#DDDDDD'>
									<select name='editor' id='editor'>
										<?php
										foreach ($this->editors AS $row)
										{
											echo '<option ';
												if ($row->id==62){echo "selected='selected' ";}
												echo "value='$row->id'>";
												echo $row->username;
											echo '</option>';
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td style='background-color:#EEEEEE'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_TEMPLATES'); ?></td>
								<td style='background-color:#EEEEEE'>
									<select name='copyTemplate' id='copyTemplate'>
										<option value="0" selected><?php echo JText::_('JL_ADMIN_LMO_IMPORT_TEMPLATES_USEOWN'); ?></option>
										<?php
										foreach ($this->templates AS $row)
										{
											echo "<option value=\"$row->id\">$row->name</option>\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td style='background-color:#DDDDDD'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_TIMEOFFSET'); ?></td>
								<td style='background-color:#DDDDDD'>
									<?php
									$timeoffset=array();
									for($h=(-12); $h <= 12; $h++)
									{
										if ($h==0){$output=sprintf('%02d',$h);}else{$output=sprintf('%+03d',$h);}
										$timeoffset[]=JHTML::_('select.option',$h.':00',JText::_($output.':00').'&nbsp;&nbsp;','id','name');
									}
// 									$serveroffsetList=JHTML::_(	'select.genericlist',
// 																$timeoffset,
// 																'serverOffset',
// 																' class="inputbox" size="1" style="text-align:right;" onchange="showActOffset();" ',
// 																'id',
// 																'name',
// 																'00:00',
// 																'timeoffset');
// 																// $proj->serveroffset
// 									unset($timeoffset);
// 									echo $serveroffsetList.'&nbsp;';
                  echo $this->lists['serveroffset'].'&nbsp;';
                  
// 									$output1="<input type='text' name='lmoacttime' id='lmoacttime' size='4' value=\"".JHTML::date(time(),'%H:%M')."\" style='font-weight: bold;'  />";
// 									echo sprintf(JText::_('JL_ADMIN_LMO_IMPORT_SERVER_ACTTIME'),$output1);
									?>
								</td>
							</tr>
							<tr>
								<td style='background-color:#EEEEEE'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_PUBLISH'); ?></td>
								<td style='background-color:#EEEEEE'>
									<input type='radio' name='publish' value="0" /><?php echo JText::_('JL_GLOBAL_NO'); ?>
									<input type='radio' name='publish' value="1" checked='checked' /><?php echo JText::_('JL_GLOBAL_YES'); ?>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</fieldset>
				<p style='text-align:right;'><a href='#page_bottom'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_BOTTOM'); ?></a></p>
				<?php
			}
			?>
			<?php
			// club anfang
			if ((isset($clubs) && count($clubs) > 0) || (isset($teams) && count($teams) > 0))
			{
				?>
				<fieldset>
					<legend><strong><?php
						if (!empty($clubs) && !empty($teams))
						{
							echo JText::_('JL_ADMIN_LMO_IMPORT_CLUBS_TEAMS_LEGEND'); //JL_XML_IMPORT_TEAM_CLUB_LEGEND
						}
						elseif (!empty($clubs))
						{
							echo JText::_('JL_ADMIN_LMO_IMPORT_CLUBS_LEGEND');
						}
						else
						{
							echo JText::_('JL_ADMIN_LMO_IMPORT_TEAMS_LEGEND');
						}
						?></strong></legend>
					<table class='adminlist'>
						<thead>
							<tr>
								<th width='5%' nowrap='nowrap'><?php
									$checkCount=((isset($clubs) && count($clubs) > 0)) ? count($clubs) : count($teams);
									echo JText::_('JL_ADMIN_LMO_IMPORT_ALL_NEW').'<br />';
									echo '<input type="checkbox" name="toggleTeamsClubs" value="" onclick="checkAllCustom('.$checkCount.')" />';
								?></th>
								<?php if (!empty($clubs)){ ?><th><?php echo JText::_('JL_ADMIN_LMO_IMPORT_TEAM_DATA'); ?></th><?php } ?>
								<?php if (!empty($teams)){ ?><th><?php echo JText::_('JL_ADMIN_LMO_IMPORT_CLUB_DATA'); ?></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$color1="#DDDDDD";
							$color2="#EEEEEE";
							//foreach ($teams AS $key=> $team)
							foreach ($teamsClubs AS $key=> $teamClub)
							{
								if ($key%2==1){$color=$color1;}else{$color=$color2;}
								?>
								<tr>
									<td width='10' nowrap='nowrap' style='text-align:center; vertical-align:middle; background-color:<?php echo $color; ?>'>
										<?php
										if (count($teamsClubs) > 0)
										{
											?>
											<input type='checkbox' value="<?php echo $key; ?>" name='cid[]' id='tc<?php echo $i; ?>' onchange='testTeamsClubsData(this,<?php echo $key; ?>)' />
											<?php
										}
										?>
									</td>
									<?php
									if (!empty($teams))
									{
										// Team column starts here
										$color='orange';
										$foundMatchingTeam=0;
										$foundMatchingTeamName='';
										
										$foundMatchingTeamShort='';
										$foundMatchingTeamMiddle='';
										$foundMatchingTeamAlias='';
										$foundMatchingTeamInfo='';
										
										$matchingTeam_ClubID=0;
										$matchingClubName='';

										if (count($this->teams) > 0)
										{
											foreach ($this->teams AS $row1)
											{
												if ($this->import_version=='OLD')
												{
													$teamInfo=$teamClub->description;
												}
												else
												{
													$teamInfo=$teamClub->info;
												}
												/*
												if (strtolower($teamClub->name)==strtolower($row1->name) &&
// 													strtolower($teamClub->short_name)==strtolower($row1->short_name) &&
// 													strtolower($teamClub->middle_name)==strtolower($row1->middle_name) &&
													strtolower($teamInfo)==strtolower($row1->info)
													)
													*/
												if ( ( strtolower($teamClub->name)==strtolower($row1->name)  ) ||
												( strtolower($teamClub->name)==strtolower($row1->alias)  ) ||
												( JFilterOutput::stringURLSafe($teamClub->name)==strtolower($row1->alias)  )
													)
                          	
												{
													$foundMatchingTeam = $row1->id;
													//$foundMatchingTeamName = $teamClub->name;
													$foundMatchingTeamName = $row1->name;
													$foundMatchingTeamShort= $row1->short_name;
										      $foundMatchingTeamMiddle= $row1->middle_name;
										      $foundMatchingTeamAlias= $row1->alias;
										      $foundMatchingTeamInfo= $row1->info;
										
													$matchingTeam_ClubID = $row1->club_id;
													
													$clubCountry = $row1->country;
													
													if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                          {
                          echo 'diddi 10 ('.$foundMatchingTeamName.')'.' ('.$clubCountry.')<br>';
                          }
                          
                          
													if (!empty($clubs))
													{
														foreach ($this->clubs AS $row2)
														{
															if ($row2->id==$matchingTeam_ClubID)
															{
																$matchingClubName=$row2->name;
																break;
															}
														}
													}
													break;
												}
											}
										}
										if ($foundMatchingTeam){$color='lightgreen';}
										?>
										<td width='45%' style='text-align:left; vertical-align:top; background-color:<?php echo $color; ?>' id='tetd<?php echo $key; ?>'>
											<?php
											if ($foundMatchingTeam)
											{
												$checked="checked='checked' ";
												$disabled='';
											}
											else
											{
												$checked='';
												$disabled="disabled='disabled' ";
											}
											echo "<input type='checkbox' name='chooseTeam_$key' $checked";
											echo "onclick='if(this.checked)
													{
														document.adminForm.selectTeam_$key.checked=false;
														document.adminForm.createTeam_$key.checked=false;
														document.adminForm.teamName_$key.disabled=true;
														document.adminForm.teamShortname_$key.disabled=true;
														document.adminForm.teamInfo_$key.disabled=true;
														document.adminForm.teamMiddleName_$key.disabled=true;
													}
													else
													{
													}' $disabled ";
											echo "/>&nbsp;";
											$output="<input type='text' name='dbTeamName_$key' size='40' maxlength='60' value=\"".stripslashes(htmlspecialchars($foundMatchingTeamName))."\" style='font-weight: bold;' disabled='disabled' />";
											echo JText::sprintf('JL_ADMIN_LMO_IMPORT_USE_TEAM',$output,$foundMatchingTeam);
											//echo "<input type='hidden' name='dbTeamID_$key' value=\"".stripslashes(htmlspecialchars($foundMatchingTeam))."\" $disabled />";
											
                      if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                      {
                      $debugtext = 'text';
                      }
                      else
                      {
                      $debugtext = 'hidden';
                      }
                      
                      echo "<input type='$debugtext' name='dbTeamID_$key' value=\"".stripslashes(htmlspecialchars($foundMatchingTeam))."\" $disabled />";
											echo '<br />';

											if (count($this->teams) > 0)
											{
												echo "<input type='checkbox' name='selectTeam_$key' ";
												echo "onclick='javascript:openSelectWindow(";
												echo $foundMatchingTeam;
												echo ",".$key;
												echo ',"selector"';
												echo ",this";
												echo ",1";
												echo ")' ";
												echo "/>&nbsp;";
												echo JText::_('JL_ADMIN_LMO_IMPORT_ASSIGN_TEAM');
												echo '<br />';
											}
											else
											{
												echo "<input type='hidden' name='selectTeam_$key' />";
											}

											if ($foundMatchingTeam)
											{
												$checked='';
												$disabled="disabled'disabled' ";
												
												$teamClub->name = $foundMatchingTeamName;
												$teamClub->short_name = $foundMatchingTeamShort;
										    $teamClub->middle_name = $foundMatchingTeamMiddle;
										    $teamClub->alias = $foundMatchingTeamAlias;
										    $teamClub->info = $foundMatchingTeamInfo;
										      
												//$disabled = 'disabled=true';
											}
											else
											{
												$checked="checked='checked' ";
												$disabled='';
											}
											echo "<input type='checkbox' name='createTeam_$key' $checked ";
											echo "onclick='if(this.checked)
													{
														document.adminForm.chooseTeam_$key.checked=false;
														document.adminForm.selectTeam_$key.checked=false;
														document.adminForm.teamName_$key.disabled=false;
														document.adminForm.teamShortname_$key.disabled=false;
														document.adminForm.teamInfo_$key.disabled=false;
														document.adminForm.teamMiddleName_$key.disabled=false;
													}
													else
													{
														document.adminForm.teamName_$key.disabled=true;
														document.adminForm.teamShortname_$key.disabled=true;
														document.adminForm.teamInfo_$key.disabled=true;
														document.adminForm.teamMiddleName_$key.disabled=true;
													}' ";
											echo "/>&nbsp;";
											echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_TEAM');
											
                      if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                      {
                      echo ' ('.$teamClub->club_id.')';
                      }
                      
                      
											
											?>
											<br />
											<table cellspacing='0' cellpadding='0'>
												<tr>
													<td>
														<?php
														echo '<b>'.JText::_('JL_ADMIN_LMO_IMPORT_TEAMNAME').'</b>';
														?><br /><input type='hidden' name='teamID_<?php echo $key; ?>' value="<?php echo $key; ?>" <?php echo $disabled; ?> />
														<input type='text' name='teamName_<?php echo $key; ?>' size='45' maxlength='60' value="<?php echo stripslashes(htmlspecialchars($teamClub->name)); ?>" <?php echo $disabled; ?> />
													</td>
													<td>
														<?php
														echo '<b>'.JText::_('JL_ADMIN_LMO_IMPORT_TEAMSHORT').'</b>';
														?><br /><input type='text' name='teamShortname_<?php echo $key; ?>' size='20' maxlength='15' value="<?php echo stripslashes(htmlspecialchars($teamClub->short_name)); ?>" <?php echo $disabled; ?> />
													</td>
												</tr>
												<tr>
													<td>
														<?php
														if ($this->import_version=='OLD')
														{
															$teamInfo=$teamClub->description;
														}
														else
														{
															$teamInfo=$teamClub->info;
														}
														echo '<b>'.JText::_('Info').'</b>';
														?><br /><input type='text' name='teamInfo_<?php echo $key; ?>' size='45' maxlength='255' value="<?php echo stripslashes(htmlspecialchars($teamInfo)); ?>" <?php echo $disabled; ?> />
													</td>
													<td>
														<?php
														echo '<b>'.JText::_('Middle Name').'</b>';
														?><br /><input type='text' name='teamMiddleName_<?php echo $key; ?>' size='20' maxlength='25' value="<?php echo stripslashes(htmlspecialchars($teamClub->middle_name)); ?>" <?php echo $disabled; ?> />
													</td>
												</tr>
											</table>
										</td>
										<?php
									}

									if (!empty($clubs))
									{
										// Club column starts here
										$color='orange';
										$clubname='';
										$clubid=0;
										$clubPlaygroundID=0;
										$clubCountry = '';
										
										//$clubCountry=0;
										// $clubCountry = 'DEU';

										if (!empty($teams))
										{
											foreach ($clubs as $club)
											{
												if ((int)$club->id==(int)$teamClub->club_id)
												{
													$clubid=$club->id;
													$clubname=$club->name;
													//$clubCountry=$club->country;
													//echo $clubname.":".$clubCountry.",";
													
													//if (preg_match('=^[0-9]+$=',$clubCountry)){$clubCountry=$this->OldCountries[(int)$clubCountry];}
													$clubPlaygroundID=$club->standard_playground;
													
													if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                          {
                          echo 'diddi 1 ('.$clubname.')'.' ('.$clubCountry.')<br>';
                          }
                            
													break; //only one club possible...
												}
											}
											
											if (count($this->clubs) > 0)
											{
												foreach ($this->clubs AS $row1)
												{
													
                          // $clubCountry = $row1->country;
													
// 											if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
//                       {
//                       echo ' [ diddi 8 ('.$row1->name.')'.' ('.$clubCountry.') ]<br>';
//                       }
                      
													if ( strtolower($clubname) == strtolower($row1->name) )
													{
														$color='lightgreen';
														$matchingTeam_ClubID=$row1->id;
														$matchingClubName=$row1->name;
														$clubCountry=$row1->country;
														$clubid=$club->id;
														//maybe also here row1->standard_playground???
														$clubPlaygroundID=$club->standard_playground;
														
                            if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                            {
                            echo 'diddi 2 ('.$clubname.')'.' ('.$clubCountry.')<br>';
                            }
                            
														break;
													}
												}
											}
											/**/
										}
										else
										/**/
										{
											$matchingTeam_ClubID=0;
											$matchingClubName='';
											$club=$teamClub;
											$clubid=$club->id;
											$clubCountry = $club->country;
											
                      if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                      {
                      echo ' [ diddi 7 ('.$club->name.')'.' ('.$clubCountry.') ]<br>';
                      }
                      
											if (count($this->clubs) > 0)
											{
												foreach ($this->clubs AS $row1)
												{
													if ( strtolower($club->name)==strtolower($row1->name) &&
														strtolower($club->country)==strtolower($row1->country) )
													{
														$color='lightgreen';
														$matchingTeam_ClubID = $row1->id;
														$matchingClubName = $teamClub->name;

														$clubid = $club->id;
														$clubCountry = $club->country;
														$clubPlaygroundID = $club->standard_playground;
														
														if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                            {
                            echo 'diddi 3 ('.$club->name.')'.' ('.$clubCountry.')<br>';
                            }
                      
														break;
													}
												}
											}
											
										}
										if ($matchingTeam_ClubID){$color='lightgreen';}
										?>
										<td width='45%' style='text-align:left; vertical-align:top; background-color:<?php echo $color; ?>' id='cltd<?php echo $key; ?>'>
											<?php
											if ($matchingTeam_ClubID)
											{
												$checked="checked='checked' ";
												$disabled='';
											}
											else
											{
												$checked='';
												$disabled="disabled='disabled' ";
											}
											echo "<input type='checkbox' name='chooseClub_$key' $checked";
											echo "onclick='if(this.checked)
													{
														document.adminForm.selectClub_$key.checked=false;
														document.adminForm.createClub_$key.checked=false;
														document.adminForm.clubName_$key.disabled=true;
														document.adminForm.clubCountry_$key.disabled=true;
													}
													else
													{
													}' $disabled ";
											echo "/>&nbsp;";
											$output="<input type='text' name='dbClubName_$key' size='45' maxlength='100' value=\"".stripslashes(htmlspecialchars($matchingClubName))."\" style='font-weight: bold; ' disabled='disabled' />";
											echo JText::sprintf('JL_ADMIN_LMO_IMPORT_USE_CLUB',$output,$matchingTeam_ClubID);
											
                      if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                      {
                      $debugtext = 'text';
                      }
                      else
                      {
                      $debugtext = 'hidden';
                      }
                      
                      echo "<input type='$debugtext' name='dbClubPlaygroundID_$key' value=\"$clubPlaygroundID\" $disabled />";
											echo "<input type='$debugtext' name='dbClubID_$key' value=\"$matchingTeam_ClubID\" $disabled $disabled />";
											echo '<br />';

											if (count($this->clubs) > 0)
											{
												echo "<input type='checkbox' name='selectClub_$key' ";
												echo "onclick='javascript:openSelectWindow(";
												echo $matchingTeam_ClubID;
												echo ",".$key;
												echo ',"selector"';
												echo ",this";
												echo ",2";
												echo ")' ";
												echo "/>&nbsp;";
												echo JText::_('JL_ADMIN_LMO_IMPORT_ASSIGN_CLUB');
												echo '<br />';
											}
											else
											{
												echo "<input type='hidden' name='selectClub_$key' />";
											}
											if ($matchingTeam_ClubID)
											{
												$checked='';
												$disabled="disabled'disabled' ";
											}
											else
											{
												$checked="checked='checked' ";
												$disabled='';
											}
											echo "<input type='checkbox' name='createClub_$key' $checked ";
											echo "onclick='if(this.checked)
																{
																	document.adminForm.chooseClub_$key.checked=false;
																	document.adminForm.selectClub_$key.checked=false;
																	document.adminForm.clubName_$key.disabled=false;
																	document.adminForm.clubCountry_$key.disabled=false;
																	document.adminForm.clubID_$key.disabled=false;
																}
																else
																{
																	document.adminForm.clubName_$key.disabled=true;
																	document.adminForm.clubCountry_$key.disabled=true;
																	document.adminForm.clubID_$key.disabled=true;
														}' ";
											echo "/>&nbsp;";
											echo JText::_('JL_ADMIN_LMO_IMPORT_CREATE_CLUB');
											
                      if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                      {
                      echo ' [ diddi 4 ('.$club->id.')'.' ('.$club->country.') ]<br>';
                      echo ' [ diddi 6 ('.$club->id.')'.' ('.$clubCountry.') ]<br>';
                      echo ' [ diddi 20 (name datei: '.$club->name.')'.' (name db: '.$matchingClubName.') ]<br>';
                      $debugtext = 'text';
                      }
                      else
                      {
                      $debugtext = 'hidden';
                      }
                      
											?>
											<br />
											<table cellspacing='0' cellpadding='0'>
												<tr>
													<td>
														<?php
														echo '<b>'.JText::_('JL_ADMIN_LMO_IMPORT_CLUBNAME').'</b>';
														?><br />
														<input type='<?php echo $debugtext; ?>' name='clubID_<?php echo $key; ?>' value="<?php echo $clubid; ?>" <?php echo $disabled; ?> />
														<?php
														if ( $matchingClubName )
														{
														//$disabled = 'disabled=true';
														?>
                            <input type='text' name='clubName_<?php echo $key; ?>' size='60' maxlength='100' value="<?php echo stripslashes(htmlspecialchars($matchingClubName)); ?>" <?php echo $disabled; ?> />
                            <?php
                            }
                            else
                            {
                            ?>
                            <input type='text' name='clubName_<?php echo $key; ?>' size='60' maxlength='100' value="<?php echo stripslashes(htmlspecialchars($club->name)); ?>" <?php echo $disabled; ?> />
                            <?php
                            }
                            ?>
                            
													</td>
													<td>
														<?php
														echo '<b>'.JText::_('JL_ADMIN_LMO_IMPORT_CLUBCOUNTRY').'</b>';
                            if ( !$clubCountry )
                            {
                            $clubCountry = $club->country;
                            }
														$dCountry = $clubCountry; 
                            //echo ": ".$dCountry;
                            echo ": ".$clubCountry;
														//if (preg_match('=^[0-9]+$=',$dCountry)){$dCountry=$this->OldCountries[(int)$dCountry];}
														?><br />
														<?php 
															//build the html select list for countries
															$countries[] = JHTML::_( 'select.option', '', '- ' . JText::_( 'Select country' ) . ' -' );
															if ( $res =& Countries::getCountryOptions() )
															{
																$countries = array_merge( $countries, $res );
																
																if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
                                {
                                echo ' [ diddi 5 ('.$club->id.')'.' ('.$club->country.') ('.$key.') ]<br>';
                                //$clubCountry = 'DEU';
                                }
                      
															}
// 															$countrieslist = JHTML::_(	'select.genericlist',
// 																						$countries,
// 																						'clubCountry_'.$key,
// 																						'class="inputbox" size="1" '.$disabled,
// 																						'value',
// 																						'text',
// 																						$dCountry);
                                $countrieslist = JHTML::_(	'select.genericlist',
																						$countries,
																						'clubCountry_'.$key,
																						'class="inputbox" size="1" '.$disabled,
																						'value',
																						'text',
																						$clubCountry);
															unset($countries);
															echo $countrieslist;
														?>
													</td>
												</tr>
											</table>
										</td>
										<?php
									}
									?>
								</tr>
								<?php
								$i++;
							}
							?>
						</tbody>
					</table>
				</fieldset>
				<p style='text-align:right;'><a href='#page_top'><?php echo JText::_('JL_ADMIN_LMO_IMPORT_TOP'); ?></a></p>
				<?php
			}
      // club ende
			?>
			
			
			<p>
				<a name='page_bottom'></a><input type='submit' value="<?php echo JText::_('JL_ADMIN_LMO_IMPORT_START_BUTTON'); ?>" />
			</p>
		</form>
	</div>
	<?php
	if (JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0))
	{
		echo '<center><hr>';
			echo JText::sprintf('Memory Limit is %1$s',ini_get('memory_limit')).'<br />';
			echo JText::sprintf('Memory Peak Usage was %1$s Bytes',number_format(memory_get_peak_usage(true),0,'','.')).'<br />';
			echo JText::sprintf('Time Limit is %1$s seconds',ini_get('max_execution_time')).'<br />';
			$mtime=microtime();
			$mtime=explode(" ",$mtime);
			$mtime=$mtime[1] + $mtime[0];
			$endtime=$mtime;
			$totaltime=($endtime - $this->starttime);
			echo JText::sprintf('This page was created in %1$s seconds',$totaltime);
		echo '<hr></center>';
	}

}
?>