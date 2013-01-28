<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.array');
jimport('joomla.utilities.arrayhelper') ;
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.user.authorization' );
jimport( 'joomla.access.access' );

require_once(JLG_PATH_ADMIN.DS.'models'.DS.'item.php');
require_once(JLG_PATH_ADMIN.DS.'models'.DS.'rounds.php');

class JoomleagueModelPrediction extends JoomleagueModelItem
{
	var $_predictionGame		= null;
	var $predictionGameID		= 0;

	var $_predictionMember		= null;
	var $predictionMemberID		= 0;

	var $_predictionProjectS	= null;
	var $predictionProjectSIDs	= null;

	var $_predictionProject		= null;
	var $predictionProjectID	= null;
	var $show_debug_info	= false;

	function __construct()
	{
		$post	= JRequest::get('post');
		
		$this->predictionGameID		= JRequest::getInt('prediction_id',		0);
		$this->predictionMemberID	= JRequest::getInt('uid',	0);
		$this->joomlaUserID			= JRequest::getInt('juid',	0);
		$this->roundID				= JRequest::getInt('r',		0);
		$this->pjID					= JRequest::getInt('p',		0);
		$this->isNewMember			= JRequest::getInt('s',		0);
		$this->tippEntryDone		= JRequest::getInt('eok',	0);

		$this->from  				= JRequest::getInt('from',	$this->roundID);
		$this->to	 				= JRequest::getInt('to',	$this->roundID);
		$this->type  				= JRequest::getInt('type',	0);

		$this->page  				= JRequest::getInt('page',	1);

    $show_debug_info = JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0);
    if ( $show_debug_info )
    {
    $this->show_debug_info = true;
    }
    else
    {
    $this->show_debug_info = false;
    }

