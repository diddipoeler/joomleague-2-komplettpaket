<?php
/**
 * @category	Core
 * @package		
 * @copyright (C) 2013
 * @license		GNU/GPL, see LICENSE.php
 */
 
;##################################################################
;/* 
;* Modified by 
;*  
;* email: 
;* date: 2013
;* Release: 1.0
;* License : http://www.gnu.org/copyleft/gpl.html GNU/GPL 
;*/
################################################################### 
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<table width="100%" border="0">
	<tr>
		<td width="100%" valign="top">
			<div id="cpanel">
				<?php echo $this->addIcon('tippspiel.png','index.php?option=com_joomleague&view=predictiongames', JText::_('COM_JOOMLEAGUE_MENU_PREDICTIONGAMES'));?>
        		<?php echo $this->addIcon('groups.gif','index.php?option=com_joomleague&view=predictiongroups', JText::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUPS_TITLE'));?>
      			<?php echo $this->addIcon('userpoints.gif','index.php?option=com_joomleague&view=predictionmembers', JText::_('COM_JOOMLEAGUE_MENU_PREDICTIONMEMBERS'));?>
               	<?php echo $this->addIcon('templates.gif','index.php?option=com_joomleague&view=predictiontemplates', JText::_('COM_JOOMLEAGUE_MENU_PREDICTIONTEMPLATES'));?>

			</div>
		</td>
		
	</tr>
	<!-- FOOTER INFO DASHBOARD TODO ALL PAGES -->
	<tr>
		<td style="text-align: left; width: 50%;">
			<a href="http://www.facebook.com/pages/Sportsmanager/558711710835555" target="_blank"><?php echo JText::_( "COM_JOOMLEAGUE_FACEBOOK_FOLLOW" ); ?></a>
			<br/>
			<a href="https://github.com/diddipoeler/sportsmanagement" target="_blank"><?php echo JText::_( "COM_JOOMLEAGUE_GITHUB_FOLLOW" ); ?></a>
			<br/>				
			<a href="http://gplus.to/JoomlaCBE" target="_blank"><?php echo JText::_( "COM_JOOMLEAGUE_GPLUS_FOLLOW" ); ?></a>
			<br/>
			<a href="http://extensions.joomla.org/extensions/owner/JoomlaCBE" target="_blank"><?php echo JText::_( "COM_JOOMLEAGUE_JED_FEEDBACK" ); ?></a>
		</td>
		<td style="text-align: left; width: 50%;">
			<?php echo JText::_( "COM_JOOMLEAGUE_CBE_DESC" ); ?>
			<br/>
			<?php echo JText::_( "COM_JOOMLEAGUE_COPYRIGHT" ); ?>: &copy; <a href="http://www.fussballineuropa.de" target="_blank">Fussball in Europa</a>
			<br/>
			<?php echo JText::_( "COM_JOOMLEAGUE_VERSION" ); ?>: <?php echo JText::sprintf( 'Version: %1$s', $this->version ); ?>
		</td>
		
	</tr>
</table>
