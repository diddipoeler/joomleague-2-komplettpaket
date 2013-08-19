<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- START of match commentary -->
<?php

if (!empty($this->matchcommentary))
{
	?>
	<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				echo '&nbsp;' . JText::_( 'COM_JOOMLEAGUE_MATCHREPORT_MATCH_COMMENTARY' );
				?>
			</td>
		</tr>
	</table>
    
<table class="matchreport" border="0">
			<?php
			foreach ( $this->matchcommentary as $commentary )
			{
				?>
				
				<tr>
					<td class="list">
						<dl>
							<?php echo $commentary->event_time; ?>
						</dl>
					</td>
					<td class="list">
						<dl>
							<?php echo $commentary->notes; ?>
						</dl>
					</td>
				</tr>
				<?php
			}
			?>
</table>        
<?PHP    
}    

?>