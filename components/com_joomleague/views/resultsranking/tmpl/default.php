<?php defined('_JEXEC') or die('Restricted access');

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer', 'results', 'ranking');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<a name="jl_top" id="jl_top"></a>
	<?php 
	echo $this->loadTemplate('projectheading');

	if ($this->config['show_matchday_dropdown'])
	{
		echo $this->loadTemplate('selectround');
	}
    
    if ( $this->use_joomlaworks == 0 )
    {

	$results = '';
	if ($this->config['show_sectionheader'])
	{
		$results .= $this->loadTemplate('sectionheaderres');
	}
	$results .= $this->loadTemplate('results');
		
	if ($this->params->get('what_to_show_first', 0) == 0)
	{
		echo $results;
	}

	if ($this->config['show_ranking']==1)
	{
		if ($this->config['show_sectionheader'])
		{
			echo $this->loadTemplate('sectionheaderrank');
		}
		echo $this->loadTemplate('ranking');
		
		if ($this->config['show_colorlegend'])
		{
			echo $this->loadTemplate('colorlegend');
		}
		
		if ($this->config['show_explanation']==1)
		{
			echo $this->loadTemplate('explanation');
		}
	}

	if ($this->params->get('what_to_show_first', 0) == 1)
	{
		echo '<br />'.$results;
	}
	
    }
    else
    {
    // diddipoeler
    // anzeige als tabs von joomlaworks
    $params  = '{tab='.JText::_('COM_JOOMLEAGUE_RANKING_PAGE_TITLE' ).'}';
	$params .= $this->loadTemplate('ranking');
    $params .= '{tab='.JText::_( 'COM_JOOMLEAGUE_RESULTS_ROUND_RESULTS' ).'}';
	$params .= $this->loadTemplate('results');
	$params .= "{/tabs}";
    echo JHTML::_('content.prepare', $params);     
        
        
    }
    	
	if ($this->config['show_pagnav']==1)
	{
		echo $this->loadTemplate('pagnav');
	}

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
