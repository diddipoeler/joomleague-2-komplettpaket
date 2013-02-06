<?php defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php 
	if ($this->config['show_projectheader'] == 1)
	{
		echo $this->loadTemplate('projectheading');
	}

	if ($this->projectteam)
	{
		if (($this->config['show_sectionheader'])==1)
		{
			echo $this->loadTemplate('sectionheader');
		}

		if (($this->config['show_team_logo'])==1)
		{
			echo $this->loadTemplate('picture');
		}

		if (($this->config['show_description'])==1)
		{
			echo $this->loadTemplate('description');
		}

		if (($this->config['show_players'])==1)
		{
			if (($this->config['show_players_layout'])=='player_standard')
			{
				echo $this->loadTemplate('players');
			} else if (($this->config['show_players_layout'])=='player_card') {
				$document 	= JFactory::getDocument();
				$option 	= JRequest::getCmd('option');
				$version 	= urlencode(JoomleagueHelper::getVersion());
				$document->addStyleSheet(  $this->baseurl . '/components/'.$option.'/assets/css/'.$this->getName().'_card.css?v=' . $version );
				echo $this->loadTemplate('players_card');
			}
			else if (($this->config['show_players_layout'])=='player_johncage') {
			$document 	= JFactory::getDocument();
			$option 	= JRequest::getCmd('option');
			$version 	= urlencode(JoomleagueHelper::getVersion());
			$document->addStyleSheet(  $this->baseurl . '/components/'.$option.'/assets/css/'.$this->getName().'_johncage.css?v=' . $version );
			echo $this->loadTemplate('players_johncage');
//            echo $this->loadTemplate('person_player');
			}
		}

		if (($this->config['show_staff'])==1)
		{
			if (($this->config['show_staff_layout'])=='staff_standard')
			{
				echo $this->loadTemplate('staff');
			} else if (($this->config['show_staff_layout'])=='staff_card') {
				$document 	= JFactory::getDocument();
				$option 	= JRequest::getCmd('option');
				$version 	= urlencode(JoomleagueHelper::getVersion());
				$document->addStyleSheet(  $this->baseurl . '/components/'.$option.'/assets/css/'.$this->getName().'_card.css?v=' . $version );
				echo $this->loadTemplate('staff_card');
			}
			else if (($this->config['show_staff_layout'])=='staff_johncage') {
			$document 	= JFactory::getDocument();
			$option 	= JRequest::getCmd('option');
			$version 	= urlencode(JoomleagueHelper::getVersion());
			$document->addStyleSheet(  $this->baseurl . '/components/'.$option.'/assets/css/'.$this->getName().'_johncage.css?v=' . $version );
			echo $this->loadTemplate('staff_johncage');
//            echo $this->loadTemplate('person_staff');
            
			}
		}
	}
	else
	{
		echo "<p>Project team could not be determined</p>";
	}

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
