<?php
/**
 * @copyright   Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license	 GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');
jimport('joomla.filesystem.file');

/**
 * HTML View class for the Joomleague component
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	1.5.0a
 */
require_once(JPATH_COMPONENT.DS.'models'.DS.'sportstypes.php');
require_once(JPATH_COMPONENT.DS.'models'.DS.'leagues.php');
class JoomleagueViewJoomleague extends JLGView
{

	function display($tpl=null)
	{
		// hide main menu in image selector window
		$viewName=JRequest::getCmd('view');
		if ($viewName == 'imagehandler')
		{
			return;
		}

		// hide main menu in result/match edit view
		$viewName=JRequest::getCmd('view');
		if ($viewName == 'matches')
		{
			return;
		}

		if ($this->getLayout() == 'default')
		{
			$this->_displayMenu($tpl);
			return;
		}

		if ($this->getLayout() == 'panel')
		{
			$this->_displayPanel($tpl);
			return;
		}

		if ($this->getLayout() == 'footer')
		{
			parent::display();
		}
	}

	// display control panel
	function _displayPanel($tpl)
	{
			
		//get the project
		$project = $this->get('data');
		
		$iProjectDivisionsCount = 0;
		$mdlProjectDivisions = JModel::getInstance("divisions", "JoomleagueModel");
		$iProjectDivisionsCount = $mdlProjectDivisions->getProjectDivisionsCount($project->id);
		
		$iProjectPositionsCount = 0;
		$mdlProjectPositions = JModel::getInstance("Projectposition", "JoomleagueModel");
		$iProjectPositionsCount = $mdlProjectPositions->getProjectPositionsCount($project->id);
		
		$iProjectRefereesCount = 0;
		$mdlProjectReferees = JModel::getInstance("Projectreferees", "JoomleagueModel");
		$iProjectRefereesCount = $mdlProjectReferees->getProjectRefereesCount($project->id);
		
		$iProjectTeamsCount = 0;
		$mdlProjecteams = JModel::getInstance("Projectteams", "JoomleagueModel");
		$iProjectTeamsCount = $mdlProjecteams->getProjectTeamsCount($project->id);
		
		$iMatchDaysCount = 0;
		$mdlRounds = JModel::getInstance("Rounds", "JoomleagueModel");
		$iMatchDaysCount = $mdlRounds->getRoundsCount($project->id);
		
		$this->assignRef('project',$project);
		$this->assignRef('count_projectdivisions',$iProjectDivisionsCount);
		$this->assignRef('count_projectpositions',$iProjectPositionsCount);
		$this->assignRef('count_projectreferees', $iProjectRefereesCount);
		$this->assignRef('count_projectteams', $iProjectTeamsCount );
		$this->assignRef('count_matchdays', $iMatchDaysCount);
		$this->assignRef('params',$params);
		$this->assignRef('comp_params',$comp_params);

		parent::display($tpl);
	}

