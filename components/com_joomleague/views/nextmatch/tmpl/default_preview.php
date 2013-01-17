<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- START of match preview -->
<?php

// workaround to support {jcomments (off|lock)} in match preview
// no comments are shown if {jcomments (off|lock)} is found in the match preview
$commentsDisabled = 0;

if (!empty($this->match->preview) && preg_match('/{jcomments\s+(off|lock)}/is', $this->match->preview))
{
	$commentsDisabled = 1;
}

if (!empty($this->match->preview))
{
	?>
<h2><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_PREVIEW'); ?></h2>
<table class="matchreport">
	<tr>
		<td><?php
		$preview = $this->match->preview;
		$preview = JHTML::_('content.prepare', $preview);

		if ($commentsDisabled) {
			$preview = preg_replace('#{jcomments\s+(off|lock)}#is', '', $preview);
		}

		echo $preview;
		?>
		</td>
	</tr>
</table>
<!-- END of match preview -->

<?php
}

// Comments integration
if (!$commentsDisabled) {

	JPluginHelper::importPlugin( 'joomleague' );
	$dispatcher =& JDispatcher::getInstance();
	$comments = '';

	// get joomleague comments plugin params
	$plugin				= & JPluginHelper::getPlugin('joomleague', 'comments');
	if (is_object($plugin)) {
		$pluginParams = new JParameter($plugin->params);
	}
	else {
		$pluginParams = new JParameter('');
	}
	$separate_comments 	= $pluginParams->get( 'separate_comments', 0 );

	if ($separate_comments) {
		// Comments integration trigger when separate_comments in plugin is set to yes/1
		if ($dispatcher->trigger( 'onNextMatchComments', array( &$this->match, $this->teams[0]->name .' - '. $this->teams[1]->name, &$comments ) )) {
			echo $comments;
		}
	}
	else {
		// Comments integration trigger when separate_comments in plugin is set to no/0
		if ($dispatcher->trigger( 'onMatchComments', array( &$this->match, $this->teams[0]->name .' - '. $this->teams[1]->name, &$comments ) )) {
			echo $comments;
		}
	}
}
