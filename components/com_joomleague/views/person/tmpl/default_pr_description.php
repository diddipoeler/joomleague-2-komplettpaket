<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- Person description START -->
<?php
	if ( $this->person->info != '' )
	{
		$description = $this->person->info;
	}
	elseif ( $this->person->info != '' )
		{
			$description = $this->person->info;
		}
		else $description = "";

	if ( $description != '' )
	{
		?>
		<table width="100%" class="contentpaneopen">
			<tr>
				<td class="contentheading">
					<?php
					echo '&nbsp;' . JText::_( 'Description of Person' );
					?>
				</td>
			</tr>
		</table>
		<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<?php
					echo stripslashes( $description );
					?>
				</td>
			</tr>
		</table>
		<br /><br />
		<?php
	}
?>
<!-- Person description END -->