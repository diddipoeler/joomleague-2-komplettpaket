<?php defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);


//echo 'all time player<pre>'.print_r($this->rows,true).'</pre><br>';
//echo 'all time player config<pre>'.print_r($this->config,true).'</pre><br>';
//echo 'all time player playerposition<pre>'.print_r($this->playerposition,true).'</pre><br>';
//echo 'all time player positioneventtypes<pre>'.print_r($this->positioneventtypes,true).'</pre><br>';

echo $this->loadTemplate('players');

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
