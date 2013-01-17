<?php defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php
	// General part of person view START
	echo $this->loadTemplate( 'projectheading' );

	if ( $this->config['show_sectionheader'] == 1 )
	{
		echo $this->loadTemplate( 'pr_sectionheader' );
	}

	if ( $this->config['show_plinfo'] == 1 )
	{
		echo $this->loadTemplate( 'pr_info' );
	}

	if ( $this->config['show_plstatus'] == 1 )
	{
		echo $this->loadTemplate( 'pr_status' );
	}

	if ( $this->config['show_description'] == 1 )
	{
		echo $this->loadTemplate( 'pr_description' );
	}
	// General part of person view END

	if ( !isset( $this->config['show_rfhistory'] ) )
	{
		$this->config['show_rfhistory'] = 1;
	}
	if ( !isset( $this->config['show_csthistory'] ) )
	{
		$this->config['show_csthistory'] = 1;
	}

	switch ( $this->showType )
	{
		case 1:
			// Player specific part of person view START
			if ( $this->config['show_plcareer'] == 1 )
			{
				echo $this->loadTemplate( 'pl_playercareer' );
			}

			if ( $this->config['show_plhistory'] == 1 )
			{
				echo $this->loadTemplate( 'pl_playerhistory' );
			}
			// Player specific part of person view END
			break;

		case 2:
			// Staffmember specific part of person view START
			if ( $this->config['show_sthistory'] == 1 )
			{
				echo $this->loadTemplate( 'st_staffhistory' );
			}
			// Staffmember specific part of person view END
			break;

		case 3:
			// Referee specific part of person view START
			if ( $this->config['show_rfhistory'] == 1 )
			{
				echo $this->loadTemplate( 'rf_refereehistory' );
			}
			// Referee specific part of person view END
			break;

		case 4:
			// Referee specific part of person view START
			// INTENTIONALLY EMPTY
			// Referee specific part of person view END
			break;
	}

	echo "<div>";
		echo $this->loadTemplate( 'backbutton' );
		echo $this->loadTemplate( 'footer' );
	echo "</div>";
	?>
</div>
