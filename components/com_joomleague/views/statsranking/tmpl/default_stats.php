<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php
$colspan	= 4;
$show_icons	= 0;
if ($this->config['show_picture_thumb'] == 1) $colspan++;
if ($this->config['show_nation'] == 1) $colspan++;
if ($this->config['show_icons'] == 1) $show_icons = 1;
?>

<?php foreach ( $this->stats AS $rows ): ?>
<?php if ($this->multiple_stats == 1) :?>
<h2><?php echo $rows->name; ?></h2>
<?php endif; ?>
<table width="100%" align="center" border="0" cellpadding="3"
	cellspacing="0" class="statranking">
	<thead>
	<tr class="sectiontableheader">
		<th class="rank"><?php	echo JText::_( 'COM_JOOMLEAGUE_STATSRANKING_RANK' );	?></th>

		<?php if ($this->config['show_picture_thumb'] == 1 ):	?>
		<th class="td_c">&nbsp;</th>
		<?php endif; ?>

		<th class="td_l"><?php	echo JText::_( 'COM_JOOMLEAGUE_STATSRANKING_PLAYER_NAME' ); ?>
		</th>

		<?php	if ( $this->config['show_nation'] == 1 ):	?>
		<th class="td_c">&nbsp;</th>
		<?php endif; ?>
		
		<?php	if ( $this->config['show_team'] == 1 ):	?>
		<th class="td_l"><?php	echo JText::_( 'COM_JOOMLEAGUE_STATSRANKING_TEAM' );	?></th>
		<?php endif; ?>
		<?php	if ( $show_icons == 1 ):	?>
		<th class="td_r" class="nowrap"><?php	echo $rows->getImage(); ?></th>
		<?php else: ?>	
		<th class="td_r" class="nowrap"><?php	echo JText::_($rows->name); ?></th>
		<?php endif; ?>		
	</tr>
	</thead>
	<tbody>
	<?php
	if ( count( $this->playersstats[$rows->id]->ranking ) > 0 )
	{
		$k = 0;
		$lastrank = 0;
		foreach((array) $this->playersstats[$rows->id]->ranking as $row )
		{
			if ( $lastrank == $row->rank )
			{
				$rank = '-';
			}
			else
			{
				$rank = $row->rank;
			}
			$lastrank  = $row->rank;
			
			$class = $this->config['style_class2'];
			if ( $k == 0 ) {
				$class = $this->config['style_class1'];
			}
			$favStyle = '';
			$isFavTeam = in_array($row->team_id,$this->favteams);
			if ( $this->config['highlight_fav'] == 1 && $isFavTeam && $this->project->fav_team_highlight_type == 1 )
			{
				$format = "%s";
				$favStyle = ' style="';
				$favStyle .= ($this->project->fav_team_text_bold != '') ? 'font-weight:bold;' : '';
				$favStyle .= (trim($this->project->fav_team_text_color) != '') ? 'color:'.trim($this->project->fav_team_text_color).';' : '';
				$favStyle .= (trim($this->project->fav_team_color) != '') ? 'background-color:' . trim($this->project->fav_team_color) . ';' : '';
				if ($favStyle != ' style="')
				{
				  $favStyle .= '"';
				}
				else {
				  $favStyle = '';
				}
			}

			?>
			
	<tr class="<?php echo $class; ?>"<?php echo $favStyle; ?>>
		<td class="rank"><?php	echo $rank;	?></td>
		<?php	$playerName = JoomleagueHelper::formatName(null, $row->firstname, $row->nickname, $row->lastname, $this->config["name_format"]);?>
		<?php	if ( $this->config['show_picture_thumb'] == 1 ): ?>
		<td class="td_c playerpic">
		<?php 
 		$picture = isset($row->teamplayerpic) ? $row->teamplayerpic : null;
 		if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ))
 		{
 			$picture = $row->picture;
 		}
 		if ( !file_exists( $picture ) )
 		{
 			$picture = JoomleagueHelper::getDefaultPlaceholder("player");
 		}
		echo JoomleagueHelper::getPictureThumb($picture, $playerName,
												$this->config['player_picture_width'],
												$this->config['player_picture_height']);
		?>
		</td>
		<?php endif; ?>

		<td class="td_l playername">
		<?php
		if ( $this->config['link_to_player'] == 1 ) {
			$link = JoomleagueHelperRoute::getPlayerRoute( $this->project->id, $row->team_id, $row->person_id );
			echo JHTML::link( $link, $playerName );
		}
		else {
			echo $playerName;
		}
		?>
		</td>

		<?php	if ( $this->config['show_nation'] == 1 ): ?>
		<td class="td_c playercountry"><?php echo Countries::getCountryFlag($row->country); ?></td>
		<?php endif;	?>

		<?php	if ( $this->config['show_team'] == 1 ):	?>
		<td class="td_l playerteam">
			<?php
			$team=$this->teams[$row->team_id];
			if ( ( $this->config['link_to_team'] == 1 ) && ( $this->project->id > 0 ) && ( $row->team_id > 0 ) )
			{
				$link = JoomleagueHelperRoute::getTeamInfoRoute( $this->project->id, $row->team_id  );
			} else {
				$link = null;
			}
			$teamName = JoomleagueHelper::formatTeamName($team,"t".$row->team_id,$this->config, $isFavTeam, $link);
			echo $teamName;
			?>
		</td>
		<?php endif; ?>

		<td class="td_r playertotal"><?php echo $row->total; ?></td>
	</tr>
	<?php
	$k=(1-$k);
		}
	}
	?>
	</tbody>
</table>

<?php 
if ($this->multiple_stats == 1)
{
?>
<div class="fulltablelink">
<?php echo JHTML::link(JoomleagueHelperRoute::getStatsRankingRoute($this->project->id, ($this->division ? $this->division->id : 0), $this->teamid, $rows->id), JText::_('COM_JOOMLEAGUE_STATSRANKING_VIEW_FULL_TABLE')); ?>
</div>
<?php
}
else
{
	jimport('joomla.html.pagination');
	$pagination = new JPagination( $this->playersstats[$rows->id]->pagination_total, $this->limitstart, $this->limit );
?>
<div class="pageslinks">
	<?php echo $pagination->getPagesLinks(); ?>
</div>

<p class="pagescounter">
	<?php echo $pagination->getPagesCounter(); ?>
</p>
<?php
}
?>

<?php endforeach; ?>
