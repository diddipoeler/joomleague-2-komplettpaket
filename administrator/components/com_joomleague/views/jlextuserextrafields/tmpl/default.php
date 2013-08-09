<?php defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering=($this->lists['order'] == 's.ordering');

JHTML::_('behavior.tooltip');JHTML::_('behavior.modal');
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php
				echo JText::_('JGLOBAL_FILTER_BUTTON');
				?>&nbsp;<input	type="text" name="search" id="search"
								value="<?php echo $this->lists['search']; ?>"
								class="text_area" onchange="$('adminForm').submit(); " />
				<button onclick="this.form.submit(); "><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_GO'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit(); ">
					<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_RESET'); ?>
				</button>
			</td>
		</tr>
	</table>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th width="20">&nbsp;</th>
					<th>
						<?php echo JHTML::_('grid.sort','COM_JOOMLEAGUE_ADMIN_EXTRA_FIELDS_NAME','s.name',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
                    
                    <th>
						<?php echo JHTML::_('grid.sort','COM_JOOMLEAGUE_ADMIN_EXTRA_FIELDS_TEMPLATE_BACKEND','s.template_backend',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
                    <th>
						<?php echo JHTML::_('grid.sort','COM_JOOMLEAGUE_ADMIN_EXTRA_FIELDS_TEMPLATE_FRONTEND','s.template_frontend',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
                    <th>
					<?php
						echo JHTML::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_PUBLISHED','s.published',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="10%">
						<?php
						echo JHTML::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ORDER','s.ordering',$this->lists['order_Dir'],$this->lists['order']);
						echo JHTML::_('grid.order',$this->items, 'filesave.png', 'jlextuserextrafield.saveorder');
						?>
					</th>
					<th width="20">
						<?php echo JHTML::_('grid.sort','JGRID_HEADING_ID','s.id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot><tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
			<tbody>
				<?php
				$k=0;
				for ($i=0,$n=count($this->items); $i < $n; $i++)
				{
					$row =& $this->items[$i];
					$link=JRoute::_('index.php?option=com_joomleague&task=jlextuserextrafield.edit&cid[]='.$row->id);
					$checked=JHTML::_('grid.checkedout',$row,$i);
					$published  = JHTML::_('grid.published',$row,$i, 'tick.png','publish_x.png','jlextuserextrafield.');
                    ?>
					<tr class="<?php echo "row$k"; ?>">
						<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td class="center"><?php echo $checked; ?></td>
						<?php
						if (JTable::isCheckedOut($this->user->get('id'),$row->checked_out))
						{
							$inputappend=' disabled="disabled"';
							?><td class="center">&nbsp;</td><?php
						}
						else
						{
							$inputappend='';
							?>
							<td class="center">
								<a href="<?php echo $link; ?>">
									<?php
									$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_EXTRA_FIELDS_EDIT_DETAILS');
									echo JHTML::_(	'image','administrator/components/com_joomleague/assets/images/edit.png',
													$imageTitle,'title= "'.$imageTitle.'"');
									?>
								</a>
							</td>
							<?php
						}
						?>
						<td><?php echo $row->name; ?></td>
                        <td><?php echo $row->template_backend; ?></td>
                        <td><?php echo $row->template_frontend; ?></td>
                        <td class="center"><?php echo $published; ?></td>
						<td class="order">
							<span>
								<?php echo $this->pagination->orderUpIcon($i,$i > 0,'jlextuserextrafield.orderup','COM_JOOMLEAGUE_GLOBAL_ORDER_UP',$ordering); ?>
							</span>
							<span>
								<?php echo $this->pagination->orderDownIcon($i,$n,$i < $n,'jlextuserextrafield.orderdown','COM_JOOMLEAGUE_GLOBAL_ORDER_DOWN',$ordering); ?>
								<?php $disabled=true ? '' : 'disabled="disabled"'; ?>
							</span>
							<input	type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?>
									class="text_area" style="text-align: center" />
						</td>
						<td class="center"><?php echo $row->id; ?></td>
					</tr>
					<?php
					$k=1 - $k;
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_('form.token')."\n"; ?>
</form>