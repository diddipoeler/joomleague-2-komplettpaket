<?php defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php 
	echo $this->loadTemplate('projectheading');

	if (($this->config['show_matrix'])==1)
	{
		if(isset($this->divisions) && count($this->divisions) > 1) {
			foreach ($this->divisions as $division) {
				$this->teams 		= $this->model->getTeamsIndexedByPtid($division->id);
				$this->division 	= $division;
				$this->divisionid 	= $division->id;
				if (($this->config['show_sectionheader'])==1)
				{
					echo $this->loadTemplate('sectionheader');
				}
				echo $this->loadTemplate('matrix').'<br />';
			}
		} else {
			echo $this->loadTemplate('matrix');
		}
	}

	if ($this->config['show_help']==1)
	{
		echo $this->loadTemplate('hint');
	}

	echo "<div>";
	echo $this->loadTemplate('backbutton');
	echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
