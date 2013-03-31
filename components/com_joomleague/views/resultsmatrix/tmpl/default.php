<?php defined('_JEXEC') or die('Restricted access');

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer', 'results', 'matrix');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague"><a name="jl_top" id="jl_top"></a>
	<?php 
	echo $this->loadTemplate('projectheading');
		
	echo $this->loadTemplate('selectround');
	
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

	if ($this->config['show_matrix']==1)
	{
		if ($this->config['show_sectionheader'])
		{
			echo $this->loadTemplate('sectionheadermatrix');
		}
		echo $this->loadTemplate('matrix');
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
    $params  = '{tab='.JText::_('COM_JOOMLEAGUE_RESULTS_ROUND_RESULTS' ).'}';
	$params .= $this->loadTemplate('results');
    if(isset($this->divisions) && count($this->divisions) > 1) 
  {
    $params .= '{tab='.JText::_( 'COM_JOOMLEAGUE_MATRIX' ).'}';
	$params .= $this->loadTemplate('matrix_division');
    }
    else
    {
    $params .= '{tab='.JText::_( 'COM_JOOMLEAGUE_MATRIX' ).'}';
	$params .= $this->loadTemplate('matrix');
    }
    
	$params .= "{/tabs}";
    echo JHTML::_('content.prepare', $params);        
    }

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
