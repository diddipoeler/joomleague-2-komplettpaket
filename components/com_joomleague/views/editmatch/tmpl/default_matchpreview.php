<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!-- match preview -->
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_JOOMLEAGUE_EDITMATCH_MATCHPREVIEW' ); ?></legend>
	<table>
		<tr>
			<td colspan="2">
				<?php
				$editor = JFactory::getEditor();
				$this->assignRef( 'editor', $editor );
				// parameters : areaname, content, hidden field, width, height, rows, cols
				echo $this->editor->display( 'preview', $this->match->preview, '98%', '200', '50', '15', 'true' );
				?>
				<br />
			 </td>
		</tr>
	</table>
</fieldset>
<div style="text-align:right; ">
	<input type="submit" value="<?php echo JText::_( 'JSAVE' ); ?>">
</div><br />