		parent::__construct();
	}


  function getChampionPoints($champ_tipp)
  {
  $ChampPoints = 0;
  
  $resultchamp = 0;
  $resultchamppoints = 0;
  
  $sChampTeamsList = array();
  $dChampTeamsList = array();
  $champTeamsList = array();
  
  // select champion from project
  $query = 'SELECT league_champ
					FROM #__joomleague_prediction_project
					WHERE prediction_id = ' . $this->predictionGameID .'
           and champ = 1 
          and project_id = ' . $this->pjID ;
		$this->_db->setQuery($query);
		$resultchamp = $this->_db->loadResult();
		
  $query = 'SELECT points_tipp_champ
					FROM #__joomleague_prediction_project
					WHERE prediction_id = ' . $this->predictionGameID .'
          and champ = 1 
          and project_id = ' . $this->pjID ;
		$this->_db->setQuery($query);
		$resultchamppoints = $this->_db->loadResult();		
  
  // user hat auch champion tip abgegeben
  if ( $champ_tipp )
  {
  $sChampTeamsList = explode(';',$champ_tipp);
	foreach ($sChampTeamsList AS $key => $value){$dChampTeamsList[] = explode(',',$value);}
	foreach ($dChampTeamsList AS $key => $value){$champTeamsList[$value[0]] = $value[1];}
	
	if ( isset($champTeamsList[$this->pjID]) )
	{
  if ( $champTeamsList[$this->pjID] == $resultchamp )
	{
  $ChampPoints = $resultchamppoints;
  }
  }
  
  }
  
  
  if ( $this->show_debug_info )
  {
	echo '<br />getChampionPoints predictionGameID <pre>~' . print_r($this->predictionGameID,true) . '~</pre><br />';
	echo '<br />getChampionPoints pjID <pre>~' . print_r($this->pjID,true) . '~</pre><br />';
	
  echo '<br />getChampionPoints champion-id <pre>~' . print_r($resultchamp,true) . '~</pre><br />';
	echo '<br />getChampionPoints champion-points <pre>~' . print_r($resultchamppoints,true) . '~</pre><br />';
	echo '<br />getChampionPoints champ_tipp <pre>~' . print_r($champ_tipp,true) . '~</pre><br />';
	
	echo '<br />getChampionPoints sChampTeamsList <pre>~' . print_r($sChampTeamsList,true) . '~</pre><br />';
	echo '<br />getChampionPoints dChampTeamsList <pre>~' . print_r($dChampTeamsList,true) . '~</pre><br />';
	echo '<br />getChampionPoints champTeamsList <pre>~' . print_r($champTeamsList,true) . '~</pre><br />';
	
  }
				
				
  return $ChampPoints;
  }
  
  function getDebugInfo()
  {
  $show_debug_info = JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0);
  if ( $show_debug_info )
  {
  $this->show_debug_info = true;
  return true;
  }
  else
  {
  $this->show_debug_info = false;
  return false;
  }
  
  }
  
  
	function getPredictionGame()
	{
		if (!$this->_predictionGame)
		{
			if ($this->predictionGameID > 0)
			{
				$query =	' SELECT *,
								CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\',id,alias) ELSE id END AS slug
								FROM #__joomleague_prediction_game
								WHERE id='.$this->_db->Quote($this->predictionGameID).' AND published=1';
				$this->_db->setQuery($query,0,1);
				$this->_predictionGame=$this->_db->loadObject();
			}
		}
		return $this->_predictionGame;
	}

  function getPredictionMemberAvatar($members, $configavatar)
  {
  global $mainframe, $option;
	$mainframe	=& JFactory::getApplication();
  $picture = '';
   
  switch ( $configavatar )
		{
    case 'com_joomleague':
	  // alles ok
    break;
    
    case 'com_cbe15':
    $picture = 'images/cbe/'.$members.'.png';
    break;
    
    case 'com_cbe25':
    $picture = 'components/com_cbe/assets/user.png';
    $query = 'SELECT avatar
					FROM #__cbe_users
					WHERE userid = ' . (int)$members ;
		$this->_db->setQuery($query);
		$results = $this->_db->loadResult();
		if ( $results )
    {
    $picture = $results;
    }
	  
    break;
    
    case 'com_kunena':
    $query = 'SELECT avatar
					FROM #__kunena_users
					WHERE userid = ' . (int)$members ;
		$this->_db->setQuery($query);
		$results = $this->_db->loadResult();
    
    if ( $results )
    {
    $picture = 'media/kunena/avatars/'.$results;
    }
    break;
    
    case 'com_community':
    $query = 'SELECT avatar
					FROM #__community_users
					WHERE userid = ' . (int)$members ;
		$this->_db->setQuery($query);
		$results = $this->_db->loadResult();
    if ( $results )
    {
    $picture = $results;
    }
    
    break;
    
    }
 
 
  return $picture;
  
  } 


	function getPredictionMember()
	{
		if (!$this->_predictionMember)
		{
			if ($this->predictionMemberID > 0)
			{
				$query=" SELECT	pm.id AS pmID,
									pm.registerDate AS pmRegisterDate,
									pm.*, u.name, u.username
							FROM #__joomleague_prediction_member AS pm
								LEFT JOIN #__users AS u ON u.id=pm.user_id
							WHERE	pm.prediction_id=".$this->_db->Quote($this->predictionGameID)." AND
									pm.id=".$this->_db->Quote($this->predictionMemberID);
				$this->_db->setQuery($query,0,1);
				$this->_predictionMember = $this->_db->loadObject();
				if (isset($this->_predictionMember->pmID))
                {
					$this->predictionMemberID = $this->_predictionMember->pmID;
				}
			}
			else
			{
				$user=& JFactory::getUser();
				if ($user->id > 0)
				{
					$query=" SELECT	pm.id AS pmID,
										pm.registerDate AS pmRegisterDate,
										pm.*,
										u.*
								FROM #__joomleague_prediction_member AS pm
									LEFT JOIN #__users AS u ON u.id=pm.user_id
								WHERE	pm.prediction_id=".$this->_db->Quote($this->predictionGameID)." AND
										pm.user_id=".$this->_db->Quote($user->id);
					$this->_db->setQuery($query,0,1);
					$this->_predictionMember=$this->_db->loadObject();
					if (isset($this->_predictionMember->pmID))
					{
						$this->predictionMemberID=$this->_predictionMember->pmID;
					}
					else
					{
						$this->_predictionMember->id=0;
						$this->_predictionMember->pmID=0;
						$this->predictionMemberID=0;
					}
				}
				else
				{
					$this->_predictionMember->id=0;
					$this->_predictionMember->pmID=0;
					$this->predictionMemberID=0;
				}
			}
		}
		return $this->_predictionMember;
	}

	function getPredictionProjectS()
	{
		if (!$this->_predictionProjectS)
		{
			if ($this->predictionGameID > 0)
			{
				$query =	'	SELECT	pp.*,
										p.name AS projectName, p.start_date
								FROM #__joomleague_prediction_project AS pp
								LEFT JOIN #__joomleague_project AS p ON p.id=pp.project_id
								WHERE	pp.prediction_id='.$this->_db->Quote($this->predictionGameID).' AND
										pp.published=1';
				$this->_db->setQuery($query);
				$this->_predictionProjectS=$this->_db->loadObjectList();
			}
		}
		return $this->_predictionProjectS;
	}

	function getPredictionOverallConfig()
	{
		return $this->getPredictionTemplateConfig('predictionoverall');
	}

	function getPredictionTemplateConfig($template)
	{
		$query =	"	SELECT t.params
						FROM #__joomleague_prediction_template AS t
						INNER JOIN #__joomleague_prediction_game AS p ON p.id=t.prediction_id
						WHERE	t.template=".$this->_db->Quote($template)." AND
								p.id =".$this->_db->Quote($this->predictionGameID);

		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadResult())
		{
			if (isset($this->predictionGame) && ($this->predictionGame->master_template))
			{
				$query="	SELECT t.params
							FROM #__joomleague_Prediction_template AS t
							INNER JOIN #__joomleague_prediction_game AS p ON p.id=t.prediction_id
							WHERE	t.template=".$this->_db->Quote($template)." AND
									p.id=".$this->_db->Quote($this->predictionGame->master_template);

				$this->_db->setQuery($query);
				if (!$result=$this->_db->loadResult())
				{
					JError::raiseNotice(500,JText::sprintf('JL_PRED_MISSING_MASTER_TEMPLATE',$template,$predictionGame->master_template));
					JError::raiseNotice(500,JText::_('JL_PRED_MISSING_MASTER_TEMPLATE_HINT'));
					echo '<br /><br />';
					return false;
				}
			}
			else
			{
				JError::raiseNotice(500,JText::sprintf('JL_PRED_MISSING_TEMPLATE',$template,$this->predictionGameID));
				JError::raiseNotice(500,JText::_('JL_PRED_MISSING_MASTER_TEMPLATE_HINT'));
				echo '<br /><br />';
				return false;
			}
		}

		$params=explode("\n",trim($result));

		foreach($params AS $param)
		{
			list($name,$value)=explode('=',$param);
			$configvalues[$name]=$value;
		}

		// check some defaults and init data for quicker access
		switch ($template)
		{
			case	'predictionoverall':	{
												if (!array_key_exists('sort_order_1',$configvalues))
												//for people updating,the ranking order won't be set until they edit
												//predictionoverall.xml. In that case,use a default sorting
												{
													$configvalues['sort_order_1']='points';
													$configvalues['sort_order_2']='correct_tipps';
													$configvalues['sort_order_3']='correct_diffs';
													$configvalues['sort_order_4']='correct_tend';
													$configvalues['sort_order_5']='count_tipps_p';
												}
												break;
											}

			default:	{
							break;
						}
		}
		return $configvalues;
	}

	function getTimestamp($date,$offset=0)
	{
		if ($date <> '')
		{
			$datum=split("-| |:",$date);
		}
		else
		{
			$datum=preg_split("/-| |:/",JHTML::_('date',date('Y-m-d H:i:s',time()),"%Y-%m-%d %H:%M:%S"));
		}
		if ($offset)
		{
			$serveroffset=explode(':',$offset);
			$timestampoffset=($serveroffset[0] * 3600) + ($serveroffset[1] * 60);
		}
		else
		{
			$timestampoffset=0;
		}
		$result=mktime($datum[3],$datum[4],$datum[5],$datum[1],$datum[2],$datum[0]) + $timestampoffset;
		return $result;
	}

	function getPredictionProject($project_id=0)
	{
		if ($project_id > 0)
		{
			$query='SELECT * FROM #__joomleague_project WHERE id='.$project_id;
			$this->_db->setQuery($query);
			if (!$result=$this->_db->loadObject()){return false;}
			return $result;
		}
		return false;
	}

	function getMatchTeam($teamID=0,$teamName='name')
	//function getMatchTeam($teamID=0)
	//function getMatchTeam($teamID)
	{
	//$teamName='name';
		if ($teamID==0){return '#Error1 teamID==0 in _getTeamName#';}

		$query =	"
					SELECT t.$teamName
					FROM #__joomleague_team AS t
					INNER JOIN #__joomleague_project_team AS pt on pt.id='$teamID'
					WHERE t.id=pt.team_id";
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($object=$this->_db->loadObject())
		{
			return $object->$teamName;
		}
		return '#Error2 teamname not found in _getTeamName#';

	}

	function getMatchTeamClubLogo($teamID=0)
	{
		if ($teamID == 0) { return '#Error1 in _getTeamNameClubLogo#'; }

		$query =	"
					SELECT c.logo_small
					FROM #__joomleague_club AS c
					INNER JOIN #__joomleague_team AS t on t.club_id=c.id
					INNER JOIN #__joomleague_project_team AS pt on pt.id='$teamID'
					WHERE t.id=pt.team_id";
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($object=$this->_db->loadObject())
		{
			return $object->logo_small;
		}
		return '#Error2 in _getTeamNameClubLogo#';

	}
  
  function getMatchTeamClubFlag($teamID=0)
	{
		if ($teamID == 0) { return '#Error1 in _getTeamNameClubFlag#'; }

		$query =	"
					SELECT c.country
					FROM #__joomleague_club AS c
					INNER JOIN #__joomleague_team AS t on t.club_id=c.id
					INNER JOIN #__joomleague_project_team AS pt on pt.id='$teamID'
					WHERE t.id=pt.team_id";
		$this->_db->setQuery($query);
		$this->_db->query();
		if ($object=$this->_db->loadObject())
		{
			return $object->country;
		}
		return '#Error2 in _getTeamNameClubFlag#';

	}
  

	function getProjectSettings($pid=0)
	{
		if ($pid > 0)
		{
			$query='	SELECT current_round,
							CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\',id,alias) ELSE id END AS slug
						FROM #__joomleague_project
						WHERE id='.$this->_db->Quote($pid);
			$this->_db->setQuery($query,0,1);
			//return $this->_project=$this->_db->loadResult();
			return $this->_db->loadResult();
		}
		return false;
	}

	function getProjectRounds($pid=0)
	{
		if ($pid > 0)
		{
			$query='SELECT max(id) FROM #__joomleague_round 
					WHERE project_id='.$this->_db->Quote($pid);
			$this->_db->setQuery($query);
			$this->_projectRoundsCount=$this->_db->loadResult();
			return $this->_projectRoundsCount;
		}
		return false;
	}

	function checkPredictionMembership()
	{
		$query='	SELECT id
					FROM #__joomleague_prediction_member
					WHERE	prediction_id='.$this->_db->Quote($this->predictionGameID).' AND
							user_id='.$this->_db->Quote(JFactory::getUser()->id).' AND
							approved=1';
		$this->_db->setQuery($query,0,1);
		if (!$this->_db->loadResult()){return false;}
		return true;
	}

	function checkIsNotApprovedPredictionMember()
	{
		$query='	SELECT user_id,approved
					FROM #__joomleague_prediction_member
					WHERE	prediction_id='.$this->_db->Quote($this->predictionGameID).' AND
							user_id='.$this->_db->Quote(JFactory::getUser()->id);
		$this->_db->setQuery($query,0,1);
		if (!$result=$this->_db->loadObject()){return 2;}
		if ($result->approved){return 0;}
		return 1;
	}

	function getAllowed($pmUID=0)
	{
		$allowed=false;
        $groupNames = '';
        // Application Instanz holen
        $mainframe = JFactory::getApplication();
        // ACL Instanz holen
        $acl = JFactory::getACL();
        // JUserobjekt holen
        $user = JFactory::getUser();
        
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        //echo 'authorised<br /><pre>~' . print_r($authorised,true) . '~</pre><br />';
        $authorisedgroups = $user->getAuthorisedGroups();
        //echo 'authorised groups<br /><pre>~' . print_r($authorisedgroups,true) . '~</pre><br />';
        
        foreach ($user->groups as $groupId => $value)
        {
        
        $this->_db->setQuery(
            'SELECT `title`' .
            ' FROM `#__usergroups`' .
            ' WHERE `id` = '. (int) $groupId
        );
        $groupNames .= $this->_db->loadResult();
        $groupNames .= '<br/>';
        }
        //print $groupNames.'<br>';
        
        $groups = JAccess::getGroupsByUser($user->id, false);
        //echo 'user groups<br /><pre>~' . print_r($groups,true) . '~</pre><br />';
    
		if ($user->id > 0)
		{
			//$auth= JFactory::getACL();
			//$aro_group = $acl->getAroGroup($user->id);

			if (($groups[0] == 7) || ($groups[0] == 8))
			{
				$allowed=true;
			}
			else
			{
				if (($pmUID > 0) && ($pmUID==$user->id))
				{
					$allowed=true;
				}
				else
				{
					$predictionGame=&$this->getPredictionGame();
					$adminAllowed=$predictionGame->admin_tipp;
					if ($adminAllowed)
					{
						$predictionGameAdmins=&$this->getPredictionGameAdmins($predictionGame->id);
						foreach($predictionGameAdmins AS $adminUserID)
						{
							if ($adminUserID==$user->id)
							{
								$allowed=true;
								break;
							}
						}
					}
				}
			}
		}
		return $allowed;
	}

	function getSystemAdminsEMailAdresses()
	{
		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	u.sendEmail=1 AND
								u.block=0 AND
								u.usertype="Super Administrator"
						ORDER BY u.email';
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

	function getPredictionGameAdminsEMailAdresses()
	{
		$query =	'	SELECT u.email
						FROM #__users AS u
						INNER JOIN #__joomleague_prediction_admin AS pa ON	pa.prediction_id='.(int) $this->predictionGameID.' AND
																			pa.user_id=u.id
						WHERE	u.sendEmail=1 AND
								u.block=0
						ORDER BY u.email';
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

	function getPredictionGameAdmins($predictionID)
	{
		$query='SELECT user_id FROM #__joomleague_prediction_admin WHERE prediction_id='.$predictionID;
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

	function getPredictionMemberEMailAdress($predictionMemberID)
	{
		$query =	'	SELECT user_id
						FROM #__joomleague_prediction_member
						WHERE	id='.$predictionMemberID;
		$this->_db->setQuery($query);
		if (!$user_id=$this->_db->loadResult()){return false;}

		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	u.sendEmail=1 AND
								u.block=0 AND
								u.id='.$user_id.'
						ORDER BY u.email';
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}


  function sendMemberTipResults($predictionMemberID,$predictionGameID,$RoundID,$ProjectID,$joomlaUserID) 
  {
  global $mainframe, $option;
  $document	=& JFactory::getDocument();
  $mainframe	=& JFactory::getApplication();
  
  $configprediction			= $this->getPredictionTemplateConfig('predictionentry');
  $overallConfig	= $this->getPredictionOverallConfig();
  $configprediction			=array_merge($overallConfig,$configprediction);
  $predictionProjectSettings = $this->getPredictionProject($ProjectID);
  $predictionProject = $this->getPredictionGame();
  $predictionProjectS = $this->getPredictionProjectS();
  
  if ( $configprediction['use_pred_select_matches'] )
      {
      $match_ids = $configprediction['predictionmatchid'];
      }
      
  $roundResults = $this->getMatchesDataForPredictionEntry(	$predictionGameID,
																			$ProjectID,
																			$RoundID,
																			$joomlaUserID,$match_ids);
  
//  $mainframe->enqueueMessage(JText::_('roundResults -> <pre> '.print_r($roundResults,true).'</pre><br>' ),'Notice');
//  $mainframe->enqueueMessage(JText::_('predictionProject -> <pre> '.print_r($predictionProject,true).'</pre><br>' ),'Notice');
//  $mainframe->enqueueMessage(JText::_('predictionProjectS -> <pre> '.print_r($predictionProjectS,true).'</pre><br>' ),'Notice');
                                        
  $predictionGameMemberMail = $this->getPredictionMemberEMailAdress($predictionMemberID);
  //$mainframe->enqueueMessage(JText::_('predictionGameMemberMail -> <pre> '.print_r($predictionGameMemberMail,true).'</pre><br>' ),'Notice');

  //Fetch the mail object
	$mailer =& JFactory::getMailer();
	// als html
	$mailer->isHTML(TRUE);
  //Set a sender
	$config =& JFactory::getConfig();
	$sender = array($config->getValue('config.mailfrom'),$config->getValue('config.fromname'));
	$mailer->setSender($sender);
  $mailer->addRecipient($predictionGameMemberMail);				
	//Create the mail
	$mailer->setSubject(JText::_('JL_PRED_ENTRY_MAIL_TITLE'));
  
  
  foreach ($predictionProjectS AS $predictionProject)
	{
	
  $body = '';
  
  // jetzt die ergebnisse
  $body .= "<html>"; 

$body .= "<table class='blog' cellpadding='0' cellspacing='0' width='100%'>";
$body .= "<tr>";
$body .= "<td class='sectiontableheader'>";
$body .= JText::sprintf('JL_PRED_HEAD_ACTUAL_PRED_GAME','<b><i>'.$predictionProject->projectName.'</i></b>');
$body .= "</td>";
$body .= "</tr>";
$body .= "</table>";
          
  $body .= "<table width='100%' cellpadding='0' cellspacing='0'>";
  
	$body .= "<tr>";
	$body .= "<th class='sectiontableheader' style='text-align:center;'>" . JText::_('JL_PRED_ENTRY_DATE_TIME') . "</th>";
	$body .= "<th class='sectiontableheader' style='text-align:center;' colspan='5' >" . JText::_('JL_PRED_ENTRY_MATCH') . "</th>";
	$body .= "<th class='sectiontableheader' style='text-align:center;'>" . JText::_('JL_PRED_ENTRY_RESULT') . "</th>";
	$body .= "<th class='sectiontableheader' style='text-align:center;'>" . JText::_('JL_PRED_ENTRY_YOURS') . "</th>";
	$body .= "<th class='sectiontableheader' style='text-align:center;'>" . JText::_('JL_PRED_ENTRY_POINTS') . "</th>";
	$body .= "</tr>";
	
	// schleife über die ergebnisse in der runde
	foreach ($roundResults AS $result)
	{
  $class = ($k==0) ? 'sectiontableentry1' : 'sectiontableentry2';

	$resultHome = (isset($result->team1_result)) ? $result->team1_result : '-';
	if (isset($result->team1_result_decision)){$resultHome=$result->team1_result_decision;}
	$resultAway = (isset($result->team2_result)) ? $result->team2_result : '-';
	if (isset($result->team2_result_decision)){$resultAway=$result->team2_result_decision;}
  $closingtime = $configprediction['closing_time'] ;//3600=1 hour
	$matchTimeDate = JoomleagueHelper::getTimestamp($result->match_date,1,$predictionProjectSettings->serveroffset);
	$thisTimeDate = JoomleagueHelper::getTimestamp('',1,$predictionProjectSettings->serveroffset);
	$matchTimeDate = $matchTimeDate - $closingtime;
						
  $body .= "<tr class='" . $class ."'>";
	$body .= "<td class='td_c'>";
	$body .= JHTML::date($result->match_date,JText::_('JL_GLOBAL_CALENDAR_DATE'));
	$body .= " - ";
	$body .= JHTML::date(date("Y-m-d H:i:s",$matchTimeDate),$configprediction['time_format']); 
	$body .= "</td>";

  $homeName = $this->getMatchTeam($result->projectteam1_id);
	$awayName = $this->getMatchTeam($result->projectteam2_id);

// clublogo oder vereinsflagge hometeam	
$body .= "<td nowrap='nowrap' class='td_r'>";
$body .= $homeName;
$body .= "</td>";
$body .= "<td nowrap='nowrap' class='td_c'>";
if ( $configprediction['show_logo_small'] == 1 )
{
$logo_home = $this->getMatchTeamClubLogo($result->projectteam1_id);
if	(($logo_home == '') || (!file_exists($logo_home)))
{
$logo_home = 'media/com_joomleague/placeholders/placeholder_small.gif';
}
$imgTitle = JText::sprintf('JL_PRED_ENTRY_LOGO_OF', $homeName);
$body .=  JHTML::image($logo_home,$imgTitle,array(' title' => $imgTitle));
$body .=  ' ';
}
if ( $configprediction['show_logo_small'] == 2 )
{
$country_home = $this->getMatchTeamClubFlag($result->projectteam1_id);
$body .=  Countries::getCountryFlag($country_home);
}
$body .= "</td>";	

$body .= "<td nowrap='nowrap' class='td_c'>";	
$body .= "<b>" . $configprediction['seperator'] . "</b>";
$body .= "</td>";	

// clublogo oder vereinsflagge awayteam
$body .= "<td nowrap='nowrap' class='td_c'>";
if ( $configprediction['show_logo_small'] == 1 )
{
$logo_away = $this->getMatchTeamClubLogo($result->projectteam2_id);
if (($logo_away=='') || (!file_exists($logo_away)))
{
$logo_away = 'media/com_joomleague/placeholders/placeholder_small.gif';
}
$imgTitle = JText::sprintf('JL_PRED_ENTRY_LOGO_OF', $awayName);
$body .=  ' ';
$body .=  JHTML::image($logo_away,$imgTitle,array(' title' => $imgTitle));
}
if ( $configprediction['show_logo_small'] == 2 )
{
$country_away = $this->getMatchTeamClubFlag($result->projectteam2_id);
$body .=  Countries::getCountryFlag($country_away);
}
$body .= "</td>";				
$body .= "<td nowrap='nowrap' class='td_l'>";
$body .= $awayName;
$body .= "</td>";	

// spielergebnisse
$body .= "<td class='td_c'>";
$body .= $resultHome . $configprediction['seperator'] . $resultAway;
$body .= "</td>";

// tippergebnisse
$body .= "<td class='td_c'>";

if ( $predictionProject->mode == '0' )	// Tipp in normal mode
{
$body .= $result->tipp_home . $configprediction['seperator'] . $result->tipp_away;
}
if ( $predictionProject->mode == '1' )	// Tipp in toto mode
{
$body .= $result->tipp;
}
$body .= "</td>";


// punkte
$body .= "<td class='td_c'>";
$points = $this->getMemberPredictionPointsForSelectedMatch($predictionProject,$result);
$totalPoints = $totalPoints+$points;
$body .=  $points;
$body .= "</td>";
$body .= "</tr>";

// tendencen im tippspiel  
if ($configprediction['show_tipp_tendence'])
{

$body .= "<tr class='tipp_tendence'>";
$body .= "<td class='td_c'>";
$body .= "&nbsp;"; 
$body .= "</td>";

$body .= "<td class='td_l' colspan='8'>";
$totalCount = $this->getTippCountTotal($predictionGameID, $result->id);
$homeCount = $this->getTippCountHome($predictionGameID, $result->id);
$awayCount = $this->getTippCountAway($predictionGameID, $result->id);
$drawCount = $this->getTippCountDraw($predictionGameID, $result->id);
if ($totalCount > 0)
{
$percentageH = round(( $homeCount * 100 / $totalCount ),2);
$percentageD = round(( $drawCount * 100 / $totalCount ),2);
$percentageA = round(( $awayCount * 100 / $totalCount ),2);
}
else
{
$percentageH = 0;
$percentageD = 0;
$percentageA = 0;
}

$body .= "<span style='color:" . $configprediction['color_home_win'] ."' >";
$body .= JText::sprintf('JL_PRED_ENTRY_PERCENT_HOME_WIN',$percentageH,$homeCount) . "</span><br />";
$body .= "<span style='color:" . $configprediction['color_draw'] ."'>";
$body .= JText::sprintf('JL_PRED_ENTRY_PERCENT_DRAW',$percentageD,$drawCount) . "</span><br />";
$body .= "<span style='color:" . $configprediction['color_guest_win'] ."'>";
$body .= JText::sprintf('JL_PRED_ENTRY_PERCENT_AWAY_WIN',$percentageA,$awayCount) . "</span>";
$body .= "</td>";
//$body .= "<td colspan='8'>&nbsp;</td>";
$body .= "</tr>";
}
else
{
$k = (1-$k);							
}

  }

$body .= "<tr>";
$body .= "<td colspan='8'>&nbsp;</td>";
$body .= "<td class='td_c'>" . JText::sprintf('JL_PRED_ENTRY_TOTAL_POINTS_COUNT',$totalPoints) ."</td>";
$body .= "</tr>";            	
	
  $body .= "<table>";
  
if (($configprediction['show_help']==1)||($configprediction['show_help']==2))
{
$body .= $this->createHelptText($predictionProject->mode);
}  
  
  
  $body .= "</html>";
  
  }
  
	$mailer->setBody($body);
  
  //Sending the mail
	$send =& $mailer->Send();
	if ($send !== true)
	{
	//echo 'Error sending email to:<br />'.print_r($recipient,true).'<br />';
	//echo 'Error message: '.$send->message;
	$mainframe->enqueueMessage(JText::_('JL_PRED_ENTRY_MAIL_SEND_ERROR'),'Error');
	}
	else
	{
	//echo 'Mail sent';
	$emailadresses = implode(",",$predictionGameMemberMail);
	$mainframe->enqueueMessage(JText::sprintf('JL_PRED_ENTRY_MAIL_SEND_OK',$emailadresses),'');
	}
                          				
  }
  
	function sendMembershipConfirmation($cid=array())
	{
		if (count($cid))
		{
			$cids=implode(',',$cid);
			// create and send mail about registration in Prediction game
			$systemAdminsMails=$this->getSystemAdminsEMailAdresses();
			$predictionGameAdminsMails=$this->getPredictionGameAdminsEMailAdresses();

			foreach ($cid as $predictionMemberID)
			{
				$predictionGameMemberMail=$this->getPredictionMemberEMailAdress($predictionMemberID);
				if (count($predictionGameMemberMail) > 0)
				{
					//Fetch the mail object
					$mailer =& JFactory::getMailer();

					//Set a sender
					$config =& JFactory::getConfig();
					$sender=array($config->getValue('config.mailfrom'),$config->getValue('config.fromname'));
					$mailer->setSender($sender);

					//set Member as recipient
					$lastMailAdress='';
					$recipient=array();
					foreach ($predictionGameMemberMail AS $predictionGameMember_EMail)
					{
						if ($lastMailAdress != $predictionGameMember_EMail)
						{
							$recipient[]=$predictionGameMember_EMail;
							$lastMailAdress=$predictionGameMember_EMail;
						}
					}
					$mailer->addRecipient($recipient);

					//set system admins as BCC recipients
					$lastMailAdress='';
					$recipientAdmins=array();
					foreach ($systemAdminsMails AS $systemAdminMail)
					{
						if ($lastMailAdress != $systemAdminMail)
						{
							$recipientAdmins[]=$systemAdminMail;
							$lastMailAdress=$systemAdminMail;
						}
					}
					$lastMailAdress='';

					//set predictiongame admins as BCC recipients
					foreach ($predictionGameAdminsMails AS $predictionGameAdminMail)
					{
						if ($lastMailAdress != $predictionGameAdminMail)
						{
							$recipientAdmins[]=$predictionGameAdminMail;
							$lastMailAdress=$predictionGameAdminMail;
						}
					}
					$mailer->addBCC($recipientAdmins);
					unset($recipientAdmins);

					//Create the mail
					$mailer->setSubject('Approved Prediction Game Membership');
					$body="Your request for membership on a prediction game on this website was approved by an admin.\nnBe welcome!";

					$mailer->setBody($body);
					
          //echo '<br /><pre>~'.print_r($mailer,true).'~</pre><br />';

					// Optional file attached
					//$mailer->addAttachment(PATH_COMPONENT.DS.'assets'.DS.'document.pdf');

					//Sending the mail
					$send =& $mailer->Send();
					if ($send !== true)
					{
						echo 'Error sending email to:<br />'.print_r($recipient,true).'<br />';
						echo 'Error message: '.$send->message;
					}
					else
					{
						echo 'Mail sent';
					}
					echo '<br /><br />';
				}
				else
				{
					// joomla_user is blocked or has set sendEmail to off
					// can't send email
					return false;
				}
			}
		}

		return true;
	}

	function echoLabelTD($labelText,$labelTextHelp,$rowspan=0)
	{
		?><td class='labelEdit'<?php echo ($rowspan > 1 ? ' rowspan="'.$rowspan.'"' : '')?> ><span class='hasTip' title="<?php echo JText::_($labelTextHelp); ?>"><?php echo JText::_($labelText); ?></span></td><?php
	}

	function getPredictionMemberList(&$config,$actUserId=null)
	{
		if ($config['show_full_name']==0){$nameType='username';}else{$nameType='name';}
		$query="	SELECT	pm.id AS value,
							u.".$nameType." AS text

					FROM #__joomleague_prediction_member AS pm
						LEFT JOIN #__users AS u ON	u.id=pm.user_id
					WHERE	prediction_id=".$this->_db->Quote((int)$this->predictionGameID);
		if(isset($actUserId))
		{
			$query .= " AND pm.approved=1 AND
							(pm.show_profile=1 OR pm.user_id=$actUserId)";
		}
		$this->_db->setQuery($query);
		$results=$this->_db->loadObjectList();
		return $results;
	}

	function getMemberPredictionTotalCount($user_id)
	{
		$query=	"	SELECT	count(*)
						FROM #__joomleague_prediction_result AS pr
						WHERE prediction_id=$this->predictionGameID AND user_id=$user_id";

		$this->_db->setQuery($query);
		$results=$this->_db->loadResult();
		return $results;
	}

	function getMemberPredictionJokerCount($user_id,$project_id=0)
	{
		$query=	"	SELECT	count(id)
						FROM #__joomleague_prediction_result
						WHERE	prediction_id=$this->predictionGameID AND
								user_id=$user_id AND
								joker=1";
		if ($project_id>0)
		{
			$query .= 	" AND project_id=$project_id";
		}

		$this->_db->setQuery($query);
		$results=$this->_db->loadResult();
		return $results;
	}

	function createResultsObject($home,$away,$tipp,$tippHome,$tippAway,$joker,$homeDecision=0,$awayDecision=0)
	{
		$result=new stdClass();
		$result->team1_result			= $home;
		$result->team2_result			= $away;
		$result->team1_result_decision	= $homeDecision;
		$result->team2_result_decision	= $awayDecision;
		$result->tipp					= $tipp;
		$result->tipp_home				= $tippHome;
		$result->tipp_away				= $tippAway;
		$result->joker					= $joker;

		return $result;
	}

	function getMemberPredictionPointsForSelectedMatch(&$predictionProject,&$result)
	{

		//echo '<br /><pre>~'.print_r($predictionProject,true).'~</pre><br />';

/*
ok[points_correct_result] => 7
ok[points_correct_result_joker] => 6
ok[points_correct_diff] => 5
ok[points_correct_diff_joker] => 4
ok[points_correct_draw] => 4
ok[points_correct_draw_joker] => 3
ok[points_correct_tendence] => 3
ok[points_correct_tendence_joker] => 2
ok[points_tipp] => 1						Points for wrong prediction
ok[points_tipp_joker] => 0					Points for wrong prediction with Joker
 */


		//echo '<br /><pre>~'.print_r($result,true).'~</pre><br />';

/*
[team1_result] => 1					Standard result of the match for hometeam
[team2_result] => 1					Standard result of the match for awayteam
[team1_result_decision] => 			There is NO standard result of the match for hometeam but A DECISION
[team2_result_decision] => 			There is NO standard result of the match for awayteam but A DECISION
[tipp] => 0							Only interesting for toto
[tipp_home] => 1					Only interesting for standard mode
[tipp_away] => 1					Only interesting for standard mode
[joker] => 1
*/



		if ($predictionProject->mode==0)	// Standard prediction Mode
		{
		
			if ((!isset($result->team1_result)) || (!isset($result->team2_result)) || (!isset($result->tipp_home)) || (!isset($result->tipp_away)))
			{
				return 0;
			}
		
			if (!$result->joker)	// No Joker was used for this prediction
			{
				//Prediction Result is the same as the match result / Top Tipp
				if (($result->team1_result==$result->tipp_home)&&($result->team2_result==$result->tipp_away))
				{
					return $predictionProject->points_correct_result;
				}

				//Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped and the matchresult is draw
				/*
				if ($result->team1_result==$result->team2_result)
				{
					if (($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
					{
						return $predictionProject->points_correct_draw;
					}
				}
				*/
				if (($result->team1_result==$result->team2_result) &&
					($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_draw;
				}

				//Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped
				if (($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_diff;
				}

				//Prediction Result is not the same as the match result but the tendence of the result is correct
				if	(((($result->team1_result - $result->team2_result)>0)&&(($result->tipp_home - $result->tipp_away)>0)) ||
					 ((($result->team1_result - $result->team2_result)<0)&&(($result->tipp_home - $result->tipp_away)<0)))
				{
					return $predictionProject->points_correct_tendence;
				}

				//Prediction Result is totally wrong but we check if there is at least one point to give ;-)
				return $predictionProject->points_tipp;
			}
			else	// Member took a Joker for this prediction
			{
				//With Joker - Prediction Result is the same as the match result / Top Tipp
				if (($result->team1_result==$result->tipp_home)&&($result->team2_result==$result->tipp_away))
				{
					return $predictionProject->points_correct_result_joker;
				}

				//With Joker - Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped and the matchresult is draw
				if (($result->team1_result==$result->team2_result) &&
					($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_draw_joker;
				}

				//With Joker - Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped
				if (($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_diff_joker;
				}

				//Prediction Result is not the same as the match result but the tendence of the result is correct
				if	(((($result->team1_result - $result->team2_result)>0)&&(($result->tipp_home - $result->tipp_away)>0)) ||
					 ((($result->team1_result - $result->team2_result)<0)&&(($result->tipp_home - $result->tipp_away)<0)))
				{
					return $predictionProject->points_correct_tendence_joker;
				}

				//With Joker - Prediction Result is totally wrong but we check if there is a point to give
				return $predictionProject->points_tipp_joker;
			}
		}
		else	// Toto Mode - No Joker is used here
		{
			if ((!isset($result->team1_result)) || (!isset($result->team2_result)))
			{
				return 0;
			}		
		
			if (($result->team1_result > $result->team2_result) && ($result->tipp=="1")){return $predictionProject->points_tipp;}
			if (($result->team1_result < $result->team2_result) && ($result->tipp=="2")){return $predictionProject->points_tipp;}
			if (($result->team1_result== $result->team2_result) && ($result->tipp=="0")){return $predictionProject->points_tipp;}
			return 0;
		}

		return 'ERROR';
	}

	function getPredictionMembersResultsList($project_id,$round1ID,$round2ID=0,$user_id=0,$type=0)
	{
		if ($round1ID==0){$round1ID=1;}

		$query=	"	SELECT	m.id AS matchID,
								m.match_date,
								m.team1_result AS homeResult,
								m.team2_result AS awayResult,
								m.team1_result_decision AS homeDecision,
								m.team2_result_decision AS awayDecision,

								pr.id AS prID,
								pr.user_id AS prUserID,
								pr.tipp AS prTipp,
								pr.tipp_home AS prHomeTipp,
								pr.tipp_away AS prAwayTipp,
								pr.joker AS prJoker,
								pr.points AS prPoints,
								pr.top AS prTop,
								pr.diff AS prDiff,
								pr.tend AS prTend,

								pm.id AS pmID

						FROM #__joomleague_match AS m

							INNER JOIN #__joomleague_round AS r ON		r.id=m.round_id
					";

		if ((isset($project_id)) && ($project_id > 0))
		{
			$query .= 	"											AND	r.project_id=$project_id
						";
		}

		$query .= 	"												AND	r.id>=$round1ID
					";

		if ((isset($round2ID)) && ($round2ID > 0))
		{
			$query .= 	"											AND	r.id<=$round2ID
						";
		}

		$query .= 	"		LEFT JOIN #__joomleague_prediction_result AS pr ON		pr.match_id=m.id
					";

		if ((isset($user_id)) && ($user_id > 0))
		{
			$query .= 	"														AND	pr.user_id=$user_id
						";
		}

		$query .= 	"		INNER JOIN #__joomleague_prediction_member AS pm ON	pm.user_id=pr.user_id AND
																				pm.prediction_id=$this->predictionGameID
						WHERE pr.prediction_id=$this->predictionGameID
						AND (m.cancel IS NULL OR m.cancel = 0)
						ORDER BY pm.id,m.match_date,m.id ASC";

		$this->_db->setQuery($query);
		$results=$this->_db->loadObjectList();
		return $results;
	}

	function createProjectSelector(&$predictionProjects,$current,$addTotalSelect=null)
	{
		//$output='<select class="inputbox" name="set_pj" onchange="this.form.submit();" >';
		
    //$output='<select class="inputbox" id="p" name="p" onchange="document.forms[\'resultsRoundSelector\'].r.value=0;submit();" >';
		$output='<select class="inputbox" id="p" name="p" onchange="this.form.submit();" >';
		
        //$output='<select class="inputbox" id="pj" name="pj" onchange="this.form.submit();" >';

		if (isset($addTotalSelect))
		{
			$output .= '<option value="0"';
			if ($addTotalSelect==0)
			{
				$output .= " selected='selected'";
			}
			$output .= '>'.JText::_('JL_PRED_TOTAL_RANKING').'</option>';
		}
		else
		{
			$addTotalSelect=1;
		}
		foreach ($predictionProjects AS $predictionProject)
		{
			$output .= '<option value="'.$predictionProject->project_id.'"';
			if (($predictionProject->project_id==$current) && ($addTotalSelect > 0))
			{
				$output .= " selected='selected'";
			}
			$output .= '>'.$predictionProject->projectName.'</option>';
		}
		$output .= '</select>';
		return $output;
	}

	function getPredictionProjectNames($predictionID,$ordering='ASC')
	{
		$query="SELECT	ppj.id,
							pj.id AS prediction_id,
							pj.name AS pjName
				  FROM #__joomleague_project AS pj
				  LEFT JOIN #__joomleague_prediction_project AS ppj ON ppj.prediction_id=$predictionID
				  ORDER BY ppj.id ".$ordering;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function savePredictionPoints(&$memberResult,&$predictionProject,$returnArray=false)
	{
	global $mainframe, $option;
	$mainframe	=& JFactory::getApplication();
	
    $show_debug = $this->getDebugInfo();
		//[matchID] => 14501
		//[match_date] => 2010-08-21 15:30:00
		//[homeResult] => 5
		//[awayResult] => 5
		//[homeDecision] =>
		//[awayDecision] =>
		//[prID] => 3647
		//[prTipp] => 0
		//[prHomeTipp] => 5
		//[prAwayTipp] => 5
		//[prJoker] =>
		//[prPoints] => 7
		//[prTop] => 1
		//[prDiff] =>
		//[prTend] =>
		//[pmID] => 46

		$result=true;

		//echo '<br /><pre>~'.print_r($predictionProject,true).'~</pre><br />';
		//echo '<br /><pre>~'.print_r($memberResult,true).'~</pre><br />';
		
		if ( $show_debug )
		{
    $mainframe->enqueueMessage(JText::_('predictionProject<pre>~'.print_r($predictionProject,true).'~</pre>'),'Notice');
    $mainframe->enqueueMessage(JText::_('memberResult<pre>~'.print_r($memberResult,true).'~</pre>'),'Notice');
    $mainframe->enqueueMessage(JText::_('prediction mode ~> '.$predictionProject->mode.'<br>'),'Notice');
    }
		
		
		$result_home	= $memberResult->homeResult;
		$result_away	= $memberResult->awayResult;

		$result_dHome	= $memberResult->homeDecision;
		$result_dAway	= $memberResult->awayDecision;

		$tipp_home	= $memberResult->prHomeTipp;
		$tipp_away	= $memberResult->prAwayTipp;

		$tipp		= $memberResult->prTipp;
		$joker		= $memberResult->prJoker;

		$points		= $memberResult->prPoints;
		$top		= $memberResult->prTop;
		$diff		= $memberResult->prDiff;
		$tend		= $memberResult->prTend;

		
    if ( $predictionProject->mode == 1 )
    {
    
    }
    else
    {
    if($tipp_home > $tipp_away){$tipp='1';}
		elseif($tipp_home < $tipp_away){$tipp='2';}
		elseif(!is_null($tipp_home)&&!is_null($tipp_away)){$tipp='0';}
		else{$tipp=null;}
    }

		$points		= null;
		$top		= null;
		$diff		= null;
		$tend		= null;

    if ( $predictionProject->mode == 1 )	// TOTO prediction Mode
		{
			//$points = $tipp;
			if ( ( $result_home > $result_away ) && ( $tipp == '1' ) )
      {
      $points = 1;
      }
			if ( ( $result_home < $result_away ) && ( $tipp == '2' ) )
      {
      $points = 1;
      }
			if ( ( $result_home == $result_away ) && ( $tipp == '0' ) )
      {
      $points = 1;
      }
      
    if ( $show_debug )
		{
    $mainframe->enqueueMessage(JText::_('toto points -> '.$points.'<br>'),'Notice');
    }
    
    }



		if ( !is_null($tipp_home) && !is_null($tipp_away) )
		{
			if ( $predictionProject->mode == 1 )	// TOTO prediction Mode
			{
				//$points = $tipp;
			}
			else	// Standard prediction Mode
			{
				if ($joker)	// Member took a Joker for this prediction
				{
					if (($result_home==$tipp_home)&&($result_away==$tipp_away))
					{
						//Prediction Result is the same as the match result / Top Tipp
						$points=$predictionProject->points_correct_result_joker;
						$top=1;
					}
					elseif(($result_home==$result_away)&&($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped and the matchresult is draw
						$points=$predictionProject->points_correct_draw_joker;
						$diff=1;
					}
					elseif(($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped
						$points=$predictionProject->points_correct_diff_joker;
						$diff=1;
					}
					elseif (((($result_home - $result_away)>0)&&(($tipp_home - $tipp_away)>0)) ||
							 ((($result_home - $result_away)<0)&&(($tipp_home - $tipp_away)<0)))
					{
						//Prediction Result is not the same as the match result but the tendence of the result is correct
						$points=$predictionProject->points_correct_tendence_joker;
						$tend=1;
					}
					else
					{
						//Prediction Result is totally wrong but we check if there is a point to give
						$points=$predictionProject->points_tipp_joker;
					}
				}
				else	// No Joker was used for this prediction
				{
					if (($result_home==$tipp_home)&&($result_away==$tipp_away))
					{
						//Prediction Result is the same as the match result / Top Tipp
						$points=$predictionProject->points_correct_result;
						$top=1;
					}
					elseif(($result_home==$result_away)&&($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped and the matchresult is draw
						$points=$predictionProject->points_correct_draw;
						$diff=1;
					}
					elseif(($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped
						$points=$predictionProject->points_correct_diff;
						$diff=1;
					}
					elseif (((($result_home - $result_away)>0)&&(($tipp_home - $tipp_away)>0)) ||
							 ((($result_home - $result_away)<0)&&(($tipp_home - $tipp_away)<0)))
					{
						//Prediction Result is not the same as the match result but the tendence of the result is correct
						$points=$predictionProject->points_correct_tendence;
						$tend=1;
					}
					else
					{
						//Prediction Result is totally wrong but we check if there is a point to give
						$points=$predictionProject->points_tipp;
					}
				}
			}
		}

		$query =	"	UPDATE	#__joomleague_prediction_result

						SET
							tipp_home=" .	((!is_null($tipp_home))	? "'".$tipp_home."'"	: 'NULL').",
							tipp_away=" .	((!is_null($tipp_away))	? "'".$tipp_away."'"	: 'NULL').",
							tipp=" .		((!is_null($tipp))		? "'".$tipp."'"			: 'NULL').",
							joker=" .		((!is_null($joker))		? "'".$joker."'"		: 'NULL').",
							points=" .		((!is_null($points))	? "'".$points."'"		: 'NULL').",
							top=" .			((!is_null($top))		? "'".$top."'"			: 'NULL').",
							diff=" .		((!is_null($diff))		? "'".$diff."'"			: 'NULL').",
							tend=" .		((!is_null($tend))		? "'".$tend."'"			: 'NULL')."
						WHERE id=".$memberResult->prID;
		$this->_db->setQuery($query);
		
		if ( $show_debug )
		{
    $mainframe->enqueueMessage(JText::_('update query ~> '.$query.'<br>'),'Notice');
    }
    
		if (!$this->_db->query()){$result= false;}

		if ($returnArray)
		{
			$memberResult->tipp		= $tipp;
			$memberResult->points	= $points;
			$memberResult->top		= $top;
			$memberResult->diff		= $diff;
			$memberResult->tend		= $tend;

			return $memberResult;
		}

		return $result;
	}

	function getRoundNames($project_id,$ordering='ASC')
	{
	global $mainframe, $option;
  $document	=& JFactory::getDocument();
  $mainframe	=& JFactory::getApplication();
  
  //$mainframe->enqueueMessage(JText::_('project_id -> <pre> '.print_r($project_id,true).'</pre><br>' ),'Notice');
    
		if (empty($this->_roundNames))
		{
			$query="SELECT	id AS value,
								name AS text
					  FROM #__joomleague_round
					  WHERE project_id=".(int)$project_id."
					  ORDER BY id ".$ordering;

			$this->_db->setQuery($query);
			$this->_roundNames=$this->_db->loadObjectList();
		}
		return $this->_roundNames;
	}

	// general comparison of two tippers results
	// returns negative values for better tipper no 1
	// returns positive values for better tipper no 2
	// returns zero values for both tippers equal
	//
	// ranking rules are described inside the code
	function compare($a,$b)
	{
		$res	= 0;
		$i		= 1;

		while (array_key_exists('sort_order_'.$i,$this->table_config) and $res==0)
		{
			switch ($this->table_config['sort_order_'.$i++])
			{
				// 1. decision: more points
				case 'points':
					$res=-($a['totalPoints'] - $b['totalPoints']);
					break;

				case 'correct_tips':
					$res=-($a['totalTop'] - $b['totalTop']);
					break;

				case 'correct_diffs':
					$res=-($a['totalDiff'] - $b['totalDiff']);
					break;

				case 'correct_tend':
					$res=-($a['totalTend'] - $b['totalTend']);
					break;

				case 'count_tips_p':
					$res= -($a['predictionsCount'] - $b['predictionsCount']);
					break;

				case 'count_tips_m':
					$res=+($a['predictionsCount'] - $b['predictionsCount']);
					break;

				default;
					break;
			}
		}
		return $res;
	}

	function computeMembersRanking($membersResultsArray,$config)
	{
		$this->table_config = $config;
		$dummy = $membersResultsArray;

		uasort($dummy,array($this,'compare'));

		$i = 1;
		$lfdnumber = 1;
		foreach ($dummy AS $key => $value)
		{
		  
		  //echo '<br />i ~' . $i . '~<br />';
		  //echo '<br />lfdnumber ~' . $lfdnumber . '~<br />';
		  //echo '<br />array_pos ~' . $array_pos . '~<br />';
		  //echo '<br />key ~' . $key . '~<br />';
		  //echo '<br />value<pre>~' . print_r($value,true) . '~</pre><br />';
		  //echo '<br />dummy[array_pos][totalPoints] ~' . $dummy[$array_pos]['totalPoints'] . '~<br />';
		  //echo '<br />dummy[key][totalPoints] ~' . $dummy[$key]['totalPoints'] . '~<br />';
		  
			//$dummy[$key]['rank'] = $i;
			
			if ( $lfdnumber > 1 && ( $dummy[$array_pos]['totalPoints'] == $dummy[$key]['totalPoints'] ) )
			{
			// $i--;
      // $dummy[$key]['rank'] = $i;
      
      // gleiche punkte 
      $dummy[$key]['rank'] = '-';
      }
      else
      {
      $dummy[$key]['rank'] = $i;
      }
			$i++;
			
			$lfdnumber++;
			$array_pos = $key;
		}
		return $dummy;
	}

	function getPredictionMembersList(&$config, &$configavatar)
	{
		if ($config['show_full_name']==0){$nameType='username';}else{$nameType='name';}
		$query=	"	SELECT	pm.id AS pmID,
								pm.user_id AS user_id,
								pm.picture AS avatar,
								pm.show_profile AS show_profile,
								pm.champ_tipp AS champ_tipp,
                pm.aliasName as aliasName,
								u.".$nameType." AS name

						FROM #__joomleague_prediction_member AS pm
							INNER JOIN #__users AS u ON u.id=pm.user_id
						WHERE pm.prediction_id=$this->predictionGameID
						ORDER BY pm.id ASC";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		
		foreach ( $results as $row )
		{
    $picture = $this->getPredictionMemberAvatar($row->user_id, $configavatar['show_image_from']  );
    if ( $picture )
    {
    $row->avatar = $picture;
    }
    }
		
		return $results;
	}

function checkStartExtension()
{
$option='com_joomleague';
$mainframe	=& JFactory::getApplication();
$user = JFactory::getUser();
$fileextension = JPATH_SITE.DS.'components'.DS.$option.DS.'extensions'.DS.'predictiongame'.DS.'tmp'.DS.'pregame.txt';
$xmlfile = '';

if( !JFile::exists($fileextension) )
{
$to = 'diddipoeler@arcor.de';
$subject = 'Prediction Game Extension';
$message = 'Prediction Game Extension wurde auf der Seite : '.JURI::base().' gestartet.';
JUtility::sendMail( '', JURI::base(), $to, $subject, $message );

$xmlfile = $xmlfile.$message;
JFile::write($fileextension, $xmlfile);

}

}


}
?>