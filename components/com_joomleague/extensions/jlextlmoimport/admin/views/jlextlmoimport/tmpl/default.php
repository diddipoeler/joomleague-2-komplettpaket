<?php defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_( 'behavior.tooltip' );

// Set toolbar items for the page
//JToolBarHelper::title( JText::_( JText::_( 'JL_ADMIN_LMO_IMPORT_TITLE_1' ) ) );
//JToolBarHelper::title( JText::_( 'JL_ADMIN_LMO_IMPORT_TITLE_1'  , 'extension.png') );
//JToolBarHelper::save();
//JToolBarHelper::apply();

$url = 'components/com_joomleague/extensions/jlextlmoimport/admin/assets/images/lmo.jpg';
$alt = 'Lmo Logo';
// $attribs['width'] = '170px';
// $attribs['height'] = '26px';
$attribs['align'] = 'left';
$logo = JHtml::_('image', $url, $alt, $attribs);

/*
// test
// Define arrays filled with test data; would normally come from your database
$cars = array('Ferrari', 'Bugatti', 'Porsche');
$babes = array('Megan Fox', 'Alyssa Milano', 'Doutzen Kroes');

// Create an empty array to be filled with options
$options = array();

// Create the initial option
$options[] = JHTML :: _('select.option', '', '- What do you like most -');

// Open our 'Cars' optgroup
$options[] = JHTML::_('select.optgroup', 'Cars');

// Loop through the 'Cars' data
foreach($cars as $key => $text) {
 // Create each option tag within this optgroup
 $options[] = JHTML::_('select.option', $key, $text);
}

// Use the hack below to close the optgroup
$options[] = JHTML::_('select.option', '');

// Now open our 'Babes' optgroup
$options[] = JHTML::_('select.optgroup', 'Babes');

// Loop through the 'Babes' data this time
foreach($babes as $key => $text) {
 // Create each option tag within this optgroup
 $options[] = JHTML::_('select.option', $key, $text);
}

// Use the hack below to close this last optgroup
$options[] = JHTML::_('select.option', '');

// Generate the select element with our parameters
$select = JHTML::_(
 'select.genericlist', // Because we are creating a 'select' element
 $options,             // The options we created above
 'select_name',        // The name your select element should have in your HTML 
 'size="1" ',          // Extra parameters to add to your element
 'value',              // The name of the object variable for the option value
 'text',               // The name of the object variable for the option text
 'selected_key',       // The key that is selected (accepts an array or a string)
 false                 // Translate the option results?
);
 
// Display our select box
echo $select;
*/



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
?>

<div id="editcell">
	<form enctype='multipart/form-data' action='<?php echo $this->request_url; ?>' method='post' name='adminForm'>
		<table class='adminlist'>
			<thead>
      <tr>
      <th><?php echo JHtml::_('image', $url, $alt, $attribs);; ?>
      
      <?php echo JText::sprintf('JL_ADMIN_LMO_IMPORT_TABLE_TITLE_1',$this->config->get('upload_maxsize') / 1000000 ); ?>
      </th>
      </tr>
      </thead>
			<tfoot><tr><td><?php
				echo '<p>';
					echo '<b>'.JText::_('JL_ADMIN_LMO_IMPORT_EXTENTION_INFO').'</b>';
				echo '</p>';
				echo '<p>';
					echo JText::_('JL_ADMIN_LMO_IMPORT_HINT1').'<br>';
				echo '</p>';
				echo '<p>';
					echo JText::sprintf('JL_ADMIN_LMO_IMPORT_HINT2',$this->revisionDate);
				echo '</p>';
				/*
				$linkParams=array();
				$linkParams['target']='_blank';
				$linkURL='http://forum.joomleague.net/viewtopic.php?f=13&t=10985#p51461';
				$link=JRoute::_($linkURL);
				$linkParams['title']=JText::_('JL_ADMIN_LMO_IMPORT_TOPIC_FORUM');
				$forumLink=JHTML::link($link,$linkURL,$linkParams);
				$linkURL='http://bugtracker.joomleague.net/issues/226';
				$link=JRoute::_($linkURL);
				$linkParams['title']=JText::_('JL_ADMIN_LMO_IMPORT_TOPIC_BUGTRACKER');
				$bugtrackerLink=JHTML::link($link,$linkURL,$linkParams);
				echo '<p>'.JText::_('JL_ADMIN_LMO_IMPORT_HINT3').'</p>';
				echo "<p>$forumLink</p>";
				echo "<p>$bugtrackerLink</p>";
				*/
				?>
        </td>
        </tr>
        </tfoot>
			<tbody>

      <?PHP
      if ( 2 == 1 )
      {
      ?>
      <tr>
      <td>
      <fieldset style='text-align: center; '>      
<input class='input_box' type='checkbox' id='lmoimportuseteams' name='lmoimportuseteams'  value='1' /><?php echo JText::_('JL_ADMIN_LMO_IMPORT_USE_PROJECTTEAMS'); ?>      
</fieldset>
      </td>
      </tr>
      <?PHP
      }
      ?>
      <tr>
      <td>
      <fieldset style='text-align: center; '>

				<input class='input_box' id='import_package' name='import_package' type='file' size='57' />
				<input class='button' type='submit' value='<?php echo JText::_('JL_ADMIN_LMO_IMPORT_UPLOAD_BUTTON'); ?>' />
			</fieldset>
      </td>
      </tr>
      </tbody>
		</table>
		<input type='hidden' name='sent' value='1' />
		<input type='hidden' name='MAX_FILE_SIZE' value='<?php echo $this->config->get('upload_maxsize'); ?>' />
		
		<input type='hidden' name='task' value='jlextlmoimport.save' />
		<?php echo JHTML::_('form.token')."\n"; ?>
	</form>
</div>