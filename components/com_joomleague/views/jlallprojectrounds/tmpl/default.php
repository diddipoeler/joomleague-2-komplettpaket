<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'projectheading' . DS . 'tmpl' ); ?>
<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'footer' . DS . 'tmpl' ); ?>
<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'backbutton' . DS . 'tmpl' ); ?>

<div class="joomleague">
<!-- projectheading -->

<?php echo $this->loadTemplate('projectheading'); ?>

<?php echo $this->loadTemplate('sectionheader'); ?>
<?php echo $this->loadTemplate('results_all'); ?>
<?PHP

// if ($this->config['show_colorlegend']==1){echo $this->loadTemplate('colorlegend');}
	
// if ($this->config['show_explanation']==1){echo $this->loadTemplate('explanation');}

?>
	
<!-- backbutton -->
<div>
<?php echo $this->loadTemplate('backbutton'); ?>
<!-- footer -->
<?php echo $this->loadTemplate('footer'); ?>
</div>

</div>