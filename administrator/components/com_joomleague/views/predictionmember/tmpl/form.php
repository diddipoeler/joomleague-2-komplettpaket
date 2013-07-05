<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_( 'behavior.tooltip' );

// Set toolbar items for the page
$edit = JRequest::getVar( 'edit', true );
$text = !$edit ? JText::_( 'COM_JOOMLEAGUE_GLOBAL_NEW' ) : JText::_( 'COM_JOOMLEAGUE_GLOBAL_EDIT' );
JToolBarHelper::title( JText::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_PGAME' ) . ': <small><small>[ ' . $text . ' ]</small></small>' );
JToolBarHelper::save('predictionmember.save');

if ( !$edit )
{
	JToolBarHelper::divider();
	JToolBarHelper::cancel('predictionmember.cancel');
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	JToolBarHelper::apply('predictionmember.apply');
	JToolBarHelper::divider();
	JToolBarHelper::cancel( 'predictionmember.cancel');
}
JLToolBarHelper::onlinehelp();

$uri =& JFactory::getURI();
?>
<!-- import the functions to move the events between selection lists  -->
<?php
#echo JHTML::script( 'JL_eventsediting.js','administrator/components/com_joomleague/assets/js/' );
?>


<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col50">
		<fieldset class="adminform">
			<legend>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER' );
				?>
			</legend>

			<table class="admintable">
                
    			
				
				<tr>
					<td valign="top" align="right" class="key">
		 				<label for="ordering">
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_PREDICTION_GROUP' );
							?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['parents'];
						?>
					</td>
				</tr>
				
				
				
				
			</table>
		</fieldset>

		

		<div class="clr"></div>
		<input type="hidden" name="eventschanges_check"	id="eventschanges_check"	value="0" />
		<input type="hidden" name="option"											value="com_joomleague" />
		
		<input type="hidden" name="cid[]"											value="<?php echo $this->predictionuser->id; ?>" />
		<input type="hidden" name="task"											value="" />
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>