<?php defined( '_JEXEC' ) or die( 'Restricted access' ); 

if ( $this->show_debug_info )
{
echo 'club address_string<pre>',print_r($this->address_string,true),'</pre><br>';
echo 'club teams<pre>',print_r($this->teams,true),'</pre><br>';
echo 'club extended<pre>',print_r($this->extended,true),'</pre><br>';

}

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php 
	echo $this->loadTemplate('projectheading');

	if (($this->config['show_sectionheader'])==1)
	{ 
		echo $this->loadTemplate('sectionheader');
	}

	// Needs some changing &Mindh4nt3r
	echo $this->loadTemplate('clubinfo');
		
	echo "<div class='jl_defaultview_spacing'>";
	echo "&nbsp;";
	echo "</div>";


	//fix me
	if (($this->config['show_extended'])==1)
	{
		echo $this->loadTemplate('extended');
		echo "<div class='jl_defaultview_spacing'>";
		echo "&nbsp;";
		echo "</div>";	
	}

	if (($this->config['show_maps'])==1)
	{ 
		echo $this->loadTemplate('maps');
		
		echo "<div class='jl_defaultview_spacing'>";
		echo "&nbsp;";
		echo "</div>";
	}

		
	if (($this->config['show_teams_of_club'])==1)
	{ 
		echo $this->loadTemplate('teams');
			
		echo "<div class='jl_defaultview_spacing'>";
		echo "&nbsp;";
		echo "</div>";
	}
    
    if (($this->config['show_club_rssfeed']) == 1  && !empty($this->rssfeedoutput) )
	{
		echo $this->loadTemplate('rssfeed-table');
        echo $this->loadTemplate('rssfeed');
	}


	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
