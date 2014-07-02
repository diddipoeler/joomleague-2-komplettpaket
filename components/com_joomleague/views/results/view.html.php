<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
jimport( 'joomla.filesystem.file' );

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

class JoomleagueViewResults extends JLGView
{

	public function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document	= JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$css		= 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);

		//add js file
		JHTML::_('behavior.mootools');
		$model	= $this->getModel();
				
		$matches = $model->getMatches();
		
		$config	= $model->getTemplateConfig($this->getName());
		$project = $model->getProject();
		$mdlRound = JModel::getInstance("Round", "JoomleagueModel");
		$roundcode = $mdlRound->getRoundcode($model->roundid);
		$rounds = JoomleagueHelper::getRoundsOptions($project->id, 'ASC', true);
		
			
		$this->assignRef('project', $project);
		$lists=array();
		
		if (isset($this->project))
		{
			$this->assignRef('overallconfig',	$model->getOverallConfig());
			$this->assignRef('config',			array_merge($this->overallconfig, $config));
			$this->assignRef('teams',			$model->getTeamsIndexedByPtid());
			$this->assignRef('showediticon',	$model->getShowEditIcon());
			$this->assignRef('division',		$model->getDivision());
			$this->assignRef('matches',			$matches);
			$this->assignRef('roundid',			$model->roundid);
			$this->assignRef('roundcode',		$roundcode);
			$this->assignRef('rounds',			$model->getRounds());
			$this->assignRef('favteams',		$model->getFavTeams($project));
			$this->assignRef('projectevents',	$model->getProjectEvents());
			$this->assignRef('model',			$model);
			$this->assignRef('isAllowed',		$model->isAllowed());
            $extended = $this->getExtended($this->project->extended, 'project');
            $this->assignRef( 'extended', $extended );

if ( ($this->overallconfig['show_project_rss_feed']) == 1 )
	  {
	  $mod_name               = "mod_jw_srfr";
	  $rssfeeditems = '';
    $rssfeedlink = $this->extended->getValue('COM_JOOMLEAGUE_PROJECT_RSS_FEED_LIVE_RESULTS');
    if ( $rssfeedlink )
    {
    $this->assignRef( 'rssfeeditems', $model->getRssFeeds($rssfeedlink,$this->overallconfig['rssitems']) );
    }
    else
    {
    $this->assignRef( 'rssfeeditems', $rssfeeditems );
    }
    //echo 'rssfeed<br><pre>'.print_r($rssfeedlink,true).'</pre><br>';
    
    /*
    if ( $rssfeedlink )
    {
    $srfrFeedsArray 							= explode(",",$rssfeedlink);
    $perFeedItems 								= $this->overallconfig['perFeedItems'];
    $totalFeedItems 							= $this->overallconfig['totalFeedItems'];
    $feedTimeout									= $this->overallconfig['feedTimeout'];
    $this->assignRef( 'feedTitle' , $this->overallconfig['feedTitle'] );
    $this->assignRef( 'feedFavicon' , $this->overallconfig['feedFavicon'] );
    $this->assignRef( 'feedItemTitle' , $this->overallconfig['feedItemTitle'] );
    $this->assignRef( 'feedItemDate' , $this->overallconfig['feedItemDate'] );
    $feedItemDateFormat						= $this->overallconfig['feedItemDateFormat'];
    $this->assignRef( 'feedItemDescription' , $this->overallconfig['feedItemDescription'] );
    $feedItemDescriptionWordlimit	= $this->overallconfig['feedItemDescriptionWordlimit'];
    $feedItemImageHandling				= $this->overallconfig['feedItemImageHandling'];
    $feedItemImageResizeWidth			= $this->overallconfig['feedItemImageResizeWidth'];
    $feedItemImageResampleQuality	= $this->overallconfig['feedItemImageResampleQuality'];
    $this->assignRef( 'feedItemReadMore' , $this->overallconfig['feedItemReadMore'] );
    
    $this->assignRef( 'feedsBlockPreText' ,	$this->overallconfig['feedsBlockPreText'] );
    $this->assignRef( 'feedsBlockPostText' , $this->overallconfig['feedsBlockPostText'] );
    $this->assignRef( 'feedsBlockPostLink' , $this->overallconfig['feedsBlockPostLink'] );
    $feedsBlockPostLinkURL				= $this->overallconfig['feedsBlockPostLinkURL'];
    $feedsBlockPostLinkTitle			= $this->overallconfig['feedsBlockPostLinkTitle'];
    $srfrCacheTime								= $this->overallconfig['srfrCacheTime'];
    $cacheLocation								= 'cache'.DS.$mod_name;
    $this->assignRef( 'rssfeedoutput',SimpleRssFeedReaderHelper::getFeeds($srfrFeedsArray,$totalFeedItems,$perFeedItems,$feedTimeout,$feedItemDateFormat,$feedItemDescriptionWordlimit,$cacheLocation,$srfrCacheTime,$feedItemImageHandling,$feedItemImageResizeWidth,$feedItemImageResampleQuality,$this->feedFavicon) );
    $css = JURI::root().'components/com_joomleague/assets/css/rssfeedstyle.css';
		$document->addStyleSheet($css); 
		}
        */ 
       
       }
       
       
			$lists['rounds'] = JHTML::_('select.genericlist',$rounds,'current_round','class="inputbox" size="1" onchange="joomleague_changedoc(this);','value','text',$project->current_round);
			$this->assignRef('lists',$lists);
		
			if (!isset($this->config['switch_home_guest'])){$this->config['switch_home_guest']=0;}
			if (!isset($this->config['show_dnp_teams_icons'])){$this->config['show_dnp_teams_icons']=0;}
			if (!isset($this->config['show_results_ranking'])){$this->config['show_results_ranking']=0;}
		}
        
        $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
		
		// Set page title
		$pageTitle = JText::_('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE');
		if ( isset( $this->project->name ) )
		{
			$pageTitle .= ': ' . $this->project->name;
		}
		$document->setTitle($pageTitle);

		//build feed links
		$feed = 'index.php?option=com_joomleague&view=results&p='.$this->project->id.'&format=feed';
		$rss = array('type' => 'application/rss+xml', 'title' => JText::_('COM_JOOMLEAGUE_RESULTS_RSSFEED'));

		// add the links
		$document->addHeadLink(JRoute::_($feed.'&type=rss'), 'alternate', 'rel', $rss);

		parent::display($tpl);
	}

	/**
	 * return html code for not playing teams
	 * 
	 * @param array $games
	 * @param array $teams
	 * @param array $config
	 * @param array $favteams
	 * @param object $project
	 * @return string html
	 */
	public function showNotPlayingTeams(&$games,&$teams,&$config,&$favteams,&$project)
	{
		$output='';
		$playing_teams=array();
		foreach($games as $game)
		{
			self::addPlayingTeams($playing_teams,$game->projectteam1_id,$game->projectteam2_id,$game->published);
		}
		$x=0;
		$not_playing=count($teams) - count($playing_teams);
		if ($not_playing > 0)
		{
			$output .= '<b>'.JText::sprintf('COM_JOOMLEAGUE_RESULTS_TEAMS_NOT_PLAYING',$not_playing).'</b> ';
			foreach ($teams AS $id => $team)
			{
				if (isset($team->projectteamid) && in_array($team->projectteamid,$playing_teams))
				{
					continue; //if team is playing,go to next
				}
				if ($x > 0)
				{
					$output .= ', ';
				}
				if ($config['show_logo_small'] > 0 && $config['show_dnp_teams_icons'])
				{
					$output .= self::getTeamClubIcon($team,$config['show_logo_small']).'&nbsp;';
				}
				$isFavTeam = in_array($team->id, $favteams);
				$output .= JoomleagueHelper::formatTeamName($team, 't'.$team->id, $config, $isFavTeam );
				$x++;
			}
		}
		return $output;
	}

	public function addPlayingTeams(&$playing_teams,$hometeam,$awayteam,$published=false)
	{
		if ($hometeam>0 && !in_array($hometeam,$playing_teams) && $published){$playing_teams[]=$hometeam;}
		if ($awayteam>0 && !in_array($awayteam,$playing_teams) && $published){$playing_teams[]=$awayteam;}
	}
	
	/**
	 * returns html <img> for club assigned to team
	 * @param object team
	 * @param int type=1 for club small image,or 2 for club country
	 * @param boolean $with_space
	 * @return unknown_type
	 */
	public function getTeamClubIcon($team,$type=1,$attribs=array())
	{
		if(!isset($team->name)) return "";
		$title=$team->name;
		$attribs=array_merge(array('title' => $title,$attribs));
		if ($type==1)
		{
			if (!empty($team->logo_small) && JFile::exists($team->logo_small))
			{
				$image=JHTML::image($team->logo_small,$title,$attribs);
			}
			else
			{
				$image=JHTML::image(JURI::root().JoomleagueHelper::getDefaultPlaceholder("clublogosmall"),$title,$attribs);
			}
		}
		elseif ($type==2 && !empty($team->country))
		{
			$image=Countries::getCountryFlag($team->country);
			if (empty($image))
			{
				$image=JHTML::image(JURI::root().JoomleagueHelper::getDefaultPlaceholder("icon"),$title,$attribs);
			}
		}
        elseif ($type==3 && !empty($team->country) && !empty($team->logo_small) && JFile::exists($team->logo_small) )
		{
			//$image = Countries::getCountryFlag($team->country);
			if (empty($image))
			{
				$image = JHTML::image($team->logo_small,$title,$attribs).' '.Countries::getCountryFlag($team->country);
			}
		}
        elseif ($type==3 && !empty($team->country) && !empty($team->logo_small) && !JFile::exists($team->logo_small) )
		{
			//$image = Countries::getCountryFlag($team->country);
			if (empty($image))
			{
				$image = JHTML::image(JURI::root().JoomleagueHelper::getDefaultPlaceholder("icon"),$title,$attribs).' '.Countries::getCountryFlag($team->country);
			}
		}
    elseif ($type==4 && !empty($team->country) && !empty($team->logo_small) && JFile::exists($team->logo_small) )
		{
			//$image = Countries::getCountryFlag($team->country);
			if (empty($image))
			{
				$image = Countries::getCountryFlag($team->country).' '.JHTML::image($team->logo_small,$title,$attribs);
			}
		}
        elseif ($type==4 && !empty($team->country) && !empty($team->logo_small) && !JFile::exists($team->logo_small) )
		{
			//$image = Countries::getCountryFlag($team->country);
			if (empty($image))
			{
				$image = Countries::getCountryFlag($team->country).' '.JHTML::image(JURI::root().JoomleagueHelper::getDefaultPlaceholder("icon"),$title,$attribs);
			}
		}
		else
		{
			$image='';
		}

		return $image;
	}
	

	/**
	 * return an array of matches indexed by date
	 *
	 * @return array
	 */
	public function sortByDate()
	{
		$dates=array();
		foreach ((array) $this->matches as $m)
		{
			$date=substr($m->match_date,0,10);
			if (!isset($dates[$date]))
			{
				$dates[$date]=array($m);
			}
			else
			{
				$dates[$date][]=$m;
			}
		}
		return $dates;
	}
	
	/**
	 * used in form row template
	 * TODO: move to own template file
	 * 
	 * @param int $i
	 * @param object $match
	 * @param boolean $backend
	 */
	public function editPartResults($i,$match,$backend=false)
	{
		$link="javascript:void(0)";
		$params=array("onclick" => "switchMenu('part".$match->id."')");
		$imgTitle=JText::_('COM_JOOMLEAGUE_ADMIN_EDIT_MATRIX_ROUNDS_PART_RESULT');
		$desc=JHTML::image(	JURI::root()."media/com_joomleague/jl_images/sort01.gif",
		$imgTitle,array("border" => 0,"title" => $imgTitle));
		echo JHTML::link($link,$desc,$params);

		echo '<span id="part'.$match->id.'" style="display:none">';
		echo '<br />';

		$partresults1= explode(";",$match->team1_result_split);
		$partresults2= explode(";",$match->team2_result_split);

		for ($x=0; $x < ($this->project->game_parts); $x++)
		{
			echo ($x+1).".:";
			echo '<input type="text" style="font-size:9px;" name="team1_result_split';
			echo (! is_null($i)) ? $match->id : '';
			echo '[]" value="';
			echo (isset($partresults1[$x])) ? $partresults1[$x] : '';
			echo '" size="2" tabindex="1" class="inputbox"';
			if (! is_null($i))
			{
				echo ' onchange="';
				if ($backend)
				{
					echo 'document.adminForm.cb'.$i.'.checked=true; isChecked(this.checked);';
				}
				else
				{
					echo '$(\'cb'.$i.'\').checked=true;';
				}
				echo '"';
			}
			echo ' />';
			echo ':';
			echo '<input type="text" style="font-size:9px;"';
			echo ' name="team2_result_split';
			echo (! is_null($i)) ? $match->id : '';
			echo '[]" value="';
			echo (isset($partresults2[$x])) ? $partresults2[$x] : '';
			echo '" size="2" tabindex="1" class="inputbox" ';
			if (! is_null($i))
			{
				echo 'onchange="';
				if ($backend)
				{
					echo 'document.adminForm.cb'.$i.'.checked=true; isChecked(this.checked);';
				}
				else
				{
					echo '$(\'cb'.$i.'\').checked=true;';
				}
				echo '"';
			}
			echo '/>';
			if (($x) < ($this->project->game_parts)){echo '<br />';}
		}

		if ($this->project->allow_add_time)
		{
			if($match->match_result_type >0){
				echo JText::_('COM_JOOMLEAGUE_RESULTS_OVERTIME').':';
				echo '<input type="text" style="font-size:9px;"';
				echo ' name="team1_result_ot'.$match->id.'"';
				echo ' value="';
				echo isset($match->team1_result_ot) ? ''.$match->team1_result_ot : '';
				echo '"';
				echo ' size="2" tabindex="1" class="inputbox" onchange="$(\'cb'.$i.'\').checked=true;" />';
				echo ':';
				echo '<input type="text" style="font-size:9px;"';
				echo ' name="team2_result_ot'.$match->id.'"';
				echo ' value="';
				echo isset($match->team2_result_ot) ? ''.$match->team2_result_ot : '';
				echo '"';
				echo ' size="2" tabindex="1" class="inputbox" onchange="$(\'cb'.$i.'\').checked=true;" />';
			}
			if($match->match_result_type == 2){
				echo '<br />';
				echo JText::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT').':';
				echo '<input type="text" style="font-size:9px;"';
				echo ' name="team1_result_so'.$match->id.'"';
				echo ' value="';
				echo isset($match->team1_result_so) ? ''.$match->team1_result_so : '';
				echo '"';
				echo ' size="2" tabindex="1" class="inputbox" onchange="$(\'cb'.$i.'\').checked=true;" />';
				echo ':';
				echo '<input type="text" style="font-size:9px;"';
				echo ' name="team2_result_so'.$match->id.'"';
				echo ' value="';
				echo isset($match->team2_result_so) ? ''.$match->team2_result_so : '';
				echo '"';
				echo ' size="2" tabindex="1" class="inputbox" onchange="$(\'cb'.$i.'\').checked=true;" />';
			}
		}
		echo "</span>";
	}
	
	
	/**
	* formats the score according to settings
	*
	* @param object $game
	* @return string
	*/
	function formatScoreInline($game,&$config)
	{
		if ($config['switch_home_guest'])
		{
			$homeResult	= $game->team2_result;
			$awayResult	= $game->team1_result;
			$homeResultOT	= $game->team2_result_ot;
			$awayResultOT	= $game->team1_result_ot;
			$homeResultSO	= $game->team2_result_so;
			$awayResultSO	= $game->team1_result_so;
			$homeResultDEC	= $game->team2_result_decision;
			$awayResultDEC	= $game->team1_result_decision;
		}
		else
		{
			$homeResult	= $game->team1_result;
			$awayResult	= $game->team2_result;
			$homeResultOT	= $game->team1_result_ot;
			$awayResultOT	= $game->team2_result_ot;
			$homeResultSO	= $game->team1_result_so;
			$awayResultSO	= $game->team2_result_so;
			$homeResultDEC	= $game->team1_result_decision;
			$awayResultDEC	= $game->team2_result_decision;
		}
	
		$result=$homeResult.'&nbsp;'.$config['seperator'].'&nbsp;'.$awayResult;
		if ($game->alt_decision)
		{
			$result='<b style="color:red;">';
			$result .= $homeResultDEC.'&nbsp;'.$config['seperator'].'&nbsp;'.$awayResultDEC;
			$result .= '</b>';
	
		}
		if (isset($homeResultSO) || isset($formatScoreawayResultSO))
		{
			if ($this->config['result_style']==1){
				$result .= '<br />';
			}else{$result .= ' ';
			}
			$result .= '('.JText::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT').' ';
			$result .= $homeResultSO.'&nbsp;'.$config['seperator'].'&nbsp;'.$awayResultSO;
			$result .= ')';
		}
		else
		{
			if ($game->match_result_type==2)
			{
				if ($this->config['result_style']==1){
					$result .= '<br />';
				}else{$result .= ' ';
				}
				$result .= '('.JText::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT');
				$result .= ')';
			}
		}
		if (isset($homeResultOT) || isset($awayResultOT))
		{
			if ($this->config['result_style']==1){
				$result .= '<br />';
			}else{$result .= ' ';
			}
			$result .= '('.JText::_('COM_JOOMLEAGUE_RESULTS_OVERTIME').' ';
			$result .= $homeResultOT.'&nbsp;'.$config['seperator'].'&nbsp;'.$awayResultOT;
			$result .= ')';
		}
		else
		{
			if ($game->match_result_type==1)
			{
				if ($this->config['result_style']==1){
					$result .= '<br />';
				}else{$result .= ' ';
				}
				$result .= '('.JText::_('COM_JOOMLEAGUE_RESULTS_OVERTIME');
				$result .= ')';
			}
		}

		return $result;
	}
	
	/**
	 * return match state html code
	 * @param $game
	 * @param $config
	 * @return unknown_type
	 */
	function showMatchState(&$game,&$config)
	{
		$output='';

		if ($game->cancel > 0)
		{
			$output .= $game->cancel_reason;
		}
		else
		{
			$output .= self::formatScoreInline($game,$config);
		}

		return $output;
	}
    
    function showMatchSummaryAsSqueezeBox(&$game)
	{
	/*
    $output = '<script language="JavaScript">';
    $output .= 'var options = {size: {x: 300, y: 250}}';
	$output .= 'SqueezeBox.initialize(options)';
    $output .= "SqueezeBox.setContent('string','nummer:')";
    $output .= '</script>'; 
    echo $output;
    */  
    return $game->summary;
    }   

	function showMatchRefereesAsTooltip(&$game)
	{
		if ($this->config['show_referee'])
		{
			if ($this->project->teams_as_referees)
			{
				$referees=$this->model->getMatchRefereeTeams($game->id);
			}
			else
			{
				$referees=$this->model->getMatchReferees($game->id);
			}

			if (!empty($referees))
			{
				$toolTipTitle	= JText::_('COM_JOOMLEAGUE_RESULTS_REF_TOOLTIP');
				$toolTipText	= '';

				foreach ($referees as $ref)
				{
					if ($this->project->teams_as_referees)
					{
						$toolTipText .= $ref->teamname.' ('.$ref->position_name.')'.'&lt;br /&gt;';
					}
					else
					{
						$toolTipText .= ($ref->firstname ? $ref->firstname.' '.$ref->lastname : $ref->lastname).' ('.$ref->position_name.')'.'&lt;br /&gt;';
					}
				}

				?>
			<!-- Referee tooltip -->
			<span class="hasTip"
				title="<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>"> <img
				src="<?php echo JURI::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png"
				alt="" title="" /> </span>
			
				<?php
			}
			else
			{
				?>&nbsp;<?php
			}
		}
	}
	
	function showReportDecisionIcons(&$game)
	{
		//echo '<br /><pre>~'.print_r($game,true).'~</pre><br />';

		$output='';
		$report_link=JoomleagueHelperRoute::getMatchReportRoute($game->project_id,$game->id);

		if ((($game->show_report) && (trim($game->summary) != '')) || ($game->alt_decision) || ($game->match_result_type > 0))
		{
			if ($game->alt_decision)
			{
				$imgTitle=JText::_($game->decision_info);
				$img='media/com_joomleague/jl_images/court.gif';
			}
			else
			{
				$imgTitle=JText::_('Has match summary');
				$img='media/com_joomleague/jl_images/zoom.png';
			}
			$output .= JHTML::_(	'link',
			$report_link,
			JHTML::image(JURI::root().$img,$imgTitle,array("border" => 0,"title" => $imgTitle)),
			array("title" => $imgTitle));
		}
		else
		{
			$output .= '&nbsp;';
		}

		return $output;
	}
	
	
	/**
	 * returns html for events in tabs
	 * @param object match
	 * @param array project events
	 * @param array match events
	 * @param aray match substitutions
	 * @param array $config
	 * @return string
	 */
	function showEventsContainerInResults($matchInfo,$projectevents,$matchevents,$substitutions=null,$config)
	{
		$output='';
		$result='';

		if ($this->config['use_tabs_events'])
		{
			// Make event tabs with JPane integrated function in Joomla 1.5 API
			$result	=& JPane::getInstance('tabs',array('startOffset'=>0));
			$output .= $result->startPane('pane');

			// Size of the event icons in the tabs (when used)
			$width = 20; $height = 20; $type = 4;
			// Never show event text or icon for each event list item (info already available in tab)
			$showEventInfo = 0;

			$cnt = 0;
			foreach ($projectevents AS $event)
			{
				//display only tabs with events
				foreach ($matchevents AS $me)
				{
					$cnt=0;
					if ($me->event_type_id == $event->id)
					{
						$cnt++;
						break;
					}
				}
				if($cnt==0){continue;}
				
				if ($this->config['show_events_with_icons'] == 1)
				{
					// Event icon as thumbnail on the tab (a placeholder icon is used when the icon does not exist)
					$imgTitle = JText::_($event->name);
					$tab_content = JoomleagueHelper::getPictureThumb($event->icon, $imgTitle, $width, $height, $type);
				}
				else
				{
					$tab_content = JText::_($event->name);
				}

				$output .= $result->startPanel($tab_content, $event->id);
				$output .= '<table class="matchreport" border="0">';
				$output .= '<tr>';

				// Home team events
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($matchevents AS $me)
				{
					$output .= self::_formatEventContainerInResults($me, $event, $matchInfo->projectteam1_id, $showEventInfo);
				}
				$output .= '</ul>';
				$output .= '</td>';

				// Away team events
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($matchevents AS $me)
				{
					$output .= self::_formatEventContainerInResults($me, $event, $matchInfo->projectteam2_id, $showEventInfo);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '</tr>';
				$output .= '</table>';
				$output .= $result->endPanel();
			}

			if (!empty($substitutions))
			{
				if ($this->config['show_events_with_icons'] == 1)
				{
					// Event icon as thumbnail on the tab (a placeholder icon is used when the icon does not exist)
					$imgTitle = JText::_('COM_JOOMLEAGUE_IN_OUT');
					$pic_tab	= 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/subst.png';
					$tab_content = JoomleagueHelper::getPictureThumb($pic_tab, $imgTitle, $width, $height, $type);
				}
				else
				{
					$tab_content = JText::_('COM_JOOMLEAGUE_IN_OUT');
				}

				$pic_time	= JURI::root().'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/playtime.gif';
				$pic_out	= JURI::root().'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/out.png';
				$pic_in		= JURI::root().'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/in.png';
				$imgTime = JHTML::image($pic_time,JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE'),array(' title' => JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE')));
				$imgOut  = JHTML::image($pic_out,JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_WENT_OUT'),array(' title' => JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_WENT_OUT')));
				$imgIn   = JHTML::image($pic_in,JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_CAME_IN'),array(' title' => JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_CAME_IN')));

				$output .= $result->startPanel($tab_content,'0');
				$output .= '<table class="matchreport" border="0">';
				$output .= '<tr>';
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($substitutions AS $subs)
				{
					$output .= self::_formatSubstitutionContainerInResults($subs,$matchInfo->projectteam1_id,$imgTime,$imgOut,$imgIn);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($substitutions AS $subs)
				{
					$output .= self::_formatSubstitutionContainerInResults($subs,$matchInfo->projectteam2_id,$imgTime,$imgOut,$imgIn);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '</tr>';
				$output .= '</table>';
				$output .= $result->endPanel();
			}
			$output .= $result->endPane();
		}
		else
		{
			$showEventInfo = ($this->config['show_events_with_icons'] == 1) ? 1 : 2;
			$output .= '<table class="matchreport" border="0">';
			$output .= '<tr>';

			// Home team events
			$output .= '<td class="list-left">';
			$output .= '<ul>';
			foreach ((array) $matchevents AS $me)
			{
				if ($me->ptid == $matchInfo->projectteam1_id)
				{
					$output .= self::_formatEventContainerInResults($me, $projectevents[$me->event_type_id], $matchInfo->projectteam1_id, $showEventInfo);
				}
			}
			$output .= '</ul>';
			$output .= '</td>';

			// Away team events
			$output .= '<td class="list-right">';
			$output .= '<ul>';
			foreach ($matchevents AS $me)
			{
				if ($me->ptid == $matchInfo->projectteam2_id)
				{
					$output .= self::_formatEventContainerInResults($me, $projectevents[$me->event_type_id], $matchInfo->projectteam2_id, $showEventInfo);
			    }
			}
			$output .= '</ul>';
			$output .= '</td>';
			$output .= '</tr>';
			$output .= '</table>';
		}

		return $output;
	}
	
	function formatResult(&$team1,&$team2,&$game,&$reportLink)
	{
		$output			= '';
		// check home and away team for favorite team
		$fav 			= isset($team1->id) && in_array($team1->id,$this->favteams) ? 1 : 0;
		if(!$fav)
		{
			$fav		= isset($team2->id) && in_array($team2->id,$this->favteams) ? 1 : 0;
		}
		// 0=no links
		// 1=For all teams
		// 2=For favorite team(s) only
		if($this->config['show_link_matchreport'] == 1 || ($this->config['show_link_matchreport'] == 2 && $fav))
		{
			$output = JHTML::_(	'link', $reportLink,
					'<span class="score0">'.$this->showMatchState($game,$this->config).'</span>',
			array("title" => JText::_('COM_JOOMLEAGUE_RESULTS_SHOW_MATCHREPORT')));
		}
		else
		{
			$output = $this->showMatchState($game,$this->config);
		}
		$search_empty_part_results = array(";", "NULL");
		if($this->config['show_part_results'] && ((str_replace($search_empty_part_results, '', $game->team1_result_split) != "") && (str_replace($search_empty_part_results, '', $game->team2_result_split) != "")) ){
		$output .= ' (' . implode(':',explode(';', $game->team1_result_split)) . ", " . implode(':', explode(';', $game->team2_result_split)). ')';
		}
		return $output;

	}
	
	function _formatEventContainerInResults($matchevent, $event, $projectteamId, $showEventInfo)
	{
		// Meaning of $showEventInfo:
		// 0 : do not show event as text or as icon in a list item
		// 1 : show event as icon in a list item (before the time)
		// 2 : show event as text in a list item (after the time)
		$output='';
		if ($matchevent->event_type_id == $event->id && $matchevent->ptid == $projectteamId)
		{
			$output .= '<li class="events">';
			if ($showEventInfo == 1)
			{
				// Size of the event icons in the tabs
				$width = 20; $height = 20; $type = 4;
				$imgTitle = JText::_($event->name);
				$icon = JoomleagueHelper::getPictureThumb($event->icon, $imgTitle, $width, $height, $type);

				$output .= $icon;
			}

			$event_minute = str_pad($matchevent->event_time, 2 ,'0', STR_PAD_LEFT);
			if ($this->config['show_event_minute'] == 1 && $matchevent->event_time > 0)
			{
				$output .= '<b>'.$event_minute.'\'</b> ';
			} 

			if ($showEventInfo == 2)
			{
				$output .= JText::_($event->name).' ';
			}

			if (strlen($matchevent->firstname1.$matchevent->lastname1) > 0)
			{
				$output .= JoomleagueHelper::formatName(null, $matchevent->firstname1, $matchevent->nickname1, $matchevent->lastname1, $this->config["name_format"]);
			}
			else
			{
				$output .= JText :: _('COM_JOOMLEAGUE_UNKNOWN_PERSON');
			}
			
			// only show event sum and match notice when set to on in template cofig
			if($this->config['show_event_sum'] == 1 || $this->config['show_event_notice'] == 1)
			{
				if (($this->config['show_event_sum'] == 1 && $matchevent->event_sum > 0) || ($this->config['show_event_notice'] == 1 && strlen($matchevent->notice) > 0))
				{
					$output .= ' (';
						if ($this->config['show_event_sum'] == 1 && $matchevent->event_sum > 0)
						{
							$output .= $matchevent->event_sum;
						}
						if (($this->config['show_event_sum'] == 1 && $matchevent->event_sum > 0) && ($this->config['show_event_notice'] == 1 && strlen($matchevent->notice) > 0))
						{
							$output .= ' | ';
						}
						if ($this->config['show_event_notice'] == 1 && strlen($matchevent->notice) > 0)
						{
							$output .= $matchevent->notice;
						}
					$output .= ')';
				}
			}
			
			$output .= '</li>';
		}
		return $output;
	}

	function _formatSubstitutionContainerInResults($subs,$projectteamId,$imgTime,$imgOut,$imgIn)
	{
		$output='';
		if ($subs->ptid == $projectteamId)
		{
			$output .= '<li class="events">';
			// $output .= $imgTime;
			$output .= '&nbsp;'.$subs->in_out_time.'. '.JText::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE');
			$output .= '<br />';

			$output .= $imgOut;
			$output .= '&nbsp;'.JoomleagueHelper::formatName(null, $subs->out_firstname, $subs->out_nickname, $subs->out_lastname, $this->config["name_format"]);
			$output .= '&nbsp;('.JText :: _($subs->out_position).')';
			$output .= '<br />';

			$output .= $imgIn;
			$output .= '&nbsp;'.JoomleagueHelper::formatName(null, $subs->firstname, $subs->nickname, $subs->lastname, $this->config["name_format"]);
			$output .= '&nbsp;('.JText :: _($subs->in_position).')';
			$output .= '<br /><br />';
			$output .= '</li>';
		}
		return $output;
	}
}
?>
