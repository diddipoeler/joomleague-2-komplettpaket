<?php

/**
 * @version	 $Id: default.php zeta65$
 * @package	 Joomla
 * @subpackage  Joomleague ranking module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die('Restricted access');

// check if any player returned
$items = count($list['player']);
if (!$items) {
	echo '<p class="modjlgrandomplayer">' . JText::_('NO ITEMS') . '</p>';
	return;
}?>

<div class="modjlgrandomplayer">
<ul>
<?php if ($params->get('show_project_name')):?>
<li class="projectname"><?php echo $list['project']->name; ?></li>
<?php endif; ?> <?php
$person=$list['player'];
$link = JoomleagueHelperRoute::getPlayerRoute( $list['project']->slug, 
												$list['infoteam']->team_id, 
												$person->slug );
?>

<li class="modjlgrandomplayer"><?php
$picturetext=JText::_( 'JL_PERSON_PICTURE' );
$text = JoomleagueHelper::formatName(null, $person->firstname, 
												$person->nickname, 
												$person->lastname, 
												$params->get("name_format"));
	
$imgTitle = JText::sprintf( $picturetext .' %1$s', $text);
$picture = $list['inprojectinfo']->picture;
$pic = JoomleagueHelper::getPictureThumb($picture, $imgTitle, $params->get('picture_width'), $params->get('picture_heigth'));
echo '<a href="'.$link.'">'.$pic.'</a>' ;
?></li>
<li class="playerlink">
<?php 
	if($params->get('show_player_flag')) {
		echo Countries::getCountryFlag($person->country)." ";
	}
	if ($params->get('show_player_link'))
	{
		$link = JoomleagueHelperRoute::getPlayerRoute($list['project']->slug, 
														$list['infoteam']->team_id, 
														$person->slug );
		echo JHTML::link($link, $text);
	}
	else
	{
		echo JText::sprintf( '%1$s', $text);
	}
?>
</li>
<?php if ($params->get('show_team_name')):?>
<li class="teamname">
<?php 
	echo JoomleagueHelper::getPictureThumb($list['infoteam']->team_picture,
											$list['infoteam']->name,
											$params->get('team_picture_width',21),
											$params->get('team_picture_height',0),
											1)." ";
	$text = $list['infoteam']->name;
	if ($params->get('show_team_link'))
	{
		$link = JoomleagueHelperRoute::getTeamInfoRoute($list['project']->slug, 
														$list['infoteam']->team_id);
		echo JHTML::link($link, $text);
	}
	else
	{
		echo JText::sprintf( '%1$s', $text);
	}
?>
</li>
<?php endif; ?>
<?php if ($params->get('show_position_name')):?>
<li class="positionname"><?php 
	$positionName = $list['inprojectinfo']->position_name;
	echo JText::_($positionName);?>
</li>
<?php endif; ?>
</ul>
</div>
