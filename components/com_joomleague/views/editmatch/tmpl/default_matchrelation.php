<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<fieldset class="adminform">
	<legend>
		<?php
		echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHRELATION' );
		?>
	</legend>
	<table class='adminForm' cellpadding='0' cellspacing='7' border='0'>
		<tr>
			<td><label><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHRELATION_PREV_MATCH' ); ?></label></td>
			<td>
				<?php
				echo $this->lists['old_match'];
				if($this->match->old_match_id >0)
				{
					?>
					<a href="index.php?option=com_joomleague&tmpl=component&controller=match&task=edit&cid[]=<?php echo $this->match->old_match_id?>">Match Link</a>
					<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td><label><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHRELATION_NEW_MATCH' ); ?></label></td>
			<td>
				<?php
				echo $this->lists['new_match'];
				if($this->match->new_match_id >0)
				{
					?>
					<a href="index.php?option=com_joomleague&tmpl=component&controller=match&task=edit&cid[]=<?php echo $this->match->new_match_id?>">Match Link</a>
					<?php
				}
				?>
			</td>
		</tr>
	</table>
</fieldset>
<div style="text-align:right; ">
	<input type="submit" value="<?php echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_SAVE' ); ?>">
</div><br />