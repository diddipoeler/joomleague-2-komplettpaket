<?php defined('_JEXEC') or die('Restricted access');

JHtml::_( 'behavior.tooltip' );
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<fieldset class="adminform">
		<legend>
			<?php
			echo JText::sprintf(	'COM_JOOMLEAGUE_ADMIN_DIVS_TITLE2',
									'<i>' . $this->projectws->name . '</i>' );
			?>
		</legend>
		<table>
			<tr>
				<td align="left" width="100%">
					<?php
					echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_FILTER' );
					?>:&nbsp;<input	type="text" name="search" id="search"
									value="<?php echo $this->lists['search']; ?>"
									class="text_area" onchange="$('adminForm').submit(); " />
					<button onclick="this.form.submit(); ">
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_GO' );
						?>
					</button>
					<button onclick="document.getElementById('search').value='';this.form.submit(); ">
						<?php
						echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_RESET' );
						?>
					</button>
				</td>
				<td class="nowrap">
					<?php
						echo $this->lists['state'];
					?>
					&nbsp;
				</td>
			</tr>
		</table>

		<div id="editcell">
			<table class="adminlist">
				<thead>
					<tr>
						<th width="5" style="vertical-align: top; ">
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_GLOBAL_RESET' );
							?>
						</th>
						<th width="20" style="vertical-align: top; ">
							<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
						</th>
						<th width="20" style="vertical-align: top; ">
							&nbsp;
						</th>
						<th class="title" style="vertical-align: top; ">
							<?php
							echo JHtml::_( 'grid.sort', 'COM_JOOMLEAGUE_ADMIN_DIVS_NAME', 'dv.name', $this->lists['order_Dir'], $this->lists['order'] );
							?>
						</th>
						<th class="title" style="vertical-align: top; ">
							<?php
							echo JHtml::_( 'grid.sort', 'COM_JOOMLEAGUE_ADMIN_DIVS_S_NAME', 'dv.shortname', $this->lists['order_Dir'], $this->lists['order'] );
							?>
						</th>
						<th class="title" style="vertical-align: top; ">
							<?php
							echo JHtml::_( 'grid.sort', 'COM_JOOMLEAGUE_ADMIN_DIVS_PARENT_NAME', 'parent_name', $this->lists['order_Dir'], $this->lists['order'] );
							?>
						</th>
						<th width="85" style="vertical-align: top; ">
							<?php
							echo JHtml::_( 'grid.sort', 'COM_JOOMLEAGUE_GLOBAL_ORDER', 'dv.ordering', $this->lists['order_Dir'], $this->lists['order'] );
							echo '<br />';
							echo JHtml::_( 'grid.order', $this->items );
							?>
						</th>
						<th style="vertical-align: top; ">
							<?php
							echo JHtml::_( 'grid.sort', 'COM_JOOMLEAGUE_GLOBAL_ID', 'dv.id', $this->lists['order_Dir'], $this->lists['order'] );
							?>
						</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td colspan="9">
							<?php
							echo $this->pagination->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>

				<tbody>
				<?php
					$k = 0;
					for ( $i = 0, $n = count( $this->items ); $i < $n; $i++ )
					{
						$row		=& $this->items[$i];
						$link 		= JRoute::_( 'index.php?option=com_joomleague&task=division.edit&cid[]=' . $row->id );
						$checked 	= JHtml::_( 'grid.checkedout',   $row, $i );
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td style="text-align:center; ">
								<?php echo $this->pagination->getRowOffset( $i ); ?>
							</td>
							<td style="text-align:center; ">
								<?php echo $checked; ?>
							</td>
							<?php
							if ( JLTable::_isCheckedOut( $this->user->get( 'id' ), $row->checked_out ) )
							{
								$inputappend = ' disabled="disabled"';
								?>
								<td style="text-align:center; ">
									&nbsp;
								</td>
								<?php
							}
							else
							{
								$inputappend = '';
								?>
								<td style="text-align:center; ">
									<a href="<?php echo $link; ?>">
										<?php
										$imageTitle = JText::_( 'COM_JOOMLEAGUE_ADMIN_DIVS_EDIT_DETAILS' );
										echo JHtml::_(	'image', 'administrator/components/com_joomleague/assets/images/edit.png',
														$imageTitle,
														'title= "' . $imageTitle . '"' );
										?>
									</a>
								</td>
								<?php
							}
							?>
							<td>
								<?php
								echo $row->name;
								?>
							</td>
							<td>
								<?php
								echo $row->shortname;
								?>
							</td>
							<td>
								<?php
								echo $row->parent_name;
								?>
							</td>
							<td class="order">
								<span>
									<?php
									echo $this->pagination->orderUpIcon( $i, $i > 0 , 'division.orderup', 'COM_JOOMLEAGUE_GLOBAL_ORDER_UP', true );
									?>
								</span>
								<span>
									<?php
									echo $this->pagination->orderDownIcon( $i, $n, $i < $n, 'division.orderdown', 'COM_JOOMLEAGUE_GLOBAL_ORDER_DOWN', true );
									$disabled = true ?  '' : 'disabled="disabled"';
									?>
								</span>
								<input  type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled; ?>
										class="text_area" style="text-align: center" />
							</td>
							<td style="text-align:center; ">
								<?php
								echo $row->id;
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
				</tbody>
			</table>
		</div>
	</fieldset>

	<input type="hidden" name="task"				value="" />
	<input type="hidden" name="boxchecked"			value="0" />
	<input type="hidden" name="filter_order"		value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir"	value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
