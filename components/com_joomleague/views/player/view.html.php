<?php defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.view');

class JoomleagueViewPlayer extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$model = $this->getModel();
		$config=$model->getTemplateConfig($this->getName());

		$person=$model->getPerson();
		$nickname = isset($person->nickname) ? $person->nickname : "";
		if(!empty($nickname)){$nickname="'".$nickname."'";}
		$this->assignRef('isContactDataVisible',$model->isContactDataVisible($config['show_contact_team_member_only']));
		$project = $model->getProject();
		$this->assignRef('project', $project);
		$this->assignRef('overallconfig',$model->getOverallConfig());
		$this->assignRef('config',$config);
		$this->assignRef('person',$person);
		$this->assignRef('nickname',$nickname);

		$this->assignRef('teamPlayers',$model->getTeamPlayers());

		// Select the teamplayer that is currently published (in case the player played in multiple teams in the project)
		$teamPlayer = null;
		if (count($this->teamPlayers))
		{
			$currentProjectTeamId=0;
			foreach ($this->teamPlayers as $teamPlayer)
			{
				if ($teamPlayer->published == 1)
				{
					$currentProjectTeamId=$teamPlayer->projectteam_id;
					break;
				}
			}
			if ($currentProjectTeamId)
			{
				$teamPlayer = $this->teamPlayers[$currentProjectTeamId];
			}
		}
		$sportstype = $config['show_plcareer_sportstype'] ? $model->getSportsType() : 0;
		$this->assignRef('teamPlayer',$teamPlayer);
		$this->assignRef('historyPlayer',$model->getPlayerHistory($sportstype, 'ASC'));
		$this->assignRef('historyPlayerStaff',$model->getPlayerHistoryStaff($sportstype, 'ASC'));
		$this->assignRef('AllEvents',$model->getAllEvents($sportstype));
		$this->assignRef('showediticon',$model->getAllowed($config['edit_own_player']));
		$this->assignRef('stats',$model->getProjectStats());

		// Get events and stats for current project
		if ($config['show_gameshistory'])
		{
			$this->assignRef('games',$model->getGames());
			$this->assignRef('teams',$model->getTeamsIndexedByPtid());
			$this->assignRef('gamesevents',$model->getGamesEvents());
			$this->assignRef('gamesstats',$model->getPlayerStatsByGame());
		}

		// Get events and stats for all projects where player played in (possibly restricted to sports type of current project)
		if ($config['show_career_stats'])
		{
			$this->assignRef('stats',$model->getStats());
			$this->assignRef('projectstats',$model->getPlayerStatsByProject($sportstype));
		}

		$extended = $this->getExtended($person->extended, 'person');
		$this->assignRef( 'extended', $extended );
        $form_value = $this->extended->getValue('COM_JOOMLEAGUE_EXT_PERSON_PARENT_POSITIONS');
        $this->assignRef ('person_parent_positions', explode(",",$form_value) );
        
        $form_value = $this->extended->getValue('COM_JOOMLEAGUE_EXT_PERSON_POSITION');
        $this->assignRef ('person_position', $form_value );
        
        $this->assignRef( 'hasDescription',$this->teamPlayer->notes);
        
        foreach ($this->extended->getFieldsets() as $fieldset)
	{
	$hasData = false;
    $fields = $this->extended->getFieldset($fieldset->name);
			foreach ($fields as $field)
			{
				// TODO: backendonly was a feature of JLGExtraParams, and is not yet available.
				//       (this functionality probably has to be added later)
				$value = $field->value;	// Remark: empty($field->value) does not work, using an extra local var does
				if (!empty($value)) // && !$field->backendonly
				{
					$hasData = true;
					break;
				}
			}   
	}
    $this->assignRef( 'hasExtendedData',$hasData);
    
    $hasStatus = false;
    if (	( isset($this->teamPlayer->injury) && $this->teamPlayer->injury > 0 ) ||
		( isset($this->teamPlayer->suspension) && $this->teamPlayer->suspension > 0 ) ||
		( isset($this->teamPlayer->away) && $this->teamPlayer->away > 0 ) )
    {
    $hasStatus = true;
    }
    $this->assignRef( 'hasStatus',$hasStatus);
    	
    $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
    $this->assign('use_joomlaworks', JComponentHelper::getParams('com_joomleague')->get('use_joomlaworks',0) );
        
		if (isset($person))
		{
			$name = JoomleagueHelper::formatName(null, $this->person->firstname, $this->person->nickname,  $this->person->lastname,  $this->config["name_format"]);
		}
		$this->assignRef('playername', $name);
		$document->setTitle(JText::sprintf('COM_JOOMLEAGUE_PLAYER_INFORMATION', $name));

		parent::display($tpl);
	}

}
?>