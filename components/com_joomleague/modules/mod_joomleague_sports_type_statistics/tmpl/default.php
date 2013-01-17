<?php

/**
 * @author And_One <andone@mfga.at>
 * @version	 $Id$
 * @package	 Joomla
 * @subpackage  Joomleague sports type statistics module
 * @copyright   Copyright (C) 2012 JoomLeague.net. All rights reserved.
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
<div class="modjlgsports <?PHP echo $params->get( 'moduleclass_sfx' ); ?>">
	<h4>
	<?php if($data['sportstype']->icon) { ?><img src="<?php echo $data['sportstype']->icon; ?>" class="icon"><?php } ?> <?php echo $data['sportstype']->name; ?>
	</h4>
	<?php if($params->get('show_project',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_leagues',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['leaguescount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_seasons',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['seasonscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_teams',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_TEAMS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectteamscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_players',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYERS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectteamsplayerscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_divisions',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_DIVISIONS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectdivisionscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_rounds',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_ROUNDS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectroundscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_matches',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_MATCHES"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatchescount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_player_events',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_EVENTS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatcheseventscount']?></li>
	</ul>
	<?php } ?>
	<?php if($params->get('show_player_stats',1) == 1) { ?>
	<ul>
		<li class="label <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_STATS"); ?></li>
		<li class="text <?PHP echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatchesstatscount']?></li>
	</ul>
	<?php } ?>
</div>
<?php 
}
?>
