<?php defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php
	echo $this->loadTemplate('projectheading');

	if ($this->match)
	{
		if (($this->config['show_sectionheader'])==1)
		{
			echo $this->loadTemplate('sectionheader');
		}

		if (($this->config['show_nextmatch'])==1)
		{
			echo $this->loadTemplate('nextmatch');
		}

		if (($this->config['show_details'])==1)
		{
			echo $this->loadTemplate('details');
		}

		if (($this->config['show_preview'])==1)
		{
			echo $this->loadTemplate('preview');
		}

		if (($this->config['show_stats'])==1)
		{
			echo $this->loadTemplate('stats');
		}

		if (($this->config['show_history'])==1)
		{
			echo $this->loadTemplate('history');
		}

		if (($this->config['show_previousx'])==1)
		{
			$this->currentteam = $this->match->projectteam1_id;
			echo $this->loadTemplate('previousx');
			$this->currentteam = $this->match->projectteam2_id;
			echo $this->loadTemplate('previousx');
		}

		echo "<div>";
			echo $this->loadTemplate('backbutton');
			echo $this->loadTemplate('footer');
		echo "</div>";
	}
	else
	{
		echo "<p>" . JText::_('COM_JOOMLEAGUE_NEXTMATCH_NO_MORE_MATCHES') . "</p>";
	}
	?>
</div>
