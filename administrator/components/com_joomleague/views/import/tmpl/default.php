<?php

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
window.addEvent('domready', function() {
	$('csv-file-upload-submit').addEvent('click', function(){
		$('task').value = 'import.csv<?php echo $this->table; ?>import';
		$('adminForm').submit();
	});
});

</script>
<form method="post" id="adminForm" enctype="multipart/form-data">
	<fieldset>
		<legend>
			<?php echo JText::sprintf('CSV-IMPORT [%1$s]', '<i>' . $this->table . '</i>'); ?>
		</legend>
		<?php echo '<strong>' . JText::_('CSV-IMPORT INSTRUCTIONS') . '</strong>'; ?>
		<ul>
			<li>
			<?php echo JText::sprintf('IMPORT %1$s COLUMN NAMES', $this->table); ?>
			</li>
			<li>
			<?php echo JText::sprintf('IMPORT %1$s CSV FORMAT', $this->table); ?>
			</li>
			<li><?php echo JText::sprintf(	'CSV FORMAT AS DEFINED IN %1$s',
											'<a target="_blank" href="http://tools.ietf.org/html/rfc4180">rfc 4180</a>'); ?>
			</li>
			<li>
			<?php echo JText::sprintf(	'IMPORT %1$s POSSIBLE COLUMNS: %2$s',
										$this->table,
										'<strong>' . implode(", ",$this->tablefields) . '</strong>'); ?>
			</li>
		</ul>
		<table>
			<tr>
				<td>
				<label for="file">
					<?php echo JText::_('IMPORT SELECT-CSV:'); ?>
				</label>
				</td>
				<td>
						<input type="file" id="csv-file-upload" accept="text/*" name="FileCSV" />
						<input type="submit" id="csv-file-upload-submit" value="<?php echo JText::_('START CSV-IMPORT'); ?>" />
						<span id="upload-clear"></span>
				</td>
			</tr>
			<tr>
				<td>
				<label for="replace">
					<?php echo JText::_('IMPORT REPLACE IF EXISTS:'); ?>
				</label>
				</td>
				<td>
				<?php
				echo JHTML::_('select.booleanlist', 'csv-replace', 'class="inputbox"', 0);
				?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="replace">
						<?php echo JText::_('Enter delimiter:'); ?>
					</label>
				</td>
				<td>
					<input type="text" id="csv-delimiter" name="csvdelimiter" value=";" size="2" />
				</td>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" name="option"		value="com_joomleague" />
	<input type="hidden" name="task" id="task" value="import.import" />
	<?php echo JHTML::_('form.token'); ?>
</form>