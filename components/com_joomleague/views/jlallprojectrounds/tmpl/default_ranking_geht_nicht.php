<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

// echo '<pre>';
// print_r($this->currentRanking);
// echo '</pre><br>';

?>

<!-- Main START -->
<a name="jl_top" id="jl_top"></a>

<!-- content -->
<?php
// foreach ( $this->currentRanking as $division => $cu_rk )
// {
	
	
	?>
	<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
		<?php
			echo $this->loadTemplate('rankingheading');
			$this->division = $division;
			$this->current  = &$this->currentRanking;
			echo $this->loadTemplate('rankingrows');
		?>
	</table>
	<br />
	<?php
	
// }
	?>
<!-- ranking END -->



