<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS' ); ?></legend>
	<!-- Additional Details Table START -->
	<table class='adminForm' border='0'>
		<!-- Cancel Match-->
		<tr>
			<td><label for="cancel"><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS_CANCEL' ); ?></label></td>
			<td><?php echo $this->lists['cancel']; ?></td>
		</tr>
		<tr>
			<td><label for="cancel_reason"><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS_CANCEL_REASON' ); ?></label></td>
			<td colspan='3'>
				<input	type="text" name="cancel_reason" value="<?php echo $this->match->cancel_reason; ?>" size="40" class="inputbox" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS_PLAYGROUND' ); ?></td>
			<td>
				<?php echo $this->lists['playgrounds']; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS_CROWD' ); ?></td>
			<td>
				<input	type="text" name="crowd" value="<?php echo $this->match->crowd;?>" size="6" class="inputbox" />
			</td>
		</tr>

	</table>
</fieldset>
<div style="text-align:right; ">
	<input type="submit" value="<?php echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_SAVE' ); ?>">
</div><br />