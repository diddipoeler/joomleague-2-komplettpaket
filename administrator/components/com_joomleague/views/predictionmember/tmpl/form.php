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
$text = !$edit ? JText::_( 'JL_GLOBAL_NEW' ) : JText::_( 'JL_GLOBAL_EDIT' );
JToolBarHelper::title( JText::_( 'JL_ADMIN_PMEMBER_PGAME' ) . ': <small><small>[ ' . $text . ' ]</small></small>' );
JToolBarHelper::save();

if ( !$edit )
{
	JToolBarHelper::divider();
	JToolBarHelper::cancel();
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	JToolBarHelper::apply();
	JToolBarHelper::divider();
	JToolBarHelper::cancel( 'cancel', 'JL_GLOBAL_CLOSE' );
}
JToolBarHelper::help( 'screen.joomleague', true );

$uri =& JFactory::getURI();
?>
<!-- import the functions to move the events between selection lists  -->
<?php
#echo JHTML::script( 'JL_eventsediting.js','administrator/components/com_joomleague/assets/js/' );
?>
<script language="javascript" type="text/javascript">

		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel')
			{
				submitform( pressbutton );
				return;
			}
			var mylist = document.getElementById('position_eventslist');
	 		for(var i=0; i<mylist.length; i++)
	 		{
				mylist[i].selected = true;
			}
			// do field validation
			if (form.name.value == "")
			{
				alert( "<?php echo JText::_( 'JL_ADMIN_PMEMBER_ERROR_POS_NAME', true ); ?>" );
			}
			else
			{
				submitform( pressbutton );
			}
		}
</script>

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
				echo JText::_( 'JL_ADMIN_PMEMBER_POS' );
				?>
			</legend>

			<table class="admintable">
                <tr>
    			    <td width="100" align="right" class="key">
    				    <label for="name">
    					    <?php
    					    echo JText::_( 'JL_ADMIN_PMEMBER_SPORTTYPE' );
    					    ?>
    				    </label>
    			    </td>
    			        			    <td>
    				    <input  class="text_area" type="text" name="sports_type_id" id="title" size="25" maxlength="25"
    				            value="<?php echo JText::_( $this->position->sports_type_id ); ?>" />
    			    </td>
    		    </tr>
    			<tr>
					<td width="100" align="right" class="key">
						<label for="name">
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_NAME' );
							?>
						</label>
					</td>
					<td>
						<input  class="text_area" type="text" name="name" id="title" size="32" maxlength="250"
								value="<?php echo $this->position->name; ?>" />
					</td>
				</tr>
				<!--
				<tr>
					<td valign="top" align="right" class="key">
		 				<label for="ordering">
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_ORDERING' );
							?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['ordering'];
						?>
					</td>
				</tr>
				-->
				<tr>
					<td valign="top" align="right" class="key">
		 				<label for="ordering">
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_POS_PLAYERS' );
							?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['isPlayer'];
						?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" class="key">
		 				<label for="ordering">
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_POS_TEAMSTAFFS' );
							?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['isStaff'];
						?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" class="key">
		 				<label for="ordering">
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_POS_REFEREES' );
							?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['isReferee'];
						?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" class="key">
		 				<label for="ordering">
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_POS_CLUBSTAFFS' );
							?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['isClubStaff'];
						?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" class="key">
						<?php
						echo JText::_( 'JL_ADMIN_PMEMBER_PARENT_POS' );
						?>:
					</td>
					<td>
						<?php
						echo $this->lists['parents'];
						?>
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend>
				<?php
				echo JText::_( 'JL_ADMIN_PMEMBER_POS_EVENTS' );
				?>
			</legend>
			<table class="admintable">
				<tr>
					<td >
						<b>
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_EXIST_EVENTS' );
							?>
							<br />
						</b>
						<?php
						echo $this->lists['events'];
						?>
					</td>
					<td>
						<input  type="button" class="inputbox"
								onclick="document.getElementById('eventschanges_check').value=1;move(document.getElementById('eventslist'), document.getElementById('position_eventslist'));selectAll(document.getElementById('position_eventslist'));"
								value="&gt;&gt;" />
						<br /><br />
	 					<input  type="button" class="inputbox"
	 							onclick="document.getElementById('eventschanges_check').value=1;move(document.getElementById('position_eventslist'), document.getElementById('eventslist'));selectAll(document.getElementById('position_eventslist'));"
								value="&lt;&lt;" />
					</td>
					<td>
						<b>
							<?php
							echo JText::_( 'JL_ADMIN_PMEMBER_ASSIGN_POS' );
							?>
							<br />
						</b>
						<?php
						echo $this->lists['position_events'];
						?>
					</td>
					<td align='center'>
						<input  type="button" class="inputbox"
								onclick="document.getElementById('eventschanges_check').value=1;moveOptionUp( 'position_eventslist' );"
								value="<?php echo JText::_( 'JL_GLOBAL_UP' ); ?>" />
						<br /><br />
						<input type="button" class="inputbox"
							   onclick="document.getElementById('eventschanges_check').value=1;moveOptionDown( 'position_eventslist' );"
							   value="<?php echo JText::_( 'JL_GLOBAL_DOWN' ); ?>" />
					</td>
				   </tr>
			</table>
		</fieldset>

		<div class="clr"></div>
		<input type="hidden" name="eventschanges_check"	id="eventschanges_check"	value="0" />
		<input type="hidden" name="option"											value="com_joomleague" />
		<input type="hidden" name="controller"										value="predictionmember" />
		<input type="hidden" name="cid[]"											value="<?php echo $this->prediction->id; ?>" />
		<input type="hidden" name="task"											value="" />
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>