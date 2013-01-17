<?php defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php /**/ ?>
<script type="text/javascript">
<!--
	var homeroster = new Array;
	<?php
	$i = 0;
	
	foreach ( $this->rosters['home'] as $player )
	{
		$obj = new stdclass();
		$obj->value = $player->value;
		$obj->text  = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->config["name_format"]);
		echo 'homeroster['.($i++).']='.json_encode($obj).";\n";
	}
	?>
	var awayroster = new Array;
	<?php
	$i = 0;
	foreach ( $this->rosters['away'] as $player )
	{
		$obj = new stdclass();
		$obj->value = $player->value;
		$obj->text  = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->config["name_format"]);
		echo 'awayroster['.($i++).']='.json_encode($obj).";\n";
	}
	?>
	var rosters = Array(homeroster, awayroster);
	var str_delete = "<?php echo JText::_('COM_JOOMLEAGUE_EDIT_EVENTS_DELETE'); ?>";

//-->
</script>
<div id="gamesevents">
	<?php // Don't remove following <div id='ajaxresponse-event'></div> as it is neede for ajax changings ?>
	<?php // <div id='ajaxresponse-event'></div> ?>
	<fieldset class="adminform">
		<legend>
			<?php
			echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_ADD_EDIT_DELETE' );
			?>
		</legend>
		<table border='0' >
			<thead>
				<tr>
					<th style='text-align:left; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_TEAM' ); ?></th>
					<th style='text-align:left; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_PLAYER' ); ?></th>
					<th style='text-align:left; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_EVENT' ); ?></th>
					<th style='text-align:center; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_SUM' ); ?></th>
					<th style='text-align:center; ' >
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_TIME' );
						#echo JText::_( 'Hrs' ) . ' ' . JText::_( 'Mins' ) . ' ' . JText::_( 'Secs' );
						?>
					</th>
					<th style='text-align:center; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_NOTICE' ); ?></th>
					<th style='text-align:center; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_ACTION' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( isset( $this->matchevents ) )
				{
					foreach ( $this->matchevents as $event )
					{
						if ($event->event_type_id != 0) {
						?>
						<tr id="row-<?php echo $event->event_id; ?>">
							<td>
								<?php echo $event->team_name; ?>
							</td>
							<td>
								<?php
								// TODO: now remove the empty nickname quotes, but that should probably be solved differently
								echo preg_replace('/\'\' /', "", $event->firstname1 . ' ' . $event->nickname1 .' ' . $event->lastname1 );
								?>
							</td>
							<td>
								<?php echo JText::_($event->eventtype_name); ?>
							</td>
							<td style='text-align:center; ' >
								<?php echo $event->event_sum; ?>
							</td>
							<td style='text-align:center; ' >
								<?php
								echo $event->event_time;
								?>
							</td>
							<td title='' class='hasTip' >
								<?php
								echo ( strlen( $event->notice ) > 20 ) ? substr( $event->notice, 0, 17 ) . '...' : $event->notice;
								?>
							</td>
							<td style='text-align:center; ' >
								<input	id="delete-<?php echo $event->event_id; ?>" type="button" class="inputbox button-delete"
										value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_DELETE' ); ?>" />
							</td>
						</tr>
						<?php
						}
					}
				}
				?>
				<tr id="row-new-event">
					<td>
						<?php
						echo $this->lists['teams'];
						?>
					</td>
					<td id="cell-player">
						&nbsp;
					</td>
					<td>
						<?php
						echo $this->lists['events'];
						?>
					</td>
					<td style='text-align:center; ' >
						<input type="text" size="3" value="" id="event_sum" name="event_sum" class="inputbox" />
					</td>
					<td style='text-align:center; ' >
						<input type="text" size="3" value="" id="event_time" name="event_time" class="inputbox" />
					</td>
					<td style='text-align:center; ' >
						<input type="text" size="20" value="" id="notice" name="notice" class="inputbox" />
					</td>
					<td style='text-align:center; ' >
						<input id="save-new-event" type="button" class="inputbox button-save" value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_SAVE' ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<br>
		
		<table border='0' >
			<thead>
				<tr>
					<th style='text-align:left; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_LIVE_TYPE' ); ?></th>
					<th style='text-align:center; ' >
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_TIME' );
						#echo JText::_( 'Hrs' ) . ' ' . JText::_( 'Mins' ) . ' ' . JText::_( 'Secs' );
						?>
					</th>
					<th style='text-align:center; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_LIVE_NOTES' ); ?></th>
					<th style='text-align:center; ' ><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_ACTION' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( isset( $this->matchevents ) )
				{
					foreach ( $this->matchevents as $event )
					{
						if ($event->event_type_id == 0) {
						?>
						<tr id="row-<?php echo $event->event_id; ?>">
							<td>
								<?php 
								switch ($event->event_sum) {
                                    case 2:
                                        echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_LIVE_TYPE_2' );
                                        break;
                                    case 1:
                                        echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_LIVE_TYPE_1' );
                                        break;
		                        } ?>
							</td>

							<td style='text-align:center; ' >
								<?php
								echo $event->event_time;
								?>
							</td>
							<td title='' class='hasTip' style="width: 500px;">
								<?php
								echo $event->notes;
								?>
							</td>
							<td style='text-align:center; ' >
								<input	id="delete-<?php echo $event->event_id; ?>" type="button" class="inputbox button-delete"
										value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_DELETE' ); ?>" />
							</td>
						</tr>
						<?php
						}
					}
				}
				?>
				<tr id="row-new-comment">

					<td>
						<select name="ctype" id="ctype" class="inputbox select-commenttype">
                            <option value="1"><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_LIVE_TYPE_1' ); ?></option>
                            <option value="2"><?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_LIVE_TYPE_2' ); ?></option>
                        </select> 
					</td>
					<td style='text-align:center; ' >
						<input type="text" size="3" value="" id="c_event_time" name="c_event_time" class="inputbox" />
					</td>
					<td style='text-align:center; ' >
						<textarea rows="2" cols="80" id="notes" name="notes" ></textarea>
					</td>
					<td style='text-align:center; ' >
						<input id="save-new-comment" type="button" class="inputbox button-save-c" value="<?php echo JText::_( 'COM_JOOMLEAGUE_EDIT_EVENTS_SAVE' ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>
<?php /**/ ?>
<br />