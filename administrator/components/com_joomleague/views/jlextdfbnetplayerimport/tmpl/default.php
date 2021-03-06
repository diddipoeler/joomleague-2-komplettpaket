<?php defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_( 'behavior.tooltip' );
JHTML::_( 'behavior.modal' );

$url = 'administrator/components/com_joomleague/assets/icons/dfbnet-logo.gif';
$url16 = 'components/com_joomleague/extensions/jlextdfbnetplayerimport/admin/assets/images/dfbnet-logo.gif';
$alt = 'DFBNET Logo';
// $attribs['width'] = '170px';
// $attribs['height'] = '26px';
$attribs['align'] = 'left';
$logo = JHtml::_('image', $url, $alt, $attribs);

// Set toolbar items for the page
$doc =& JFactory::getDocument();
$style = " .icon-48-fb {components/com_joomleague/extensions/jlextdfbnetplayerimport/admin/assets/images/dfbnet-logo-16.gif); no-repeat; }";
$doc->addStyleDeclaration( $style );
//JToolBarHelper::title( JText::_( 'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT' ), 'generic.png' );
JToolBarHelper::title(   JText::_( 'DFB-Net Import / BFV ICalc Import' ), 'generic.png' );

//JToolBarHelper::title(   JText::_( 'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT' ), $url16 );

//JToolBarHelper::save();
//JToolBarHelper::apply();


/*
echo 'default project <br>';
echo '<pre>';
print_r($this->project);
echo '</pre>';
*/

/*
echo 'default projectteams <br>';
echo '<pre>';
print_r($this->projectteams);
echo '</pre>';
*/


// enctype='multipart/form-data'

$pfad = JURI::root().'media/com_joomleague/description/extensions_dfbnet_import.pdf';													
?>

<div id="editcell">

	<form enctype='multipart/form-data' action='<?php echo $this->request_url; ?>' method='post' name='adminForm'>
		<table class='adminlist'>
			<thead><tr><th>
      <?php echo JHtml::_('image', $url, $alt, $attribs);; ?>
      <?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_TABLE_TITLE_1',$this->config->get('upload_maxsize') / 1000000 ); ?></th></tr></thead>
			<tfoot><tr><td><?php
				echo '<p>';
					echo '<b>'.JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_EXTENTION_INFO').'</b>';
				echo '</p>';
				echo '<p>';
					echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_HINT1').'<br>';
				echo '</p>';
				echo '<p>';
					echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_HINT2',$this->revisionDate);
				echo '</p>';
				
				?>
        </td>
        </tr>
        </tfoot>
			<tbody>
      <?php
      if ( 1 == 1 )
      {
      ?>
      <tr>
      <td>
      <fieldset style='text-align: center; '>
      <legend>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_SELECT_USE_PROJECT');
				?>
			</legend>      
      <input class='input_box' type='checkbox' id='dfbimportupdate' name='dfbimportupdate'  /><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_USE_PROJECT'); ?>      
      </fieldset>
      </td>
      </tr>
      
      <tr>
      <td>
      <fieldset style='text-align: center; '>
      <legend>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_SELECT_PROJECT_FUSSBALLINEUROPA');
				?>
			</legend>      
      <input class='input_box' type='text' id='projektfussballineuropa' name='projektfussballineuropa'  value=""/><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_USE_PROJECT_ID'); ?>      
      </fieldset>
      </td>
      </tr>
      
      <?php
      }
      ?>
      <tr>
      <td>
      <fieldset style='text-align: center; '>
      <legend>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_WHICH_FILE');
				?>
			</legend>
      <input type="radio" name="whichfile" value="playerfile" checked> <?PHP echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_PLAYERFILE'); ?><br>
      <input type="radio" name="whichfile" value="matchfile"> <?PHP echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_MATCHFILE'); ?><br>
      <input type="radio" name="whichfile" value="icsfile"> <?PHP echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_ICSFILE'); ?><br>
      </fieldset>
      </td>
      </tr>

      <tr>
      <td>
      <fieldset style='text-align: center; '  >
      <legend>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_DELIMITER' );
				?>
			</legend>
			
      <input type="radio" name="delimiter" value=";" checked> <?PHP echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_DELIMITER_SEMICOLON'); ?><br>
      <input type="radio" name="delimiter" value=","> <?PHP echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_DELIMITER_COMMA'); ?><br>
      <input type="radio" name="delimiter" value="\t"> <?PHP echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_DELIMITER_TABULAR'); ?><br>
      </fieldset>
      </td>
      </tr>
            
      <tr>
      <td>
      <fieldset style='text-align: center; '>

				<input class='input_box' id='import_package' name='import_package' type='file' size='57' />
				<input class='button' type='submit' value='<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_UPLOAD_BUTTON'); ?>' />
			</fieldset>
      </td>
      </tr>
      </tbody>
		</table>
		<input type='hidden' name='sent' value='1' />
		<input type='hidden' name='MAX_FILE_SIZE' value='<?php echo $this->config->get('upload_maxsize'); ?>' />
		
		<input type='hidden' name='task' value='jlextdfbnetplayerimport.save' />
		<?php echo JHTML::_('form.token')."\n"; ?>
	</form>
</div>