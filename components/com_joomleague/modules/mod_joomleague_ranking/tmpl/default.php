<?php

/**
 * @version	 $Id: default.php 4905 2010-01-30 08:51:33Z and_one $
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

// check if any results returned
$items = count($list['ranking']);
if (!$items) {
   echo '<p class="modjlgranking">' . JText::_('NO ITEMS') . '</p>';
   return;
}

$columns     = explode(',', $params->get('columns', 'JL_PLAYED, JL_POINTS'));
$column_names = explode(',', $params->get('column_names', 'MP, PTS'));

if (count($columns) != count($column_names)) {
	JError::raiseWarning(1, JText::_('MOD_JOOMLEAGUE_RANKING_COLUMN_NAMES_COUNT_MISMATCH'));
	$columns     = array();
	$column_name = array();
}

$nametype = $params->get('nametype', 'short_name');
$colors = $list['colors'];

if ( $show_debug_info )
{
echo 'this->mod_joomleague_ranking colors <br /><pre>~' . print_r($colors ,true) . '~</pre><br />';
echo 'this->mod_joomleague_ranking ranking<br /><pre>~' . print_r($list['ranking'],true) . '~</pre><br />';
}

?>

<div class="modjlgranking">

<?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>


<table class="ranking">
<?php if ($params->get('show_tableheader', 0)):?>
	<thead>
		<tr class="sectiontableheader">
			<th class="rank"><?php echo JText::_('MOD_JOOMLEAGUE_RANKING_COLUMN_RANK')?></th>
			<th class="team"><?php echo JText::_('MOD_JOOMLEAGUE_RANKING_COLUMN_TEAM')?></th>
			<?php foreach ($column_names as $col): ?>
			<th class="rankcolval"><?php echo JText::_(trim($col)); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
<?php endif; ?>
	<tbody>
	<?php
		$k = 0;
		$exParam=explode(':',$params->get('visible_team'));
		$favTeamId=$exParam[0];
		$projfavTeamId = $list['project']->fav_team;
		$favEntireRow = $params->get('fav_team_highlight_type', 0);

		$i = 0;
	?>
	<?php foreach (array_slice($list['ranking'], 0, $params->get('limit', 5)) as $item) :  ?>
		<?php
			$class = $params->get('style_class2', 0);
			if ( $k == 0 ) { $class = $params->get('style_class1', 0); }
			$i++;
			$color = "";
			if ($params->get('show_rank_colors', 0))
			{
			  foreach ($colors as $colorItem) {
			    if ($item->rank >= $colorItem['from'] && $item->rank <= $colorItem['to']) {
						$color = $colorItem['color'];
					}
				}
			}
			if (!$favTeamId) $favTeamId = $projfavTeamId;
			$rowStyle = ' style="';
			$spanStyle = '';
			if ( $item->team->id == $favTeamId)
			{
				if( trim( $list['project']->fav_team_color ) != "" )
				{
					if ($favEntireRow == 1) {
						$color = $list['project']->fav_team_color;
					}
				}
				if ($favEntireRow) {
				  $rowStyle .= ($params->get('fav_team_bold', 0) != 0) ? 'font-weight:bold;' : '';
				  $rowStyle .= ($list['project']->fav_team_text_color != '') ? 'color:' . $list['project']->fav_team_text_color . ';' : '';
				}
				$spanStyle = '<span style="padding:2px;';
				$spanStyle .= ($params->get('fav_team_bold', 0) != 0) ? 'font-weight:bold;' : '';
				$spanStyle .= ($list['project']->fav_team_text_color != '') ? 'color:'.$list['project']->fav_team_text_color.';' : '';
				$spanStyle .= ($list['project']->fav_team_color != '') ? 'background-color:'.$list['project']->fav_team_color.';' : '';
				$spanStyle .= '">';

			}
			$rowStyle .= 'background-color:' . $color . ';';
			$rowStyle .= '"';

		?>
		<tr class="<?php echo $class; ?>">
			<td class="rank"<?php if ($color != '') echo $rowStyle; ?>><?php echo $item->rank; ?></td>
			<td class="team"<?php if ($color != '' AND $params->get('show_full_row_colors') =='1') echo $rowStyle; ?>>
				<?php if ($params->get('show_logo', 0)): ?>
				<?php echo modJLGRankingHelper::getLogo($item, $params->get('show_logo', 0)); ?>
				<?php endif; ?>
				<?php if ($spanStyle != '') echo $spanStyle; ?>
				<?php if ($params->get('teamlink', 'none') != 'none'): ?>
				<?php echo JHTML::link(modJLGRankingHelper::getTeamLink($item, $params, $list['project']), $item->team->$nametype); ?>
				<?php else: ?>
				<?php echo $item->team->$nametype; ?>
				<?php endif; ?>
				<?php if ($spanStyle != '') echo '</span>'; ?>
			</td>
			<?php foreach ($columns as $col): ?>
			<td class="rankcolval"<?php if ($color != '' AND $params->get('show_full_row_colors') =='1') echo $rowStyle; ?>>
			<?php echo modJLGRankingHelper::getColValue(trim($col), $item); ?>
			</td>
			<?php endforeach; ?>
		</tr>
	<?php $k = 1 - $k; ?>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if ($params->get('show_ranking_link', 1)):?>
<p class="fulltablelink"><?php 
	$divisionid = explode(':', $params->get('division_id', 0));
	$divisionid = $divisionid[0];
	echo JHTML::link(JoomleagueHelperRoute::getRankingRoute($list['project']->slug, null, null, null, null, $divisionid), 
			         JText::_('MOD_JOOMLEAGUE_RANKING_VIEW_FULL_TABLE')); ?></p>
<?php endif; ?>
</div>
