<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?>">
<?php
$rssitems_colums = $params->def('rssitems_colums', 1);

foreach ($this->rssfeeditems as $feed) 
{
	if( $feed != false )
	{
		//image handling
		$iUrl 	= isset($feed->image->url)   ? $feed->image->url   : null;
		$iTitle = isset($feed->image->title) ? $feed->image->title : null;
		?>
		<table cellpadding="0" cellspacing="0" class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
		<?php
		// feed description
		if (!is_null( $feed->title ) && $params->get('rsstitle', 1)) {
			?>
			<tr>
				<td>
					<div class="jefeedpro_heading_title">
					<?php if ($params->get('rsstitle_linkable', 1)) { ?>
						<a href="<?php echo str_replace( '&', '&amp', $feed->link ); ?>" target="<?php echo $params->get('link_target', '_blank') ?>">
						<?php echo $feed->title; ?></a>
					<?php } else { 
						echo $feed->title;
					 } ?>	
					</div>
				</td>
			</tr>
			<?php
		}
	
		// feed description
		if ($params->get('rssdesc', 1)) {
		?>
			<tr>
				<td class="jefeedpro_heading_desc"><div class="jefeedpro_heading_desc"><?php echo $feed->description; ?></div></td>
				<?php if ($params->get('rssimage', 1) && $iUrl) {?>
				<td align="center" class="jefeedpro_heading_image"><div class="jefeedpro_heading_image"><img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>"/></div></td>
				<?php } ?>
			</tr>
			<?php
		}
	
		$actualItems = count( $feed->items );
		$setItems    = $params->get('rssitems', 5);
	
		if ($setItems > $actualItems) {
			$totalItems = $actualItems;
		} else {
			$totalItems = $setItems;
		}
		?>
		<tr>
			<td colspan="2">
				<table class="jefeedpro<?php echo $params->get( 'moduleclass_sfx'); ?>">
				<?php
				$words = $params->def('word_count', 0);
				$word_tooltip = $params->def('tooltip_wordcount_desc', 0);

				for ($j = 0; $j < $totalItems; $j ++)
				{
					$currItem = & $feed->items[$j];
					// item title
					if (($j % $rssitems_colums) == 0 ) : 
						if ($params->get('row_alternate', 1)) {
							$row = 'row'.(floor($j / $rssitems_colums) % $rssitems_colums) ;
						} else {
							$row = 'row0';
						}
					?>
					<tr class="<?php echo $row; ?>">
					<?php endif; ?>
					<td class="item" style="width:<?php echo floor(99/$rssitems_colums)."%";?>">
					<?php
					if ( !is_null( $currItem->get_link() ) ) {
						// Get tooltip description
						//$des_tooltip = ($word_tooltip == 0) ? $currItem->get_description() : modJeFeedHelper::limitText($currItem->get_description(),$word_tooltip); 		
						$des_tooltip	= modJeFeedProHelper::limitText($currItem->get_description(),$word_tooltip);

					?>
					<?php 
						if ($params->get('enable_tooltip', '1') && (!$params->get('rssitemdesc', 1))){
							$tooltip_content =  ' class="editlinktip hasTip" title="' . $currItem->get_title() . '::' . addslashes(htmlspecialchars($des_tooltip)) . '"';
						} else {
							$tooltip_content = '';
						}
					?>
						<span <?php echo $tooltip_content ?>><a href="<?php echo $currItem->get_link(); ?>" target="<?php echo $params->get('link_target', '_blank') ?>" rel="<?php echo $params->get('no_follow', '') ?>" ><?php echo $currItem->get_title(); ?></a></span>
					<?php
					}
					// item description
					if ($params->get('rssitemdesc', 1))
					{
						?>
						<div style="text-align: <?php echo $params->get('rssrtl', 0) ? 'right': 'left'; ?> !important">
							<?php echo  $des_tooltip ; ?>
						</div>
						<?php
					}
					?>
					</td>
					<?php if (($j % $rssitems_colums) == ($rssitems_colums-1) ) : ?>
					</tr>
					<?php endif; ?>
					<?php
				}
				?>
				</table>
			</td>
			</tr>
		</table>
	<?php 
	} 
} 
?>
</div>
