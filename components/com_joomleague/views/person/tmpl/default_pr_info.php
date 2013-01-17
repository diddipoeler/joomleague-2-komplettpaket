<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- person data START -->
<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr class="sectiontableheader">
		<td colspan="2">
			<?php
			echo '&nbsp;' . JText::_( 'Personal data' );
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<?php
		if ( $this->config['show_player_photo'] == 1 )
		{
			?>
			<td width="50%" align="center" valign="middle">
				<?php
				$imgTitle = JText::sprintf( 'Picture of %1$s %2$s', $this->person->firstname, $this->person->lastname );
				$picture = $this->person->picture;
				if ( !file_exists( $picture ) )
				{
					$picture = JoomleagueHelper::getDefaultPlaceholder("player");
				}
				echo JoomleagueHelper::getPictureThumb($picture, 
														$imgTitle, 
														$this->config['picture_width'],
														$this->config['picture_height']);
				//echo JHTML::image( $picture, $imgTitle, array( ' title' => $imgTitle ) );
				?>
			</td>
			<?php
		}
		?>
		<td width="50%" align="center" valign="top">
			<table width="100%" border="0" cellpadding="2" cellspacing="2">
				<tr>
					<td width="40%" align="right">
						<b>
							<?php
							echo JText::_( 'Name of Person' );
							?>
						</b>
					</td>
					<td width="60%" class="td_l">
						<?php
						$outputName = JText::sprintf( 'PLAYERNAME %1$s %2$s', $this->person->firstname, $this->person->lastname);
						if ( $this->person->user_id )
						{
							switch ( $this->config['show_user_profile'] )
							{
								case 1:	 // Link to Joomla Contact Page
											$link = JoomleagueHelperRoute::getContactRoute( $this->person->user_id );
											$outputName = JHTML::link( $link, $outputName );
											break;

								case 2:	 // Link to CBE User Page with support for JoomLeague Tab
											$link = JoomleagueHelperRoute::getUserProfileRouteCBE(	$this->person->user_id,
																									$this->project->id,
																									$this->person->id );
											$outputName = JHTML::link( $link, $outputName );
											break;

								default:	break;
							}
						}
						echo $outputName . '&nbsp;&nbsp;' . Countries::getCountryFlag( $this->person->country );
						if ( ! empty( $this->person->nickname ) )
						{
							?>
							<tr>
								<td align="right">
									<b>
										<?php
										echo JText::_( 'Nickname' );
										?>
									</b>
								</td>
								<td class="td_l">
									<?php
									echo $this->person->nickname;
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</td>
				</tr>
				<?php
				if (	( $this->config[ 'show_birthday' ] > 0 ) &&
						( $this->config[ 'show_birthday' ] < 5 ) &&
						( $this->person->birthday != '0000-00-00' ) )
				{
					#$this->config['show_birthday'] = 4;
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								switch ( $this->config['show_birthday'] )
								{
									case 	1:			// show Birthday and Age
														$outputStr = 'Birthday / Age';
														break;

									case 	2:			// show Only Birthday
														$outputStr = 'Birthday';
														break;

									case 	3:			// show Only Age
														$outputStr = 'Age';
														break;

									case 	4:			// show Only Year of birth
														$outputStr = 'Year of Birth';
														break;
								}
								echo JText::_( $outputStr );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							#$this->assignRef( 'playerage', $model->getAge( $this->player->birthday, $this->project->start_date ) );
							switch ( $this->config['show_birthday'] )
							{
								case 1:	 // show Birthday and Age
											$birthdateStr =	$this->person->birthday != "0000-00-00" ?
															JHTML::date( $this->person->birthday, JText::_( 'COM_JOOMLEAGUE_DAYDATE' ) ) : "-";
											$birthdateStr .= "&nbsp;(" . JoomleagueHelper::getAge( $this->person->birthday,$this->person->deathday ) . ")";
											break;

								case 2:	 // show Only Birthday
											$birthdateStr =	$this->person->birthday != "0000-00-00" ?
															JHTML::date( $this->person->birthday, JText::_( 'COM_JOOMLEAGUE_DAYDATE' ) ) : "-";
											break;

								case 3:	 // show Only Age
											$birthdateStr = JoomleagueHelper::getAge( $this->person->birthday,$this->person->deathday );
											break;

								case 4:	 // show Only Year of birth
											$birthdateStr =	$this->person->birthday != "0000-00-00" ?
															JHTML::date( $this->person->birthday, JText::_( '%Y' ) ) : "-";
											break;

								default:	$birthdateStr = "";
											break;
							}
							echo $birthdateStr;
							?>
						</td>
					</tr>
					<?php
				}

				if ( $this->person->address != '' )
				{
					?>
					<tr>
						<td class="td_r" style="vertical-align:top; ">
							<b>
								<?php
								echo JText::_( 'Address' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo Countries::convertAddressString(	$club->name,
																	$club->address,
																	$club->state,
																	$club->zipcode,
																	$club->location,
																	$club->country,
																	'COM_JOOMLEAGUE_PERSON_ADDRESS_FORM' );
							?>
						</td>
					</tr>
					<?php
				}

				if ( $this->person->phone != "" )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PHONE' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo $this->person->phone;
							?>
						</td>
					</tr>
					<?php
				}

				if ( $this->person->mobile != "" )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_MOBILE' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo $this->person->mobile;
							?>
						</td>
					</tr>
					<?php
				}

				if ( $this->person->email != "" )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'Email' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							$user = JFactory::getUser();
							if ( ( $user->id ) || ( ! $this->overallconfig['nospam_email'] ) )
							{
								?>
								<a href="mailto: <?php echo $this->person->email; ?>">
									<?php
									echo $this->club->email;
									?>
								</a>
								<?php
							}
							else
							{
								echo JHTML::_('email.cloak', $this->person->email );
							}
							?>
						</td>
					</tr>
					<?php
				}

				if ( $this->person->website != "" )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_WEBSITE' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo JHTML::_(	'link',
											$this->person->website,
											$this->person->website,
											array( 'target' => '_blank' ) );
							?>
						</td>
					</tr>
					<?php
				}

				if( $this->person->height > 0 )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PLAYER_HEIGHT' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo str_replace( "%HEIGHT%", $this->person->height, JText::_( 'COM_JOOMLEAGUE_PLAYER_HEIGHT_FORM' ) );
							?>
						</td>
					</tr>
					<?php
				}
				if ( $this->person->weight > 0 )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PLAYER_WEIGHT' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo str_replace( "%WEIGHT%", $this->person->weight, JText::_( 'COM_JOOMLEAGUE_PLAYER_WEIGHT_FORM' ) );
							?>
						</td>
					</tr>
					<?php
				}
				if ( ( $this->config['show_player_number'] ) && isset($this->inprojectinfo->jerseynumber) &&
					 ( $this->inprojectinfo->jerseynumber > 0 ) )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PLAYER_NUMBER' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							if ( $this->config['player_number_picture'] )
							{
								$posnumber = $this->inprojectinfo->jerseynumber;
								echo JHTML::image( 'images/com_joomleague/database/events/shirt.php?text=' . $posnumber,
													 $posnumber,
													 array( 'title' => $posnumber ) );
							}
							else
							{
								echo $this->inprojectinfo->jerseynumber;
							}
							?>
						</td>
					</tr>
					<?php
				}
				if ( $this->inprojectinfo->position_id != "" )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								$outputStr = 'Position';
								if ($this->showType == 1)
								{
									$outputStr = 'Position in roster';
								}
								elseif ($this->showType == 2)
								{
									$outputStr = 'Position in Roster';
								}
								elseif ($this->showType == 3)
								{
									$outputStr = 'Position in project';
								}
								elseif ($this->showType == 4)
								{
									$outputStr = 'Position in Club';
								}
								echo JText::_( $outputStr );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo JText::_( $this->inprojectinfo->position_name );
							?>
							</td>
					</tr>
					<?php
				}
				if ( ! empty( $this->person->knvbnr ) )
				{
					?>
					<tr>
						<td class="td_r">
							<b>
								<?php
								echo JText::_( 'Registration-Nr.' );
								?>
							</b>
						</td>
						<td class="td_l">
							<?php
							echo $this->person->knvbnr;
							?>
						</td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan="2">
						&nbsp;
					</td>
				</tr>

			</table>
		</td>
	</tr>
</table>
<br />