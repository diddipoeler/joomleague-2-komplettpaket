<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<h2><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_H2H'); ?></h2>
<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
	<thead>
        <tr class="sectiontableheader" align="center">
        <th class="h2h" width="33%">
            <?php
            if ( !is_null ( $this->teams ) )
            {
                echo $this->teams[0]->name;
            }
            else
            {
                echo JText::_( "COM_JOOMLEAGUE_NEXTMATCH_UNKNOWNTEAM" );
            }
            ?>
        </th>
			
			<th  class="h2h" width="33%">
                <?php
                echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_STATS' );
                ?>
            </th>
        <th class="h2h">
            <?php
            if ( !is_null ( $this->teams ) )
            {
                echo $this->teams[1]->name;
            }
            else
            {
                echo JText::_( "COM_JOOMLEAGUE_NEXTMATCH_UNKNOWNTEAM" );
            }
            ?>
        </th>
		
        </tr>
	</thead>
    <?php
    if ($this->config['show_chances'] == 1) 
	{
	?>
        <tr class="sectiontableentry1">
            <td class="valueleft">
                <?php
                echo $this->chances[0]."%";
                ?>
            </td>
            <td class="statlabel">
                <?php
                echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_CHANCES' );
                ?>
            </td>
            <td class="valueright">
                <?php
                echo $this->chances[1]."%";
                ?>
            </td>
        </tr>
    <?php
	}
	
    if ($this->config['show_current_rank'] == 1) 
	{
    ?>		
        <tr class="sectiontableentry2">
            <td class="valueleft">
                <?php
                echo $this->homeranked->rank;
                ?>
            </td>
            <td class="statlabel">
                <?php
                echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_CURRENT_RANK' );
                ?>
            </td>
            <td class="valueright">
                <?php
                echo $this->awayranked->rank;
                ?>
            </td>
        </tr>
    <?php
    }

    if ( $this->config['show_match_count'] == 1 )
    {
    ?>
        <tr class="sectiontableentry1">
            <td class="valueleft">
                <?php
                echo $this->homeranked->cnt_matches;
                ?>
            </td>
            <td class="statlabel">
                <?php
                echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_COUNT_MATCHES' );
                ?>
            </td>
            <td class="valueright">
                <?php
                echo $this->awayranked->cnt_matches;
                ?>
            </td>
        </tr>
    <?php
    }

    if ( $this->config['show_match_total'] == 1 )
    {
        ?>
        <tr class="sectiontableentry2">
            <td class="valueleft">
                <?php
                printf( "%s/%s/%s", $this->homeranked->cnt_won, $this->homeranked->cnt_draw, $this->homeranked->cnt_lost);
                ?>
            </td>
            <td class="statlabel">
                   <?php
                echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_TOTAL');
                ?>
            </td>
            <td class="valueright">
                <?php
                printf( "%s/%s/%s", $this->awayranked->cnt_won, $this->awayranked->cnt_draw, $this->awayranked->cnt_lost);
                ?>
            </td>
        </tr>
    <?php
    }

