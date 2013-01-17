<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<!-- START of match details -->
<h2><?php echo JText::_('COM_JOOMLEAGUE_NEXTMATCH_DETAILS'); ?></h2>
<table
	width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<!-- Prev Match-->
	<?php
	if ($this->match->old_match_id > 0)
	{
		?>
	<tr>
		<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_OLD_MATCH' ); ?></span>
		<span><?php echo JHTML :: link(JoomleagueHelperRoute::getMatchReportRoute( $this->project->id, 
		$this->match->old_match_id ),
		$this->oldmatchtext); ?></span></td>
	</tr>
	<?php
	}
	?>
	<!-- Next Match-->
	<?php
	if ($this->match->new_match_id > 0)
	{
		?>
	<tr>
		<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_NEW_MATCH' ); ?></span>
		<span><?php echo JHTML :: link(JoomleagueHelperRoute::getNextMatchRoute( $this->project->id, 
		$this->match->new_match_id ),
		$this->newmatchtext);?></span></td>
	</tr>
	<?php
	}
	?>

	<!-- Date -->
	<?php
	if ($this->config['show_match_date'] == 1)
	{
		if ($this->match->match_date > 0): ?>
			<tr>
				<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_DATE' ); ?></span>
					<span><?php echo JHTML::date($this->match->match_date, JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_GAMES_DATE' ) ); ?></span>
				</td>
			</tr>
			<?php endif;
	} ?>

	<!-- Time -->
	<?php
	if ($this->config['show_match_time'] == 1)
	{
		if ($this->match->match_date > 0): ?>
			<tr>
				<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_TIME' ); ?></span>
					<span><?php echo JoomleagueHelperHtml::showMatchTime($this->match, $this->config, $this->overallconfig, $this->project); ?></span>
				</td>
			</tr>
			<?php endif;
	} ?>

	<!-- present -->
	<?php
	if ($this->config['show_time_present'] == 1)
	{
		if ($this->match->time_present > 0): ?>
			<tr>
				<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_PRESENT' ); ?></span>
					<span><?php echo $this->match->time_present; ?></span></td>
			</tr>
			<?php endif;
	} ?>

	<!-- match number -->
	<?php
	if ($this->config['show_match_number'] == 1)
	{
		if ($this->match->match_number > 0): ?>
			<tr>
				<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_NUMBER' ); ?></span>
				<span><?php echo $this->match->match_number; ?></span></td>
			</tr>
		<?php endif;
	} ?>

	<!-- match canceled -->
	<?php if ($this->match->cancel > 0): ?>
	<tr>
		<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_CANCEL_REASON' ); ?></span>
		<span><?php echo $this->match->cancel_reason; ?></span></td>
	</tr>
	<?php endif; ?>

	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>


	<!-- playground -->
	<?php
	if ($this->config['show_match_playground'] == 1)
    {
		if ($this->match->playground_id > 0): ?>
			<?php $playground_link = JoomleagueHelperRoute::getPlaygroundRoute( $this->project->id, $this->match->playground_id);?>
			<tr>
				<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_PLAYGROUND' ); ?></span>
					<span><?php echo JHTML::link ($playground_link, $this->playground->name); ?></span>
				</td>
			</tr>
		<?php endif;
	} ?>

	<!-- referee -->
	<?php
	if ($this->config['show_match_referees'] == 1)
    {
		if (!empty($this->referees)): ?>
			<?php $html = array(); ?>
			<tr>
				<td colspan="3"><span class=""><?php echo JText::_( 'COM_JOOMLEAGUE_NEXTMATCH_REFEREE' ); ?></span>
				<?php foreach ($this->referees AS $ref): ?> <?php $referee_link = JoomleagueHelperRoute::getRefereeRoute($this->project->id, $ref->person_id);?>
				<?php $html[] = JHTML::link ($referee_link, JoomleagueHelper::formatName(null, $ref->firstname, $ref->nickname, $ref->lastname, $this->config["name_format"])) .' ('.$ref->position_name.')'; ?>
				<?php endforeach;?> <span><?php echo implode('</span>, <span>', $html); ?></span>
				</td>
			</tr>
		<?php endif;
	} ?>

</table>

<br />



