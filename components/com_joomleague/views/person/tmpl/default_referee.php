<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

	<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr class="sectiontableheader">
			<td colspan="2">
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_REFEREE_DATA' );
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<?php
			if ( $this->config['show_referee_photo'] == 1 )
			{
				?>
				<td width="50%" align="center" valign="middle">
					<?php
					$imgTitle = JText::_( "COM_JOOMLEAGUE_REFEREE_PICTURE" ) . " " . $this->referee->firstname . ' ' . $this->referee->lastname;
					$picture = ( $this->referee->picture!=''&&!is_null( $this->referee->picture )&&file_exists( $this->referee->picture ) ) ?
								 $this->referee->picture : $this->referee->default_picture;
					if (!file_exists( $picture ))
					{
						$picture = JoomleagueHelper::getDefaultPlaceholder("player");
					}
					echo JHTML::image( $picture, $imgTitle, array( ' title' => $imgTitle ) );
					?>
				</td>
				<?php
			}
			?>

			<td width="50%" align="center" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="40%" align="right">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_REFEREE_NAME');
								?>
							</b>
						</td>
						<td width="60%" class="td_l">
							<?php
							$dummy = str_replace( "%FIRSTNAME%", $this->referee->firstname, JText::_( 'COM_JOOMLEAGUE_REFEREE_NAME_FORM' ) );
							$dummy = str_replace( "%LASTNAME%", $this->referee->lastname, $dummy );
							$outputName = $dummy;

							echo $outputName;

                        	echo "&nbsp;&nbsp;" . Countries::getCountryFlag($row->country);;
							?>
						</td>
					</tr>
				</table>
			</td>


	</table>
<br />