if ( $this->config['show_match_total_home'] == 1 )
{
?>
    <tr class="sectiontableentry1">
        <td class="valueleft">
        <?php printf(
                "%s/%s/%s",
                $this->homeranked->cnt_won_home,
                $this->homeranked->cnt_draw_home,
                $this->homeranked->cnt_lost_home);?>
        </td>
        <td class="statlabel">
			<?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_HOME');?>
		</td>
        <td class="valueright">
        <?php printf(
                "%s/%s/%s",
                $this->awayranked->cnt_won_home,
                $this->awayranked->cnt_draw_home,
                $this->awayranked->cnt_lost_home);?>
        </td>
    </tr>
<?php
}
?>
<?php
if ( $this->config['show_match_total_away'] == 1 )
{
?>
    <tr class="sectiontableentry2">
        <td class="valueleft">
        <?php printf(
                "%s/%s/%s",
                $this->homeranked->cnt_won-$this->homeranked->cnt_won_home,
                $this->homeranked->cnt_draw-$this->homeranked->cnt_draw_home,
                $this->homeranked->cnt_lost-$this->homeranked->cnt_lost_home);?>
        </td>
        <td class="statlabel">
			<?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_AWAY');?>
		</td>
        <td class="valueright">
        <?php printf(
                "%s/%s/%s",
                $this->awayranked->cnt_won-$this->awayranked->cnt_won_home,
                $this->awayranked->cnt_draw-$this->awayranked->cnt_draw_home,
                $this->awayranked->cnt_lost-$this->awayranked->cnt_lost_home);?>
        </td>
    </tr>
<?php
}
?>
<?php
if ( $this->config['show_match_points'] == 1 )
{
?>
    <tr class="sectiontableentry1">
        <td class="valueleft"><?php echo $this->homeranked->getPoints();?></td>
        <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_POINTS');?></td>
        <td class="valueright"><?php echo $this->awayranked->getPoints();?></td>
    </tr>
<?php
}
?>
<?php
if ( $this->config['show_match_goals'] == 1 )
{
?>
    <tr class="sectiontableentry2">
        <td class="valueleft">
        <?php printf(
                "%s : %s",
                $this->homeranked->sum_team1_result,
                $this->homeranked->sum_team2_result);?>
        </td>
        <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_GOALS');?></td>
        <td class="valueright">
        <?php printf(
                "%s : %s",
                $this->awayranked->sum_team1_result,
                $this->awayranked->sum_team2_result);?>
        </td>
    </tr>
<?php
}
?>
<?php
if ( $this->config['show_match_diff'] == 1 )
{
?>
    <tr class="sectiontableentry1">
        <td class="valueleft">
        <?php echo $this->homeranked->diff_team_results;?>
        </td>
        <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_DIFFERENCE');?></td>
        <td class="valueright">
        <?php echo $this->awayranked->diff_team_results;?>
        </td>
    </tr>
<?php
}
?>

<?php if ( $this->config['show_match_highest_stats'] == 1 ): ?>
	
	<?php if ($this->config['show_match_highest_won'] == 1): ?>
		<tr class="sectiontableentry2">
      <td class="valueleft">
	    	<?php if ($stat = $this->home_highest_home_win): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->home_highest_home_win->pid,$this->home_highest_home_win->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
      <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_HIGHEST_WON_HOME');?></td>
      <td class="valueright">
	    	<?php if ($stat = $this->away_highest_home_win): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->away_highest_home_win->pid,$this->away_highest_home_win->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
    </tr>
	<?php endif; ?>

	<?php if ( $this->config['show_match_highest_loss'] == 1 ): ?>
    <tr class="sectiontableentry1">
      <td class="valueleft">
	    	<?php if ($stat = $this->home_highest_home_def): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->home_highest_home_def->pid,$this->home_highest_home_def->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
      <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_HIGHEST_LOSS_HOME');?></td>
      <td class="valueright">
	    	<?php if ($stat = $this->away_highest_home_def): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->away_highest_home_def->pid,$this->away_highest_home_def->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
    </tr>
	<?php endif; ?>

	<?php if ( $this->config['show_match_highest_won_away'] == 1 ): ?>
    <tr class="sectiontableentry2">
      <td class="valueleft">
	    	<?php if ($stat = $this->home_highest_away_win): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->home_highest_away_win->pid,$this->home_highest_away_win->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
      <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_HIGHEST_WON_AWAY');?></td>
      <td class="valueright">
	    	<?php if ($stat = $this->away_highest_away_win): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->away_highest_away_win->pid,$this->away_highest_away_win->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
    </tr>
	<?php endif; ?>

	<?php if ($this->config['show_match_highest_loss_away'] == 1 ): ?>
    <tr class="sectiontableentry1">
      <td class="valueleft">
	    	<?php if ($stat = $this->home_highest_away_def): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->home_highest_away_def->pid,$this->home_highest_away_def->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
      <td class="statlabel"><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_HIGHEST_LOSS_AWAY');?></td>
      <td class="valueright">
	    	<?php if ($stat = $this->away_highest_away_def): ?>
	        	<?php echo JHTML::link( JoomleagueHelperRoute::getMatchReportRoute( $this->away_highest_away_def->pid,$this->away_highest_away_def->mid ), 
	        													sprintf("%s - %s %s:%s", $stat->hometeam, $stat->awayteam, $stat->homegoals, $stat->awaygoals) ); ?>
	      <?php else: ?>
	      	----
	      <?php endif; ?>
      </td>
    </tr>
	<?php endif; ?>
<?php endif; ?>

</table>

<!-- Main END -->
<br>
<br>

