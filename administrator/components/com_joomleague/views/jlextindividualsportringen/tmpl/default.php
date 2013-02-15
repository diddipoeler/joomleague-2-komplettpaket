<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');



?>
<div id="editcell">
	<form enctype='multipart/form-data' action='<?php echo $this->request_url; ?>' method='post' name='adminForm'>
		<table class='adminlist'>
			<thead><tr><th><?php echo JText::sprintf('JL_ADMIN_EXTENSION_XML_IMPORT_TABLE_TITLE_1',$this->config->get('upload_maxsize') / 1000000 ); ?></th></tr></thead>
			<tfoot><tr><td><?php
				echo '<p>';
					echo '<b>'.JText::_('JL_ADMIN_EXTENSION_XML_IMPORT_EXTENSION_INFO').'</b>';
				echo '</p>';
				echo '<p>';
					echo JText::_('JL_ADMIN_EXTENSION_XML_IMPORT_HINT1').'<br>';
				echo '</p>';
				?>
                </td>
                </tr>
                </tfoot>
			<tbody>
            <tr>
            <td>
            <fieldset style='text-align: center; '>
				<input class='input_box' id='import_package' name='import_package' type='file' size='57' />
				<input class='button' type='submit' value='<?php echo JText::_('JL_ADMIN_XML_IMPORT_UPLOAD_BUTTON'); ?>' />
				</fieldset>
                </td>
                </tr>
<thead><tr><th><?php echo JText::sprintf('JL_ADMIN_EXTENSION_XML_IMPORT_TABLE_TITLE_1_2',$this->config->get('upload_maxsize') / 1000000 ); ?></th></tr></thead>
<tr>
                <td>
                <?PHP echo $this->tmpfiles; ?>
                </td>
                </tr>
                </tbody>
		</table>
		<input type='hidden' name='sent' value='1' />
		<input type='hidden' name='MAX_FILE_SIZE' value='<?php echo $this->config->get('upload_maxsize'); ?>' />
		<input type='hidden' name='controller' value='jlextindividualsportringen' />
		<input type='hidden' name='task' value='save' />
		<?php echo JHTML::_('form.token')."\n"; ?>
	</form>
</div>