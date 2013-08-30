<?php
/**
 * @author And_One <andone@mfga.at>
 * @version	 2.0
 * @package	 Joomla
 * @subpackage  Joomleague sports type statistics module
 * @copyright   Copyright (C) 2012-2013 JoomLeague.net. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die('Restricted access');

// check if any results returned
if ($data['projectscount'] == 0) {
	echo '<p class="modjlgsports">' . JText::_('MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_NO_PROJECTS') . '</p>';
	return;
} else {
	?>
<div class="modjlgsports <?php echo $params->get( 'moduleclass_sfx' ); ?>">
	<h4>
	<?php 
	if($data['sportstype']->icon) { ?><img src="<?php echo $data['sportstype']->icon; ?>" alt=""/><?php } ?> <?php echo JText::_($data['sportstype']->name); ?>
	</h4>
  
	<?php if($params->get('show_project',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS").'" src="administrator/components/com_joomleague/assets/images/projects.png"/>';
			echo ''.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS"); 
		} else {
			echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS"); 
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectscount']?></li>
	</ul>
	<?php } ?>
  
	<?php if($params->get('show_leagues',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES").'" src="administrator/components/com_joomleague/assets/images/leagues.png"/>';
			echo ''.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES");
		} else {
			echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES"); 
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['leaguescount']?></li>
	</ul>
	<?php } ?>
  
	<?php if($params->get('show_seasons',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS").'" src="administrator/components/com_joomleague/assets/images/seasons.png"/>';
			echo ''.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS");
		} else {
			echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS");
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['seasonscount']?></li>
	</ul>
	<?php } ?>
  
  <?php if($params->get('show_playgrounds',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYGROUNDS").'" src="administrator/components/com_joomleague/assets/images/playground.png"/>';
			echo ''.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYGROUNDS");
		} else {
			echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYGROUNDS");
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['playgroundscount']?></li>
	</ul>
	<?php } ?>
	
	
	<?php if($params->get('show_clubs',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_clubs' )).'" src="administrator/components/com_joomleague/assets/images/clubs.png"/>';
			echo ''.JText::_($params->get( 'text_clubs' )); 
		} else {
			echo JText::_($params->get( 'text_clubs' ));
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['clubscount']?></li>
	</ul>
	<?php } ?>
	
	
	<?php if($params->get('show_teams',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_teams' )).'" src="administrator/components/com_joomleague/assets/images/teams.png"/>';
			echo ''.JText::_($params->get( 'text_teams' )); 
		} else {
			echo JText::_($params->get( 'text_teams' ));
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectteamscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_players',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_players' )).'" src="administrator/components/com_joomleague/assets/images/players.png"/>';
			echo ''.JText::_($params->get( 'text_players' )); 
		} else {
			echo JText::_($params->get( 'text_players' ));
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['personscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_divisions',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_divisions' )).'" src="administrator/components/com_joomleague/assets/images/division.png"/>';
			echo ''.JText::_($params->get( 'text_divisions' ));
		} else {
			echo JText::_($params->get( 'text_divisions' ));
		} 
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectdivisionscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_rounds',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_rounds' )).'" src="administrator/components/com_joomleague/assets/images/icon-16-Matchdays.png"/>';
			echo ''.JText::_($params->get( 'text_rounds' )); 
		} else {
			echo JText::_($params->get( 'text_rounds' ));
		}
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectroundscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_matches',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_matches' )).'" src="administrator/components/com_joomleague/assets/images/matches.png"/>';
			echo ''.JText::_($params->get( 'text_matches' ));
		} else {
			echo JText::_($params->get( 'text_matches' ));
		} 
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatchescount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_player_events',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_player_events' )).'" src="administrator/components/com_joomleague/assets/images/events.png"/>';
			echo ''.JText::_($params->get( 'text_player_events' )); 
		} else {
			echo JText::_($params->get( 'text_player_events' ));
		}	
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatcheseventscount']?></li>
  </ul>  
    <?PHP
    foreach ( $data['projectmatcheseventsnamecount'] as $row )
    {
    ?>
    <ul>
    <li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($row->name).'" src="'.$row->icon.'"/>';
			echo ''.JText::_($row->name); 
		} else {
			echo JText::_($row->name);
		}	
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $row->total ?></li>
    </ul>
    <?PHP
    }
    
    ?>
	
	<?php } ?>
	<?php if($params->get('show_player_stats',1) == 1) { ?>
	<ul>
		<li class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_($params->get( 'text_player_stats' )).'" src="administrator/components/com_joomleague/assets/images/icon-48-statistics.png"/>';
			echo ''.JText::_($params->get( 'text_player_stats' )); 
		} else {
			echo JText::_($params->get( 'text_player_stats' ));
		} 
		?></li>
		<li class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatchesstatscount']?></li>
	</ul>
	<?php } ?>
</div>
<?php 
}
?>
