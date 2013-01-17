<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- START of match events -->

<h2><?php echo JText::_('COM_JOOMLEAGUE_MATCHREPORT_EVENTS'); ?></h2>		

<table class="eventstable" border="0">

		<thead>
	        <tr class="sectiontableheader">
	            <th style="text-align:center"><?php echo JText::_('COM_JOOMLEAGUE_MATCHREPORT_EVENT_TIME'); ?></th>
	            <th colspan=3><?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_EVENT'); ?></th>
	        </tr>
	    </thead>
			
        <?php
        $k = 0;
        
        $IconArr = array(0 => '');
        $TextArr = array(0 => '');
        foreach ( $this->eventtypes as $event )
        {
        $TextArr[$event->id] =  $event->name;
        $IconArr[$event->id] =  $event->icon;
        }
        
        foreach ($this->matchevents AS $me)
            {
                    ?>
                    
                    <tr class="<?php echo ($k == 0)? $this->config['style_class1'] : $this->config['style_class2']; ?>" id="event-<?php echo $me->event_id; ?>">
                    
                    <?php
                    //Icon
                    $pic_tab=$IconArr[$me->event_type_id];
                    $eventname=JText::_($TextArr[$me->event_type_id]);
                    
                    //Time
                    $prefix = '';
                    if ($this->config['show_event_minute'] == 1 && $me->event_time > 0)
                    {
                        $prefix = str_pad($me->event_time, 2 ,'0', STR_PAD_LEFT);
                    }
                    echo '<td class="tcenter">' . $prefix . '</td>';
                    
                    
                    if ($me->event_type_id > 0) {
                        
                        if ($pic_tab == 'images/com_joomleague/database/events/event.gif')
                        {
                            $txt_tab = $eventname;
                        }
                        else
                        {
                            $imgTitle = $eventname;
                            $imgTitle2 = array(' title' => $imgTitle, ' alt' => $imgTitle, ' style' => 'max-height:40px;');
                            $txt_tab = JHTML::image($pic_tab,$imgTitle,$imgTitle2);
                        }
                        echo  '<td class="ecenter">' . $txt_tab . '</td>';
                        
                        //Teamname
                        if ($me->ptid == $this->match->projectteam1_id) { 
                            if ($me->playerid != 0) {
                            $teamname = '';
                            } else {
                            $teamname = ' ' . $this->team1->middle_name;
                            }
                            $eventteam = $this->team1;
                        } else { 
                            if ($me->playerid != 0) {
                            $teamname = '';
                            } else {
                            $teamname = ' ' . $this->team2->middle_name;
                            }
                            $eventteam = $this->team2;
                        }
                        
                        //Club
                        echo '<td class="ecenter">' . JoomleagueModelProject::getClubIconHtml($eventteam,1) . '</td>';
                        
                        //Event
                        // only show event sum and match notice when set to on in template cofig
                        $sum_notice = "";
                        if($this->config['show_event_sum'] == 1 || $this->config['show_event_notice'] == 1)
                        {
                            if (($this->config['show_event_sum'] == 1 && $me->event_sum > 0) || ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0))
                            {
                                $sum_notice .= ' (';
                                    if ($this->config['show_event_sum'] == 1 && $me->event_sum > 0)
                                    {
                                        $sum_notice .= $me->event_sum;
                                    }
                                    if (($this->config['show_event_sum'] == 1 && $me->event_sum > 0) && ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0))
                                    {
                                        $sum_notice .= ' | ';
                                    }
                                    if ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0)
                                    {
                                        $sum_notice .= $me->notice;
                                    }
                                $sum_notice .= ')';
                            }
                        }
                        
                        $match_player=JoomleagueHelper::formatName('', $me->firstname1, $me->nickname1, $me->lastname1, $this->config["name_format"]);
                        
                        if ($this->config['event_link_player'] == 1 && $me->playerid != 0)
                        {
                            $player_link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$me->team_id,$me->playerid);
                            $match_player = JHTML::link($player_link,$match_player);
                        }                         
                        
                        echo '<td>' . $eventname . $sum_notice . ' - ' . $match_player . $teamname . '</td>';
                    }
                    else
                    {
                        //EventType 0, therefore text comment
                        echo '<td colspan=2 style="text-align:center">...</td>';
                        
                        if ($me->event_sum != '') {
                        $commentType = substr($me->event_sum, 0, 1);
                        }else{
                        $commentType = 0;
                        }
                        
                        switch ($commentType) {
                            case 2:
                                /** Before match, between periods or after match */
                                echo '<td class="tickerStyle2">' . $me->notes . '</td>';
                                break;
                            case 1:
                                /** Standard text comment */
                                echo '<td class="tickerStyle1">' . $me->notes . '</td>';
                                break;
		                }
                    }    
                echo '</tr>';
                $k = 1 - $k;
                }
            
            ?>
			
</table>
<!-- END of match events -->
<br />