<?php defined('_JEXEC') or die('Restricted access');

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php
	echo $this->loadTemplate('projectheading');

	if ($this->config['show_sectionheader'])
	{
		echo $this->loadTemplate('sectionheader');
	}

	if ($this->config['show_rankingnav']==1)
	{
		echo $this->loadTemplate('rankingnav');
	}

	if ($this->config['show_ranking']==1)
	{
		echo $this->loadTemplate('ranking');
	}

	if ($this->config['show_colorlegend']==1)
	{
		echo $this->loadTemplate('colorlegend');
	}
	
	if ($this->config['show_explanation']==1)
	{
		echo $this->loadTemplate('explanation');
	}
	
	if ($this->config['show_pagnav']==1)
	{
		echo $this->loadTemplate('pagnav');
	}
	
	if (($this->config['show_ranking_maps'])==1)
	{ 
		echo $this->loadTemplate('maps');
	}
	
	if ($this->config['show_help'] == "1")
	{
		echo $this->loadTemplate('hint');
	}

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
