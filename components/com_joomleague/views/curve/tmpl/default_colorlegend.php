<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!-- colors legend -->
<?php
if ($this->config['show_colorlegend'])
{
	?>
	<table width='96%' align='center' cellpadding='0' cellspacing='0'>
		<tr>
			<?php
			JoomleagueHelper::showColorsLegend($this->colors);
			?>
		</tr>
	</table>
	<br />
	<?php
}
?>