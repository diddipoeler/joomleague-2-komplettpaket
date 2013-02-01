<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');

/*
beispielseite:
https://www.basketball-bund.net/public/spielplan_list.jsp?print=1&viewDescKey=sport.dbb.liga.SpielplanViewPublic/index.jsp_&liga_id=973
*/


?>
<div id="editcell">
	<form  action='<?php echo $this->request_url; ?>' method='post' name='adminForm'>
		<table class='adminlist'>
			<thead><tr><th><?php echo JText::sprintf('JL_ADMIN_XML_DBB_IMPORT_TABLE_TITLE_1',$this->config->get('upload_maxsize') / 1000000 ); ?></th></tr></thead>
			<tfoot><tr><td><?php
				echo '<p>';
					echo '<b>'.JText::_('JL_ADMIN_XML_DBB_IMPORT_EXTENTION_INFO').'</b>';
				echo '</p>';
				echo '<p>';
					echo JText::_('JL_ADMIN_XML_DBB_IMPORT_HINT1').'<br>';
				echo '</p>';
				echo '<p>';
					echo JText::sprintf('JL_ADMIN_XML_DBB_IMPORT_HINT2',$this->revisionDate);
				echo '</p>';
// 				$linkParams=array();
// 				$linkParams['target']='_blank';
// 				$linkURL='http://forum.joomleague.net/viewtopic.php?f=13&t=10985#p51461';
// 				$link=JRoute::_($linkURL);
// 				$linkParams['title']=JText::_('JL_ADMIN_XML_IMPORT_TOPIC_FORUM');
// 				$forumLink=JHTML::link($link,$linkURL,$linkParams);
// 				$linkURL='http://bugtracker.joomleague.net/issues/226';
// 				$link=JRoute::_($linkURL);
// 				$linkParams['title']=JText::_('JL_ADMIN_XML_IMPORT_TOPIC_BUGTRACKER');
// 				$bugtrackerLink=JHTML::link($link,$linkURL,$linkParams);
// 				echo '<p>'.JText::_('JL_ADMIN_XML_IMPORT_HINT3').'</p>';
// 				echo "<p>$forumLink</p>";
// 				echo "<p>$bugtrackerLink</p>";
				?></td></tr></tfoot>
			<tbody>
      <tr>
      <td>
      <fieldset style='text-align: center; '>
				<?PHP echo '<b>'.JText::_('JL_ADMIN_XML_DBB_IMPORT_LINK').'</b>'; ?>
				<input name='dbblink' type='text' size='200' value='<?php echo $this->dbblink; ?>' />
				
				</fieldset>
        </td>
        </tr>
        
        <tr>
      <td>
     
     <fieldset style='text-align: center; '>
      <?PHP echo '<b>'.JText::_('JL_ADMIN_XML_IMPORT_DBB_TEAMART').'</b>'; ?>
			<?php echo $this->lists['teamart']; ?>
			</fieldset>
       
     <fieldset style='text-align: center; '>
			
				<input class='button' type='submit' value='<?php echo JText::_('JL_ADMIN_XML_DBB_IMPORT_LINK_BUTTON'); ?>' />
			</fieldset> 
      	
        </td>
        </tr>
        
        </tbody>
		</table>
		<input type='hidden' name='sent' value='1' />
		<input type='hidden' name='MAX_FILE_SIZE' value='<?php echo $this->config->get('upload_maxsize'); ?>' />
		
		<input type='hidden' name='task' value='jlextdbbimports.save' />
		<?php echo JHTML::_('form.token')."\n"; ?>
	</form>
</div>