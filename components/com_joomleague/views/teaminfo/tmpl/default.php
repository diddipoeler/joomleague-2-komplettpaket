<?php defined( '_JEXEC' ) or die( 'Restricted access' ); 


if ( $this->show_debug_info )
{
echo 'this->team<br /><pre>~' . print_r($this->team,true) . '~</pre><br />';
echo 'this->merge_clubs<br /><pre>~' . print_r($this->merge_clubs,true) . '~</pre><br />';
}


// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);

?>
<div class="joomleague">
	<?php
	if ($this->config['show_projectheader']==1)
	{	
		echo $this->loadTemplate('projectheading');
	}
		
	if ($this->config['show_sectionheader']==1)
	{
		echo $this->loadTemplate('sectionheader');
	}
		
	if ($this->config['show_teaminfo']==1)
	{
		echo $this->loadTemplate('teaminfo');
	}

	if ($this->config['show_description']==1)
	{
		echo $this->loadTemplate('description');
	}
	//fix me css	
	if ($this->config['show_extended']==1)
	{
		echo $this->loadTemplate('extended');
	}	
		
	if ($this->config['show_history']==1)
	{
		echo $this->loadTemplate('history');
	}
    
    if ($this->config['show_history_leagues']==1)
	{
		echo $this->loadTemplate('history_leagues');
	}
		
	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
