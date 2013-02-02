<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'footer' . DS . 'tmpl' ); ?>
<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'backbutton' . DS . 'tmpl' ); ?>

<div class="joomleague">
<!-- projectheading -->

<?PHP

//$this->overallconfig['show_footer'] = 1;

echo '<div class="componentheading">';
			//echo $this->leaguename;
			#JoomleagueHTML::PrintIcon( $row, $params, false, '' );
			#JoomleagueModelProject::PrintIcon( null, null, true, '' );
			echo '</div>';
?>

<?php echo $this->loadTemplate('sectionheader'); ?>
<?php echo $this->loadTemplate('ranking'); ?>
<?PHP
if ($this->config['show_colorlegend']==1){echo $this->loadTemplate('colorlegend');}
	
if ($this->config['show_explanation']==1){echo $this->loadTemplate('explanation');}
?>
	
<!-- backbutton -->
<div>
<?php echo $this->loadTemplate('backbutton'); ?>
<!-- footer -->
<?php echo $this->loadTemplate('footer'); ?>
</div>

</div>