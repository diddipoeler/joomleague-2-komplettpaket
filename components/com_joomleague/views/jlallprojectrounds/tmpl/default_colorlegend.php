<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );


// echo 'tableconfig<pre>';
// print_r($this->tableconfig);
// echo '</pre>';

// echo 'show_colors_legend -> '.$this->tableconfig['show_colors_legend'].'<br>';

?>
<!-- colors legend START -->
<?php
	if (!isset($this->tableconfig['show_colors_legend'])){$this->tableconfig['show_colors_legend']=1;}
	if ($this->tableconfig['show_colors_legend'])
	{
		?>
		<br />
		<table width='96%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<?php
				JoomleagueHelper::showColorsLegend($this->colors);
				?>
			</tr>
		</table>
		<?php
	}
?>
<!-- colors legend END -->