	// diplay left Menu
	function _displayMenu($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		JHTML::_('behavior.mootools');
		$db = JFactory::getDBO();
		$document = JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript(JURI::base().'components/com_joomleague/assets/js/quickmenu.js?v='.$version);
		$uri = JFactory::getURI();
		$model = $this->getModel('project') ;
		$params = JComponentHelper::getParams($option);

		$stid=JRequest::getVar('stid',array(0),'','array');
		$sports_type_id=$mainframe->getUserState($option.'sportstypes',0);
		
		if($stid[0] > 0 || $sports_type_id >0)
		{
			if($stid[0] >0) {
				$mainframe->setUserState($option.'sportstypes',$stid[0]);
			}
		} else {
			$defsportstype = $params->get("defsportstype");
			$defsportstype = (empty($defsportstype)) ? "1" : $params->get("defsportstype");
			$mainframe->setUserState($option.'sportstypes', $defsportstype);
		}
		$seasonnav = $mainframe->getUserState($option.'seasonnav');
		
		$pid=JRequest::getVar('pid',array(0),'','array');
		if($pid[0] > 0)
		{
			$mainframe->setUserState($option.'project',$pid[0]);
			$model->setId($pid[0]);
		}

		$project = $this->get('Data');
		$model = $this->getModel();

		$use_seasons=$params->get('cfg_show_seasons_in_project_drop_down',0); //Use seasons in dropdown or not

		//build the html select list for sports-types
		$sports_type_id=$mainframe->getUserState($option.'sportstypes',0);
		$project_id=$pid[0];
		if($sports_type_id > 0)
		{
			$project_id = $mainframe->getUserState($option.'project', 0);
		}

		$allSportstypes = JoomleagueModelSportsTypes::getSportsTypes();
		$sportstypes[]	= JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SPORTSTYPE'),'id','name');
		$allSportstypes=array_merge($sportstypes,$allSportstypes);
		$lists['sportstypes']=JHTML::_('select.genericList',
										$allSportstypes,
										'stid[]',
										'class="inputbox" style="width:100%"',
										'id',
										'name',
										$sports_type_id);

		if($mainframe->getUserState($option.'sportstypes',0))
		{
			// seasons
			$seasons[] = JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SEASON'),'id','name');
			$seasons = array_merge($seasons, $model->getSeasons());

			$lists['seasons']=JHTML::_('select.genericList',
										$seasons,
										'seasonnav',
										'class="inputbox" style="width:100%"',
										'id',
										'name',
										$seasonnav);
			
			//build the html select list for projects
			$projects[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_PROJECT'),'id','name');

			//check if the season filter is set and select the needed projects
			if (!$use_seasons)
			{
				if ($res = $model->getProjectsBySportsType($sports_type_id, $seasonnav)){
					$projects=array_merge($projects,$res);
				}
			}
			else
			{
				if ($res = $model->getSeasonProjects($seasonnav)){
					$projects=array_merge($projects,$res);
				}
			}

			$lists['projects']=JHTML::_('select.genericList',
										$projects,
										'pid[]',
										'class="inputbox" style="width:100%"',
										'id',
										'name',
										$project_id);
		}

		// if a project is active we create the teams and rounds select lists
		if($project_id > 0)
		{
			$team_id = JRequest::getInt("ptid", 0);
			if($team_id==0) {
				$team_id = $mainframe->getUserState($option.'project_team_id');
			}
			$projectteams[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'),'value','text');

			if ($res = $model->getProjectteams())
			{
				$projectteams=array_merge($projectteams,$res);
			}

			$lDummy ='class="inputbox" ';
			$lDummy .= 'style="width:100%"';
			$lists['projectteams']=JHTML::_('select.genericList',
											$projectteams,
											'tid[]',
											'class="inputbox" style="width:100%"',
											'value',
											'text',
											$team_id);

			$round_id=$mainframe->getUserState($option.'round_id');
			$projectrounds[]=JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_ROUND'),'value','text');
			
			$mdlRound = JModel::getInstance("Round", "JoomleagueModel");
			$mdlRound->setId($project->current_round);
			$round = $mdlRound->getData();
			$projectrounds[]=JHTML::_('select.option', $round->id ,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_CURRENT_ROUND'),'value','text');
			if ($ress = JoomleagueHelper::getRoundsOptions($project_id, 'ASC', true))
			{
				foreach($ress as $res)
				{
					$project_roundslist[]=JHTML::_('select.option',$res->value,$res->text);
				}
				$projectrounds=array_merge($projectrounds,$project_roundslist);
			}

			$lists['projectrounds']=JHTML::_('select.genericList',$projectrounds,
											'rid[]',
											'class="inputbox" style="width:100%"',
											'value',
											'text',
											$round_id);
		}

		$imagePath='administrator/components/com_joomleague/assets/images/';
		$tabs=array();
		$pane=new stdClass();
		$pane->title=JText::_('COM_JOOMLEAGUE_D_MENU_GENERAL');
		$pane->name='General data';
		$pane->alert=false;
		$tabs[]=$pane;
		$content['General data']=

		$link=array();
		$label=array();
		$limage=array();

		$link1=array();
		$label1=array();
		$limage1=array();

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=projects&task=project.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_PROJECTS');
		$limage1[]=JHTML::_('image',$imagePath.'projects.png',JText::_('COM_JOOMLEAGUE_D_MENU_PROJECTS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=sportstypes&task=sportstype.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_SPORTSTYPES');
		$limage1[]=JHTML::_('image',$imagePath.'sportstypes.png',JText::_('COM_JOOMLEAGUE_D_MENU_SPORTSTYPES'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=leagues&task=league.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_LEAGUES');
		$limage1[]=JHTML::_('image',$imagePath.'leagues.png',JText::_('COM_JOOMLEAGUE_D_MENU_LEAGUES'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=seasons&task=season.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_SEASONS');
		$limage1[]=JHTML::_('image',$imagePath.'seasons.png',JText::_('COM_JOOMLEAGUE_D_MENU_SEASONS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=clubs&task=club.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_CLUBS');
		$limage1[]=JHTML::_('image',$imagePath.'clubs.png',JText::_('COM_JOOMLEAGUE_D_MENU_CLUBS'));


		$link1[]=JRoute::_('index.php?option=com_joomleague&view=teams&task=team.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_TEAMS');
		$limage1[]=JHTML::_('image',$imagePath.'icon-16-Teams.png',JText::_('COM_JOOMLEAGUE_D_MENU_TEAMS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=persons&task=person.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_PERSONS');
		$limage1[]=JHTML::_('image',$imagePath.'players.png',JText::_('COM_JOOMLEAGUE_D_MENU_PERSONS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=eventtypes&task=eventtype.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_EVENTS');
		$limage1[]=JHTML::_('image',$imagePath.'events.png',JText::_('COM_JOOMLEAGUE_D_MENU_EVENTS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=statistics&task=statistic.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_STATISTICS');
		$limage1[]=JHTML::_('image',$imagePath.'calc16.png',JText::_('COM_JOOMLEAGUE_D_MENU_STATISTICS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=positions&task=position.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_POSITIONS');
		$limage1[]=JHTML::_('image',$imagePath.'icon-16-Positions.png',JText::_('COM_JOOMLEAGUE_D_MENU_POSITIONS'));

		$link1[]=JRoute::_('index.php?option=com_joomleague&view=playgrounds&task=playground.display');
		$label1[]=JText::_('COM_JOOMLEAGUE_D_MENU_VENUES');
		$limage1[]=JHTML::_('image',$imagePath.'playground.png',JText::_('COM_JOOMLEAGUE_D_MENU_VENUES'));

		$link[]=$link1;
		$label[]=$label1;
		$limage[]=$limage1;

		if ($project->id)
		{
			$link2=array();
			$label2=array();
			$limage2=array();

			$project_type=$project->project_type;

			if ($project_type == 0) // No divisions
			{
				$pane=new stdClass();
				$pane->title=JText::_('COM_JOOMLEAGUE_P_MENU_PROJECT');
				$pane->name='PMenu';
				$pane->alert=false;
				$tabs[]=$pane;

				$link2[]=JRoute::_('index.php?option=com_joomleague&task=project.edit&cid[]='.$project->id);
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_PSETTINGS');
				$limage2[]=JHTML::_('image',$imagePath.'projects.png',JText::_('COM_JOOMLEAGUE_P_MENU_PSETTINGS'));

				$link2[]=JRoute::_('index.php?option=com_joomleague&view=templates&task=template.display');
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_FES');
				$limage2[]=JHTML::_('image',$imagePath.'icon-16-FrontendSettings.png',JText::_('COM_JOOMLEAGUE_P_MENU_FES'));

				if ((isset($project->project_type)) &&
					($project->project_type == 'DIVISIONS_LEAGUE'))
				{
					$link2[]=JRoute::_('index.php?option=com_joomleague&view=divisions&task=division.display');
					$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_DIVISIONS');
					$limage2[]=JHTML::_('image',$imagePath.'icon-16-Divisions.png',JText::_('COM_JOOMLEAGUE_P_MENU_DIVISIONS'));
				}
				if ((isset($project->project_type)) &&
					(($project->project_type == 'TOURNAMENT_MODE') ||
					($project->project_type == 'DIVISIONS_LEAGUE')))
				{
					$link2[] =JRoute::_('index.php?option=com_joomleague&view=treetos&task=treeto.display');
					$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_TREE');
					$limage2[]=JHTML::_('image',$imagePath.'icon-16-Tree.png',JText::_('COM_JOOMLEAGUE_P_MENU_TREE'));
				}

				$link2[]=JRoute::_('index.php?option=com_joomleague&view=projectposition&task=projectposition.display');
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_POSITIONS');
				$limage2[]=JHTML::_('image',$imagePath.'icon-16-Positions.png',JText::_('COM_JOOMLEAGUE_P_MENU_POSITIONS'));

				$link2[]=JRoute::_('index.php?option=com_joomleague&view=projectreferees&task=projectreferee.display');
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_REFEREES');
				$limage2[]=JHTML::_('image',$imagePath.'icon-16-Referees.png',JText::_('COM_JOOMLEAGUE_P_MENU_REFEREES'));

				$link2[]=JRoute::_('index.php?option=com_joomleague&view=projectteams&task=projectteam.display');
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_TEAMS');
				$limage2[]=JHTML::_('image',$imagePath.'icon-16-Teams.png',JText::_('COM_JOOMLEAGUE_P_MENU_TEAMS'));

				$link2[]=JRoute::_('index.php?option=com_joomleague&view=rounds&task=round.display');
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_MATCHDAYS');
				$limage2[]=JHTML::_('image',$imagePath.'icon-16-Matchdays.png',JText::_('COM_JOOMLEAGUE_P_MENU_MATCHDAYS'));

				$link2[]=JRoute::_('index.php?option=com_joomleague&view=jlxmlexports&task=jlxmlexport.display');
				$label2[]=JText::_('COM_JOOMLEAGUE_P_MENU_XML_EXPORT');
				$limage2[]=JHTML::_('image',$imagePath.'icon-16-XMLExportData.png',JText::_('COM_JOOMLEAGUE_P_MENU_XML_EXPORT'));
			}

			$link[]=$link2;
			$label[]=$label2;
			$limage[]=$limage2;
		}

		$link3=array();
		$label3=array();
		$limage3=array();

		$pane=new stdClass();
		$pane->title=JText::_('COM_JOOMLEAGUE_M_MENU_MAINTENANCE');
		$pane->name='MMenu';
		$pane->alert=false;
		$tabs[]=$pane;

		$link3[]=JRoute::_('index.php?option=com_joomleague&task=settings.edit');
		$label3[]=JText::_('COM_JOOMLEAGUE_M_MENU_SETTINGS');
		$limage3[]=JHTML::_('image',$imagePath.'settings.png',JText::_('COM_JOOMLEAGUE_M_MENU_SETTINGS'));

		$link3[]=JRoute::_('index.php?option=com_joomleague&view=jlxmlimports&task=jlxmlimport.display');
		$label3[]=JText::_('COM_JOOMLEAGUE_M_MENU_XML_IMPORT');
		$limage3[]=JHTML::_('image',$imagePath.'import.png',JText::_('COM_JOOMLEAGUE_M_MENU_XML_IMPORT'));

		$link3[]=JRoute::_('index.php?option=com_joomleague&view=databasetools&task=databasetool.display');
		$label3[]=JText::_('COM_JOOMLEAGUE_M_MENU_TOOLS');
		$limage3[]=JHTML::_('image',$imagePath.'repair.gif',JText::_('COM_JOOMLEAGUE_M_MENU_TOOLS'));

		$link3[]=JRoute::_('index.php?option=com_joomleague&view=updates&task=update.display');
		$label3[]=JText::_('COM_JOOMLEAGUE_M_MENU_UPDATES');
		$limage3[]=JHTML::_('image',$imagePath.'update.png',JText::_('COM_JOOMLEAGUE_M_MENU_UPDATES'));
		
		$link[]=$link3;
		$label[]=$label3;
		$limage[]=$limage3;

    $link4=array();
		$label4=array();
		$limage4=array();
		$pane=new stdClass();
		$pane->title=JText::_('COM_JOOMLEAGUE_DIDDIPOELER');
		$pane->name='MMenuDiddipoeler';
		$pane->alert=false;
		$tabs[]=$pane;
		$link4[]=JRoute::_('index.php?option=com_joomleague&view=jlextlmoimports&task=jlextlmoimports.display');
		$label4[]=JText::_('COM_JOOMLEAGUE_LMO_IMPORT');
		$limage4[]=JHTML::_('image',$imagePath.'update.png',JText::_('COM_JOOMLEAGUE_LMO_IMPORT'));
		$link4[]=JRoute::_('index.php?option=com_joomleague&view=jlextdbbimports&task=jlextdbbimports.display');
		$label4[]=JText::_('COM_JOOMLEAGUE_DBB_IMPORT');
		$limage4[]=JHTML::_('image',$imagePath.'update.png',JText::_('COM_JOOMLEAGUE_DBB_IMPORT'));
    $link4[]=JRoute::_('index.php?option=com_joomleague&view=jlextdfbnetplayerimport&task=jlextdfbnetplayerimport.display');
		$label4[]=JText::_('COM_JOOMLEAGUE_DFBNET_IMPORT');
		$limage4[]=JHTML::_('image',$imagePath.'update.png',JText::_('COM_JOOMLEAGUE_DFBNET_IMPORT'));
		$link4[]=JRoute::_('index.php?option=com_joomleague&view=jlextassociations&task=jlextassociation.display');
		$label4[]=JText::_('COM_JOOMLEAGUE_ASSOCIATION');
		$limage4[]=JHTML::_('image',$imagePath.'update.png',JText::_('COM_JOOMLEAGUE_ASSOCIATION'));
		$link[]=$link4;
		$label[]=$label4;
		$limage[]=$limage4;

  $link5=array();
	$label5=array();
	$limage5=array();
  $pane=new stdClass();
	$pane->title=JText::_('JL_T_MENU_PREDICTIONGAMES');
	$pane->name='PREDICTIONGAMES';
	$pane->alert=false;
	$tabs[]=$pane;
  $link5[]=JRoute::_('index.php?option=com_joomleague&view=predictiongames&task=predictiongame.display');
	$label5[]=JText::_('JL_T_MENU_PREDICTIONGAMES');
	$limage5[]=JHTML::_('image',$imagePath.'icon-16-FrontendSettings.png',JText::_('JL_T_MENU_PREDICTIONGAMES'));
	$link5[]=JRoute::_('index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display');
	$label5[]=JText::_('JL_T_MENU_PREDICTIONMEMBERS');
	$limage5[]=JHTML::_('image',$imagePath.'icon-16-FrontendSettings.png',JText::_('JL_T_MENU_PREDICTIONMEMBERS'));
	$link5[]=JRoute::_('index.php?option=com_joomleague&view=predictiontemplates&task=predictiontemplate.display');
	$label5[]=JText::_('JL_T_MENU_PREDICTIONTEMPLATES');
	$limage5[]=JHTML::_('image',$imagePath.'icon-16-FrontendSettings.png',JText::_('JL_T_MENU_PREDICTIONTEMPLATES'));
	$link[]=$link5;
	$label[]=$label5;
	$limage[]=$limage5;
    		
		// active pane selector
		if ($project->id)
		{
			switch (JRequest::getVar('view'))
			{
				case 'projects':			
				case 'leagues':
				case 'seasons':
				case 'sportstypes':
				case 'clubs':
				case 'teams':
				case 'persons':
				case 'eventtypes':				
				case 'statistics':
				case 'positions':
				case 'playgrounds':			$active=0;
				break;

				case 'settings': 
				case 'updates':
				case 'jlxmlimports':
				case 'databasetools': 		$active=2;		
				break;

        case 'jlextlmoimport': 
				 		$active=3;		
				break;
				
				case 'predictiongames':
        case 'predictionmembers':
        case 'predictiontemplates':    
				 		$active=4;		
				break;
				
				break;

				default:					$active=JRequest::getInt("active",1);

			}
		}
		else
		{
			switch (JRequest::getVar('view'))
			{
				case 'projects':			
				case 'leagues':
				case 'seasons':
				case 'sportstypes':
				case 'clubs':
				case 'teams':
				case 'persons':
				case 'eventtypes':
				case 'statistics':
				case 'positions':
				case 'playgrounds':			$active=0;
				break;

				case 'settings':
				case 'updates':
				case 'jlxmlimports':
				case 'databasetools':		$active=1;
				break;
				
				case 'jlextlmoimport': 
				 		$active=2;		
				break;
				
				case 'predictiongames': 
				case 'predictionmembers':
				case 'predictiontemplates':
				 		$active=3;		
				break;

				default:					$active=JRequest::getInt("active",0);

			}
		}

		$mdlJoomleague = JModel::getInstance('Joomleague', 'JoomleagueModel');
		$versions  = $mdlJoomleague->getVersion();
		if ($versions) {$version=$versions[0]->version;} else {$version='';}

		$this->assignRef('version',$version);
		$this->assignRef('link',$link);
		$this->assignRef('tabs',$tabs);
		$this->assignRef('label',$label);
		$this->assignRef('lists',$lists);
		$this->assignRef('active',$active);
		$this->assignRef('limage',$limage);
		$this->assignRef('project',$project);
		$this->assignRef('sports_type_id',$sports_type_id);
		$this->assignRef('management',$management);

		parent::display('admin');
	}

}
?>
